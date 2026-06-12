<?php

namespace App\Console\Commands;

use App\Models\EliteEmail;
use App\Support\EducationEliteMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZBateson\MailMimeParser\IMessage;
use ZBateson\MailMimeParser\MailMimeParser;

class SesInboundSyncCommand extends Command
{
    protected $signature = 'ses:sync-inbound
                            {--dry-run : List files without importing}
                            {--limit=100 : Max emails to import per run}';

    protected $description = 'Import .eml files from S3 SES inbound bucket into the Elite inbox';

    private const DISK = 's3_inbound';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $limit  = (int) $this->option('limit');

        $disk = Storage::disk(self::DISK);

        // List everything in the configured prefix (root of s3_inbound disk)
        try {
            $allFiles = $disk->allFiles();
        } catch (\Throwable $e) {
            $this->error('Cannot list S3 inbound bucket: ' . $e->getMessage());
            Log::error('ses.inbound.sync.list_failed', ['error' => $e->getMessage()]);

            return 1;
        }

        // SES system files to ignore regardless of prefix
        $systemFiles = ['amazon_ses_setup_notification', 'ses_setup_notification'];

        // Filter to .eml files only; also accept files with no extension (raw SES format)
        $files = array_values(array_filter($allFiles, static function (string $path) use ($systemFiles): bool {
            $lower    = strtolower($path);
            $basename = strtolower(basename($path));

            // skip already-processed folder and known SES system files
            if (str_starts_with($lower, 'processed/')) {
                return false;
            }
            if (in_array($basename, $systemFiles, true)) {
                return false;
            }
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            return $ext === 'eml' || $ext === '';
        }));

        if (empty($files)) {
            $this->info('No inbound .eml files found.');

            return 0;
        }

        $this->info(count($files) . ' file(s) found.');

        if ($dryRun) {
            foreach ($files as $f) {
                $this->line("  [dry-run] $f");
            }

            return 0;
        }

        $imported = 0;
        $skipped  = 0;
        $failed   = 0;

        foreach (array_slice($files, 0, $limit) as $key) {
            // Deduplication: if already imported, skip
            if (EliteEmail::where('s3_inbound_key', $key)->exists()) {
                $skipped++;
                continue;
            }

            try {
                $raw = $disk->get($key);
                if ($raw === null || $raw === '') {
                    $this->warn("Empty file, skipping: $key");
                    $failed++;
                    continue;
                }

                $record = $this->importEml($key, $raw);

                // Move to processed/ so the inbox folder stays clean
                $processedKey = 'processed/' . basename($key);
                try {
                    $disk->move($key, $processedKey);
                } catch (\Throwable $moveEx) {
                    Log::warning('ses.inbound.sync.move_failed', [
                        'key' => $key,
                        'error' => $moveEx->getMessage(),
                    ]);
                }

                Log::info('ses.inbound.sync.imported', [
                    'id'   => $record->id,
                    'key'  => $key,
                    'from' => $record->from_address,
                    'subj' => $record->subject,
                ]);

                $imported++;
                $this->line("  ✓ [{$record->id}] {$record->subject} — from {$record->from_address}");
            } catch (\Throwable $e) {
                Log::error('ses.inbound.sync.import_failed', [
                    'key'   => $key,
                    'error' => $e->getMessage(),
                ]);
                $this->warn("  ✗ $key — " . $e->getMessage());
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Done. imported={$imported} skipped={$skipped} failed={$failed}");

        return $failed > 0 ? 1 : 0;
    }

    private function importEml(string $s3Key, string $raw): EliteEmail
    {
        $parser  = new MailMimeParser();
        $message = $parser->parse($raw, false);

        $from    = $this->extractFrom($message);
        $to      = $this->extractTo($message);
        $subject = $this->extractSubject($message);
        $html    = $this->extractHtml($message);
        $text    = $this->extractText($message, $html);
        $date    = $this->extractDate($message);

        // Reject files that clearly aren't real emails
        if (! filter_var($from, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException("Not a valid email (From: {$from}). Likely a system file — skipping.");
        }

        $record = EliteEmail::create([
            'from_address'   => $from,
            'to_address'     => $to,
            'subject'        => $subject,
            'body_html'      => $html,
            'body_text'      => $text,
            's3_inbound_key' => $s3Key,
            'payload'        => [
                'source'     => 'ses_s3',
                's3_key'     => $s3Key,
                'message_id' => $message->getMessageId() ?? '',
            ],
        ]);

        // Back-date to the actual email date if available
        if ($date !== null) {
            $record->created_at = $date;
            $record->updated_at = $date;
            $record->saveQuietly();
        }

        return $record;
    }

    private function extractFrom(IMessage $message): string
    {
        $header = $message->getHeader('From');
        if ($header === null) {
            return 'unknown@unknown';
        }

        // getDecodedValue() returns the full decoded string e.g. "John Doe <john@example.com>"
        $value = $header->getDecodedValue();

        // Extract bare address from angle brackets
        if (preg_match('/<([^>]+)>/', $value, $m)) {
            return strtolower(trim($m[1]));
        }

        return strtolower(trim($value));
    }

    private function extractTo(IMessage $message): ?string
    {
        $header = $message->getHeader('To');
        if ($header === null) {
            return null;
        }

        // getDecodedValue() returns the full decoded value e.g. "Name <email>, Name2 <email2>"
        $value = $header->getDecodedValue();

        // Prefer the Elite-domain address if multiple recipients
        $addresses = preg_split('/\s*,\s*/', $value);
        $apex = EducationEliteMail::apexDomain();

        foreach ($addresses as $addr) {
            // Extract bare address from "Name <email>" or plain "email"
            if (preg_match('/<([^>]+)>/', $addr, $m)) {
                $clean = strtolower(trim($m[1]));
            } else {
                $clean = strtolower(trim($addr));
            }
            if (str_ends_with($clean, '@' . $apex)) {
                return $clean;
            }
        }

        // Fallback: return full To header value trimmed
        return substr(trim($value), 0, 255) ?: null;
    }

    private function extractSubject(IMessage $message): ?string
    {
        // IMessage::getSubject() is the dedicated API for this header
        $v = trim((string) $message->getSubject());

        return $v !== '' ? substr($v, 0, 998) : null;
    }

    private function extractHtml(IMessage $message): ?string
    {
        $part = $message->getHtmlContent();
        if ($part === null || trim($part) === '') {
            return null;
        }

        return $part;
    }

    private function extractText(IMessage $message, ?string $html): ?string
    {
        $text = $message->getTextContent();
        if (is_string($text) && trim($text) !== '') {
            return substr($text, 0, 60000);
        }

        if ($html !== null) {
            $plain = trim(strip_tags($html));

            return $plain !== '' ? substr($plain, 0, 60000) : null;
        }

        return null;
    }

    private function extractDate(IMessage $message): ?\DateTimeImmutable
    {
        $header = $message->getHeader('Date');
        if ($header === null) {
            return null;
        }

        try {
            $value = trim($header->getDecodedValue());

            return $value !== '' ? new \DateTimeImmutable($value) : null;
        } catch (\Throwable) {
            return null;
        }
    }
}

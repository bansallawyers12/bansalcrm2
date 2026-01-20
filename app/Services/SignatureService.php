<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Signer;
use App\Models\Admin;
use App\Models\DocumentNote;
use App\Models\ActivitiesLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SignatureService
{
    /**
     * Send a document for signature
     *
     * @param Document $document
     * @param array $signers Array of ['email' => '', 'name' => '']
     * @param array $options Additional options (subject, message, from_email, template)
     * @return bool
     */
    public function send(Document $document, array $signers, array $options = []): bool
    {
        try {
            $createdSigners = [];

            foreach ($signers as $signerData) {
                $signer = $document->signers()->create([
                    'email' => $signerData['email'],
                    'name' => $signerData['name'],
                    'token' => Str::random(64),
                    'status' => 'pending',
                ]);

                $createdSigners[] = $signer;
            }

            // Update document status and tracking
            $document->update([
                'status' => 'sent',
                'primary_signer_email' => $signers[0]['email'] ?? null,
                'signer_count' => count($signers),
                'last_activity_at' => now(),
            ]);

            // Send emails to all signers
            foreach ($createdSigners as $signer) {
                $this->sendSigningEmail($document, $signer, $options);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send document for signature', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Configure mail settings using config values (from .env)
     */
    protected function configureMailer(): void
    {
        // Get mail configuration from config (which reads from .env)
        $mailHost = config('mail.mailers.smtp.host');
        $mailPort = config('mail.mailers.smtp.port');
        $mailUsername = config('mail.mailers.smtp.username');
        $mailPassword = config('mail.mailers.smtp.password');
        $mailEncryption = config('mail.mailers.smtp.encryption', 'tls');
        $mailFromAddress = config('mail.from.address');
        $mailFromName = config('mail.from.name');
        
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp', [
            'transport' => 'smtp',
            'host' => $mailHost,
            'port' => $mailPort,
            'encryption' => $mailEncryption,
            'username' => $mailUsername,
            'password' => $mailPassword,
        ]);
        Config::set('mail.from.address', $mailFromAddress);
        Config::set('mail.from.name', $mailFromName);
        
        // Clear cached mail manager to apply new config
        app()->forgetInstance('mailer');
        app()->forgetInstance('mail.manager');
    }

    /**
     * Send signing email to a signer
     */
    protected function sendSigningEmail(Document $document, Signer $signer, array $options = []): void
    {
        try {
            // Configure mailer with Zepto settings
            $this->configureMailer();
            
            $signingUrl = url("/sign/{$document->id}/{$signer->token}");
            
            // Get from address from config
            $mailFromName = config('mail.from.name');
            
            $subject = $options['subject'] ?? 'Document Signature Request from Bansal Education';
            $emailMessage = $options['message'] ?? "Please review and sign the attached document.";
            
            // Email data for template
            $emailData = [
                'signerName' => $signer->name,
                'emailMessage' => $emailMessage,
                'documentTitle' => $document->display_title,
                'signingUrl' => $signingUrl,
            ];
            
            // Get from address from config
            $mailFromAddress = config('mail.from.address');
            $mailFromName = config('mail.from.name');
            
            // Send email using the signature-request template
            Mail::send('emails.signature-request', $emailData, function ($mail) use ($signer, $subject, $mailFromAddress, $mailFromName) {
                $mail->to($signer->email, $signer->name)
                     ->subject($subject)
                     ->from($mailFromAddress, $mailFromName);
            });

            // Create activity note for successful email delivery
            DocumentNote::create([
                'document_id' => $document->id,
                'created_by' => Auth::guard('admin')->id() ?? 1,
                'action_type' => 'email_sent',
                'note' => "Email sent to {$signer->name} ({$signer->email})",
                'metadata' => [
                    'signer_id' => $signer->id,
                    'signer_email' => $signer->email,
                    'signer_name' => $signer->name,
                    'subject' => $subject,
                ]
            ]);

            Log::info('Signing email sent', [
                'document_id' => $document->id,
                'signer_email' => $signer->email,
            ]);
        } catch (\Exception $e) {
            // Create activity note for failed email delivery
            try {
                DocumentNote::create([
                    'document_id' => $document->id,
                    'created_by' => Auth::guard('admin')->id() ?? 1,
                    'action_type' => 'email_failed',
                    'note' => "Failed to send email to {$signer->name} ({$signer->email}): {$e->getMessage()}",
                    'metadata' => [
                        'signer_id' => $signer->id,
                        'signer_email' => $signer->email,
                        'signer_name' => $signer->name,
                        'error' => $e->getMessage(),
                    ]
                ]);
            } catch (\Exception $noteException) {
                Log::warning('Failed to create email failure note', [
                    'document_id' => $document->id,
                    'error' => $noteException->getMessage()
                ]);
            }

            Log::error('Failed to send signing email', [
                'document_id' => $document->id,
                'signer_id' => $signer->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send reminder to a signer
     */
    public function remind(Signer $signer, array $options = []): bool
    {
        try {
            // Check if signature is cancelled
            if ($signer->status === 'cancelled') {
                throw new \Exception('Cannot send reminder. Signature has been cancelled.');
            }
            
            // Check if already signed
            if ($signer->status === 'signed') {
                throw new \Exception('Cannot send reminder. Document has already been signed.');
            }
            
            // Check reminder limits
            if ($signer->reminder_count >= 3) {
                throw new \Exception('Maximum reminders already sent');
            }

            // Configure mailer with Zepto settings
            $this->configureMailer();

            $document = $signer->document;
            $signingUrl = url("/sign/{$document->id}/{$signer->token}");
            $reminderNumber = $signer->reminder_count + 1;

            // Get from address from config
            $mailFromAddress = config('mail.from.address');
            $mailFromName = config('mail.from.name');

            // Email data for reminder template
            $emailData = [
                'signerName' => $signer->name,
                'emailMessage' => "This is reminder #{$reminderNumber} to sign your document. Please review and sign the attached document.",
                'documentTitle' => $document->display_title,
                'signingUrl' => $signingUrl,
            ];
            
            $subject = "Reminder #{$reminderNumber}: Document Signature Request from Bansal Education";
            
            // Send reminder email using the signature-request template
            Mail::send('emails.signature-request', $emailData, function ($mail) use ($signer, $subject, $mailFromAddress, $mailFromName) {
                $mail->to($signer->email, $signer->name)
                     ->subject($subject)
                     ->from($mailFromAddress, $mailFromName);
            });

            // Update reminder tracking
            $signer->update([
                'last_reminder_sent_at' => now(),
                'reminder_count' => $signer->reminder_count + 1
            ]);

            // Create activity note for reminder email
            DocumentNote::create([
                'document_id' => $document->id,
                'created_by' => Auth::guard('admin')->id() ?? 1,
                'action_type' => 'email_sent',
                'note' => "Reminder #{$signer->reminder_count} sent to {$signer->name} ({$signer->email})",
                'metadata' => [
                    'signer_id' => $signer->id,
                    'signer_email' => $signer->email,
                    'signer_name' => $signer->name,
                    'reminder_number' => $signer->reminder_count,
                ]
            ]);

            Log::info('Reminder sent', [
                'signer_id' => $signer->id,
                'reminder_count' => $signer->reminder_count,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send reminder', [
                'signer_id' => $signer->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Void a document
     */
    public function void(Document $document, string $reason = null): bool
    {
        try {
            $document->update([
                'status' => 'voided',
                'last_activity_at' => now(),
            ]);

            if ($reason) {
                Log::info('Document voided', [
                    'document_id' => $document->id,
                    'reason' => $reason
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to void document', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Associate a document with a Client
     */
    public function associate(Document $document, int $clientId, string $note = null): bool
    {
        try {
            $document->update([
                'documentable_type' => Admin::class,
                'documentable_id' => $clientId,
                'origin' => 'client',
            ]);

            // Create audit trail entry in document_notes
            DocumentNote::create([
                'document_id' => $document->id,
                'created_by' => auth('admin')->id() ?? 1,
                'action_type' => 'associated',
                'note' => $note ?? "Document associated with client",
                'metadata' => [
                    'entity_type' => 'client',
                    'entity_id' => $clientId,
                    'documentable_type' => Admin::class
                ]
            ]);

            // Create activity log on Client timeline
            ActivitiesLog::create([
                'client_id' => $clientId,
                'created_by' => auth('admin')->id() ?? 1,
                'activity_type' => 'document',
                'subject' => "Document #{$document->id} attached",
                'description' => $note ?? "Document '{$document->display_title}' was attached to this client",
                'task_status' => 0,
                'pin' => 0,
            ]);

            Log::info('Document associated', [
                'document_id' => $document->id,
                'client_id' => $clientId,
                'note' => $note
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to associate document', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Detach a document from its association
     */
    public function detach(Document $document, string $reason = null): bool
    {
        try {
            $oldEntityId = $document->documentable_id;

            $document->update([
                'documentable_type' => null,
                'documentable_id' => null,
                'origin' => 'ad_hoc',
            ]);

            // Create audit trail entry
            DocumentNote::create([
                'document_id' => $document->id,
                'created_by' => auth('admin')->id() ?? 1,
                'action_type' => 'detached',
                'note' => $reason ?? "Document detached from client",
                'metadata' => [
                    'old_entity_id' => $oldEntityId,
                ]
            ]);

            // Create activity log on Client timeline
            if ($oldEntityId) {
                ActivitiesLog::create([
                    'client_id' => $oldEntityId,
                    'created_by' => auth('admin')->id() ?? 1,
                    'activity_type' => 'document',
                    'subject' => "Document #{$document->id} detached",
                    'description' => $reason ?? "Document '{$document->display_title}' was detached from this client",
                    'task_status' => 0,
                    'pin' => 0,
                ]);
            }

            if ($reason) {
                Log::info('Document detached', [
                    'document_id' => $document->id,
                    'reason' => $reason
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to detach document', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Auto-suggest association based on signer email
     */
    public function suggestAssociation(string $email): ?array
    {
        // Try to find matching client (role = 7 is client in this system)
        $client = Admin::where('email', $email)
            ->where('role', '=', 7)
            ->whereNull('is_deleted')
            ->first();

        if ($client) {
            return [
                'type' => 'client',
                'id' => $client->id,
                'name' => trim("{$client->first_name} {$client->last_name}"),
                'email' => $client->email,
            ];
        }

        return null;
    }

    /**
     * Archive old drafts
     */
    public function archiveOldDrafts(int $daysOld = 30): int
    {
        $count = Document::where('status', 'draft')
            ->where('created_at', '<', now()->subDays($daysOld))
            ->whereNull('archived_at')
            ->update(['archived_at' => now()]);

        Log::info("Archived {$count} old draft documents");

        return $count;
    }

    /**
     * Get pending count for a user
     */
    public function getPendingCount(int $userId): int
    {
        return Document::forUser($userId)
            ->byStatus('sent')
            ->notArchived()
            ->count();
    }
}

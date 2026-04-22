<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EducationEliteInboxService
{
    public function __construct(
        private readonly string $domain = ''
    ) {}

    public static function make(): self
    {
        $d = (string) config('crm.education_elite_sender_domain', 'educationelite.com.au');
        $d = ltrim(strtolower(trim($d)), '@');

        return new self($d);
    }

    public function domainNeedleForLike(): string
    {
        return '%@'.$this->domain;
    }

    /**
     * @return list<string> Lowercase mailboxes discovered in elite_emails, sorted, deduplicated.
     */
    public function listMailboxes(): array
    {
        if ($this->domain === '' || ! Schema::hasTable('elite_emails')) {
            return [];
        }

        $like = $this->domainNeedleForLike();
        $chunks = [];

        foreach (['from_address', 'to_address'] as $col) {
            $q = DB::table('elite_emails')->whereNotNull($col)->where($col, '!=', '');
            if ($this->isPostgres()) {
                $q->whereRaw("LOWER({$col}) LIKE LOWER(?)", [$like]);
            } else {
                $q->whereRaw('LOWER('.$col.') LIKE ?', [strtolower($like)]);
            }
            $chunks = array_merge($chunks, $q->distinct()->pluck($col)->all());
        }

        return $this->dedupeMailboxes($chunks);
    }

    /**
     * @param  list<string|null>  $raw
     * @return list<string>
     */
    private function dedupeMailboxes(array $raw): array
    {
        if ($this->domain === '') {
            return [];
        }
        $domainQ = preg_quote($this->domain, '/');
        $re = '/[a-z0-9._%+\-]+@'.$domainQ.'/i';
        $out = [];
        foreach ($raw as $r) {
            if ($r === null || $r === '') {
                continue;
            }
            if (preg_match_all($re, (string) $r, $m)) {
                foreach ($m[0] as $addr) {
                    $a = strtolower((string) $addr);
                    $out[$a] = $a;
                }
            }
        }
        $list = array_values($out);
        sort($list);

        return $list;
    }

    /**
     * Returns null to mean "all mailboxes" (no per-account filter).
     */
    public function normalizeAccountFilter(?string $account): ?string
    {
        if ($this->domain === '' || $account === null) {
            return null;
        }
        $a = strtolower(trim($account));
        if ($a === '' || $a === 'all' || $a === '*') {
            return null;
        }
        if (! str_ends_with($a, '@'.$this->domain)) {
            return null;
        }
        if (! filter_var($a, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return $a;
    }

    /**
     * Escape user text for LIKE / ILIKE with operator … ESCAPE '!'.
     */
    private function escapeCharMetaForLikeExclamation(string $value): string
    {
        $value = str_replace('!', '!!', $value);

        return str_replace(['%', '_'], ['!%', '!_'], $value);
    }

    private function isPostgres(): bool
    {
        return DB::getDriverName() === 'pgsql';
    }

    /**
     * SendGrid Inbound Parse only (`elite_emails`). Folder is always 'inbox';
     * 'sent' has no records and returns [] immediately.
     *
     * @param  string|null  $folder  'inbox' or null → all inbound; 'sent' → always empty
     * @param  string|null  $account  Normalised *@{domain} address to filter to-address, or null for all
     * @return array<int, array<string, mixed>>
     */
    public function getInbox(
        string $search,
        string $dateFrom,
        string $dateTo,
        string $sort,
        int $limit,
        ?string $folder = null,
        ?string $account = null
    ): array {
        // elite_emails only stores inbound; sent folder is always empty
        if ($folder === 'sent') {
            return [];
        }

        $search = trim($search);
        $orderDir = $sort === 'oldest' ? 'asc' : 'desc';
        $limit = max(1, min(1000, $limit));

        $q = $this->buildQuery($search, $dateFrom, $dateTo);

        $acc = $this->normalizeAccountFilter($account);
        if ($acc !== null) {
            // Use contains match (%acc%) so it handles display name format: "Name <email>"
            $accLike = '%'.$acc.'%';
            $q->whereRaw($this->isPostgres()
                ? "LOWER(COALESCE(e.to_address, '')) LIKE ?"
                : "LOWER(COALESCE(e.to_address, '')) LIKE ?",
                [$accLike]
            );
        }

        $q->orderBy('e.created_at', $orderDir)->orderBy('e.id', 'desc');
        $rows = $q->limit($limit)->get();

        $out = [];
        foreach ($rows as $row) {
            $out[] = $this->rowToItem($row);
        }

        return $out;
    }

    /**
     * @deprecated Use getInbox(). Kept for any legacy callers; delegates to getInbox().
     */
    public function getMergedInbox(
        string $search,
        string $dateFrom,
        string $dateTo,
        string $sort,
        int $limit,
        ?string $folder = null,
        ?string $account = null
    ): array {
        return $this->getInbox($search, $dateFrom, $dateTo, $sort, $limit, $folder, $account);
    }

    private function buildQuery(string $search, string $dateFrom, string $dateTo): Builder
    {
        $q = DB::table('elite_emails as e')->select([
            'e.id',
            'e.from_address as from_addr',
            'e.to_address as to_addr',
            'e.subject as subj',
            DB::raw('COALESCE(e.body_html, e.body_text) as body'),
            'e.created_at as received_at',
        ]);

        $this->whereSearchOr($q, $search, [
            'e.from_address', 'e.to_address', 'e.subject', 'e.body_text', 'e.body_html',
        ]);

        $this->whereDateOnColumn($q, 'e.created_at', $dateFrom, $dateTo);

        return $q;
    }

    private function rowToItem(object $row): array
    {
        $received = $row->received_at ?? null;
        if ($received === null) {
            $dateStr = '';
        } else {
            $c = $received instanceof Carbon
                ? $received
                : Carbon::parse((string) $received);
            $dateStr = $c->format('d/m/Y g:i A');
        }

        return [
            'id' => 'elite-'.(int) $row->id,
            'from' => $row->from_addr ?? null,
            'to' => $row->to_addr ?? null,
            'subject' => $row->subj ?? null,
            'body' => (string) ($row->body ?? ''),
            'date' => $dateStr,
            'direction' => 'inbound',
            'direction_label' => 'Inbound (SendGrid)',
        ];
    }

    /**
     * @param  array<int, string>  $orColumns  Column expressions including table alias, e.g. "e.from_address"
     */
    private function whereSearchOr(Builder $q, string $search, array $orColumns): void
    {
        if ($search === '') {
            return;
        }

        $e = $this->escapeCharMetaForLikeExclamation($search);
        $p = '%'.$e.'%';
        $likeOp = $this->isPostgres() ? 'ilike' : 'like';

        $q->where(function (Builder $w) use ($orColumns, $p, $likeOp) {
            foreach ($orColumns as $i => $col) {
                $method = $i === 0 ? 'whereRaw' : 'orWhereRaw';
                $w->{$method}("{$col} {$likeOp} ? escape '!'", [$p]);
            }
        });
    }

    private function whereDateOnColumn(Builder $q, string $dateExpr, string $dateFrom, string $dateTo): void
    {
        if ($this->isPostgres()) {
            if ($dateFrom !== '') {
                $q->whereRaw("({$dateExpr})::date >= ?::date", [$dateFrom]);
            }
            if ($dateTo !== '') {
                $q->whereRaw("({$dateExpr})::date <= ?::date", [$dateTo]);
            }

            return;
        }

        if ($dateFrom !== '') {
            $q->whereRaw("DATE({$dateExpr}) >= ?", [$dateFrom]);
        }
        if ($dateTo !== '') {
            $q->whereRaw("DATE({$dateExpr}) <= ?", [$dateTo]);
        }
    }

    public function emptyListMessage(?string $folder = null): string
    {
        $mention = $this->domain !== '' ? '@'.$this->domain : 'the configured Elite domain';

        return 'No inbound mail for '.$mention.' yet. Configure SendGrid Inbound Parse to POST to the webhook URL shown below, or use "Simulate inbound" to test.';
    }
}

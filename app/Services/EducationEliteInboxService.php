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
     * @return list<string> Lowercase mailboxes at the Elite domain, sorted, deduplicated.
     */
    public function listMailboxes(): array
    {
        if ($this->domain === '') {
            return [];
        }

        $like = $this->domainNeedleForLike();
        $chunks = [];
        $driver = DB::getDriverName();

        if (Schema::hasTable('elite_emails')) {
            foreach (['from_address', 'to_address'] as $col) {
                $q = DB::table('elite_emails')->whereNotNull($col)->where($col, '!=', '');
                if ($driver === 'pgsql') {
                    $q->whereRaw("LOWER({$col}) LIKE LOWER(?)", [$like]);
                } else {
                    $q->whereRaw('LOWER('.$col.') LIKE ?', [strtolower($like)]);
                }
                $chunks = array_merge($chunks, $q->distinct()->pluck($col)->all());
            }
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
     * Returns null to mean “all mailboxes” (no per-account filter).
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
     * True if the free-text field contains an address at the Elite domain (Option A).
     */
    public function fieldMentionsElite(?string $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }
        if ($this->domain === '') {
            return false;
        }

        return str_contains(strtolower($value), '@'.$this->domain);
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
     * SendGrid Inbound Parse only (`elite_emails`). No CRM `emails` merge.
     *
     * @param  string|null  $folder  'inbox' = inbound only; 'sent' = none (inbound store has no sent rows)
     * @param  string|null  $account  Lowercase *@{$domain} address to filter, or null for all mailboxes
     * @return array<int, array<string, mixed>>
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
        $search = trim($search);
        $orderDir = $sort === 'oldest' ? 'asc' : 'desc';
        $limit = max(1, min(1000, $limit));

        $merged = $this->eliteSubquery($search, $dateFrom, $dateTo);

        $outer = DB::query()->fromSub($merged, 'merged')
            ->orderBy('sort_at', $orderDir)
            ->orderBy('src', 'desc')
            ->orderBy('ref_id', 'desc');

        $folderNorm = $folder === 'sent' ? 'sent' : ($folder === 'inbox' ? 'inbox' : null);
        if ($folderNorm === 'inbox') {
            $outer->where('direction', 'inbound');
        } elseif ($folderNorm === 'sent') {
            $outer->where('direction', 'sent');
        }

        $acc = $this->normalizeAccountFilter($account);
        if ($acc !== null) {
            $accLike = '%'.$acc;
            if ($folderNorm === 'inbox') {
                $outer->whereRaw("LOWER(COALESCE(merged.to_addr, '')) LIKE ?", [$accLike]);
            } elseif ($folderNorm === 'sent') {
                $outer->whereRaw("LOWER(COALESCE(merged.from_addr, '')) LIKE ?", [$accLike]);
            }
        }

        $rows = $outer->limit($limit)->get();

        $out = [];
        foreach ($rows as $row) {
            $out[] = $this->rowToItem($row);
        }

        return $out;
    }

    private function rowToItem(object $row): array
    {
        $src = (string) ($row->src ?? '');
        $refId = (int) ($row->ref_id ?? 0);
        $display = $row->display_at ?? $row->sort_at ?? null;
        if ($display === null) {
            $dateStr = '';
        } else {
            $c = $display instanceof Carbon
                ? $display
                : Carbon::parse((string) $display);
            $dateStr = $c->format('d/m/Y g:i A');
        }

        $dir = (string) ($row->direction ?? 'inbox');
        $dlabel = (string) ($row->direction_label ?? '');

        return [
            'id' => $src.'-'.$refId,
            'from' => $row->from_addr ?? null,
            'to' => $row->to_addr ?? null,
            'subject' => $row->subj ?? null,
            'body' => (string) ($row->body ?? ''),
            'date' => $dateStr,
            'direction' => $dir,
            'direction_label' => $dlabel,
        ];
    }

    /**
     * @param  array<int, string>  $orColumns  e.g. "e.from_address" (include table alias)
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

    private function eliteSubquery(string $search, string $dateFrom, string $dateTo): Builder
    {
        $q = DB::table('elite_emails as e')->selectRaw("
            'elite' as src,
            e.id as ref_id,
            e.from_address as from_addr,
            e.to_address as to_addr,
            e.subject as subj,
            COALESCE(e.body_html, e.body_text) as body,
            e.created_at as sort_at,
            e.created_at as display_at,
            'inbound' as direction,
            'Inbound (SendGrid)' as direction_label
        ");

        $this->whereSearchOr($q, $search, [
            'e.from_address', 'e.to_address', 'e.subject', 'e.body_text', 'e.body_html',
        ]);

        $this->whereDateOnColumn($q, 'e.created_at', $dateFrom, $dateTo);

        return $q;
    }

    public function emptyListMessage(?string $folder = null): string
    {
        $mention = $this->domain !== '' ? '@'.$this->domain : 'the configured Elite domain';
        if ($folder === 'sent') {
            return 'This view only shows messages received through SendGrid Inbound Parse. Sent/outbound mail is not listed here. Use the CRM or SendGrid for outbound history.';
        }

        return 'No incoming mail for '.$mention.' yet. Configure SendGrid Inbound Parse to POST to the webhook below, or use “Simulate inbound” to test.';
    }
}

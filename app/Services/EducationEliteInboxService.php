<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class EducationEliteInboxService
{
    public function __construct(
        private readonly string $domain = ''
    ) {
    }

    public static function make(): self
    {
        $d = (string) config('crm.education_elite_sender_domain', 'educationelite.com.au');
        $d = ltrim(strtolower(trim($d)), '@');

        return new self($d);
    }

    public function domainNeedleForLike(): string
    {
        return '%@' . $this->domain;
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

        return str_contains(strtolower($value), '@' . $this->domain);
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
     * Inbound (SendGrid) + CRM (Option A), merged in SQL so ORDER BY and LIMIT
     * apply to the combined result.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMergedInbox(
        string $search,
        string $dateFrom,
        string $dateTo,
        string $sort,
        int $limit
    ): array {
        $search = trim($search);
        $orderDir = $sort === 'oldest' ? 'asc' : 'desc';
        $limit = max(1, min(1000, $limit));

        $eliteQ = $this->eliteSubquery($search, $dateFrom, $dateTo);
        if ($this->domain === '') {
            $merged = $eliteQ;
        } else {
            $merged = $eliteQ->unionAll($this->crmSubquery($search, $dateFrom, $dateTo));
        }

        $rows = DB::query()->fromSub($merged, 'merged')
            ->orderBy('sort_at', $orderDir)
            ->orderBy('src', 'desc')
            ->orderBy('ref_id', 'desc')
            ->limit($limit)
            ->get();

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
            'id' => $src . '-' . $refId,
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
        $p = '%' . $e . '%';
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

    private function crmSubquery(string $search, string $dateFrom, string $dateTo): Builder
    {
        $needle = $this->domainNeedleForLike();
        // `text_preview` may exist on the model but not in all DBs; use `message` only.
        $bodyCol = $this->isPostgres()
            ? 'TRIM(m.message::text)'
            : 'TRIM(m.message)';

        $mailCase = "CASE WHEN m.mail_type IN (1, '1') THEN 'sent' ELSE 'inbox' END";
        $labelCase = "CASE WHEN m.mail_type IN (1, '1') THEN 'Sent (CRM)' ELSE 'Inbox (CRM)' END";

        $q = DB::table('emails as m')
            ->selectRaw("
                'crm' as src,
                m.id as ref_id,
                m.from_mail as from_addr,
                m.to_mail as to_addr,
                m.subject as subj,
                {$bodyCol} as body,
                COALESCE(m.received_date, m.created_at) as sort_at,
                COALESCE(m.received_date, m.created_at) as display_at,
                {$mailCase} as direction,
                {$labelCase} as direction_label
            ");

        if ($this->isPostgres()) {
            $q->where(function (Builder $w) use ($needle) {
                $w->where('m.from_mail', 'ilike', $needle)
                    ->orWhere('m.to_mail', 'ilike', $needle)
                    ->orWhere('m.cc', 'ilike', $needle);
            });
        } else {
            $n = $needle;
            $q->where(function (Builder $w) use ($n) {
                $w->whereRaw('LOWER(COALESCE(m.from_mail, ?)) LIKE LOWER(?)', ['', $n])
                    ->orWhereRaw('LOWER(COALESCE(m.to_mail, ?)) LIKE LOWER(?)', ['', $n])
                    ->orWhereRaw('LOWER(COALESCE(m.cc, ?)) LIKE LOWER(?)', ['', $n]);
            });
        }

        $q->whereIn('m.mail_type', [0, 1, '0', '1']);

        $this->whereSearchOr($q, $search, [
            'm.from_mail', 'm.to_mail', 'm.cc', 'm.subject', 'm.message',
        ]);

        $this->whereDateOnColumn(
            $q,
            'COALESCE(m.received_date, m.created_at)',
            $dateFrom,
            $dateTo
        );

        return $q;
    }

    public function emptyListMessage(): string
    {
        $d = $this->domain;

        return 'No @' . $d . ' activity yet. Inbound: SendGrid Inbound Parse to the webhook, or use “Simulate inbound”.'
            . ' Outbound and CRM inbox rows appear when @' . $d . ' appears in From, To, or CC.';
    }
}

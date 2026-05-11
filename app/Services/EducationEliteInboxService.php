<?php

namespace App\Services;

use App\Models\EliteEmailAttachment;
use App\Support\EliteEmailCidRewriter;
use App\Support\EducationEliteMail;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Config;
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
        if ($this->domain === '') {
            return [];
        }
        if (! Schema::hasTable('elite_emails')) {
            if ($this->shouldMergeCrm() && Schema::hasTable('emails')) {
                return $this->dedupeMailboxes($this->rawMailboxValuesFromCrm());
            }

            return [];
        }

        $chunks = [];

        foreach (['from_address', 'to_address'] as $col) {
            $q = DB::table('elite_emails')->whereNotNull($col)->where($col, '!=', '');
            $this->whereEmailColumnMatchesEliteDomain($q, $col);
            $chunks = array_merge($chunks, $q->distinct()->pluck($col)->all());
        }
        if ($this->shouldMergeCrm() && Schema::hasTable('emails')) {
            $chunks = array_merge($chunks, $this->rawMailboxValuesFromCrm());
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
        $re = '/[a-z0-9._%+\-]+@(?:[a-z0-9.-]+\.)?'.$domainQ.'/i';
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
        $list = array_values(array_filter($out, static function (string $addr): bool {
            return ! str_starts_with($addr, 'noreply@') && ! str_starts_with($addr, 'no-reply@');
        }));
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
        if (! EducationEliteMail::isEliteOwnedAddress($a)) {
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
     * Match @apex or @*.apex (SendGrid Inbound Parse subdomain). $columnSqlExpr is a SQL fragment e.g. "to_address" or "COALESCE(c.from_mail, '')".
     */
    private function whereEmailColumnMatchesEliteDomain(Builder $q, string $columnSqlExpr): void
    {
        if ($this->domain === '') {
            $q->whereRaw('1 = 0');

            return;
        }
        $apexPat = '%@'.$this->domain;
        $subPat = '%@%.'.$this->domain;
        if ($this->isPostgres()) {
            $q->where(function (Builder $w) use ($columnSqlExpr, $apexPat, $subPat) {
                $w->whereRaw("LOWER({$columnSqlExpr}) LIKE LOWER(?)", [$apexPat])
                    ->orWhereRaw("LOWER({$columnSqlExpr}) LIKE LOWER(?)", [$subPat]);
            });
        } else {
            $q->where(function (Builder $w) use ($columnSqlExpr, $apexPat, $subPat) {
                $w->whereRaw('LOWER('.$columnSqlExpr.') LIKE ?', [strtolower($apexPat)])
                    ->orWhereRaw('LOWER('.$columnSqlExpr.') LIKE ?', [strtolower($subPat)]);
            });
        }
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
        $limit = max(1, min(1000, $limit));
        $acc = $this->normalizeAccountFilter($account);

        $out = [];
        if (Schema::hasTable('elite_emails')) {
            $q = $this->buildEliteQuery($search, $dateFrom, $dateTo);
            if ($acc !== null) {
                $accLike = '%'.$acc.'%';
                $q->whereRaw('LOWER(COALESCE(e.to_address, \'\')) LIKE ?', [strtolower($accLike)]);
            }
            $q->orderBy('e.created_at', 'desc')->orderBy('e.id', 'desc');
            foreach ($q->get() as $row) {
                $out[] = $this->eliteRowToItem($row);
            }
        }

        if ($this->shouldMergeCrm() && Schema::hasTable('emails')) {
            $crm = $this->fetchCrmInboxAsItems($search, $dateFrom, $dateTo, $acc);
            $out = array_merge($out, $crm);
        }

        usort($out, function (array $a, array $b) use ($sort) {
            $ta = (float) ($a['sort_ts'] ?? 0);
            $tb = (float) ($b['sort_ts'] ?? 0);

            return $sort === 'oldest' ? $ta <=> $tb : $tb <=> $ta;
        });
        $out = array_slice($out, 0, $limit);
        foreach ($out as &$it) {
            unset($it['sort_ts']);
        }
        unset($it);

        return $this->applyEliteInboundAttachments($out);
    }

    /**
     * Attach stored inbound files + rewrite cid: in HTML for SendGrid-derived rows.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private function applyEliteInboundAttachments(array $items): array
    {
        if (! Schema::hasTable('elite_email_attachments')) {
            return $items;
        }

        $eliteIds = [];
        foreach ($items as $it) {
            if (! isset($it['id']) || ! is_string($it['id']) || ! str_starts_with($it['id'], 'elite-')) {
                continue;
            }
            $nid = (int) substr($it['id'], strlen('elite-'));
            if ($nid > 0) {
                $eliteIds[$nid] = $nid;
            }
        }

        if ($eliteIds === []) {
            return $items;
        }

        $byEmail = EliteEmailAttachment::query()
            ->whereIn('elite_email_id', array_values($eliteIds))
            ->orderBy('id')
            ->get()
            ->groupBy(static fn (EliteEmailAttachment $a) => (int) $a->elite_email_id);

        foreach ($items as &$it) {
            if (! isset($it['id']) || ! is_string($it['id']) || ! str_starts_with($it['id'], 'elite-')) {
                continue;
            }
            $eid = (int) substr($it['id'], strlen('elite-'));
            $atts = $byEmail->get($eid, collect());
            if ($atts->isEmpty()) {
                continue;
            }
            $it['has_attachments'] = true;
            $it['attachments'] = $atts->map(static function (EliteEmailAttachment $a): array {
                return [
                    'id' => $a->id,
                    'filename' => $a->original_filename,
                    'content_type' => $a->mime_type,
                    'url' => route('elite.emails.attachment', ['attachment' => $a->id]),
                ];
            })->values()->all();

            $body = (string) ($it['body'] ?? '');
            if ($body !== '' && preg_match('/<[a-z][\s\S]*>/i', $body) && str_contains(strtolower($body), 'cid:')) {
                $it['body'] = EliteEmailCidRewriter::rewrite($body, $atts);
            }
        }
        unset($it);

        return $items;
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

    private function shouldMergeCrm(): bool
    {
        if ($this->domain === '') {
            return false;
        }

        return (bool) Config::get('crm.education_elite_inbox_merge_crm', true);
    }

    /**
     * @return list<string|float|int|bool|array|null>
     */
    private function rawMailboxValuesFromCrm(): array
    {
        $chunks = [];
        foreach (['from_mail', 'to_mail'] as $col) {
            $q = DB::table('emails')->where('mail_type', 0)
                ->whereNotNull($col)->where($col, '!=', '');
            $this->whereEmailColumnMatchesEliteDomain($q, $col);
            $chunks = array_merge($chunks, $q->distinct()->pluck($col)->all());
        }

        return $chunks;
    }

    /**
     * Virtual `body` column for list/API: omit HTML when stored only on object storage.
     */
    private function eliteEmailBodySelectExpr(string $alias = 'e'): string
    {
        if (! Schema::hasTable('elite_emails') || ! Schema::hasColumn('elite_emails', 'body_html_s3_key')) {
            return "COALESCE({$alias}.body_html, {$alias}.body_text)";
        }

        if ($this->isPostgres()) {
            return "CASE WHEN {$alias}.body_html_s3_key IS NOT NULL AND length(trim(COALESCE({$alias}.body_html_s3_key, ''))) > 0 THEN NULL ELSE COALESCE({$alias}.body_html, {$alias}.body_text) END";
        }

        return "CASE WHEN {$alias}.body_html_s3_key IS NOT NULL AND TRIM(COALESCE({$alias}.body_html_s3_key, '')) != '' THEN NULL ELSE COALESCE({$alias}.body_html, {$alias}.body_text) END";
    }

    private function buildEliteQuery(string $search, string $dateFrom, string $dateTo): Builder
    {
        $select = [
            'e.id',
            'e.from_address as from_addr',
            'e.to_address as to_addr',
            'e.subject as subj',
            'e.created_at as received_at',
            'e.payload as payload_json',
        ];
        if (Schema::hasColumn('elite_emails', 'body_html_s3_key')) {
            $select[] = 'e.body_text as body_text_plain';
            $select[] = 'e.body_html_s3_key';
        }
        $select[] = DB::raw('('.$this->eliteEmailBodySelectExpr('e').') as body');

        $q = DB::table('elite_emails as e')->select($select);

        // Exclude automated no-reply addresses — they are system notifications, not real mail
        $q->whereRaw('LOWER(COALESCE(e.from_address, \'\')) NOT LIKE ?', ['noreply@%'])
          ->whereRaw('LOWER(COALESCE(e.from_address, \'\')) NOT LIKE ?', ['no-reply@%']);

        $this->whereSearchOr($q, $search, [
            'e.from_address', 'e.to_address', 'e.subject', 'e.body_text', 'e.body_html',
        ]);

        $this->whereDateOnColumn($q, 'e.created_at', $dateFrom, $dateTo);

        return $q;
    }

    private function crmBodySelectExpr(string $tableAlias = 'c'): string
    {
        $c = $tableAlias;
        $cols = ["{$c}.message"];
        if (Schema::hasColumn('emails', 'rendered_html')) {
            array_unshift($cols, "{$c}.rendered_html");
        }
        if (Schema::hasColumn('emails', 'enhanced_html')) {
            array_unshift($cols, "{$c}.enhanced_html");
        }
        $nullIf = array_map(
            static fn (string $col) => "NULLIF({$col}, '')",
            $cols
        );

        return 'COALESCE('.implode(', ', $nullIf).')';
    }

    /**
     * CRM inbound (mail_type 0) involving @education_elite domain.
     *
     * @return list<array<string, mixed>>
     */
    private function fetchCrmInboxAsItems(
        string $search,
        string $dateFrom,
        string $dateTo,
        ?string $accNormalized
    ): array {
        $bodyExpr = $this->crmBodySelectExpr('c');
        $q = DB::table('emails as c')->select([
            'c.id',
            'c.from_mail as from_addr',
            'c.to_mail as to_addr',
            'c.subject as subj',
            DB::raw("{$bodyExpr} as body"),
            'c.created_at as received_at',
        ])->where('c.mail_type', 0)
          ->whereRaw('LOWER(COALESCE(c.from_mail, \'\')) NOT LIKE ?', ['noreply@%'])
          ->whereRaw('LOWER(COALESCE(c.from_mail, \'\')) NOT LIKE ?', ['no-reply@%']);

        $q->where(function (Builder $outer) {
            $outer->where(function (Builder $w) {
                $this->whereEmailColumnMatchesEliteDomain($w, 'COALESCE(c.from_mail, \'\')');
            })->orWhere(function (Builder $w) {
                $this->whereEmailColumnMatchesEliteDomain($w, 'COALESCE(c.to_mail, \'\')');
            });
        });

        if ($accNormalized !== null) {
            $accLike = '%'.$accNormalized.'%';
            $q->whereRaw('LOWER(COALESCE(c.to_mail, \'\')) LIKE ?', [strtolower($accLike)]);
        }

        $searchCols = ['c.from_mail', 'c.to_mail', 'c.subject', 'c.message'];
        if (Schema::hasColumn('emails', 'rendered_html')) {
            $searchCols[] = 'c.rendered_html';
        }
        if (Schema::hasColumn('emails', 'enhanced_html')) {
            $searchCols[] = 'c.enhanced_html';
        }
        $this->whereSearchOr($q, $search, $searchCols);

        $this->whereDateOnColumn($q, 'c.created_at', $dateFrom, $dateTo);

        $out = [];
        foreach ($q->get() as $row) {
            $out[] = $this->crmRowToItem($row);
        }

        return $out;
    }

    private function eliteRowToItem(object $row): array
    {
        $received = $row->received_at ?? null;
        if ($received === null) {
            $dateStr = '';
            $ts = 0.0;
        } else {
            $c = $received instanceof Carbon
                ? $received
                : Carbon::parse((string) $received);
            $dateStr = $c->format('d/m/Y g:i A');
            $ts = (float) $c->getTimestamp();
        }

        $hasAttachments = false;
        $payloadJson = $row->payload_json ?? null;
        if (is_string($payloadJson) && $payloadJson !== '') {
            $p = json_decode($payloadJson, true);
            if (is_array($p)) {
                $cnt = $p['attachments'] ?? 0;
                $hasAttachments = (is_numeric($cnt) && (int) $cnt > 0)
                    || isset($p['attachment-info']);
            }
        }

        $s3Key = '';
        if (Schema::hasColumn('elite_emails', 'body_html_s3_key') && isset($row->body_html_s3_key)) {
            $s3Key = trim((string) $row->body_html_s3_key);
        }
        $bodyTextPlain = isset($row->body_text_plain) ? (string) $row->body_text_plain : '';

        if ($s3Key !== '') {
            $bodyRaw = '';
            $snippetSrc = $bodyTextPlain;
        } else {
            $bodyRaw = (string) ($row->body ?? '');
            $snippetSrc = $bodyRaw;
        }

        $snippet = '';
        if ($snippetSrc !== '') {
            $stripped = preg_replace('/\s+/', ' ', trim(strip_tags($snippetSrc)));
            if ($stripped !== null && $stripped !== '') {
                $snippet = mb_substr($stripped, 0, 120);
            }
        }

        return [
            'id' => 'elite-'.(int) $row->id,
            'from' => $row->from_addr ?? null,
            'to' => $row->to_addr ?? null,
            'subject' => $row->subj ?? null,
            'body' => $bodyRaw,
            'snippet' => $snippet,
            'date' => $dateStr,
            'direction' => 'inbound',
            'direction_label' => 'Inbound (SendGrid)',
            'has_attachments' => $hasAttachments,
            'sort_ts' => $ts,
            'body_fetch_url' => ($s3Key !== '' && Schema::hasColumn('elite_emails', 'body_html_s3_key'))
                ? route('elite.emails.message-body', ['eliteEmail' => (int) $row->id])
                : null,
        ];
    }

    private function crmRowToItem(object $row): array
    {
        $received = $row->received_at ?? null;
        if ($received === null) {
            $dateStr = '';
            $ts = 0.0;
        } else {
            $c = $received instanceof Carbon
                ? $received
                : Carbon::parse((string) $received);
            $dateStr = $c->format('d/m/Y g:i A');
            $ts = (float) $c->getTimestamp();
        }

        $bodyRaw = (string) ($row->body ?? '');
        $snippet = '';
        if ($bodyRaw !== '') {
            $stripped = preg_replace('/\s+/', ' ', trim(strip_tags($bodyRaw)));
            if ($stripped !== null && $stripped !== '') {
                $snippet = mb_substr($stripped, 0, 120);
            }
        }

        return [
            'id' => 'crm-'.(int) $row->id,
            'from' => $row->from_addr ?? null,
            'to' => $row->to_addr ?? null,
            'subject' => $row->subj ?? null,
            'body' => $bodyRaw,
            'snippet' => $snippet,
            'date' => $dateStr,
            'direction' => 'inbound',
            'direction_label' => 'Inbound (CRM)',
            'has_attachments' => false,
            'sort_ts' => $ts,
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
        if ($this->shouldMergeCrm()) {
            return 'No inbound mail for '.$mention.' yet. SendGrid Inbound Parse must POST to the webhook URL below; CRM-imported inbox (mail_type 0) appears here too. Use "Simulate inbound" to test the webhook.';
        }

        return 'No inbound mail for '.$mention.' yet. Configure SendGrid Inbound Parse to POST to the webhook URL shown below, or use "Simulate inbound" to test.';
    }
}

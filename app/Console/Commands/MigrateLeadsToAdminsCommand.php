<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MigrateLeadsToAdminsCommand extends Command
{
    protected $signature = 'leads:migrate-to-admins
                            {--limit= : Max number of leads to process this run (default: all)}
                            {--chunk=200 : Chunk size for batch processing}';

    protected $description = 'Migrate leads to admins table with progress. Run in steps with --limit for large datasets.';

    protected array $adminCols = [];
    protected ?array $adminByEmail = null;
    protected array $staffOfficeId = [];
    protected ?int $defaultUserId = null;

    public function handle(): int
    {
        if (!Schema::hasTable('leads') || !Schema::hasTable('admins')) {
            $this->error('Required tables (leads, admins) do not exist.');
            return 1;
        }

        if (!Schema::hasColumn('leads', 'is_migrate') || !Schema::hasColumn('admins', 'is_lead_migrate_to_admin')) {
            $this->error('Run migration add_lead_migration_flag_columns first.');
            return 1;
        }

        if (!Schema::hasColumn('leads', 'client_id')) {
            $this->error('Run migration add_client_id_to_leads_table first.');
            return 1;
        }

        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $chunkSize = (int) ($this->option('chunk') ?: 200);

        $total = DB::table('leads')->where('is_migrate', 0)->count();
        if ($total === 0) {
            $this->info('No leads to migrate (is_migrate=0).');
            return 0;
        }

        $toProcess = $limit ? min($limit, $total) : $total;
        $this->info("Migrating up to {$toProcess} leads (chunk size: {$chunkSize})...");
        $this->newLine();

        // Pre-cache for speed
        $this->adminCols = Schema::getColumnListing('admins');
        $this->defaultUserId = DB::table('admins')->where('id', 1)->value('id');
        $this->warmCaches();

        $processed = 0;
        $success = 0;
        $skipped = 0;
        $failed = 0;
        $now = now();

        $query = DB::table('leads')->where('is_migrate', 0)->orderBy('id');
        if ($limit) {
            $query->limit((int) $limit);
        }

        $bar = $this->output->createProgressBar($toProcess);
        $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%% - %elapsed:6s% / %memory:6s%");
        $bar->start();

        $query->chunk($chunkSize, function ($leads) use (&$processed, &$success, &$skipped, &$failed, $now, $bar) {
            foreach ($leads as $lead) {
                try {
                    DB::transaction(function () use ($lead, $now) {
                        $this->processLead($lead, $now);
                    });
                    $status = $this->getLastLeadStatus($lead->id);
                    if ($status === 1) $success++;
                    elseif ($status === 3) $skipped++;
                    else $failed++;
                } catch (\Throwable $e) {
                    DB::table('leads')->where('id', $lead->id)->update(['is_migrate' => 2]);
                    $failed++;
                }
                $processed++;
                $bar->advance();
            }
            $this->refreshAdminByEmailCache();
        });

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Status', 'Count'],
            [
                ['Migrated (new)', $success],
                ['Skipped (already exists)', $skipped],
                ['Failed', $failed],
                ['Total processed', $processed],
            ]
        );

        $remaining = DB::table('leads')->where('is_migrate', 0)->count();
        if ($remaining > 0) {
            $this->info("Remaining: {$remaining} leads. Run again to continue.");
        } else {
            $this->info('All leads processed. Run: php artisan migrate');
        }

        return 0;
    }

    protected function warmCaches(): void
    {
        $this->adminByEmail = DB::table('admins')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->pluck('id', 'email')
            ->toArray();

        if (Schema::hasTable('staff')) {
            $this->staffOfficeId = DB::table('staff')
                ->whereNotNull('office_id')
                ->pluck('office_id', 'id')
                ->toArray();
        }
    }

    protected function refreshAdminByEmailCache(): void
    {
        $newOnes = DB::table('admins')
            ->where('is_lead_migrate_to_admin', 1)
            ->whereNotNull('email')
            ->pluck('id', 'email')
            ->toArray();
        $this->adminByEmail = array_merge($this->adminByEmail ?? [], $newOnes);
    }

    protected function getLastLeadStatus(int $leadId): ?int
    {
        return (int) DB::table('leads')->where('id', $leadId)->value('is_migrate');
    }

    protected function processLead(object $lead, $now): void
    {
        $email = trim((string) ($lead->email ?? ''));
        if ($email === '') {
            DB::table('leads')->where('id', $lead->id)->update(['is_migrate' => 2]);
            return;
        }

        $adminId = $this->adminByEmail[$email] ?? null;
        if ($adminId !== null) {
            $admin = DB::table('admins')->where('id', $adminId)->first();
            if ($admin) {
                $this->backfillAdmin($lead, $admin, $now);
                DB::table('leads')->where('id', $lead->id)->update(['is_migrate' => 3]);
                return;
            }
        }

        $this->insertNewAdmin($lead, $now);
        DB::table('leads')->where('id', $lead->id)->update(['is_migrate' => 1]);
    }

    protected function backfillAdmin(object $lead, object $admin, $now): void
    {
        $updates = [];
        foreach ($this->getLeadToAdminMapping() as $leadCol => $adminCol) {
            if (!property_exists($lead, $leadCol) || !in_array($adminCol, $this->adminCols, true)) continue;
            $leadVal = $lead->{$leadCol};
            $adminVal = $admin->{$adminCol} ?? null;
            if (($adminVal === null || $adminVal === '') && ($leadVal !== null && $leadVal !== '')) {
                $updates[$adminCol] = $leadVal;
            }
        }
        if (empty($admin->lead_id)) $updates['lead_id'] = $lead->id;
        if (property_exists($lead, 'assign_to') && $lead->assign_to && empty($admin->assignee)) $updates['assignee'] = $lead->assign_to;
        if (property_exists($lead, 'assign_to') && $lead->assign_to && empty($admin->office_id)) {
            $updates['office_id'] = $this->staffOfficeId[$lead->assign_to] ?? null;
        }
        $updates = array_filter($updates);

        $updates['updated_at'] = $now;
        $updates['is_lead_migrate_to_admin'] = 1;
        DB::table('admins')->where('id', $admin->id)->update($updates);

        $this->addSecondaryContactIfNeeded($admin->id, $lead, $admin);
        $this->addAttToClientTables($admin->id, $lead);

        DB::table('leads')->where('id', $lead->id)->update(['client_id' => $admin->id]);
    }

    protected function insertNewAdmin(object $lead, $now): void
    {
        $officeId = null;
        if (property_exists($lead, 'assign_to') && $lead->assign_to) {
            $officeId = $this->staffOfficeId[$lead->assign_to] ?? null;
        }

        $data = [
            'type' => 'lead',
            'remember_token' => null,
            'lead_id' => $lead->id,
            'first_name' => $lead->first_name ?? null,
            'last_name' => $lead->last_name ?? null,
            'email' => trim((string) ($lead->email ?? '')),
            'password' => bcrypt(Str::random(32)),
            'phone' => $lead->phone ?? null,
            'country_code' => $lead->country_code ?? null,
            'gender' => $lead->gender ?? null,
            'dob' => $lead->dob ?? null,
            'marital_status' => $lead->marital_status ?? null,
            'address' => $lead->address ?? null,
            'city' => $lead->city ?? null,
            'state' => $lead->state ?? null,
            'zip' => $lead->zip ?? null,
            'country' => $lead->country ?? null,
            'user_id' => $this->resolveUserId($lead->user_id ?? null),
            'assignee' => $lead->assign_to ?? null,
            'office_id' => $officeId,
            'source' => $lead->lead_source ?? null,
            'tags' => $lead->tags_label ?? null,
            'passport_number' => $lead->passport_no ?? null,
            'visaexpiry' => $lead->visa_expiry_date ?? null,
            'visa_type' => $lead->visa_type ?? null,
            'nomi_occupation' => $lead->nomi_occupation ?? null,
            'skill_assessment' => $lead->skill_assessment ?? null,
            'high_quali_aus' => $lead->high_quali_aus ?? null,
            'high_quali_overseas' => $lead->high_quali_overseas ?? null,
            'relevant_work_exp_aus' => $lead->relevant_work_exp_aus ?? null,
            'relevant_work_exp_over' => $lead->relevant_work_exp_over ?? null,
            'naati_py' => $lead->naati_py ?? null,
            'married_partner' => $lead->married_partner ?? null,
            'total_points' => $lead->total_points ?? null,
            'comments_note' => $lead->comments_note ?? null,
            'service' => $lead->service ?? null,
            'lead_quality' => $lead->lead_quality ?? null,
            'country_passport' => $lead->country_passport ?? null,
            'contact_type' => $lead->contact_type ?? null,
            'email_type' => $lead->email_type ?? null,
            'related_files' => $lead->related_files ?? null,
            'status' => $lead->status ?? 1,
            'verified' => 0,
            'is_archived' => 0,
            'show_dashboard_per' => 0,
            'created_at' => $lead->created_at ?? $now,
            'updated_at' => $lead->updated_at ?? $now,
            'is_lead_migrate_to_admin' => 1,
            'converted' => $lead->converted ?? 0,
            'converted_date' => $lead->converted_date ?? null,
            'is_verified' => $lead->is_verified ?? 0,
            'verified_at' => $lead->verified_at ?? null,
            'verified_by' => $lead->verified_by ?? null,
        ];

        $data = array_intersect_key($data, array_flip($this->adminCols));
        $adminId = DB::table('admins')->insertGetId($data);

        $firstName = substr((string) ($lead->first_name ?? ''), 0, 4);
        $clientId = strtoupper(preg_replace('/[^A-Za-z]/', '', $firstName) ?: 'LEAD') . date('ym') . $adminId;
        if (in_array('client_id', $this->adminCols, true)) {
            DB::table('admins')->where('id', $adminId)->update(['client_id' => $clientId]);
        }

        $this->adminByEmail[trim((string) ($lead->email ?? ''))] = $adminId;
        $this->addAttToClientTables($adminId, $lead);

        DB::table('leads')->where('id', $lead->id)->update(['client_id' => $adminId]);
    }

    protected function addAttToClientTables(int $adminId, object $lead): void
    {
        $userId = $this->defaultUserId ?? 1;
        $now = now();

        if (Schema::hasTable('client_emails') && property_exists($lead, 'att_email') && !empty(trim((string) ($lead->att_email ?? '')))) {
            if (!DB::table('client_emails')->where('client_id', $adminId)->where('client_email', $lead->att_email)->exists()) {
                DB::table('client_emails')->insert([
                    'client_id' => $adminId, 'user_id' => $userId, 'email_type' => 'Secondary',
                    'client_email' => $lead->att_email, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        if (Schema::hasTable('client_phones') && property_exists($lead, 'att_phone') && !empty(trim((string) ($lead->att_phone ?? '')))) {
            if (!DB::table('client_phones')->where('client_id', $adminId)->where('client_phone', $lead->att_phone)->exists()) {
                $phoneData = [
                    'client_id' => $adminId, 'user_id' => $userId, 'contact_type' => 'Secondary',
                    'client_phone' => $lead->att_phone, 'created_at' => $now, 'updated_at' => $now,
                ];
                if (Schema::hasColumn('client_phones', 'client_country_code')) {
                    $phoneData['client_country_code'] = $lead->att_country_code ?? null;
                }
                DB::table('client_phones')->insert($phoneData);
            }
        }
    }

    protected function addSecondaryContactIfNeeded(int $adminId, object $lead, object $admin): void
    {
        $userId = $this->defaultUserId ?? 1;
        $now = now();

        if (Schema::hasTable('client_phones') && !empty(trim((string) ($lead->phone ?? '')))) {
            $adminPhone = trim((string) ($admin->phone ?? ''));
            $leadPhone = trim((string) ($lead->phone ?? ''));
            if ($adminPhone !== '' && $leadPhone !== '' && $adminPhone !== $leadPhone) {
                if (!DB::table('client_phones')->where('client_id', $adminId)->where('client_phone', $leadPhone)->exists()) {
                    $phoneData = [
                        'client_id' => $adminId, 'user_id' => $userId, 'contact_type' => 'Secondary',
                        'client_phone' => $leadPhone, 'created_at' => $now, 'updated_at' => $now,
                    ];
                    if (Schema::hasColumn('client_phones', 'client_country_code')) {
                        $phoneData['client_country_code'] = $lead->country_code ?? null;
                    }
                    DB::table('client_phones')->insert($phoneData);
                }
            }
        }

        if (Schema::hasTable('client_emails') && !empty(trim((string) ($lead->email ?? '')))) {
            $adminEmail = trim((string) ($admin->email ?? ''));
            $leadEmail = trim((string) ($lead->email ?? ''));
            if ($adminEmail !== '' && $leadEmail !== '' && $adminEmail !== $leadEmail) {
                if (!DB::table('client_emails')->where('client_id', $adminId)->where('client_email', $leadEmail)->exists()) {
                    DB::table('client_emails')->insert([
                        'client_id' => $adminId, 'user_id' => $userId, 'email_type' => 'Secondary',
                        'client_email' => $leadEmail, 'created_at' => $now, 'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    protected function getLeadToAdminMapping(): array
    {
        return [
            'first_name' => 'first_name', 'last_name' => 'last_name', 'phone' => 'phone',
            'country_code' => 'country_code', 'gender' => 'gender', 'dob' => 'dob',
            'marital_status' => 'marital_status', 'address' => 'address', 'city' => 'city',
            'state' => 'state', 'zip' => 'zip', 'country' => 'country',
            'passport_no' => 'passport_number', 'visa_expiry_date' => 'visaexpiry',
            'visa_type' => 'visa_type', 'tags_label' => 'tags', 'lead_source' => 'source',
            'nomi_occupation' => 'nomi_occupation', 'skill_assessment' => 'skill_assessment',
            'high_quali_aus' => 'high_quali_aus', 'high_quali_overseas' => 'high_quali_overseas',
            'relevant_work_exp_aus' => 'relevant_work_exp_aus', 'relevant_work_exp_over' => 'relevant_work_exp_over',
            'naati_py' => 'naati_py', 'married_partner' => 'married_partner', 'total_points' => 'total_points',
            'comments_note' => 'comments_note', 'country_passport' => 'country_passport',
            'service' => 'service', 'lead_quality' => 'lead_quality', 'related_files' => 'related_files',
        ];
    }

    protected function resolveUserId(?int $leadUserId): ?int
    {
        if ($leadUserId === null) return null;
        if (Schema::hasTable('staff') && DB::table('staff')->where('id', $leadUserId)->exists()) return $leadUserId;
        if (DB::table('admins')->where('id', $leadUserId)->exists()) return $leadUserId;
        return null;
    }
}

<?php

namespace App\Services;

use App\Events\OfficeVisitNotificationCreated;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\CheckinHistory;
use App\Models\CheckinLog;
use App\Models\ClientTestScore;
use App\Models\Notification;
use App\Models\Staff;
use App\Support\PublicOfficeCheckInAssignees;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PublicOfficeCheckInService
{
    /**
     * @return array<string, string>
     */
    public static function visitReasons(): array
    {
        return [
            'general_enquiry' => 'General Enquiry',
            'appointment_followup' => 'Appointment Follow-up',
            'document_submission' => 'Document Submission',
            'payment' => 'Payment',
            'other' => 'Other',
        ];
    }

    /**
     * @return list<string>
     */
    public static function heardAboutOptions(): array
    {
        return [
            'Facebook Advert',
            'Whatsapp',
            'Social Media',
            'Google',
            'Other Online Sources',
            'Tiktok',
            'Friends & Relatives',
            'Visiting card QR',
            'TV Advertisement',
            'Newspaper Advertisement',
            'Agent',
            'Walk-In',
            'Email',
            'Phone',
            'Existing Client',
            'Others',
        ];
    }

    /**
     * @return list<string>
     */
    public static function qualificationLevels(): array
    {
        return [
            'Year 12',
            'Certificate III',
            'Certificate IV',
            'Diploma',
            'Advanced Diploma',
            'Bachelor Degree',
            'Master Degree',
            'Doctorate',
            'Other',
        ];
    }

    /**
     * @return list<string>
     */
    public static function currentVisaOptions(): array
    {
        return [
            'Student (500)',
            'Graduate (485)',
            'Temporary Skill Shortage (482)',
            'Visitor',
            'Bridging',
            'Working Holiday',
            'PR / Citizen',
            'None',
            'Other',
        ];
    }

    public function normalizePhone(string $phone): string
    {
        return preg_replace('/\D/', '', $phone) ?? '';
    }

    public function canonicalAustralianPhone(string $phone): string
    {
        $digits = $this->normalizePhone($phone);
        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '61') && strlen($digits) === 11) {
            $digits = '0'.substr($digits, 2);
        } elseif (strlen($digits) === 9 && preg_match('/^[23478]/', $digits) === 1) {
            $digits = '0'.$digits;
        }

        if (preg_match('/^0[23478]\d{8}$/', $digits) === 1) {
            return $digits;
        }

        return '';
    }

    public function isValidAustralianPhone(string $phone): bool
    {
        return $this->canonicalAustralianPhone($phone) !== '';
    }

    /**
     * @return array<int, mixed>
     */
    public function australianPhoneValidationRules(): array
    {
        return [
            'required',
            'string',
            'max:20',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if (! is_string($value) || ! $this->isValidAustralianPhone($value)) {
                    $fail('Please enter a valid Australian phone number (10 digits, with or without the leading 0).');
                }
            },
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function leadDetailRules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:70'],
            'last_name' => ['required', 'string', 'max:70'],
            'phone' => $this->australianPhoneValidationRules(),
            'email' => ['required', 'email', 'max:150'],
            'dob' => ['required', 'date'],
            'marital_status' => ['required', 'string', Rule::in(['Single', 'Married', 'De facto', 'Separated / Divorced', 'Widowed'])],
            'gender' => ['required', 'string', Rule::in(['Male', 'Female', 'Other'])],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'current_visa' => ['nullable', 'string', Rule::in(self::currentVisaOptions())],
            'visa_expiry' => ['nullable', 'date'],
            'qualification' => ['nullable', 'string', Rule::in(self::qualificationLevels())],
            'qualification_type' => ['nullable', 'string', Rule::in(['completed', 'pursuing'])],
            'qualification_date' => ['nullable', 'date'],
            'ielts_passed' => ['nullable', 'string', Rule::in(['yes', 'no'])],
            'ielts_score' => ['nullable', 'required_if:ielts_passed,yes', 'numeric', 'min:0', 'max:9'],
            'pte_passed' => ['nullable', 'string', Rule::in(['yes', 'no'])],
            'pte_score' => ['nullable', 'required_if:pte_passed,yes', 'numeric', 'min:10', 'max:90'],
            'visit_reason' => ['required', 'string', 'max:100', Rule::in(array_keys(self::visitReasons()))],
            'visit_notes' => ['nullable', 'string', 'max:2000'],
            'source' => ['required', 'string', Rule::in(self::heardAboutOptions())],
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function matches(string $phone, ?string $email): Collection
    {
        $normalized = $this->normalizePhone($phone);
        $canonical = $this->canonicalAustralianPhone($phone);
        if ($normalized === '') {
            return collect();
        }

        $variants = array_values(array_unique(array_filter([
            $normalized,
            $canonical,
            $canonical !== '' ? ltrim($canonical, '0') : null,
            (str_starts_with($normalized, '0') && strlen($normalized) === 10) ? substr($normalized, 1) : null,
            (strlen($normalized) === 9) ? '0'.$normalized : null,
        ])));

        $query = Admin::query()
            ->whereIn('type', ['client', 'lead'])
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
            })
            ->where(function ($q) {
                $q->whereNull('is_archived')->orWhere('is_archived', 0);
            })
            ->where(function ($q) use ($variants, $phone): void {
                $expr = "REGEXP_REPLACE(COALESCE(phone, ''), '[^0-9]', '', 'g')";
                foreach ($variants as $variant) {
                    $q->orWhereRaw($expr.' LIKE ?', ['%'.$variant.'%']);
                }
                $trimmed = trim($phone);
                if ($trimmed !== '') {
                    $q->orWhere('phone', 'like', '%'.addcslashes($trimmed, '%_\\').'%');
                }
            });

        if (! empty($email)) {
            $query->where('email', 'like', '%'.trim($email).'%');
        }

        return $query
            ->orderByRaw("CASE WHEN type = 'client' THEN 0 ELSE 1 END")
            ->limit(20)
            ->get(['id', 'type', 'first_name', 'last_name', 'email', 'phone'])
            ->map(fn (Admin $record): array => [
                'id' => $record->id,
                'type' => $record->type,
                'name' => trim(($record->first_name ?? '').' '.($record->last_name ?? '')),
                'email' => $record->email,
                'phone' => $record->phone,
            ])
            ->values();
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{message: string}
     */
    public function submit(array $validated, ?string $ip): array
    {
        $this->guardOtherReason($validated['visit_reason'] ?? null, $validated['visit_notes'] ?? null);

        $adminId = isset($validated['admin_id']) && (int) $validated['admin_id'] > 0
            ? (int) $validated['admin_id']
            : null;
        $adminType = isset($validated['admin_type']) && $validated['admin_type'] !== ''
            ? (string) $validated['admin_type']
            : null;

        if (($adminId === null) !== ($adminType === null)) {
            throw ValidationException::withMessages([
                'admin_id' => 'Choose a match, or continue as a walk-in.',
            ]);
        }

        $record = null;
        if ($adminId !== null && $adminType !== null) {
            $record = $this->selectableRecord(
                (string) $validated['phone'],
                $validated['email'] ?? null,
                $adminId,
                $adminType
            );
        }

        $walkInName = trim((string) ($validated['walk_in_name'] ?? ''));
        if ($record === null && $walkInName === '') {
            throw ValidationException::withMessages([
                'walk_in_name' => 'Please enter your name, or choose a matching record.',
            ]);
        }

        return $this->persistCheckIn($record, $walkInName, $validated, $ip);
    }

    /**
     * Create a new Education lead from the public enquiry form, then check them in.
     *
     * @param  array<string, mixed>  $validated
     * @return array{message: string}
     */
    public function createLead(array $validated, ?string $ip): array
    {
        $this->guardOtherReason($validated['visit_reason'] ?? null, $validated['visit_notes'] ?? null);

        $phone = (string) $validated['phone'];
        $canonical = $this->canonicalAustralianPhone($phone);
        if ($canonical === '') {
            throw ValidationException::withMessages([
                'phone' => 'Please enter a valid Australian phone number (10 digits, with or without the leading 0).',
            ]);
        }

        if ($this->phoneIsTaken($canonical)) {
            throw ValidationException::withMessages([
                'phone' => 'This phone number is already registered. Please look up the existing record.',
            ]);
        }

        $email = trim((string) $validated['email']);
        if ($this->emailIsTaken($email)) {
            throw ValidationException::withMessages([
                'email' => 'This email is already registered. Please look up the existing record.',
            ]);
        }

        try {
            DB::beginTransaction();

            $lead = $this->storeLeadAdmin($validated, $canonical);
            $this->storeEnglishTestScores($lead, $validated);

            $result = $this->persistCheckIn($lead, '', $validated, $ip, false);

            DB::commit();
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[PublicOfficeCheckIn] createLead failed', [
                'error' => $e->getMessage(),
                'ip' => $ip,
            ]);

            throw $e;
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{message: string}
     */
    private function persistCheckIn(?Admin $record, string $walkInName, array $validated, ?string $ip, bool $useTransaction = true): array
    {
        $reasonLabel = $this->reasonLabel($validated['visit_reason'] ?? null);
        $notes = trim((string) ($validated['visit_notes'] ?? ''));
        $visitPurpose = $reasonLabel;
        if ($notes !== '') {
            $visitPurpose .= ' — '.$notes;
        }
        if ($record === null) {
            $visitPurpose = 'Walk-in: '.$walkInName.' / '.$this->normalizePhone((string) $validated['phone']).' — '.$visitPurpose;
        }

        $contactType = $record
            ? ($record->type === 'lead' ? 'Lead' : 'Client')
            : 'Walk-in';

        $run = function () use ($record, $walkInName, $visitPurpose, $contactType): array {
            $checkinLog = CheckinLog::create([
                'client_id' => $record?->id,
                'user_id' => null,
                'visit_purpose' => $visitPurpose,
                'office' => $this->defaultOfficeId(),
                'contact_type' => $contactType,
                'status' => 0,
                'date' => now()->toDateString(),
            ]);

            $this->writeHistory($checkinLog->id);
            $this->notifyReception($checkinLog, $record, $walkInName);

            return [
                'message' => 'You are checked in. Please take a seat — our team will be with you shortly.',
            ];
        };

        if (! $useTransaction) {
            return $run();
        }

        try {
            DB::beginTransaction();
            $result = $run();
            DB::commit();

            return $result;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[PublicOfficeCheckIn] Submit failed', [
                'error' => $e->getMessage(),
                'ip' => $ip,
            ]);

            throw $e;
        }
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function storeLeadAdmin(array $validated, string $canonicalPhone): Admin
    {
        $city = $this->nullableString($validated['city'] ?? null);
        $address = $this->nullableString($validated['address'] ?? null) ?? $city;
        $qualification = $this->nullableString($validated['qualification'] ?? null);
        $qualificationType = $this->nullableString($validated['qualification_type'] ?? null);
        $qualificationDate = $this->nullableString($validated['qualification_date'] ?? null);

        $comments = [];
        if ($qualificationType !== null) {
            $comments[] = 'Qualification type: '.$qualificationType;
        }
        if ($qualificationDate !== null) {
            $comments[] = 'Qualification date: '.$qualificationDate;
        }

        $assignee = $this->receptionStaffId();
        $officeId = $this->defaultOfficeId();
        if ($assignee !== null) {
            $staff = Staff::query()->find($assignee);
            if ($staff && $staff->office_id) {
                $officeId = (int) $staff->office_id;
            }
        }

        $data = [
            'type' => 'lead',
            'first_name' => trim((string) $validated['first_name']),
            'last_name' => trim((string) $validated['last_name']),
            'email' => trim((string) $validated['email']),
            'password' => bcrypt(Str::random(32)),
            'phone' => $canonicalPhone,
            'country_code' => '+61',
            'dob' => $validated['dob'] ?? null,
            'marital_status' => $validated['marital_status'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $address,
            'city' => $city,
            'source' => $validated['source'] ?? null,
            'visa_type' => $this->nullableString($validated['current_visa'] ?? null),
            'visaexpiry' => $validated['visa_expiry'] ?? null,
            'high_quali_overseas' => $qualification,
            'comments_note' => $comments !== [] ? implode('; ', $comments) : null,
            'assignee' => $assignee,
            'office_id' => $officeId,
            'user_id' => $assignee,
            'status' => 0,
            'verified' => 0,
            'is_archived' => 0,
            'is_deleted' => 0,
            'converted' => 0,
            'show_dashboard_per' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $cols = Schema::getColumnListing('admins');
        $data = array_intersect_key($data, array_flip($cols));
        $adminId = (int) DB::table('admins')->insertGetId($data);

        $firstName = substr((string) ($validated['first_name'] ?? ''), 0, 4);
        $clientId = strtoupper(preg_replace('/[^A-Za-z]/', '', $firstName) ?: 'LEAD').date('ym').$adminId;
        if (in_array('client_id', $cols, true)) {
            DB::table('admins')->where('id', $adminId)->update(['client_id' => $clientId]);
        }

        $lead = Admin::query()->find($adminId);
        if (! $lead) {
            throw new \RuntimeException('Failed to create lead for office check-in.');
        }

        return $lead;
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function storeEnglishTestScores(Admin $lead, array $validated): void
    {
        if (! Schema::hasTable('client_testscore')) {
            return;
        }

        $assignee = $this->receptionStaffId() ?? 0;

        if (($validated['ielts_passed'] ?? null) === 'yes') {
            $score = $validated['ielts_score'] ?? null;
            if ($score !== null && $score !== '') {
                ClientTestScore::create([
                    'admin_id' => $assignee,
                    'client_id' => $lead->id,
                    'test_type' => 'IELTS',
                    'overall_score' => $score,
                    'relevant_test' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if (($validated['pte_passed'] ?? null) === 'yes') {
            $score = $validated['pte_score'] ?? null;
            if ($score !== null && $score !== '') {
                ClientTestScore::create([
                    'admin_id' => $assignee,
                    'client_id' => $lead->id,
                    'test_type' => 'PTE',
                    'overall_score' => $score,
                    'relevant_test' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function phoneIsTaken(string $canonicalPhone): bool
    {
        $digits = $this->normalizePhone($canonicalPhone);
        $withoutZero = ltrim($digits, '0');

        return Admin::query()
            ->whereIn('type', ['client', 'lead'])
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
            })
            ->where(function ($q) {
                $q->whereNull('is_archived')->orWhere('is_archived', 0);
            })
            ->where(function ($q) use ($digits, $withoutZero): void {
                $expr = "REGEXP_REPLACE(COALESCE(phone, ''), '[^0-9]', '', 'g')";
                $q->whereRaw($expr.' = ?', [$digits])
                    ->orWhereRaw($expr.' = ?', [$withoutZero])
                    ->orWhereRaw($expr.' = ?', ['0'.$withoutZero]);
            })
            ->exists();
    }

    private function emailIsTaken(string $email): bool
    {
        return Admin::query()
            ->whereIn('type', ['client', 'lead'])
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
            })
            ->where(function ($q) {
                $q->whereNull('is_archived')->orWhere('is_archived', 0);
            })
            ->exists();
    }

    private function selectableRecord(string $phone, ?string $email, int $adminId, string $adminType): Admin
    {
        $match = $this->matches($phone, $email)->first(function (array $row) use ($adminId, $adminType) {
            return (int) $row['id'] === $adminId && $row['type'] === $adminType;
        });

        if (! $match) {
            throw ValidationException::withMessages([
                'admin_id' => 'That record does not match the phone number entered.',
            ]);
        }

        $record = Admin::query()->find($adminId);
        if (! $record) {
            throw ValidationException::withMessages([
                'admin_id' => 'That record could not be found.',
            ]);
        }

        return $record;
    }

    private function guardOtherReason(?string $reason, ?string $notes): void
    {
        if ($reason === 'other' && trim((string) $notes) === '') {
            throw ValidationException::withMessages([
                'visit_notes' => 'Please add a note when you choose Other.',
            ]);
        }
    }

    private function reasonLabel(?string $reason): string
    {
        if ($reason === null || $reason === '') {
            return 'Office check-in';
        }

        return self::visitReasons()[$reason] ?? $reason;
    }

    private function defaultOfficeId(): ?int
    {
        if (! Schema::hasTable('branches')) {
            return null;
        }

        $first = Branch::query()->orderBy('id')->value('id');

        return $first ? (int) $first : null;
    }

    private function receptionStaffId(): ?int
    {
        $receptionId = (int) config('constants.reception_user_id', 0);
        if ($receptionId > 0 && Staff::query()->whereKey($receptionId)->exists()) {
            return $receptionId;
        }

        return null;
    }

    private function writeHistory(int $checkinId): void
    {
        if (! Schema::hasTable('checkin_histories')) {
            return;
        }

        $staffId = $this->receptionStaffId();
        if ($staffId === null) {
            return;
        }

        CheckinHistory::create([
            'subject' => PublicOfficeCheckInAssignees::historySubject(),
            'created_by' => $staffId,
            'checkin_id' => $checkinId,
        ]);
    }

    private function notifyReception(CheckinLog $checkinLog, ?Admin $record, string $walkInName): void
    {
        $recipientId = $this->receptionStaffId();
        if ($recipientId === null) {
            return;
        }

        $clientName = $record
            ? trim(($record->first_name ?? '').' '.($record->last_name ?? ''))
            : ($walkInName !== '' ? $walkInName : 'Walk-in visitor');

        $message = 'Office check-in: '.$clientName.' has arrived.';

        $notification = Notification::create([
            'sender_id' => null,
            'receiver_id' => $recipientId,
            'module_id' => $checkinLog->id,
            'url' => url('/office-visits/waiting'),
            'notification_type' => 'officevisit',
            'message' => $message,
            'seen' => 0,
            'receiver_status' => 0,
            'sender_status' => 1,
        ]);

        try {
            broadcast(new OfficeVisitNotificationCreated(
                $notification->id,
                $notification->receiver_id,
                [
                    'id' => $notification->id,
                    'checkin_id' => $checkinLog->id,
                    'message' => $message,
                    'sender_name' => 'Public check-in',
                    'client_name' => $clientName,
                    'visit_purpose' => $checkinLog->visit_purpose,
                    'created_at' => $notification->created_at?->format('d/m/Y h:i A') ?? now()->format('d/m/Y h:i A'),
                    'url' => $notification->url,
                ]
            ));
        } catch (\Throwable $e) {
            Log::warning('[PublicOfficeCheckIn] Broadcast failed', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}

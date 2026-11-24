<?php

namespace App\Services;

use App\Events\OfficeVisitNotificationCreated;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\CheckinHistory;
use App\Models\CheckinLog;
use App\Models\Notification;
use App\Models\Staff;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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

    public function normalizePhone(string $phone): string
    {
        return preg_replace('/\D/', '', $phone) ?? '';
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function matches(string $phone, ?string $email): Collection
    {
        $normalized = $this->normalizePhone($phone);
        if ($normalized === '') {
            return collect();
        }

        $query = Admin::query()
            ->whereIn('type', ['client', 'lead'])
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
            })
            ->where(function ($q) {
                $q->whereNull('is_archived')->orWhere('is_archived', 0);
            })
            ->where(function ($q) use ($normalized, $phone) {
                $q->whereRaw(
                    "REGEXP_REPLACE(COALESCE(phone, ''), '[^0-9]', '', 'g') LIKE ?",
                    ['%'.$normalized.'%']
                );
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

        try {
            DB::beginTransaction();

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

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[PublicOfficeCheckIn] Submit failed', [
                'error' => $e->getMessage(),
                'ip' => $ip,
            ]);

            throw $e;
        }

        return [
            'message' => 'You are checked in. Please take a seat — our team will be with you shortly.',
        ];
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
            'subject' => 'Check-in created via public office check-in page',
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
}

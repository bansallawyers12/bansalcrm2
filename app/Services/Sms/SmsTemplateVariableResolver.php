<?php

namespace App\Services\Sms;

use App\Helpers\Helper;
use App\Models\Admin;
use App\Models\Application;
use App\Models\Branch;
use App\Models\Invoice;
use App\Models\Staff;
use App\Support\StaffAssigneeResolver;
use Carbon\Carbon;

/**
 * Resolves SMS template placeholders from client/application context.
 */
class SmsTemplateVariableResolver
{
    /**
     * Replace all resolvable placeholders in an SMS message.
     */
    public static function apply(string $message, int $clientId, array $context = []): string
    {
        if ($clientId <= 0 || ! str_contains($message, '{')) {
            return $message;
        }

        $replacements = self::placeholderReplacements($clientId, $context);
        if ($replacements === []) {
            return $message;
        }

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }

    /**
     * Build placeholder => value map (keys include curly braces).
     *
     * @return array<string, string>
     */
    /**
     * @param array<string, mixed> $context
     */
    public static function placeholderReplacements(int $clientId, array $context = []): array
    {
        $vars = self::resolveForClient($clientId, $context);

        $replacements = [];
        foreach ($vars as $key => $value) {
            $replacements['{'.$key.'}'] = $value;
        }

        // Legacy email-style placeholders used by Checklist Reminder SMS template.
        $replacements['{Client First Name}'] = $vars['client_first_name_legacy'];
        $replacements['{Company Name}'] = $vars['company_name'];
        $replacements['{Client Assignee Name}'] = $vars['staff_name'];

        return $replacements;
    }

    /**
     * @param array<string, mixed> $context
     * @return array<string, string>
     */
    public static function resolveForClient(int $clientId, array $context = []): array
    {
        $client = Admin::find($clientId);

        $rawFirst = trim((string) ($client?->first_name ?? ''));
        $rawLast = trim((string) ($client?->last_name ?? ''));
        $fullName = trim($rawFirst.' '.$rawLast);

        $studentName = $fullName !== '' ? $fullName : 'Client';
        $clientNameDisplay = $fullName !== ''
            ? mb_convert_case(mb_strtolower($fullName), MB_CASE_TITLE, 'UTF-8')
            : 'there';
        $firstDisplay = $rawFirst !== ''
            ? mb_convert_case(mb_strtolower($rawFirst), MB_CASE_TITLE, 'UTF-8')
            : 'there';
        $clientFirstNameLegacy = $rawFirst !== ''
            ? mb_strtoupper(mb_substr($rawFirst, 0, 1)).mb_substr($rawFirst, 1)
            : '';

        $staff = StaffAssigneeResolver::firstStaffFromAssigneeValue($client?->assignee ?? null);
        $staffName = $staff ? trim((string) ($staff->first_name ?? '')) : '';

        $officePhone = self::resolveOfficePhone($client, $staff);
        $matterNumber = trim((string) ($client?->client_id ?? ''));
        $invoiceNumber = self::resolveLatestUnpaidInvoiceNumber($clientId);
        $checklistDate = self::resolveChecklistDate($context);

        return [
            'client_name' => $clientNameDisplay,
            'first_name' => $firstDisplay,
            'last_name' => $rawLast,
            'Student_Name' => $studentName,
            'student_name' => $studentName,
            'Date' => $checklistDate,
            'date' => $checklistDate,
            'matter_number' => $matterNumber,
            'staff_name' => $staffName,
            'office_phone' => $officePhone,
            'invoice_number' => $invoiceNumber,
            'company_name' => Helper::defaultCrmCompanyName(),
            'client_first_name_legacy' => $clientFirstNameLegacy,
        ];
    }

    protected static function resolveOfficePhone(?Admin $client, ?Staff $staff): string
    {
        $branchId = $client?->office_id;

        if ($staff && ! empty($staff->office_id)) {
            $branchId = $branchId ?: $staff->office_id;
        }

        if ($branchId) {
            $branch = Branch::find($branchId);
            $phone = trim((string) ($branch?->phone ?? ''));
            if ($phone === '') {
                $phone = trim((string) ($branch?->mobile ?? ''));
            }
            if ($phone !== '') {
                return $phone;
            }
        }

        $profile = Helper::defaultCrmProfile();

        return trim((string) ($profile?->phone ?? ''));
    }

    protected static function resolveLatestUnpaidInvoiceNumber(int $clientId): string
    {
        if ($clientId <= 0) {
            return '';
        }

        try {
            // invoices table has no void_invoice column (that field lives on client receipts).
            $invoice = Invoice::query()
                ->where('client_id', $clientId)
                ->where('status', 0)
                ->orderByDesc('created_at')
                ->first();

            return $invoice ? (string) $invoice->id : '';
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * @param array<string, mixed> $context
     */
    protected static function resolveChecklistDate(array $context): string
    {
        $checklistDate = Carbon::now()->format('d/m/Y');
        $applicationId = $context['application_id'] ?? null;

        if ($applicationId && is_numeric($applicationId)) {
            $app = Application::find((int) $applicationId);
            if ($app && $app->checklist_sent_at) {
                $checklistDate = Carbon::parse($app->checklist_sent_at)->format('d/m/Y');
            }
        }

        return $checklistDate;
    }
}

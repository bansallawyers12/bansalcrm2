<?php

namespace App\Support;

use App\Models\ActivitiesLog;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Client/lead detail Activities tab: shared filters, order, and load-more pages.
 */
final class ClientDetailActivities
{
    public const PAGE_SIZE = 25;

    /**
     * @return array{keyword: string, activity_type: string, date_from: string, date_to: string}
     */
    public static function filtersFromRequest(Request $request): array
    {
        return [
            'keyword' => (string) $request->get('keyword', ''),
            'activity_type' => (string) $request->get('activity_type', 'all'),
            'date_from' => (string) $request->get('date_from', ''),
            'date_to' => (string) $request->get('date_to', ''),
        ];
    }

    /**
     * @param  array{keyword?: string, activity_type?: string, date_from?: string, date_to?: string}  $filters
     */
    public static function queryForClient(int $clientId, array $filters = []): Builder
    {
        $query = ActivitiesLog::query()
            ->forStudentRecords()
            ->where('activities_logs.client_id', $clientId);
        self::applyFilters($query, $filters);

        return $query->orderBy('activities_logs.created_at', 'DESC');
    }

    /**
     * @param  array{keyword?: string, activity_type?: string, date_from?: string, date_to?: string}  $filters
     */
    public static function paginate(int $clientId, array $filters = [], int $page = 1): Paginator
    {
        $page = max(1, $page);

        return self::queryForClient($clientId, $filters)
            ->simplePaginate(self::PAGE_SIZE, ['*'], 'page', $page);
    }

    /**
     * @param  array{keyword?: string, activity_type?: string, date_from?: string, date_to?: string}  $filters
     */
    public static function applyFilters(Builder $query, array $filters): void
    {
        $keyword = trim((string) ($filters['keyword'] ?? ''));
        if ($keyword !== '') {
            $like = '%'.$keyword.'%';
            $query->where(function ($q) use ($like) {
                $q->whereLike('activities_logs.description', $like)
                    ->orWhereLike('activities_logs.subject', $like);
            });
        }

        $activityType = (string) ($filters['activity_type'] ?? 'all');
        if ($activityType !== '' && $activityType !== 'all') {
            self::applyActivityTypeFilter($query, $activityType);
        }

        $dateFrom = self::parseFilterDate((string) ($filters['date_from'] ?? ''));
        if ($dateFrom !== null) {
            $query->whereDate('activities_logs.created_at', '>=', $dateFrom);
        }

        $dateTo = self::parseFilterDate((string) ($filters['date_to'] ?? ''));
        if ($dateTo !== null) {
            $query->whereDate('activities_logs.created_at', '<=', $dateTo);
        }
    }

    /**
     * Accepts d/m/Y (CRM dates) and Y-m-d (flatpickr). Invalid values are ignored.
     */
    public static function parseFilterDate(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        foreach (['d/m/Y', 'Y-m-d', 'd-m-Y'] as $format) {
            $parsed = \DateTimeImmutable::createFromFormat('!'.$format, $value);
            if (! $parsed instanceof \DateTimeImmutable) {
                continue;
            }
            $errors = \DateTimeImmutable::getLastErrors();
            if (is_array($errors) && (($errors['warning_count'] ?? 0) > 0 || ($errors['error_count'] ?? 0) > 0)) {
                continue;
            }

            return $parsed->format('Y-m-d');
        }

        return null;
    }

    private static function applyActivityTypeFilter(Builder $query, string $activityType): void
    {
        switch ($activityType) {
            case 'notes':
                $query->where(function ($q) {
                    $q->whereLike('activities_logs.subject', '%added a note%')
                        ->orWhereLike('activities_logs.subject', '%updated a note%')
                        ->orWhereLike('activities_logs.subject', '%deleted a note%');
                });
                break;
            case 'messages':
                $query->whereLike('activities_logs.subject', '%sent a message%');
                break;
            case 'calls':
                $query->where(function ($q) {
                    $q->whereLike('activities_logs.description', '%Call not picked%')
                        ->orWhereLike('activities_logs.subject', '%call%');
                });
                break;
            case 'reviews':
                $query->whereLike('activities_logs.subject', '%review%');
                break;
            case 'reminders':
                $query->where(function ($q) {
                    $q->whereLike('activities_logs.subject', '%Email reminder sent%')
                        ->orWhereLike('activities_logs.subject', '%SMS reminder sent%')
                        ->orWhereLike('activities_logs.subject', '%Phone reminder recorded%')
                        ->orWhereLike('activities_logs.subject', '%Checklist Email sent%')
                        ->orWhereLike('activities_logs.subject', '%Checklist Email resent%')
                        ->orWhereLike('activities_logs.subject', '%Document Checklist sent%');
                });
                break;
            case 'documents':
                $query->where(function ($q) {
                    $q->whereLike('activities_logs.subject', '%document%')
                        ->orWhereLike('activities_logs.subject', '%uploaded%')
                        ->orWhereLike('activities_logs.subject', '%verified%');
                });
                break;
            case 'action':
                $query->where(function ($q) {
                    $q->whereLike('activities_logs.subject', '%action%')
                        ->orWhereLike('activities_logs.subject', '%task%')
                        ->orWhereLike('activities_logs.subject', '%Completed action%')
                        ->orWhere('activities_logs.task_status', '=', 1);
                });
                break;
            case 'accounting':
                $query->where(function ($q) {
                    $q->whereLike('activities_logs.subject', '%receipt%')
                        ->orWhereLike('activities_logs.subject', '%invoice%')
                        ->orWhereLike('activities_logs.subject', '%payment%');
                });
                break;
            case 'applications':
                $query->whereLike('activities_logs.subject', '%started an application%');
                break;
            case 'services':
                $query->where(function ($q) {
                    $q->whereLike('activities_logs.subject', '%an interested service%');
                });
                break;
            case 'status':
                $query->where(function ($q) {
                    $q->whereLike('activities_logs.subject', '%status%')
                        ->orWhereLike('activities_logs.subject', '%rated%')
                        ->orWhereLike('activities_logs.subject', '%rating%');
                });
                break;
            case 'checkins':
                $query->where(function ($q) {
                    $q->whereLike('activities_logs.subject', '%check-in%')
                        ->orWhereLike('activities_logs.subject', '%session%')
                        ->orWhereLike('activities_logs.subject', '%commented%');
                });
                break;
            case 'other':
                $query->where(function ($q) {
                    $q->whereNotLike('activities_logs.subject', '%note%')
                        ->whereNotLike('activities_logs.subject', '%document%')
                        ->whereNotLike('activities_logs.subject', '%action%')
                        ->whereNotLike('activities_logs.subject', '%task%')
                        ->whereNotLike('activities_logs.subject', '%receipt%')
                        ->whereNotLike('activities_logs.subject', '%application%')
                        ->whereNotLike('activities_logs.subject', '%message%')
                        ->whereNotLike('activities_logs.subject', '%call%')
                        ->whereNotLike('activities_logs.subject', '%service%')
                        ->whereNotLike('activities_logs.subject', '%status%')
                        ->whereNotLike('activities_logs.subject', '%check-in%')
                        ->whereNotLike('activities_logs.subject', '%session%')
                        ->whereNotLike('activities_logs.subject', '%review%')
                        ->whereNotLike('activities_logs.subject', '%reminder%')
                        ->whereNotLike('activities_logs.subject', '%Checklist Email sent%')
                        ->whereNotLike('activities_logs.subject', '%Checklist Email resent%');
                });
                break;
        }
    }
}

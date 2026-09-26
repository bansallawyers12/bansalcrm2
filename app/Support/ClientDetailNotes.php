<?php

namespace App\Support;

use App\Models\Note;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Client/lead detail Notes tab: shared order and load-more pages.
 */
final class ClientDetailNotes
{
    public const PAGE_SIZE = 25;

    public static function queryForClient(int $clientId, string $type = 'client'): Builder
    {
        return Note::query()
            ->where('client_id', $clientId)
            ->whereNull('assigned_to')
            ->whereNull('task_group')
            ->where('type', $type)
            ->orderBy('pin', 'DESC')
            ->orderByRaw('created_at DESC NULLS LAST');
    }

    public static function paginate(int $clientId, string $type = 'client', int $page = 1): Paginator
    {
        $page = max(1, $page);

        return self::queryForClient($clientId, $type)
            ->simplePaginate(self::PAGE_SIZE, ['*'], 'page', $page);
    }
}

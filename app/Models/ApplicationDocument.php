<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ApplicationDocument extends Model
{
    use Sortable;

    /**
     * Default attributes for new rows.
     * status: 0 = unpublished, 1 = published (matches Publish/Unpublish UI).
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 0,
    ];
}

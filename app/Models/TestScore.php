<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class TestScore extends Model
{
    use Sortable;

    protected $fillable = [
        'user_id',
        'client_id',
        'type',
        'toefl_Listening', 'toefl_Reading', 'toefl_Writing', 'toefl_Speaking', 'toefl_Date',
        'ilets_Listening', 'ilets_Reading', 'ilets_Writing', 'ilets_Speaking', 'ilets_Date',
        'pte_Listening', 'pte_Reading', 'pte_Writing', 'pte_Speaking', 'pte_Date',
        'score_1', 'score_2', 'score_3',
        'sat_i', 'sat_ii', 'gre', 'gmat',
    ];
}

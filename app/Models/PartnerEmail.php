<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use App\Traits\SanitizesEmail;

class PartnerEmail extends BaseModel
{	use Sortable, SanitizesEmail;

    protected $emailAttributes = ['partner_email'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

}

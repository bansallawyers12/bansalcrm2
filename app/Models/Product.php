<?php

namespace App\Models;

use App\Models\Relations\CastingBelongsTo;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Product extends Model
{
    use Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'partner', 'branches', 'product_type', 'revenue_type', 'duration', 'intakemonth', 'descripton', 'note', 'created_at', 'updated_at',
    ];

    public $sortable = ['id', 'name', 'created_at', 'updated_at'];

    public function branchdetail()
    {
        return $this->castingBelongsTo(PartnerBranch::class, 'branches', 'branchdetail');
    }

    public function partnerdetail()
    {
        return $this->castingBelongsTo(Partner::class, 'partner', 'partnerdetail');
    }

    protected function castingBelongsTo(string $related, string $foreignKey, string $relationName): CastingBelongsTo
    {
        $instance = $this->newRelatedInstance($related);

        return new CastingBelongsTo(
            $instance->newQuery(),
            $this,
            $foreignKey,
            'id',
            $relationName,
        );
    }
}

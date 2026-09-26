<?php

namespace App\Models;

use App\Helpers\PhoneHelper;
use App\Models\Relations\CastingHasMany;
use App\Traits\SanitizesEmail;
use Kyslik\ColumnSortable\Sortable;

/**
 * @method static static|null find($id, $columns = null)
 * @method static \Illuminate\Database\Eloquent\Builder where($column, $operator = null, $value = null, $boolean = 'and')
 */
class Partner extends BaseModel
{
    use SanitizesEmail, Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'partner_name', 'service_workflow', 'created_at', 'updated_at',
    ];

    public $sortable = ['id', 'partner_name', 'created_at', 'updated_at'];

    public function workflow()
    {
        return $this->belongsTo('App\Models\Workflow', 'service_workflow', 'id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'partner_id');
    }

    public function products()
    {
        return $this->castingHasMany(Product::class, 'partner', 'id');
    }

    protected function castingHasMany(string $related, string $foreignKey, string $localKey): CastingHasMany
    {
        $instance = $this->newRelatedInstance($related);

        return new CastingHasMany(
            $instance->newQuery(),
            $this,
            $foreignKey,
            $localKey,
        );
    }

    public function agreements()
    {
        return $this->hasMany(PartnerAgreement::class, 'partner_id');
    }

    public function activeAgreements()
    {
        return $this->hasMany(PartnerAgreement::class, 'partner_id')->where('status', 'active');
    }

    /**
     * =========================================
     * PHONE COUNTRY CODE ACCESSORS/MUTATORS
     * =========================================
     */

    /**
     * Mutator: Normalize country_code when saving
     */
    public function setCountryCodeAttribute(mixed $value): void
    {
        $this->attributes['country_code'] = PhoneHelper::normalizeCountryCode($value);
    }

    /**
     * Accessor: Always return normalized country_code when reading
     */
    public function getCountryCodeAttribute(mixed $value): string
    {
        return PhoneHelper::normalizeCountryCode($value);
    }

    /**
     * Accessor: Get formatted phone number for display
     * Usage: $partner->formatted_phone
     * Returns: "+61 412345678"
     */
    public function getFormattedPhoneAttribute()
    {
        return PhoneHelper::formatPhoneNumber(
            $this->attributes['country_code'] ?? '',
            $this->attributes['phone'] ?? ''
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerAgreement extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'partner_agreements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'partner_id',
        'contract_start',
        'contract_expiry',
        'represent_region',
        'commission_percentage',
        'bonus',
        'description',
        'gst',
        'default_super_agent',
        'file_upload',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'contract_start' => 'date',
        'contract_expiry' => 'date',
        'gst' => 'boolean',
        'commission_percentage' => 'decimal:2',
        'bonus' => 'decimal:2',
    ];

    /**
     * Get the partner that owns the agreement.
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    /**
     * Scope a query to only include active agreements.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive agreements.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Get the formatted contract start date.
     */
    public function getFormattedContractStartAttribute()
    {
        return $this->contract_start ? $this->contract_start->format('d/m/Y') : 'N/A';
    }

    /**
     * Get the formatted contract expiry date.
     */
    public function getFormattedContractExpiryAttribute()
    {
        return $this->contract_expiry ? $this->contract_expiry->format('d/m/Y') : 'N/A';
    }

    /**
     * Get the representing regions as an array.
     */
    public function getRepresentRegionArrayAttribute()
    {
        return $this->represent_region ? explode(',', $this->represent_region) : [];
    }

    /**
     * Check if the agreement is currently active based on dates.
     */
    public function isCurrentlyActive()
    {
        if (!$this->contract_start || !$this->contract_expiry) {
            return false;
        }

        $today = now()->startOfDay();
        return $today->between($this->contract_start, $this->contract_expiry) && $this->status === 'active';
    }
}

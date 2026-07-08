<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static static|null find($id, $columns = null)
 * @method static \Illuminate\Database\Eloquent\Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder whereIn($column, $values, $boolean = 'and', $not = false)
 */
class EmailLabel extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'color',
        'type',
        'icon',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Staff who owns the label.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'user_id');
    }

    /** @deprecated Use staff() instead. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'user_id');
    }

    /**
     * Get the emails that have this label.
     */
    public function mailReports(): BelongsToMany
    {
        return $this->belongsToMany(Email::class, 'email_label_mail_report', 'email_label_id', 'mail_report_id')
                    ->withTimestamps();
    }

    /**
     * Check if this is a system label.
     */
    public function isSystem(): bool
    {
        return $this->type === 'system';
    }

    /**
     * Check if this is a custom label.
     */
    public function isCustom(): bool
    {
        return $this->type === 'custom';
    }

    /**
     * Resolved icon name when icon column is empty (matches label name).
     */
    public function defaultIconName(): string
    {
        $defaultIcons = [
            'inbox' => 'inbox',
            'sent' => 'paper-plane',
            'draft' => 'edit',
            'trash' => 'trash',
            'spam' => 'ban',
            'archive' => 'archive',
            'work' => 'briefcase',
            'personal' => 'user',
            'important' => 'star',
            'urgent' => 'exclamation-triangle',
            'follow up' => 'flag',
        ];

        return $defaultIcons[strtolower($this->name)] ?? 'tag';
    }

    /**
     * CSS classes for the label icon (legacy callers).
     */
    public function getDisplayIconAttribute(): string
    {
        return \App\Helpers\IconHelper::classesFromStored($this->icon, [], $this->defaultIconName());
    }

    /**
     * Rendered icon HTML for Blade templates.
     */
    public function getDisplayIconHtmlAttribute(): string
    {
        return \App\Helpers\IconHelper::renderStored($this->icon, [], $this->defaultIconName());
    }

    /**
     * Get the formatted color with fallback.
     */
    public function getFormattedColorAttribute(): string
    {
        return $this->color ?: '#3B82F6';
    }

    /**
     * Scope to filter active labels.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter system labels.
     */
    public function scopeSystem(Builder $query): Builder
    {
        return $query->where('type', 'system');
    }

    /**
     * Scope to filter custom labels.
     */
    public function scopeCustom(Builder $query): Builder
    {
        return $query->where('type', 'custom');
    }

    /**
     * Scope to filter by user.
     */
    public function scopeForUser(Builder $query, int|string|null $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhereNull('user_id'); // Include system labels
        });
    }
}

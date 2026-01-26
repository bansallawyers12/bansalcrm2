<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentCategory extends Model
{
    use Sortable;

    protected $table = 'document_categories';

    protected $fillable = [
        'name',
        'is_default',
        'user_id',
        'client_id',
        'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $sortable = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];

    // ==================== Relationships ====================

    /**
     * Get the user who created this category
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    /**
     * Get the client this category belongs to
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'client_id');
    }

    /**
     * Get all documents in this category
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'category_id');
    }

    // ==================== Scopes ====================

    /**
     * Scope to get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope to get default categories (General)
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to get categories for a specific client
     * Includes default categories and client-specific categories
     */
    public function scopeForClient($query, $clientId)
    {
        return $query->where(function($q) use ($clientId) {
            $q->where('is_default', true) // Include default categories
              ->orWhere('client_id', $clientId); // Include client-specific categories
        });
    }

    /**
     * Scope to get categories created by a specific user for a client
     */
    public function scopeForUserAndClient($query, $userId, $clientId)
    {
        return $query->where(function($q) use ($userId, $clientId) {
            $q->where('is_default', true) // Include default categories
              ->orWhere(function($subQ) use ($userId, $clientId) {
                  $subQ->where('user_id', $userId)
                       ->where('client_id', $clientId);
              });
        });
    }

    // ==================== Helper Methods ====================

    /**
     * Check if this is a default category
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Check if this category can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Default categories cannot be deleted
        if ($this->is_default) {
            return false;
        }

        // Categories with documents cannot be deleted
        if ($this->documents()->count() > 0) {
            return false;
        }

        return true;
    }

    /**
     * Get the count of documents in this category for a specific client
     */
    public function getDocumentCount($clientId = null): int
    {
        $query = $this->documents()
            ->whereNull('not_used_doc')
            ->where(function ($q) {
                // Count only documents with doc_type='documents' or NULL/empty
                $q->where('doc_type', 'documents')
                    ->orWhere(function ($subQ) {
                        $subQ->whereNull('doc_type')->orWhere('doc_type', '');
                    });
            });
        
        if ($clientId) {
            $query->where('client_id', $clientId);
        }
        
        return $query->count();
    }
}

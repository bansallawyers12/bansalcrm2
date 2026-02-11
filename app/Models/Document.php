<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    use Sortable;

    /** @var int Default: not part of edu/mig migration */
    const EDU_MIG_MIGRATE_DEFAULT = 0;
    /** @var int Education/Migration doc migrated successfully to Documents tab */
    const EDU_MIG_MIGRATE_SUCCESS = 1;
    /** @var int Education/Migration doc migration failed */
    const EDU_MIG_MIGRATE_FAILED = 2;

    protected $table = 'documents';

    // office_id column exists in DB but is unused in this CRM; omit from fillable unless used later
    protected $fillable = [
        'file_name',
        'filetype', 
        'myfile',
        'myfile_key',
        'doc_public_path',
        'user_id',
        'client_id',
        'file_size',
        'type',
        'doc_type',
        'category_id',
        'is_edu_and_mig_doc_migrate',
        'folder_name',
        'mail_type',
        'checklist',
        'checklist_verified_by',
        'checklist_verified_at',
        'not_used_doc',
        'status',
        'signature_doc_link',
        'signed_doc_link',
        'signed_hash',
        'hash_generated_at',
        'created_by',
        'origin',
        'documentable_type',
        'documentable_id',
        'title',
        'document_type',
        'labels',
        'due_at',
        'priority',
        'primary_signer_email',
        'signer_count',
        'last_activity_at',
        'archived_at',
        'application_id',
        'application_list_id',
        'application_stage',
    ];

    protected $casts = [
        'labels' => 'array',
        'checklist_verified_at' => 'datetime',
        'due_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'archived_at' => 'datetime',
        'hash_generated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $sortable = [
        'id',
        'file_name',
        'status',
        'created_at',
        'updated_at',
        'title',
        'document_type',
        'priority'
    ];

    // ==================== Relationships ====================

    public function signers(): HasMany
    {
        return $this->hasMany(Signer::class);
    }

    public function signatureFields(): HasMany
    {
        return $this->hasMany(SignatureField::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'client_id');
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(DocumentNote::class)->orderBy('created_at', 'desc');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    // ==================== Scopes ====================

    public function scopeForUser($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAssociated($query)
    {
        return $query->whereNotNull('documentable_type')
                    ->whereNotNull('documentable_id');
    }

    public function scopeAdhoc($query)
    {
        return $query->whereNull('documentable_type')
                    ->whereNull('documentable_id');
    }

    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Scope: documents stored in public folder (not on S3).
     * Local = myfile_key null/empty and myfile has a value.
     */
    public function scopeStoredLocally($query)
    {
        return $query
            ->where(function ($q) {
                $q->whereNull('myfile_key')
                    ->orWhere('myfile_key', '');
            })
            ->whereNotNull('myfile')
            ->where('myfile', '!=', '');
    }

    /**
     * Scope to filter documents based on user visibility permissions
     */
    public function scopeVisible($query, $user)
    {
        // Global access - everyone can see all documents
        return $query;
    }

    /**
     * Scope to show only signature workflow documents
     * Excludes client file uploads which don't have created_by set
     */
    public function scopeForSignatureWorkflow($query)
    {
        return $query->whereNotNull('created_by');
    }

    // ==================== Accessors ====================

    public function getDisplayTitleAttribute()
    {
        return $this->title ?: $this->file_name;
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => 'secondary',
            'signature_placed' => 'info',
            'sent' => 'warning', 
            'signed' => 'success',
            'voided' => 'danger',
            default => 'secondary'
        };
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_at && $this->due_at->isPast() && $this->status !== 'signed';
    }

    /**
     * Get visibility type for current authenticated user
     */
    public function getVisibilityTypeAttribute()
    {
        $user = auth('admin')->user();
        if (!$user) {
            return null;
        }

        // Check if user is the creator (highest priority)
        if ($this->created_by === $user->id) {
            return 'owner';
        }

        // Check if user is a signer
        if ($this->relationLoaded('signers')) {
            $isSigner = $this->signers->contains('email', $user->email);
        } else {
            $isSigner = $this->signers()->where('email', $user->email)->exists();
        }
        
        if ($isSigner) {
            return 'signer';
        }

        // Check if document is associated with user's entity
        if ($this->documentable_type && $this->documentable_id) {
            if ($this->documentable_type === Admin::class && $this->documentable_id === $user->id) {
                return 'associated';
            }
        }

        // Admin viewing all
        if ($user->role === 1) {
            return 'admin';
        }

        return null;
    }

    /**
     * Get visibility icon and label
     */
    public function getVisibilityBadgeAttribute()
    {
        return match($this->visibility_type) {
            'owner' => ['icon' => '🔒', 'label' => 'My Document', 'class' => 'badge-owner'],
            'signer' => ['icon' => '✍️', 'label' => 'Need to Sign', 'class' => 'badge-signer'],
            'associated' => ['icon' => '🔗', 'label' => 'Associated', 'class' => 'badge-associated'],
            'admin' => ['icon' => '🌐', 'label' => 'Organization', 'class' => 'badge-admin'],
            default => ['icon' => '👁️', 'label' => 'Visible', 'class' => 'badge-visible']
        };
    }

    // ==================== Hash Verification Methods ====================

    /**
     * Generate SHA-256 hash for the signed document
     */
    public function generateSignedHash(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found for hashing: {$filePath}");
        }

        $hash = hash_file('sha256', $filePath);
        
        $this->signed_hash = $hash;
        $this->hash_generated_at = now();
        $this->save();

        \Log::info('Document hash generated', [
            'document_id' => $this->id,
            'hash' => $hash,
            'file_path' => $filePath
        ]);

        return $hash;
    }

    /**
     * Verify the integrity of the signed document
     */
    public function verifySignedHash(?string $filePath = null): bool
    {
        if (!$this->signed_hash) {
            \Log::warning('No hash stored for document verification', ['document_id' => $this->id]);
            return false;
        }

        if (!$this->signed_doc_link) {
            \Log::warning('No signed document link for verification', ['document_id' => $this->id]);
            return false;
        }

        try {
            if (!$filePath) {
                // Try local storage first
                if (file_exists(storage_path('app/public/signed/' . $this->id . '_signed.pdf'))) {
                    $filePath = storage_path('app/public/signed/' . $this->id . '_signed.pdf');
                } else {
                    \Log::error('Signed document not found', ['document_id' => $this->id]);
                    return false;
                }
            }

            $currentHash = hash_file('sha256', $filePath);
            $isValid = $currentHash === $this->signed_hash;

            \Log::info('Document hash verification', [
                'document_id' => $this->id,
                'is_valid' => $isValid,
                'stored_hash' => $this->signed_hash,
                'current_hash' => $currentHash
            ]);

            return $isValid;
        } catch (\Exception $e) {
            \Log::error('Error verifying document hash', [
                'document_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get hash display for UI (shortened)
     */
    public function getHashDisplayAttribute(): ?string
    {
        if (!$this->signed_hash) {
            return null;
        }

        return substr($this->signed_hash, 0, 8) . '...' . substr($this->signed_hash, -8);
    }

    /**
     * Get comprehensive status information for display
     */
    public function getStatusInfo()
    {
        $pendingSigners = $this->signers()->where('status', 'pending')->get();
        $openedSigners = $this->signers()->where('status', 'pending')->whereNotNull('opened_at')->get();
        $signedSigners = $this->signers()->where('status', 'signed')->get();
        $reminderCount = $this->signers()->max('reminder_count') ?? 0;

        // If document is signed by all signers
        if ($signedSigners->count() > 0 && $pendingSigners->count() === 0) {
            return [
                'text' => 'Signed',
                'class' => 'signed'
            ];
        }

        // If document has been sent and signers have opened but not signed
        if ($this->status === 'sent' && $openedSigners->count() > 0) {
            return [
                'text' => 'Opened - Not Signed',
                'class' => 'opened-not-signed'
            ];
        }

        // If document has been sent and reminders have been sent
        if ($this->status === 'sent' && $reminderCount > 0) {
            if ($reminderCount === 1) {
                return ['text' => 'First Reminder Sent', 'class' => 'first-reminder'];
            } elseif ($reminderCount === 2) {
                return ['text' => 'Second Reminder Sent', 'class' => 'second-reminder'];
            } elseif ($reminderCount >= 3) {
                return ['text' => 'Final Reminder Sent', 'class' => 'final-reminder'];
            }
        }

        // If document has been sent but not opened yet
        if ($this->status === 'sent' && $openedSigners->count() === 0) {
            return ['text' => 'Sent for Signature', 'class' => 'sent'];
        }

        // If document has signature fields placed but not sent
        if ($this->status === 'signature_placed') {
            return ['text' => 'Ready to Send', 'class' => 'ready-to-send'];
        }

        // If document is in draft state
        if ($this->status === 'draft' || !$this->status) {
            return ['text' => 'Draft', 'class' => 'draft'];
        }

        // If document is voided
        if ($this->status === 'voided') {
            return ['text' => 'Voided', 'class' => 'voided'];
        }

        // If document is archived
        if ($this->status === 'archived') {
            return ['text' => 'Archived', 'class' => 'archived'];
        }

        // Default fallback
        return [
            'text' => ucfirst($this->status ?? 'Draft'),
            'class' => $this->status ?? 'draft'
        ];
    }
}

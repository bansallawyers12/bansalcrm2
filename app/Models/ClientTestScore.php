<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientTestScore extends Model
{
    protected $table = 'client_testscore';

    protected $fillable = [
        'admin_id',
        'client_id',
        'test_type',
        'listening',
        'reading',
        'writing',
        'speaking',
        'overall_score',
        'proficiency_level',
        'proficiency_points',
        'test_date',
        'relevant_test',
        'test_reference_no',
    ];

    protected $casts = [
        'test_date' => 'date',
        'relevant_test' => 'boolean',
    ];

    /**
     * Allowed test types: stored value => label (match migrationmanager2 exactly).
     * Same values stored in both systems for export/import and migration.
     */
    public const TEST_TYPES = [
        'IELTS' => 'IELTS',
        'IELTS_A' => 'IELTS Academic',
        'PTE' => 'PTE',
        'TOEFL' => 'TOEFL',
        'CAE' => 'CAE',
        'OET' => 'OET',
        'CELPIP' => 'CELPIP General',
        'MET' => 'Michigan English Test (MET)',
        'LANGUAGECERT' => 'LANGUAGECERT Academic',
    ];

    public function client()
    {
        return $this->belongsTo(Admin::class, 'client_id');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migrate existing test_scores (one row per client with toefl/ilets/pte columns)
     * to client_testscore (one row per test type with generic listening/reading/writing/speaking).
     */
    public function up(): void
    {
        if (!Schema::hasTable('test_scores') || !Schema::hasTable('client_testscore')) {
            return;
        }

        $rows = DB::table('test_scores')
            ->where('type', 'client')
            ->whereNotNull('client_id')
            ->get();

        foreach ($rows as $row) {
            $clientId = $row->client_id;
            $userId = $row->user_id;

            // TOEFL
            if ($this->hasData($row, 'toefl')) {
                DB::table('client_testscore')->insert([
                    'admin_id' => $userId,
                    'client_id' => $clientId,
                    'test_type' => 'TOEFL',
                    'listening' => $row->toefl_Listening ?? null,
                    'reading' => $row->toefl_Reading ?? null,
                    'writing' => $row->toefl_Writing ?? null,
                    'speaking' => $row->toefl_Speaking ?? null,
                    'overall_score' => $row->score_1 ?? null,
                    'test_date' => $row->toefl_Date ?? null,
                    'relevant_test' => 1,
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                ]);
            }

            // IELTS
            if ($this->hasData($row, 'ilets')) {
                DB::table('client_testscore')->insert([
                    'admin_id' => $userId,
                    'client_id' => $clientId,
                    'test_type' => 'IELTS',
                    'listening' => $row->ilets_Listening ?? null,
                    'reading' => $row->ilets_Reading ?? null,
                    'writing' => $row->ilets_Writing ?? null,
                    'speaking' => $row->ilets_Speaking ?? null,
                    'overall_score' => $row->score_2 ?? null,
                    'test_date' => $row->ilets_Date ?? null,
                    'relevant_test' => 1,
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                ]);
            }

            // PTE
            if ($this->hasData($row, 'pte')) {
                DB::table('client_testscore')->insert([
                    'admin_id' => $userId,
                    'client_id' => $clientId,
                    'test_type' => 'PTE',
                    'listening' => $row->pte_Listening ?? null,
                    'reading' => $row->pte_Reading ?? null,
                    'writing' => $row->pte_Writing ?? null,
                    'speaking' => $row->pte_Speaking ?? null,
                    'overall_score' => $row->score_3 ?? null,
                    'test_date' => $row->pte_Date ?? null,
                    'relevant_test' => 1,
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                ]);
            }
        }
    }

    private function hasData($row, string $prefix): bool
    {
        if ($prefix === 'toefl') {
            return !empty($row->toefl_Listening) || !empty($row->toefl_Reading) || !empty($row->toefl_Writing) || !empty($row->toefl_Speaking) || !empty($row->score_1);
        }
        if ($prefix === 'ilets') {
            return !empty($row->ilets_Listening) || !empty($row->ilets_Reading) || !empty($row->ilets_Writing) || !empty($row->ilets_Speaking) || !empty($row->score_2);
        }
        if ($prefix === 'pte') {
            return !empty($row->pte_Listening) || !empty($row->pte_Reading) || !empty($row->pte_Writing) || !empty($row->pte_Speaking) || !empty($row->score_3);
        }
        return false;
    }

    /**
     * Reverse: do not delete client_testscore data; migration is one-way.
     */
    public function down(): void
    {
        // No-op: keep client_testscore rows
    }
};

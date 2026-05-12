<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * For databases that already ran legacy migrations named appointment_*,
     * rename tables and FK columns to followup_* (and fix historical typo caledar → calendar).
     */
    public function up(): void
    {
        $renameConsultants = Schema::hasTable('appointment_consultants')
            && ! Schema::hasTable('followup_consultants');
        $renameSettings = Schema::hasTable('appointment_calendar_settings')
            && ! Schema::hasTable('followup_calendar_settings');
        $renameBlocks = Schema::hasTable('appointment_caledar_block_timinings')
            && ! Schema::hasTable('followup_calendar_block_timings');

        if (! $renameConsultants && ! $renameSettings && ! $renameBlocks) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        try {
            if ($renameSettings && Schema::hasTable('appointment_calendar_settings')) {
                try {
                    Schema::table('appointment_calendar_settings', function (Blueprint $table) {
                        $table->dropForeign(['appointment_consultant_id']);
                    });
                } catch (\Throwable $e) {
                    // FK missing or DB-specific naming — constraints disabled below where supported.
                }
            }

            if ($renameConsultants) {
                Schema::rename('appointment_consultants', 'followup_consultants');
            }

            if ($renameSettings) {
                Schema::rename('appointment_calendar_settings', 'followup_calendar_settings');
            }

            if (Schema::hasTable('followup_calendar_settings')
                && Schema::hasColumn('followup_calendar_settings', 'appointment_consultant_id')) {
                Schema::table('followup_calendar_settings', function (Blueprint $table) {
                    $table->renameColumn('appointment_consultant_id', 'followup_consultant_id');
                });
            }

            if ($renameBlocks) {
                Schema::rename('appointment_caledar_block_timinings', 'followup_calendar_block_timings');
            }

            if (Schema::hasTable('followup_calendar_settings')
                && Schema::hasTable('followup_consultants')
                && Schema::hasColumn('followup_calendar_settings', 'followup_consultant_id')) {
                try {
                    Schema::table('followup_calendar_settings', function (Blueprint $table) {
                        $table->foreign('followup_consultant_id')
                            ->references('id')
                            ->on('followup_consultants')
                            ->cascadeOnDelete();
                    });
                } catch (\Throwable $e) {
                    // Constraint already exists after partial reruns.
                }
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Best-effort reverse — rerolls naming only when legacy appointment_* tables are absent.
     */
    public function down(): void
    {
        if (! Schema::hasTable('followup_calendar_settings')) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        try {
            try {
                Schema::table('followup_calendar_settings', function (Blueprint $table) {
                    $table->dropForeign(['followup_consultant_id']);
                });
            } catch (\Throwable $e) {
                //
            }

            if (Schema::hasColumn('followup_calendar_settings', 'followup_consultant_id')) {
                Schema::table('followup_calendar_settings', function (Blueprint $table) {
                    $table->renameColumn('followup_consultant_id', 'appointment_consultant_id');
                });
            }

            if (Schema::hasTable('followup_calendar_block_timings')
                && ! Schema::hasTable('appointment_caledar_block_timinings')) {
                Schema::rename('followup_calendar_block_timings', 'appointment_caledar_block_timinings');
            }

            if (Schema::hasTable('followup_calendar_settings')
                && ! Schema::hasTable('appointment_calendar_settings')) {
                Schema::rename('followup_calendar_settings', 'appointment_calendar_settings');
            }

            if (Schema::hasTable('followup_consultants')
                && ! Schema::hasTable('appointment_consultants')) {
                Schema::rename('followup_consultants', 'appointment_consultants');
            }

            if (Schema::hasTable('appointment_calendar_settings')) {
                try {
                    Schema::table('appointment_calendar_settings', function (Blueprint $table) {
                        $table->foreign('appointment_consultant_id')
                            ->references('id')
                            ->on('appointment_consultants')
                            ->cascadeOnDelete();
                    });
                } catch (\Throwable $e) {
                    //
                }
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table): void {
            if (! Schema::hasColumn('borrowings', 'urgency_level')) {
                $table->string('urgency_level', 20)->default('biasa')->after('status');
            }
            if (! Schema::hasColumn('borrowings', 'purpose_category')) {
                $table->string('purpose_category', 30)->default('praktik')->after('urgency_level');
            }
            if (! Schema::hasColumn('borrowings', 'is_teacher_priority')) {
                $table->boolean('is_teacher_priority')->default(false)->after('purpose_category');
            }
            if (! Schema::hasColumn('borrowings', 'expires_at')) {
                $table->dateTime('expires_at')->nullable()->after('due_at');
            }
        });

        try {
            Schema::table('borrowings', function (Blueprint $table): void {
                $table->index(['status', 'expires_at']);
            });
        } catch (\Throwable $e) {}

        try {
            Schema::table('borrowings', function (Blueprint $table): void {
                $table->index('is_teacher_priority');
            });
        } catch (\Throwable $e) {}

        if (DB::getDriverName() === 'mysql') {
            try {
                DB::statement("ALTER TABLE `borrowings` DROP CONSTRAINT `borrowings_status_check`");
            } catch (\Throwable $e) {
                try {
                    DB::statement("ALTER TABLE `borrowings` DROP CHECK `borrowings_status_check`");
                } catch (\Throwable $e2) {}
            }

            try {
                DB::statement("ALTER TABLE `borrowings` ADD CONSTRAINT `borrowings_status_check` CHECK (`status` IN ('pending', 'approved', 'rejected', 'cancelled', 'borrowed', 'return_pending_verification', 'returned', 'delay'))");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE `borrowings` ADD CONSTRAINT `borrowings_urgency_level_check` CHECK (`urgency_level` IN ('biasa', 'mendesak'))");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE `borrowings` ADD CONSTRAINT `borrowings_purpose_category_check` CHECK (`purpose_category` IN ('mengajar', 'praktik', 'ujian', 'lainnya'))");
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table): void {
            try {
                $table->dropIndex(['status', 'expires_at']);
            } catch (\Throwable $e) {}
            try {
                $table->dropIndex(['is_teacher_priority']);
            } catch (\Throwable $e) {}

            $colsToDrop = [];
            foreach (['urgency_level', 'purpose_category', 'is_teacher_priority', 'expires_at'] as $col) {
                if (Schema::hasColumn('borrowings', $col)) {
                    $colsToDrop[] = $col;
                }
            }
            if (! empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });

        if (DB::getDriverName() === 'mysql') {
            try {
                DB::statement("ALTER TABLE `borrowings` DROP CONSTRAINT `borrowings_status_check`");
            } catch (\Throwable $e) {
                try {
                    DB::statement("ALTER TABLE `borrowings` DROP CHECK `borrowings_status_check`");
                } catch (\Throwable $e2) {}
            }
            try {
                DB::statement("ALTER TABLE `borrowings` DROP CONSTRAINT `borrowings_urgency_level_check`");
            } catch (\Throwable $e) {
                try {
                    DB::statement("ALTER TABLE `borrowings` DROP CHECK `borrowings_urgency_level_check`");
                } catch (\Throwable $e2) {}
            }
            try {
                DB::statement("ALTER TABLE `borrowings` DROP CONSTRAINT `borrowings_purpose_category_check`");
            } catch (\Throwable $e) {
                try {
                    DB::statement("ALTER TABLE `borrowings` DROP CHECK `borrowings_purpose_category_check`");
                } catch (\Throwable $e2) {}
            }
            try {
                DB::statement("ALTER TABLE `borrowings` ADD CONSTRAINT `borrowings_status_check` CHECK (`status` IN ('pending', 'approved', 'rejected', 'cancelled', 'borrowed', 'return_pending_verification', 'returned'))");
            } catch (\Throwable $e) {}
        }
    }
};

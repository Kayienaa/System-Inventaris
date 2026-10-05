<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table): void {
            if (! Schema::hasColumn('borrowings', 'damage_evidence_path')) {
                $table->string('damage_evidence_path', 255)->nullable()->after('return_verification_note');
            }
        });

        Schema::table('assets', function (Blueprint $table): void {
            if (! Schema::hasColumn('assets', 'latest_damage_photo')) {
                $table->string('latest_damage_photo', 255)->nullable()->after('photo_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table): void {
            if (Schema::hasColumn('borrowings', 'damage_evidence_path')) {
                $table->dropColumn('damage_evidence_path');
            }
        });

        Schema::table('assets', function (Blueprint $table): void {
            if (Schema::hasColumn('assets', 'latest_damage_photo')) {
                $table->dropColumn('latest_damage_photo');
            }
        });
    }
};

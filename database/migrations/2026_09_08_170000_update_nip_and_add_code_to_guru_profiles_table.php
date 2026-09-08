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
        Schema::table('guru_profiles', function (Blueprint $table): void {
            $table->string('nip', 32)->change();

            if (! Schema::hasColumn('guru_profiles', 'code')) {
                $table->string('code', 30)->nullable()->after('nip');
            }

            if (Schema::hasColumn('guru_profiles', 'phone')) {
                $table->string('phone', 32)->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_profiles', function (Blueprint $table): void {
            if (Schema::hasColumn('guru_profiles', 'code')) {
                $table->dropColumn('code');
            }

            $table->string('nip', 30)->change();
        });
    }
};

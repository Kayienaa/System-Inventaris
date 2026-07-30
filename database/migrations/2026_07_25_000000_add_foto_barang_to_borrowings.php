<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            // foto_pinjam = foto wajah siswa (bukti pengambilan)
            // foto_barang = foto kondisi fisik barang saat dipinjam
            $table->string('foto_barang')->after('foto_pinjam');
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn('foto_barang');
        });
    }
};
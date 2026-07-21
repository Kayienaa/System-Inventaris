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
    Schema::create('borrowings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('item_id')->constrained()->onDelete('cascade');
        $table->dateTime('tgl_pinjam');
        $table->dateTime('tgl_kembali_rencana'); // Otomatis +3 hari dari tgl_pinjam
        $table->dateTime('tgl_kembali_realitas')->nullable();
        $table->string('foto_bukti_pinjam'); // Menyimpan path foto ber-timestamp
        $table->enum('status', ['Dipinjam', 'Dikembalikan', 'Terlambat', 'Rusak/Hilang'])->default('Dipinjam');
        $table->text('catatan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};

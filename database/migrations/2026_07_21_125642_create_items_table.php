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
    Schema::create('items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained()->restrictOnDelete();
        $table->string('kode_unik')->unique();
        $table->string('nama_barang');
        $table->string('merk')->nullable();
        $table->string('lokasi_ruangan')->default('Ruang TEFA 1');
        $table->enum('status', ['Tersedia', 'Dipinjam', 'Maintenance', 'Rusak'])->default('Tersedia');
        $table->string('gambar')->nullable(); // Menyimpan path gambar ber-timestamp
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};

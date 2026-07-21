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
        $table->string('kode_unik')->unique(); // Dibuat auto-generate nanti pas add
        $table->string('nama_barang');
        $table->enum('kategori', ['Laptop', 'HP', 'Komputer', 'Kabel', 'Kaos', 'Tumbler', 'Mug', 'Lainnya']);
        $table->enum('jenis', ['Elektronik', 'Stok']); // Pembedan barang pinjaman & merchandise
        $table->string('merk')->nullable();
        $table->string('lokasi_ruangan')->default('Ruang TEFA 1');
        $table->integer('stok')->default(1);
        $table->enum('status', ['Tersedia', 'Dipinjam', 'Maintenance'])->default('Tersedia');
        $table->string('gambar')->nullable();
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

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

        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('item_id')->constrained()->cascadeOnDelete();

        $table->dateTime('tgl_pinjam');
        $table->dateTime('tgl_kembali_rencana');
        $table->dateTime('tgl_kembali_realitas')->nullable();

        //Foto saat meminjam
        $table->string('foto_pinjam');

        //Foto saat mengembalikan
        $table->string('foto_pengembalian')->nullable();

        //Jika meminjam laptop
        $table->boolean('include_charger')->default(false);
        $table->boolean('include_mouse')->default(false);

        $table->enum('status', [
            'Dipinjam',
            'Dikembalikan',
            'Terlambat',
            'Rusak',    
            'Hilang'
        ])->default('Dipinjam');

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

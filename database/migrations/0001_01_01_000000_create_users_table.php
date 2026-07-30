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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->enum('role', ['admin', 'guru', 'siswa'])->default('siswa');

            // Kredensial login berbeda per role
            $table->string('nis')->nullable()->unique();   // Login Siswa
            $table->string('nip')->nullable()->unique();   // Login Guru
            $table->string('email')->nullable()->unique(); // Login Admin + tujuan notifikasi email

            $table->date('tanggal_lahir')->nullable(); // Dipakai sebagai "password" login Siswa
            $table->string('password')->nullable();    // Wajib utk admin/guru, siswa = hash(tanggal_lahir)

            $table->string('no_wa')->nullable();
            $table->string('foto_profil')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

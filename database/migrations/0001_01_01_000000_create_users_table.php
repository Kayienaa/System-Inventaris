<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->enum('role', ['admin', 'guru', 'siswa'])->default('siswa');

            $table->string('nis')->nullable()->unique();
            $table->string('nip')->nullable()->unique();
            $table->string('email')->nullable()->unique();

            $table->date('tanggal_lahir')->nullable();
            $table->string('password')->nullable();

            $table->string('no_wa')->nullable();
            $table->string('foto_profil')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
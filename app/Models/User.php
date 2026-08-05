<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

#[Fillable(['name', 'role', 'nis', 'nip', 'email', 'tanggal_lahir', 'password', 'no_wa', 'foto_profil'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'role',
        'nis',
        'nip',
        'email',
        'tanggal_lahir',
        'password',
        'no_wa',
        'foto_profil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'tanggal_lahir' => 'date',
            'password' => 'hashed',
        ];
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    /**
     * Password Siswa diturunkan dari tanggal lahir.
     * Format WAJIB sama persis dengan yang divalidasi di RegisterRequest & LoginRequest: Y-m-d.
     */
    public static function passwordFromTanggalLahir(string $tanggalLahirYmd): string
    {
        return Hash::make($tanggalLahirYmd);
    }

    // Dipakai Laravel saat mengirim Mailable/Notification ke user ini
    public function routeNotificationForMail(): ?string
    {
        return $this->email;
    }
}
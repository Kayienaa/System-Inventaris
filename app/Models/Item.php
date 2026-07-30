<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['category_id', 'kode_unik', 'nama_barang', 'merk', 'lokasi_ruangan', 'status', 'gambar'])]
class Item extends Model
{
    use HasFactory;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    // Peminjaman aktif (status = Dipinjam) yang terbaru untuk item ini
    public function activeBorrowing(): HasOne
    {
        return $this->hasOne(Borrowing::class)->where('status', 'Dipinjam')->latestOfMany();
    }

    public function isAvailable(): bool
    {
        return $this->status === 'Tersedia';
    }
}
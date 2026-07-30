<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'item_id',
    'tgl_pinjam', 'tgl_kembali_rencana', 'tgl_kembali_realitas',
    'foto_pinjam', 'foto_barang', 'foto_pengembalian',
    'include_charger', 'include_mouse',
    'status', 'catatan',
])]
class Borrowing extends Model
{
    protected function casts(): array
    {
        return [
            'tgl_pinjam' => 'datetime',
            'tgl_kembali_rencana' => 'datetime',
            'tgl_kembali_realitas' => 'datetime',
            'include_charger' => 'boolean',
            'include_mouse' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'Dipinjam');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->active()->where('tgl_kembali_rencana', '<', now());
    }
}
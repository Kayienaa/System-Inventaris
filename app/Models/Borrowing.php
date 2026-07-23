<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'item_id',
    'tgl_pinjam',
    'tgl_kembali_rencana',
    'tgl_kembali_realitas',
    'foto_pinjam',
    'foto_pengembalian',
    'include_charger',
    'include_mouse',
    'status',
    'catatan'
])]
class Borrowing extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}

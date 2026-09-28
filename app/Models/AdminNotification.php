<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk notifikasi yang belum dibaca.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Tandai semua notifikasi menjadi dibaca.
     */
    public static function markAllAsRead(?int $userId = null): int
    {
        $query = static::unread();

        if ($userId !== null) {
            $query->where(function (Builder $q) use ($userId) {
                $q->whereNull('user_id')->orWhere('user_id', $userId);
            });
        }

        return $query->update(['is_read' => true]);
    }

    /**
     * Hapus seluruh riwayat notifikasi admin.
     */
    public static function deleteAll(?int $userId = null): int
    {
        $query = static::query();

        if ($userId !== null) {
            $query->where(function (Builder $q) use ($userId) {
                $q->whereNull('user_id')->orWhere('user_id', $userId);
            });
        }

        return $query->delete();
    }
}

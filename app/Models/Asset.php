<?php

namespace App\Models;

use App\Enums\AssetAvailabilityStatus;
use App\Enums\AssetCondition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'asset_category_id',
        'asset_code',
        'name',
        'brand',
        'model',
        'serial_number',
        'condition',
        'availability_status',
        'photo_path',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'condition' => AssetCondition::class,
            'availability_status' => AssetAvailabilityStatus::class,
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * Peminjaman aktif (belum dikembalikan) yang terbaru untuk aset ini.
     */
    public function activeBorrowing(): HasOne
    {
        return $this->hasOne(Borrowing::class)
            ->whereIn('status', ['approved', 'borrowed', 'return_pending_verification'])
            ->latestOfMany();
    }

    /**
     * Cek apakah aset saat ini berstatus tersedia untuk dipinjam.
     */
    public function isAvailable(): bool
    {
        return $this->availability_status === AssetAvailabilityStatus::Tersedia;
    }

    /**
     * Retrieve the model for a bound value (by id or asset_code).
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)
            ->orWhere('asset_code', $value)
            ->first() ?? abort(404);
    }
}

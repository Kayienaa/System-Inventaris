<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AssetAvailabilityStatus;
use App\Enums\AssetCondition;
use App\Enums\BorrowingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAssetRequest;
use App\Http\Requests\Admin\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AssetManagementController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'verified', 'role:admin']),
        ];
    }

    /**
     * Menampilkan tabel aset dengan pencarian nama/kode unik, filter kategori, dan filter status.
     */
    public function index(Request $request): View
    {
        $categories = AssetCategory::query()->orderBy('name')->get();
        $conditions = AssetCondition::cases();
        $statuses = AssetAvailabilityStatus::cases();

        $query = Asset::query()
            ->with(['category', 'activeBorrowing.borrower'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%' . trim($request->string('search')) . '%';
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', $search)
                        ->orWhere('asset_code', 'like', $search)
                        ->orWhere('serial_number', 'like', $search)
                        ->orWhere('brand', 'like', $search)
                        ->orWhere('model', 'like', $search);
                });
            })
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $q->where('asset_category_id', $request->integer('category_id'));
            })
            ->when($request->filled('category'), function ($q) use ($request) {
                $cat = $request->string('category')->toString();
                $q->whereHas('category', function ($sub) use ($cat) {
                    $sub->where('name', $cat)->orWhere('code', $cat);
                });
            })
            ->when($request->filled('availability_status'), function ($q) use ($request) {
                $q->where('availability_status', $request->string('availability_status')->toString());
            })
            ->when($request->filled('condition'), function ($q) use ($request) {
                $q->where('condition', $request->string('condition')->toString());
            });

        $assets = $query->latest('id')->paginate(12)->withQueryString();

        // Metrik statistik untuk ringkasan di panel admin
        $stats = [
            'total' => Asset::query()->count(),
            'tersedia' => Asset::query()->where('availability_status', AssetAvailabilityStatus::Tersedia->value)->count(),
            'dipinjam' => Asset::query()->where('availability_status', AssetAvailabilityStatus::Dipinjam->value)->count(),
            'perbaikan' => Asset::query()->where('availability_status', AssetAvailabilityStatus::Perbaikan->value)->count(),
        ];

        return view('admin.assets.index', compact('assets', 'categories', 'conditions', 'statuses', 'stats'));
    }

    /**
     * Menampilkan form tambah unit aset baru.
     */
    public function create(): View
    {
        $categories = AssetCategory::query()->where('is_active', true)->orderBy('name')->get();
        if ($categories->isEmpty()) {
            $categories = AssetCategory::query()->orderBy('name')->get();
        }

        $conditions = AssetCondition::cases();
        $statuses = AssetAvailabilityStatus::cases();

        return view('admin.assets.create', compact('categories', 'conditions', 'statuses'));
    }

    /**
     * Validasi data, unggah foto ke storage publik (storage/assets/), simpan record baru, dan catat audit log.
     */
    public function store(StoreAssetRequest $request, AuditLogService $audit): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('assets', 'public');
        }
        unset($data['photo']);

        $asset = Asset::query()->create($data);

        $audit->record(
            $request->user(),
            'asset.created',
            $asset,
            null,
            $asset->getAttributes(),
            ['source' => 'admin_asset_management']
        );

        return redirect()->route('admin.assets.index')
            ->with('success', "Aset \"{$asset->name}\" ({$asset->asset_code}) berhasil ditambahkan ke inventaris.");
    }

    /**
     * Redirect show ke edit aset.
     */
    public function show(Asset $asset): View|RedirectResponse
    {
        return redirect()->route('admin.assets.edit', $asset);
    }

    /**
     * Menampilkan form edit informasi aset dan status ketersediaan.
     */
    public function edit(Asset $asset): View
    {
        $categories = AssetCategory::query()->orderBy('name')->get();
        $conditions = AssetCondition::cases();
        $statuses = AssetAvailabilityStatus::cases();

        return view('admin.assets.edit', compact('asset', 'categories', 'conditions', 'statuses'));
    }

    /**
     * Memperbarui data aset, tangani penggantian foto, dan catat audit log.
     */
    public function update(UpdateAssetRequest $request, Asset $asset, AuditLogService $audit): RedirectResponse
    {
        $data = $request->validated();
        $oldAttributes = $asset->getOriginal();

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($asset->photo_path && Storage::disk('public')->exists($asset->photo_path)) {
                Storage::disk('public')->delete($asset->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('assets', 'public');
        }
        unset($data['photo']);

        $asset->update($data);

        $audit->record(
            $request->user(),
            'asset.updated',
            $asset,
            $oldAttributes,
            $asset->getAttributes(),
            ['source' => 'admin_asset_management']
        );

        return redirect()->route('admin.assets.index')
            ->with('success', "Informasi aset \"{$asset->name}\" ({$asset->asset_code}) berhasil diperbarui.");
    }

    /**
     * Hapus aset dengan pencegahan jika aset sedang dalam transaksi peminjaman aktif.
     */
    public function destroy(Asset $asset, AuditLogService $audit): RedirectResponse
    {
        // Pencegahan jika aset sedang aktif dalam transaksi peminjaman
        $hasActiveBorrowing = $asset->borrowings()
            ->whereIn('status', [
                BorrowingStatus::Pending,
                BorrowingStatus::Approved,
                BorrowingStatus::Borrowed,
                BorrowingStatus::ReturnPendingVerification,
            ])
            ->exists();

        if ($hasActiveBorrowing) {
            return redirect()->route('admin.assets.index')
                ->with('error', "Aset \"{$asset->name}\" ({$asset->asset_code}) tidak dapat dihapus karena masih memiliki transaksi peminjaman yang aktif atau belum selesai.");
        }

        $oldAttributes = $asset->getAttributes();
        $assetName = $asset->name;
        $assetCode = $asset->asset_code;

        $asset->delete();

        $audit->record(
            request()->user(),
            'asset.deleted',
            $asset,
            $oldAttributes,
            null,
            ['source' => 'admin_asset_management']
        );

        return redirect()->route('admin.assets.index')
            ->with('success', "Aset \"{$assetName}\" ({$assetCode}) berhasil dihapus dari sistem.");
    }
}

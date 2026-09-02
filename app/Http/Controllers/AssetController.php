<?php

namespace App\Http\Controllers;

use App\Http\Requests\Assets\StoreAssetRequest;
use App\Http\Requests\Assets\UpdateAssetRequest;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use App\Services\AuditLogService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Halaman katalog barang (web view) — dilihat oleh guru & siswa.
     */
    public function webIndex(Request $request)
    {
        $categories = AssetCategory::query()->where('is_active', true)->get();

        $assets = Asset::query()
            ->with(['category', 'activeBorrowing.borrower'])
            ->when($request->filled('category'), function ($query) use ($request) {
                $categoryParam = $request->string('category')->toString();
                $query->whereHas('category', function ($cq) use ($categoryParam) {
                    $cq->where('name', $categoryParam)
                       ->orWhere('code', $categoryParam);
                });
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . $request->string('search')->trim() . '%';
                $query->where(function ($sq) use ($search) {
                    $sq->where('name', 'like', $search)
                       ->orWhere('asset_code', 'like', $search)
                       ->orWhere('brand', 'like', $search)
                       ->orWhere('model', 'like', $search)
                       ->orWhere('serial_number', 'like', $search);
                });
            })
            ->orderBy('id', 'asc')
            ->paginate(12)
            ->withQueryString();

        return view('assets.index', compact('assets', 'categories'));
    }

    public function index(): AnonymousResourceCollection
    {
        return AssetResource::collection(Asset::query()->with('category')->paginate());
    }

    public function store(StoreAssetRequest $request, AuditLogService $audit): AssetResource
    {
        $asset = Asset::query()->create($request->validated());
        $audit->record($request->user(), 'asset.created', $asset, null, $asset->getAttributes());

        return new AssetResource($asset->load('category'));
    }

    public function show(Asset $asset): AssetResource
    {
        return new AssetResource($asset->load('category'));
    }

    public function update(UpdateAssetRequest $request, Asset $asset, AuditLogService $audit): AssetResource
    {
        $old = $asset->getOriginal();
        $asset->update($request->validated());
        $audit->record($request->user(), 'asset.updated', $asset, $old, $asset->getAttributes());

        return new AssetResource($asset->load('category'));
    }

    public function destroy(Asset $asset, AuditLogService $audit): Response
    {
        $old = $asset->getAttributes();
        $asset->delete();
        $audit->record(request()->user(), 'asset.deleted', $asset, $old, null);

        return response()->noContent();
    }
}

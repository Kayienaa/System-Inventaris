<?php

namespace App\Http\Controllers;

use App\Enums\BorrowingStatus;
use App\Models\Asset;
use App\Models\Borrowing;
use App\Services\SiPintuService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        protected ?SiPintuService $sipintu = null,
    ) {
        $this->sipintu = $this->sipintu ?? app(SiPintuService::class);
    }

    public function index()
    {
        return view('dashboard', $this->analyticsData());
    }

    /**
     * Halaman Laporan (web view).
     */
    public function analytics()
    {
        return view('reports.index', $this->analyticsData());
    }

    private function analyticsData(): array
    {
        $sipintuSummary = null;
        try {
            $sipintuSummary = $this->sipintu->getDashboardSummary();
        } catch (\Throwable $e) {
            $sipintuSummary = [
                'is_connected' => false,
                'gateway_status' => 'offline',
                'total_students' => 0,
                'total_teachers' => 0,
            ];
        }

        return [
            'sipintu_summary' => $sipintuSummary,

            'total_aset' => Asset::count(),
            'per_kategori' => Asset::join('asset_categories', 'assets.asset_category_id', '=', 'asset_categories.id')
                ->select('asset_categories.name', DB::raw('count(*) as total'))
                ->groupBy('asset_categories.name')
                ->pluck('total', 'name'),
            'status_aset' => Asset::select('availability_status', DB::raw('count(*) as total'))
                ->groupBy('availability_status')
                ->pluck('total', 'availability_status'),
            'overdue' => Borrowing::with([
                    'borrower',
                    'asset',
                    'item',
                ])
                ->whereIn('status', [BorrowingStatus::Borrowed, BorrowingStatus::ReturnPendingVerification])
                ->whereNull('returned_at')
                ->where('due_at', '<', now())
                ->get()
                ->map(fn (Borrowing $b) => [
                    'peminjam' => $b->borrower->name,
                    'barang' => $b->asset?->name
                        ?? $b->item?->nama_barang
                        ?? '-',
                    'jatuh_tempo' => $b->due_at->format('d M Y, H:i'),
                    'terlambat_sejak' => $b->due_at->diffForHumans(),
                ]),
        ];
    }
}

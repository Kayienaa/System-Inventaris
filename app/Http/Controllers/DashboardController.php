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

        // Tren Peminjaman 7 Hari Terakhir — 1 query, group by tanggal
        $rangeStart = \Carbon\Carbon::today()->subDays(6)->startOfDay();
        $rangeEnd   = \Carbon\Carbon::today()->endOfDay();

        $dailyCounts = Borrowing::query()
            ->selectRaw('DATE(COALESCE(requested_at, created_at)) as day, COUNT(*) as total')
            ->where(function ($q) use ($rangeStart, $rangeEnd) {
                $q->whereBetween('requested_at', [$rangeStart, $rangeEnd])
                    ->orWhere(function ($fallback) use ($rangeStart, $rangeEnd) {
                        $fallback->whereNull('requested_at')
                            ->whereBetween('created_at', [$rangeStart, $rangeEnd]);
                    });
            })
            ->groupBy('day')
            ->pluck('total', 'day');

        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::today()->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M');
            $chartData[] = (int) ($dailyCounts[$date->toDateString()] ?? 0);
        }

        // Filter Peminjaman Rentang Minggu Aktif
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $weeklyFilter = function ($q) use ($startOfWeek, $endOfWeek) {
            $q->where(function ($sub) use ($startOfWeek, $endOfWeek) {
                $sub->whereBetween('requested_at', [$startOfWeek, $endOfWeek])
                    ->orWhere(function ($fallback) use ($startOfWeek, $endOfWeek) {
                        $fallback->whereNull('requested_at')
                            ->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                    });
            });
        };

        // Leaderboard Aset Populer Mingguan (Top 5 dengan transaksi aktif > 0)
        $popularAssets = Asset::with(['category'])
            ->withCount(['borrowings' => $weeklyFilter])
            ->whereHas('borrowings', $weeklyFilter)
            ->orderByDesc('borrowings_count')
            ->take(5)
            ->get();

        // Leaderboard Peminjam Teraktif Mingguan (Top 5 dengan transaksi aktif > 0)
        $activeBorrowers = \App\Models\User::withCount(['borrowings' => $weeklyFilter])
            ->whereHas('borrowings', $weeklyFilter)
            ->orderByDesc('borrowings_count')
            ->take(5)
            ->get();

        // Gabung 3 query count Asset menjadi 1 query agregasi
        $assetStats = Asset::query()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN availability_status = ? THEN 1 ELSE 0 END) as tersedia,
                SUM(CASE WHEN availability_status = ? THEN 1 ELSE 0 END) as dipinjam
            ', [
                \App\Enums\AssetAvailabilityStatus::Tersedia->value,
                \App\Enums\AssetAvailabilityStatus::Dipinjam->value,
            ])
            ->first();

        $totalAset = (int) ($assetStats->total ?? 0);
        $barangTersedia = (int) ($assetStats->tersedia ?? 0);
        $barangDipinjam = (int) ($assetStats->dipinjam ?? 0);
        $totalOverdue = Borrowing::whereIn('status', [BorrowingStatus::Borrowed, BorrowingStatus::ReturnPendingVerification])
            ->whereNull('returned_at')
            ->where('due_at', '<', now())
            ->count();

        $overdueList = Borrowing::with(['borrower', 'asset'])
            ->whereIn('status', [BorrowingStatus::Borrowed, BorrowingStatus::ReturnPendingVerification])
            ->whereNull('returned_at')
            ->where('due_at', '<', now())
            ->get()
            ->map(fn (Borrowing $b) => [
                'peminjam' => $b->borrower->name ?? 'User',
                'barang' => $b->asset?->name ?? '-',
                'jatuh_tempo' => $b->due_at ? $b->due_at->format('d M Y, H:i') : '-',
                'terlambat_sejak' => $b->due_at ? $b->due_at->diffForHumans() : '-',
            ]);

        return [
            'sipintu_summary' => $sipintuSummary,

            'total_aset' => $totalAset,
            'barang_tersedia' => $barangTersedia,
            'barang_dipinjam' => $barangDipinjam,
            'total_overdue' => $totalOverdue,

            'chart_labels' => $chartLabels,
            'chart_data' => $chartData,

            'popular_assets' => $popularAssets,
            'active_borrowers' => $activeBorrowers,

            'per_kategori' => Asset::join('asset_categories', 'assets.asset_category_id', '=', 'asset_categories.id')
                ->select('asset_categories.name', DB::raw('count(*) as total'))
                ->groupBy('asset_categories.name')
                ->pluck('total', 'name'),
            'status_aset' => Asset::select('availability_status', DB::raw('count(*) as total'))
                ->groupBy('availability_status')
                ->pluck('total', 'availability_status'),
            'overdue' => $overdueList,
        ];
    }
}

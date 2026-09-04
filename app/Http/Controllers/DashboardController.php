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

        // Tren Peminjaman 7 Hari Terakhir
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::today()->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M');
            $count = Borrowing::whereDate('requested_at', $date)
                ->orWhere(function ($q) use ($date) {
                    $q->whereNull('requested_at')->whereDate('created_at', $date);
                })
                ->count();
            $chartData[] = $count;
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

        $totalAset = Asset::count();
        $barangTersedia = Asset::where('availability_status', \App\Enums\AssetAvailabilityStatus::Tersedia)->count();
        $barangDipinjam = Asset::where('availability_status', \App\Enums\AssetAvailabilityStatus::Dipinjam)->count();
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

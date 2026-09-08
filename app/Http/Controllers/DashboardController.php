<?php

namespace App\Http\Controllers;

use App\Enums\BorrowingStatus;
use App\Models\Asset;
use App\Models\Borrowing;
use App\Services\SiPintuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        // Periode Minggu Berjalan (Senin s.d. Minggu)
        $startOfWeek = now()->startOfWeek(\Carbon\Carbon::MONDAY)->startOfDay();
        $endOfWeek   = now()->endOfWeek(\Carbon\Carbon::SUNDAY)->endOfDay();
        $weeklyPeriodLabel = $startOfWeek->locale('id')->translatedFormat('d F') . ' - ' . $endOfWeek->locale('id')->translatedFormat('d F Y');

        // Tren Peminjaman Minggu Berjalan (Senin s.d. Minggu) — 1 query, group by tanggal
        $dailyCounts = Borrowing::query()
            ->selectRaw('DATE(COALESCE(requested_at, created_at)) as day, COUNT(*) as total')
            ->where(function ($q) use ($startOfWeek, $endOfWeek) {
                $q->whereBetween('requested_at', [$startOfWeek, $endOfWeek])
                    ->orWhere(function ($fallback) use ($startOfWeek, $endOfWeek) {
                        $fallback->whereNull('requested_at')
                            ->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                    });
            })
            ->groupBy('day')
            ->pluck('total', 'day');

        $chartLabels = [];
        $chartData = [];
        $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $chartLabels[] = $dayNames[$i] . ' (' . $date->format('d/m') . ')';
            $chartData[] = (int) ($dailyCounts[$date->toDateString()] ?? 0);
        }

        // Filter Peminjaman Rentang Minggu Aktif
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

        $weeklyHistory = $this->getWeeklyHistory(6);

        return [
            'sipintu_summary' => $sipintuSummary,

            'total_aset' => $totalAset,
            'barang_tersedia' => $barangTersedia,
            'barang_dipinjam' => $barangDipinjam,
            'total_overdue' => $totalOverdue,

            'weekly_period_label' => $weeklyPeriodLabel,
            'weeklyPeriodLabel' => $weeklyPeriodLabel,
            'weekly_history' => $weeklyHistory,

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

    /**
     * Endpoint API Riwayat (History) Top Peminjaman Mingguan.
     */
    public function weeklyHistory(Request $request): JsonResponse
    {
        $history = $this->getWeeklyHistory(8);

        return response()->json([
            'status' => 'success',
            'data' => $history,
        ]);
    }

    /**
     * Agregasi data riwayat top peminjaman per pekan ke belakang.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getWeeklyHistory(int $weeks = 6): array
    {
        $history = [];

        for ($w = 0; $w < $weeks; $w++) {
            $wStart = now()->subWeeks($w)->startOfWeek(\Carbon\Carbon::MONDAY)->startOfDay();
            $wEnd   = now()->subWeeks($w)->endOfWeek(\Carbon\Carbon::SUNDAY)->endOfDay();
            $periodLabel = $wStart->locale('id')->translatedFormat('d M') . ' - ' . $wEnd->locale('id')->translatedFormat('d M Y');

            $filter = function ($q) use ($wStart, $wEnd) {
                $q->where(function ($sub) use ($wStart, $wEnd) {
                    $sub->whereBetween('requested_at', [$wStart, $wEnd])
                        ->orWhere(function ($fallback) use ($wStart, $wEnd) {
                            $fallback->whereNull('requested_at')
                                ->whereBetween('created_at', [$wStart, $wEnd]);
                        });
                });
            };

            $totalTx = Borrowing::query()->where($filter)->count();

            $topAssets = Asset::query()
                ->with(['category'])
                ->withCount(['borrowings' => $filter])
                ->whereHas('borrowings', $filter)
                ->orderByDesc('borrowings_count')
                ->take(3)
                ->get()
                ->map(fn (Asset $a) => [
                    'name' => $a->name,
                    'asset_code' => $a->asset_code,
                    'category' => $a->category?->name ?? 'Umum',
                    'count' => (int) $a->borrowings_count,
                ])
                ->values()
                ->all();

            $topBorrowers = \App\Models\User::query()
                ->with(['siswaProfile', 'guruProfile'])
                ->withCount(['borrowings' => $filter])
                ->whereHas('borrowings', $filter)
                ->orderByDesc('borrowings_count')
                ->take(3)
                ->get()
                ->map(function ($u) {
                    $ident = $u->siswaProfile?->class_name
                        ? 'Kelas ' . $u->siswaProfile->class_name
                        : ($u->guruProfile ? 'Guru' : ($u->hasRole('admin') ? 'Admin' : '-'));

                    return [
                        'name' => $u->name,
                        'email' => $u->email,
                        'identity' => $ident,
                        'count' => (int) $u->borrowings_count,
                    ];
                })
                ->values()
                ->all();

            $history[] = [
                'week_number' => $w,
                'is_current' => $w === 0,
                'period' => $periodLabel,
                'start_date' => $wStart->toDateString(),
                'end_date' => $wEnd->toDateString(),
                'total_transactions' => $totalTx,
                'top_assets' => $topAssets,
                'top_borrowers' => $topBorrowers,
            ];
        }

        return $history;
    }
}

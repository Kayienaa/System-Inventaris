<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', $this->analyticsData());
    }

    /**
     * Endpoint JSON khusus untuk chart/dashboard admin.
     */
    public function analytics()
    {
        return response()->json($this->analyticsData());
    }

    private function analyticsData(): array
    {
        return [
            'total_aset' => Item::count(),
            'per_kategori' => Item::join('categories', 'items.category_id', '=', 'categories.id')
                ->select('categories.nama', DB::raw('count(*) as total'))
                ->groupBy('categories.nama')
                ->pluck('total', 'nama'),
            'status_aset' => Item::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status'),
            'overdue' => Borrowing::with(['user', 'item'])
                ->overdue() // status Dipinjam & tgl_kembali_rencana < now()
                ->get()
                ->map(fn (Borrowing $b) => [
                    'peminjam' => $b->user->name,
                    'barang' => $b->item->nama_barang,
                    'jatuh_tempo' => $b->tgl_kembali_rencana->format('d M Y, H:i'),
                    'terlambat_sejak' => $b->tgl_kembali_rencana->diffForHumans(),
                ]),
        ];
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BorrowingStatus;
use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BorrowingController extends Controller
{
    /**
     * Tampilkan Pusat Pengecekan & Monitoring Peminjaman User untuk Super Admin.
     */
    public function index(Request $request): View
    {
        $query = Borrowing::with([
            'borrower.siswaProfile',
            'borrower.guruProfile',
            'borrower.roles',
            'asset.category',
        ]);

        // Filter status: all, borrowed, returned, overdue
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'overdue') {
                $query->whereIn('status', [BorrowingStatus::Borrowed, BorrowingStatus::ReturnPendingVerification])
                    ->whereNull('returned_at')
                    ->where('due_at', '<', now());
            } elseif ($status === 'borrowed') {
                $query->where('status', BorrowingStatus::Borrowed);
            } elseif ($status === 'returned') {
                $query->where('status', BorrowingStatus::Returned);
            }
        }

        // Filter pencarian berdasarkan nama, NIS/NIP, kode aset, atau nama aset
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->whereHas('borrower', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('siswaProfile', fn ($sq) => $sq->where('nis', 'like', "%{$search}%"))
                        ->orWhereHas('guruProfile', fn ($gq) => $gq->where('nip', 'like', "%{$search}%"));
                })->orWhereHas('asset', function ($assetQuery) use ($search) {
                    $assetQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('asset_code', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%");
                });
            });
        }

        $borrowings = $query->latest('id')->paginate(10)->withQueryString();

        // Counter statistik untuk kartu ringkasan di dashboard monitoring
        $stats = [
            'total' => Borrowing::count(),
            'borrowed' => Borrowing::where('status', BorrowingStatus::Borrowed)->count(),
            'returned' => Borrowing::where('status', BorrowingStatus::Returned)->count(),
            'overdue' => Borrowing::whereIn('status', [BorrowingStatus::Borrowed, BorrowingStatus::ReturnPendingVerification])
                ->whereNull('returned_at')
                ->where('due_at', '<', now())
                ->count(),
        ];

        return view('admin.borrowings.index', compact('borrowings', 'stats'));
    }

    /**
     * Tampilkan detail transaksi peminjaman (baik untuk modal JSON maupun view mandiri).
     */
    public function show(Request $request, Borrowing $borrowing)
    {
        $borrowing->load([
            'borrower.siswaProfile',
            'borrower.guruProfile',
            'borrower.roles',
            'asset.category',
            'approvedBy',
            'returnVerifiedBy',
        ]);

        $detail = $this->formatBorrowingDetail($borrowing);

        if ($request->wantsJson()) {
            return response()->json($detail);
        }

        return view('admin.borrowings.show', compact('borrowing', 'detail'));
    }

    /**
     * Helper pemformatan data transaksi lengkap untuk antarmuka Admin.
     */
    public function formatBorrowingDetail(Borrowing $borrowing): array
    {
        $identity = '-';
        if ($borrowing->borrower?->siswaProfile?->nis) {
            $identity = 'NIS: ' . $borrowing->borrower->siswaProfile->nis;
        } elseif ($borrowing->borrower?->guruProfile?->nip) {
            $identity = 'NIP: ' . $borrowing->borrower->guruProfile->nip;
        }

        $role = $borrowing->borrower?->roles->pluck('name')->first() ?? 'User';
        $isOverdue = $borrowing->isOverdue();

        $statusValue = $borrowing->status->value ?? (string) $borrowing->status;
        $displayStatus = $isOverdue ? 'overdue' : $statusValue;

        return [
            'id' => $borrowing->id,
            'transaction_code' => '#TRX-' . str_pad((string) $borrowing->id, 5, '0', STR_PAD_LEFT),
            'borrower' => [
                'name' => $borrowing->borrower?->name ?? 'Pengguna Tidak Ditemukan',
                'email' => $borrowing->borrower?->email ?? '-',
                'role' => ucfirst($role),
                'identity' => $identity,
                'phone' => $borrowing->borrower?->siswaProfile?->phone ?? $borrowing->borrower?->guruProfile?->phone ?? '-',
                'class_name' => $borrowing->borrower?->siswaProfile?->class_name ?? null,
            ],
            'asset' => [
                'id' => $borrowing->asset?->id,
                'name' => $borrowing->asset?->name ?? 'Aset Tidak Ditemukan',
                'asset_code' => $borrowing->asset?->asset_code ?? '-',
                'brand' => $borrowing->asset?->brand ?? '-',
                'model' => $borrowing->asset?->model ?? '-',
                'serial_number' => $borrowing->asset?->serial_number ?? '-',
                'category' => $borrowing->asset?->category?->name ?? '-',
                'photo_url' => $borrowing->asset?->photo_path ? asset('storage/' . $borrowing->asset->photo_path) : null,
            ],
            'dates' => [
                'borrowed_at' => $borrowing->borrowed_at ? $borrowing->borrowed_at->format('d M Y, H:i') . ' WIB' : ($borrowing->requested_at ? $borrowing->requested_at->format('d M Y, H:i') . ' WIB' : '-'),
                'due_at' => $borrowing->due_at ? $borrowing->due_at->format('d M Y, H:i') . ' WIB' : '-',
                'returned_at' => $borrowing->returned_at ? $borrowing->returned_at->format('d M Y, H:i') . ' WIB' : null,
            ],
            'status' => $displayStatus,
            'is_overdue' => $isOverdue,
            'borrower_note' => $borrowing->borrower_note ?: 'Tidak ada catatan',
            'return_note' => $borrowing->return_note ?: null,
            'borrowing_evidence_url' => $borrowing->borrowing_evidence_path ? asset('storage/' . $borrowing->borrowing_evidence_path) : null,
            'return_evidence_url' => $borrowing->return_evidence_path ? asset('storage/' . $borrowing->return_evidence_path) : null,
        ];
    }
}

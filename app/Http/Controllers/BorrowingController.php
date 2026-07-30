<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorrowingRequest;
use App\Models\Borrowing;
use App\Models\Item;
use App\Services\ImageCompressionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    public function __construct(private ImageCompressionService $imageService)
    {
    }

    /**
     * Proses peminjaman alat baru.
     */
    public function store(StoreBorrowingRequest $request)
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($validated) {
            // Lock baris item agar tidak ada race condition (2 orang pinjam bersamaan)
            $item = Item::lockForUpdate()->findOrFail($validated['item_id']);

            if (! $item->isAvailable()) {
                return back()->withErrors(['item_id' => 'Alat ini sudah tidak tersedia untuk dipinjam.']);
            }

            $fotoSiswa = $this->imageService->compressAndStore($validated['foto_siswa'], 'borrowings/siswa');
            $fotoBarang = $this->imageService->compressAndStore($validated['foto_barang'], 'borrowings/barang');

            Borrowing::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'tgl_pinjam' => now(),
                'tgl_kembali_rencana' => $validated['tgl_kembali_rencana'],
                'foto_pinjam' => $fotoSiswa,
                'foto_barang' => $fotoBarang,
                'include_charger' => $validated['include_charger'] ?? false,
                'include_mouse' => $validated['include_mouse'] ?? false,
                'status' => 'Dipinjam',
                'catatan' => $validated['catatan'] ?? null,
            ]);

            $item->update(['status' => 'Dipinjam']);

            return redirect()->route('items.index')->with('success', 'Peminjaman berhasil dicatat.');
        });
    }

    /**
     * Riwayat peminjaman milik user yang sedang login.
     */
    public function myBorrowings()
    {
            /** @var \App\Models\User $user */
        $user = Auth::user();
        $borrowings = $user->borrowings()->with('item')->latest()->paginate(10);

        return view('borrowings.index', compact('borrowings'));
    }
}
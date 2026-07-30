<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Katalog aset — dilihat oleh guru & siswa.
     * Sertakan info peminjam aktif agar frontend bisa toggle grayscale <-> full color.
     */
    public function index(Request $request)
    {
        $items = Item::with(['category', 'activeBorrowing.user'])
            ->when($request->category, fn ($q) => $q->whereHas('category', fn ($c) => $c->where('nama', $request->category)))
            ->get()
            ->map(function (Item $item) {
                $borrowing = $item->activeBorrowing;

                return [
                    'id' => $item->id,
                    'kode_unik' => $item->kode_unik,
                    'nama_barang' => $item->nama_barang,
                    'gambar' => $item->gambar,
                    'status' => $item->status,
                    'is_borrowed' => $item->status === 'Dipinjam',       // flag utk UI: true = full color
                    'borrower_name' => $borrowing?->user?->name,
                    'borrow_date' => $borrowing?->tgl_pinjam?->format('d M Y, H:i'),
                    'due_date' => $borrowing?->tgl_kembali_rencana?->format('d M Y, H:i'),
                ];
            });

        // Jika butuh untuk konsumsi API/JS, tinggal ganti baris di bawah:
        // return response()->json($items);

        return view('items.index', compact('items'));
    }
}
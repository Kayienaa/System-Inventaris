<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemBorrowingController extends Controller
{
    public function create(Item $item)
    {
        abort_unless($item->isAvailable(), 404);

        return view('items.borrow', compact('item'));
    }

    public function store(Request $request, Item $item)
    {
        $validated = $request->validate([
            'due_at' => ['required', 'date', 'after:now'],
            'borrower_note' => ['nullable', 'string', 'max:1000'],
            'borrowing_evidence' => ['required', 'image', 'max:5120'],
        ]);

        $item->refresh();

        if (!$item->isAvailable()) {
            return back()->withErrors([
                'item' => 'Barang ini sudah tidak tersedia untuk dipinjam.',
            ]);
        }

        $path = $request->file('borrowing_evidence')
            ->store('borrowing-evidence', 'public');

        $borrowing = Borrowing::create([
            'borrower_user_id' => Auth::id(),
            'item_id' => $item->id,
            'asset_id' => null,
            'status' => 'borrowed',
            'requested_at' => now(),
            'borrowed_at' => now(),
            'due_at' => $validated['due_at'],
            'borrowing_evidence_path' => $path,
            'borrower_note' => $validated['borrower_note'] ?? null,
        ]);

        $item->update([
            'status' => 'Dipinjam',
        ]);

        return redirect()
            ->route('borrowings.mine')
            ->with('success', 'Barang berhasil dipinjam.');
    }
}
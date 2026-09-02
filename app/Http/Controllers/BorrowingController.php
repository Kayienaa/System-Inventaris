<?php

namespace App\Http\Controllers;

use App\Actions\Borrowings\ApproveBorrowingAction;
use App\Actions\Borrowings\CancelBorrowingAction;
use App\Actions\Borrowings\CheckoutBorrowingAction;
use App\Actions\Borrowings\RejectBorrowingAction;
use App\Actions\Borrowings\RequestBorrowingAction;
use App\Actions\Borrowings\SubmitReturnAction;
use App\Actions\Borrowings\VerifyReturnAction;
use App\Enums\AssetCondition;
use App\Enums\BorrowingStatus;
use App\Http\Requests\Borrowings\ApproveBorrowingRequest;
use App\Http\Requests\Borrowings\CancelBorrowingRequest;
use App\Http\Requests\Borrowings\CheckoutBorrowingRequest;
use App\Http\Requests\Borrowings\RejectBorrowingRequest;
use App\Http\Requests\Borrowings\StoreBorrowingRequest;
use App\Http\Requests\Borrowings\SubmitReturnRequest;
use App\Http\Requests\Borrowings\VerifyReturnRequest;
use App\Http\Resources\BorrowingResource;
use App\Models\Asset;
use App\Models\Borrowing;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BorrowingController extends Controller
{
    /**
     * Halaman form pengajuan peminjaman barang (web view).
     */
    public function create(Asset $asset)
    {
        abort_unless($asset->isAvailable(), 404, 'Barang ini tidak tersedia untuk dipinjam.');

        return view('assets.borrow', compact('asset'));
    }

    /**
     * Helper untuk menyimpan gambar bukti baik dari File Upload maupun Webcam (Base64).
     */
    private function storeEvidenceImage(\Illuminate\Http\Request $request, string $inputName, string $folder): ?string
    {
        // 1. Jika diunggah via form file upload biasa
        if ($request->hasFile($inputName)) {
            return $request->file($inputName)->store($folder, 'public');
        }
        if ($request->hasFile($inputName . '_file')) {
            return $request->file($inputName . '_file')->store($folder, 'public');
        }

        // 2. Jika diunggah via Webcam Snapshot (Base64 Data URL)
        $base64 = $request->input($inputName);
        if (is_string($base64) && str_starts_with($base64, 'data:image/')) {
            @[$type, $data] = explode(';', $base64);
            @[, $data] = explode(',', $data);
            if ($data) {
                $decoded = base64_decode($data);
                if ($decoded !== false) {
                    $ext = 'jpg';
                    if (str_contains($type, 'png')) {
                        $ext = 'png';
                    } elseif (str_contains($type, 'webp')) {
                        $ext = 'webp';
                    }
                    $filename = $folder . '/' . \Illuminate\Support\Str::uuid() . '.' . $ext;
                    \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $decoded);
                    return $filename;
                }
            }
        }

        return null;
    }

    /**
     * Halaman daftar peminjaman milik user yang sedang login (web view).
     */
    public function webMine()
    {
        $borrowings = Borrowing::query()
            ->with(['asset.category', 'approvedBy'])
            ->where('borrower_user_id', request()->user()->id)
            ->latest('requested_at')
            ->paginate(15);

        return view('borrowings.mine', compact('borrowings'));
    }

    /**
     * Pengembalian barang instan dengan foto real-time dari kamera.
     */
    public function requestReturn(\Illuminate\Http\Request $request, Borrowing $borrowing, AuditLogService $audit)
    {
        if ($borrowing->borrower_user_id !== $request->user()->id && ! $request->user()->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki akses ke peminjaman ini.');
        }

        if ($borrowing->status !== BorrowingStatus::Borrowed) {
            abort(403, 'Hanya peminjaman dengan status "Dipinjam" yang dapat diajukan pengembaliannya.');
        }

        $oldAttributes = $borrowing->getAttributes();
        $evidencePath = $this->storeEvidenceImage($request, 'return_evidence', 'return-evidence');

        \Illuminate\Support\Facades\DB::transaction(function () use ($borrowing, $request, $evidencePath) {
            $borrowing->update([
                'status' => BorrowingStatus::Returned,
                'returned_at' => now(),
                'return_submitted_at' => now(),
                'return_evidence_path' => $evidencePath,
                'return_note' => $request->input('return_note'),
                'return_condition' => AssetCondition::Baik,
            ]);

            // Reset status ketersediaan Asset kembali ke Tersedia
            $borrowing->asset?->update([
                'availability_status' => \App\Enums\AssetAvailabilityStatus::Tersedia,
            ]);
        });

        $audit->record($request->user(), 'borrowing.returned', $borrowing, $oldAttributes, $borrowing->fresh()->getAttributes());

        return redirect()->route('borrowings.mine')
            ->with('success', 'Barang berhasil dikembalikan! Status telah selesai dan aset kini kembali tersedia.');
    }

    public function index(): AnonymousResourceCollection
    {
        $query = Borrowing::query()->with(['asset', 'borrower']);
        if (! request()->user()->hasRole('admin')) {
            $query->where('borrower_user_id', request()->user()->id);
        }

        return BorrowingResource::collection($query->paginate());
    }

    public function store(StoreBorrowingRequest $request, RequestBorrowingAction $action, AuditLogService $audit)
    {
        $asset = Asset::findOrFail($request->integer('asset_id'));

        $evidencePath = $this->storeEvidenceImage($request, 'borrowing_evidence', 'borrowing-evidence');

        $dueAt = $request->filled('due_at')
            ? \Carbon\Carbon::parse($request->input('due_at'))
            : now()->addDays(3);

        $borrowing = $action->execute(
            $request->user(),
            $asset,
            $request->input('borrower_note'),
            $evidencePath,
            $dueAt
        );

        $audit->record($request->user(), 'borrowing.requested', $borrowing, null, $borrowing->getAttributes());

        if ($request->wantsJson()) {
            return new BorrowingResource($borrowing->load(['asset', 'borrower']));
        }

        return redirect()
            ->route('borrowings.mine')
            ->with('success', 'Peminjaman berhasil dilakukan! Batas pengembalian: ' . $dueAt->format('d M Y, H:i'));
    }

    public function show(Borrowing $borrowing): BorrowingResource
    {
        $this->authorize('view', $borrowing);

        return new BorrowingResource($borrowing->load(['asset', 'borrower']));
    }

    public function approve(ApproveBorrowingRequest $request, Borrowing $borrowing, ApproveBorrowingAction $action, AuditLogService $audit, NotificationService $notifications): BorrowingResource
    {
        $old = $borrowing->getAttributes();
        $result = $action->execute($request->user(), $borrowing);
        $audit->record($request->user(), 'borrowing.approved', $result, $old, $result->getAttributes());
        $notifications->scheduleReminder($result);
        $notifications->queueApproval($result);

        return new BorrowingResource($result->load(['asset', 'borrower']));
    }

    public function reject(RejectBorrowingRequest $request, Borrowing $borrowing, RejectBorrowingAction $action, AuditLogService $audit, NotificationService $notifications): BorrowingResource
    {
        $old = $borrowing->getAttributes();
        $result = $action->execute($request->user(), $borrowing, $request->string('rejection_reason')->toString());
        $audit->record($request->user(), 'borrowing.rejected', $result, $old, $result->getAttributes());
        $notifications->queueRejection($result);

        return new BorrowingResource($result);
    }

    public function cancel(CancelBorrowingRequest $request, Borrowing $borrowing, CancelBorrowingAction $action, AuditLogService $audit): BorrowingResource
    {
        $old = $borrowing->getAttributes();
        $result = $action->execute($request->user(), $borrowing, $request->input('cancellation_reason'));
        $audit->record($request->user(), 'borrowing.cancelled', $result, $old, $result->getAttributes());

        return new BorrowingResource($result);
    }

    public function checkout(CheckoutBorrowingRequest $request, Borrowing $borrowing, CheckoutBorrowingAction $action, AuditLogService $audit): BorrowingResource
    {
        $old = $borrowing->getAttributes();
        $result = $action->execute($request->user(), $borrowing, $request->enum('checkout_condition', AssetCondition::class));
        $audit->record($request->user(), 'borrowing.checked_out', $result, $old, $result->getAttributes());

        return new BorrowingResource($result);
    }

    public function submitReturn(SubmitReturnRequest $request, Borrowing $borrowing, SubmitReturnAction $action, AuditLogService $audit): BorrowingResource
    {
        $old = $borrowing->getAttributes();
        $result = $action->execute($request->user(), $borrowing, $request->string('return_evidence_path')->toString(), $request->input('return_note'));
        $audit->record($request->user(), 'borrowing.return_submitted', $result, $old, $result->getAttributes());

        return new BorrowingResource($result);
    }

    public function verifyReturn(VerifyReturnRequest $request, Borrowing $borrowing, VerifyReturnAction $action, AuditLogService $audit, NotificationService $notifications): BorrowingResource
    {
        $old = $borrowing->getAttributes();
        $result = $action->execute($request->user(), $borrowing, $request->enum('return_condition', AssetCondition::class), $request->input('return_verification_note'));
        $audit->record($request->user(), 'borrowing.return_verified', $result, $old, $result->getAttributes());
        $notifications->queueReturnVerification($result);

        return new BorrowingResource($result);
    }
}

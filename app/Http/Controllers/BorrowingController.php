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
        $this->authorize('create', Borrowing::class);

        abort_unless($asset->isAvailable(), 404, 'Barang ini tidak tersedia untuk dipinjam.');

        return view('assets.borrow', compact('asset'));
    }

    /**
     * Helper untuk menyimpan dan mengompres gambar bukti baik dari File Upload maupun Webcam (Base64).
     */
    private function storeEvidenceImage(\Illuminate\Http\Request $request, string $inputName, string $folder): ?string
    {
        return app(\App\Services\ImageCompressionService::class)->compressAndStoreEvidence($request, $inputName, $folder);
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
     * Pengajuan serah terima barang (checkout) dengan foto real-time dari kamera.
     */
    public function webCheckout(\Illuminate\Http\Request $request, Borrowing $borrowing, CheckoutBorrowingAction $action, AuditLogService $audit)
    {
        $this->authorize('checkout', $borrowing);

        if ($borrowing->status !== BorrowingStatus::Approved) {
            return back()->with('error', 'Hanya peminjaman dengan status "Disetujui" yang dapat diserahterimakan.');
        }

        $evidencePath = $this->storeEvidenceImage($request, 'borrowing_evidence', 'borrowing-evidence');
        if (! $evidencePath && ! $borrowing->borrowing_evidence_path) {
            return back()->with('error', 'Foto bukti fisik serah terima bersama Admin wajib diunggah.');
        }

        $condition = $request->filled('checkout_condition')
            ? AssetCondition::tryFrom($request->input('checkout_condition')) ?? AssetCondition::Baik
            : AssetCondition::Baik;

        $oldAttributes = $borrowing->getAttributes();
        $result = $action->execute($request->user(), $borrowing, $condition, $evidencePath ?? $borrowing->borrowing_evidence_path);

        $audit->record($request->user(), 'borrowing.checked_out', $result, $oldAttributes, $result->getAttributes());

        return redirect()->route('borrowings.mine')
            ->with('success', 'Serah terima barang berhasil! Status unit kini resmi "Dipinjam".');
    }

    /**
     * Pengajuan pengembalian barang oleh peminjam (status return_pending_verification).
     */
    public function requestReturn(\Illuminate\Http\Request $request, Borrowing $borrowing, SubmitReturnAction $action, AuditLogService $audit)
    {
        $this->authorize('submitReturn', $borrowing);

        if ($borrowing->status !== BorrowingStatus::Borrowed) {
            return back()->with('error', 'Hanya peminjaman dengan status "Dipinjam" yang dapat diajukan pengembaliannya.');
        }

        $evidencePath = $this->storeEvidenceImage($request, 'return_evidence', 'return-evidence');
        if (! $evidencePath) {
            return back()->with('error', 'Foto bukti fisik pengembalian bersama Admin wajib diambil.');
        }

        $oldAttributes = $borrowing->getAttributes();
        $result = $action->execute($request->user(), $borrowing, $evidencePath, $request->input('return_note'));

        $audit->record($request->user(), 'borrowing.return_submitted', $result, $oldAttributes, $result->getAttributes());

        return redirect()->route('borrowings.mine')
            ->with('success', 'Pengajuan pengembalian berhasil dikirim! Menunggu verifikasi fisik oleh Admin.');
    }

    /**
     * Persetujuan pengajuan awal oleh Admin / Super Admin (Web view).
     */
    public function webApprove(\Illuminate\Http\Request $request, Borrowing $borrowing, ApproveBorrowingAction $action, AuditLogService $audit, NotificationService $notifications)
    {
        $this->authorize('approve', $borrowing);

        $old = $borrowing->getAttributes();
        $result = $action->execute($request->user(), $borrowing);
        $audit->record($request->user(), 'borrowing.approved', $result, $old, $result->getAttributes());
        $notifications->scheduleReminder($result);
        $notifications->queueApproval($result);

        return back()->with('success', 'Pengajuan peminjaman #' . str_pad((string) $borrowing->id, 5, '0', STR_PAD_LEFT) . ' berhasil disetujui. Menunggu serah terima unit.');
    }

    /**
     * Penolakan pengajuan awal oleh Admin / Super Admin (Web view).
     */
    public function webReject(\Illuminate\Http\Request $request, Borrowing $borrowing, RejectBorrowingAction $action, AuditLogService $audit, NotificationService $notifications)
    {
        $this->authorize('reject', $borrowing);

        $reason = $request->input('rejection_reason', 'Pengajuan ditolak oleh Admin.');
        $old = $borrowing->getAttributes();
        $result = $action->execute($request->user(), $borrowing, $reason);
        $audit->record($request->user(), 'borrowing.rejected', $result, $old, $result->getAttributes());
        $notifications->queueRejection($result);

        return back()->with('success', 'Pengajuan peminjaman #' . str_pad((string) $borrowing->id, 5, '0', STR_PAD_LEFT) . ' telah ditolak.');
    }

    /**
     * Verifikasi penerimaan pengembalian fisik oleh Admin / Super Admin (Web view).
     */
    public function webVerifyReturn(\Illuminate\Http\Request $request, Borrowing $borrowing, VerifyReturnAction $action, AuditLogService $audit, NotificationService $notifications)
    {
        $this->authorize('verifyReturn', $borrowing);

        $condition = $request->filled('return_condition')
            ? AssetCondition::tryFrom($request->input('return_condition')) ?? AssetCondition::Baik
            : AssetCondition::Baik;

        $old = $borrowing->getAttributes();
        $result = $action->execute($request->user(), $borrowing, $condition, $request->input('return_verification_note'));
        $audit->record($request->user(), 'borrowing.return_verified', $result, $old, $result->getAttributes());
        $notifications->queueReturnVerification($result);

        return back()->with('success', 'Pengembalian barang berhasil diverifikasi! Unit telah kembali tersedia di katalog.');
    }

    public function index(): AnonymousResourceCollection
    {
        $query = Borrowing::query()->with(['asset', 'borrower']);
        if (! request()->user()->hasAnyRole(['admin', 'super_admin'])) {
            $query->where('borrower_user_id', request()->user()->id);
        }

        return BorrowingResource::collection($query->paginate());
    }

    public function store(
        StoreBorrowingRequest $request,
        ?Asset $asset = null,
        ?RequestBorrowingAction $action = null,
        ?AuditLogService $audit = null
    ) {
        $action ??= app(RequestBorrowingAction::class);
        $audit ??= app(AuditLogService::class);

        $targetAsset = ($asset && $asset->exists)
            ? $asset
            : Asset::find($request->input('asset_id'))
                ?? Asset::where('asset_code', $request->input('asset_id'))->firstOrFail();

        $evidencePath = $this->storeEvidenceImage($request, 'borrowing_evidence', 'borrowing-evidence');

        $dueAt = $request->filled('due_at')
            ? \Carbon\Carbon::parse($request->input('due_at'))
            : now()->addDays(3);

        $borrowing = $action->execute(
            $request->user(),
            $targetAsset,
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
            ->with('success', 'Permohonan peminjaman berhasil diajukan! Menunggu persetujuan Admin.');
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
        $evidencePath = $this->storeEvidenceImage($request, 'borrowing_evidence', 'borrowing-evidence')
            ?? $request->input('borrowing_evidence_path');

        $old = $borrowing->getAttributes();
        $result = $action->execute(
            $request->user(),
            $borrowing,
            $request->enum('checkout_condition', AssetCondition::class) ?? AssetCondition::Baik,
            $evidencePath
        );
        $audit->record($request->user(), 'borrowing.checked_out', $result, $old, $result->getAttributes());

        return new BorrowingResource($result);
    }

    public function submitReturn(SubmitReturnRequest $request, Borrowing $borrowing, SubmitReturnAction $action, AuditLogService $audit): BorrowingResource
    {
        $evidencePath = $this->storeEvidenceImage($request, 'return_evidence', 'return-evidence')
            ?? $request->input('return_evidence_path');

        $old = $borrowing->getAttributes();
        $result = $action->execute($request->user(), $borrowing, (string) $evidencePath, $request->input('return_note'));
        $audit->record($request->user(), 'borrowing.return_submitted', $result, $old, $result->getAttributes());

        return new BorrowingResource($result);
    }

    public function verifyReturn(VerifyReturnRequest $request, Borrowing $borrowing, VerifyReturnAction $action, AuditLogService $audit, NotificationService $notifications): BorrowingResource
    {
        $old = $borrowing->getAttributes();
        $result = $action->execute($request->user(), $borrowing, $request->enum('return_condition', AssetCondition::class) ?? AssetCondition::Baik, $request->input('return_verification_note'));
        $audit->record($request->user(), 'borrowing.return_verified', $result, $old, $result->getAttributes());
        $notifications->queueReturnVerification($result);

        return new BorrowingResource($result);
    }
}

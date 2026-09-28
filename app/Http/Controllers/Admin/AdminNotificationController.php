<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AdminNotificationController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'role:admin|super_admin']),
        ];
    }

    /**
     * Mengembalikan JSON daftar notifikasi terbaru + hitungan unread_count.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;

        $unreadCount = AdminNotification::unread()
            ->where(function ($q) use ($userId) {
                $q->whereNull('user_id')->orWhere('user_id', $userId);
            })
            ->count();

        $notifications = AdminNotification::query()
            ->where(function ($q) use ($userId) {
                $q->whereNull('user_id')->orWhere('user_id', $userId);
            })
            ->latest('id')
            ->take(20)
            ->get()
            ->map(function (AdminNotification $notification) {
                $borrowingId = $notification->data['borrowing_id'] ?? null;
                $targetUrl = $borrowingId 
                    ? route('admin.borrowings.index', ['search' => $notification->data['asset_code'] ?? ''])
                    : route('admin.borrowings.index');

                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'data' => $notification->data,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at?->toIso8601String(),
                    'time_ago' => $notification->created_at 
                        ? $notification->created_at->locale('id')->diffForHumans() 
                        : 'Baru saja',
                    'created_at_human' => $notification->created_at 
                        ? $notification->created_at->locale('id')->diffForHumans() 
                        : 'Baru saja',
                    'target_url' => $targetUrl,
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mengubah status semua notifikasi menjadi dibaca.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;
        AdminNotification::markAllAsRead($userId);

        return response()->json([
            'success' => true,
            'status' => 'ok',
            'message' => 'Semua notifikasi telah ditandai dibaca.',
        ]);
    }

    /**
     * Menghapus seluruh riwayat notifikasi admin (fungsi hapus semua).
     */
    public function destroyAll(Request $request): JsonResponse
    {
        AdminNotification::deleteAll();

        return response()->json([
            'success' => true,
            'status' => 'ok',
            'message' => 'Seluruh notifikasi berhasil dihapus.',
        ]);
    }
}

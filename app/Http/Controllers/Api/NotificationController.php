<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * 1. GET /api/notifications
     * Mengambil daftar notifikasi milik pengguna yang sedang login (Semua Role)
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = $notifications->where('is_read', false)->count();

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar notifikasi berhasil diambil.',
            'data'    => [
                'unread_count'  => $unreadCount,
                'notifications' => $notifications,
            ],
        ], 200);
    }

    /**
     * 2. PUT /api/notifications/{id}/read
     * Menandai notifikasi sebagai sudah dibaca (Semua Role)
     */
    public function markRead(Request $request, string $id): JsonResponse
    {
        $notification = Notification::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Notifikasi tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        $notification->update([
            'is_read' => true,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Notifikasi ditandai sudah dibaca.',
            'data'    => $notification,
        ], 200);
    }
}
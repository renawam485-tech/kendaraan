<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        try {
            $items = $user->notifications()
                ->latest()
                ->limit(30)
                ->get()
                ->map(fn ($n) => [
                    'id'           => $n->id,
                    'title'        => $n->data['title']        ?? '',
                    'message'      => $n->data['message']      ?? '',
                    'icon'         => $n->data['icon']         ?? 'bell',
                    'color'        => $n->data['color']        ?? 'blue',
                    'booking_id'   => $n->data['booking_id']   ?? null,
                    'booking_code' => $n->data['booking_code'] ?? null,
                    'is_read'      => !is_null($n->read_at),
                    'created_at'   => $n->created_at->diffForHumans(),
                    'created_raw'  => $n->created_at->toDateTimeString(),
                ]);

            return response()->json([
                'success'       => true,
                'notifications' => $items,
                'unread_count'  => $user->unreadNotifications()->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('NotificationController@index error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function markRead(string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notif = $user->notifications()->find($id);

        if (!$notif) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $notif->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAllRead(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    public function destroy(string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notif = $user->notifications()->find($id);

        if (!$notif) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $notif->delete();
        return response()->json(['success' => true]);
    }

    public function clearRead(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->notifications()->whereNotNull('read_at')->delete();
        return response()->json(['success' => true]);
    }

    public function unreadCount(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['count' => 0]);
        }

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     */
    public function index(Request $request)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(DatabaseNotification $notification)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if ($notification->notifiable_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }


    /**
     * Get unread notifications count.
     */
    public function unreadCount()
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $count = auth()->user()->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications for dropdown.
     */
    public function recent(Request $request)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user = auth()->user();

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $html = '';

        foreach ($notifications as $notification) {
            // FIX: $notification->data sudah berupa array, tidak perlu di-decode lagi
            $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);

            $color = $data['color'] ?? 'info';
            $icon = $data['icon'] ?? 'bell';
            $title = $data['title'] ?? 'Notifikasi';
            $message = $data['message'] ?? 'Tidak ada pesan';
            $actionUrl = $data['action_url'] ?? '#';
            $isRead = !is_null($notification->read_at);
            $time = $notification->created_at->diffForHumans();

            $html .= '
                <li class="notification-item-' . $notification->id . '">
                    <a class="dropdown-item d-flex align-items-center py-2 ' . ($isRead ? 'text-muted' : '') . '" 
                       href="javascript:void(0);" 
                       onclick="handleDropdownNotificationClick(\'' . $notification->id . '\', \'' . $actionUrl . '\')">
                        <div class="me-3">
                            <div class="avatar avatar-sm bg-' . $color . '-light">
                                <span class="avatar-initial rounded-circle bg-' . $color . '">
                                    <i class="bx bx-' . $icon . '"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 ' . ($isRead ? 'fw-normal' : 'fw-bold') . '">' . htmlspecialchars($title) . '</h6>
                            <small class="text-muted">' . htmlspecialchars(\Illuminate\Support\Str::limit($message, 50)) . '</small>
                            <div class="text-muted"><small><i class="far fa-clock"></i> ' . $time . '</small></div>
                        </div>
                        ' . (!$isRead ? '<div class="ms-2"><span class="badge bg-' . $color . ' rounded-pill badge-dot"></span></div>' : '') . '
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
            ';
        }

        if ($notifications->isEmpty()) {
            $html = '
                <li class="text-center py-3">
                    <div class="text-muted">
                        <i class="bx bx-bell-slash fa-2x mb-2"></i>
                        <p class="mb-0"><small>Belum ada notifikasi</small></p>
                    </div>
                </li>
            ';
        }

        return response()->json([
            'success' => true,
            'html' => $html,
            'count' => $user->unreadNotifications()->count()
        ]);
    }

    /**
     * Delete notification.
     */
    public function destroy(DatabaseNotification $notification)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if ($notification->notifiable_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();

        return response()->json(['success' => true, 'message' => 'Notifikasi berhasil dihapus']);
    }

    public function clearReadNotifications()
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $deletedCount = auth()->user()->readNotifications()->delete();

        return response()->json([
            'success' => true,
            'message' => $deletedCount . ' notifikasi berhasil dihapus',
            'count' => $deletedCount
        ]);
    }

    public function clearRead()
    {
        try {
            auth()->user()->notifications()->whereNotNull('read_at')->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi yang dibaca berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

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
            $data = json_decode($notification->data, true);
            $color = $data['color'] ?? 'info';
            $icon = $data['icon'] ?? 'bell';
            $title = $data['title'] ?? 'Notifikasi';
            $message = $data['message'] ?? 'Tidak ada pesan';
            $actionUrl = $data['action_url'] ?? '#';
            $isRead = !is_null($notification->read_at);
            $time = $notification->created_at->diffForHumans();
            
            $html .= '
                <li class="notification-item_' . $notification->id . '">
                    <a class="dropdown-item d-flex align-items-center py-2 ' . ($isRead ? 'text-muted' : '') . '" 
                       href="javascript:void(0);" onclick="handleDropdownNotificationClick(' . $notification->id . ', \'' . $actionUrl . '\')">
                        <div class="me-3">
                            <div class="avatar avatar-sm bg-' . $color . '-light">
                                <span class="avatar-initial rounded-circle bg-' . $color . '">
                                    <i class="bx bx-' . $icon . '"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 ' . ($isRead ? 'fw-normal' : 'fw-bold') . '">' . $title . '</h6>
                            <small class="text-muted">' . \Illuminate\Support\Str::limit($message, 50) . '</small>
                            <div class="text-muted"><small><i class="far fa-clock"></i> ' . $time . '</small></div>
                        </div>
                        ' . (!$isRead ? '<div class="ms-2"><span class="badge bg-' . $color . ' rounded-pill">3</span></div>' : '') . '
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
            ';
        }
        
        if ($notifications->isEmpty()) {
            $html = '
                <li class="text-center py-3">
                    <div class="text-muted">
                        <i class="bx bx-bell-slash fa-2x"></i>
                    </div>
                    <small>Belum ada notifikasi</small>
                </li>
            ';
        }
        
        return response()->json(['html' => $html]);
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
        
        return response()->json(['success' => true]);
    }
}

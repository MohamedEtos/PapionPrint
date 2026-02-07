<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifications;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:الفواتير');
    }
    public function getLatest()
    {
        $notifications = Notifications::orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($notify) {
                $notify->time_ago = $notify->created_at->diffForHumans();
                // Ensure image path is correct for JS
                $notify->image_url = $notify->img_path 
                    ? (\Str::startsWith($notify->img_path, 'data:') ? $notify->img_path : asset('storage/' . $notify->img_path))
                    : null;
                return $notify;
            });
            
        $unreadCount = Notifications::where('status', 'unread')
            ->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    public function index()
    {
        $notifications = Notifications::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }
}

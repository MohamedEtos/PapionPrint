<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notifications;

class NotificationComposer
{
    public function compose(View $view)
    {
        $notifications = collect([]);
        $unreadCount = 0;

        if (Auth::check()) {
            $notifications = Notifications::orderBy('created_at', 'desc')
                            ->take(5) // Limit to recent 10, or paginate
                            ->get();
            
            $unreadCount = Notifications::where('status', 'unread')
                            ->count();
        }

        $view->with('notifications', $notifications);
        $view->with('unreadNotificationCount', $unreadCount);
    }
}

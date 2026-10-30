<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AppNotification::orderBy('is_read', 'asc')
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = AppNotification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return Redirect::back()->with('success', 'Notificação marcada como lida!');
    }

    public function markAllAsRead()
    {
        AppNotification::where('is_read', false)->update(['is_read' => true]);

        return Redirect::back()->with('success', 'Todas as notificações foram marcadas como lidas!');
    }
}

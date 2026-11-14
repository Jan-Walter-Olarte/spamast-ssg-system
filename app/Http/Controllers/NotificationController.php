<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    // The logged-in user's own notifications, newest first.
    public function index(Request $request)
    {
        $user = $request->user();

        return view('notifications.index', [
            'notifications' => $user->notifications()->paginate(15),
            'unreadCount'   => $user->unreadNotifications()->count(),
        ]);
    }

    // Mark one notification as read, then open the page it points to.
    public function read(Request $request, string $id): RedirectResponse
    {
        // Looked up inside the user's OWN notifications, so nobody can open someone else's.
        $notification = $request->user()->notifications()->findOrFail($id);

        $notification->markAsRead();

        $url = data_get($notification->data, 'url');

        // Only follow links that stay inside the system ("/..." but not "//...").
        if (is_string($url) && Str::startsWith($url, '/') && ! Str::startsWith($url, '//')) {
            return redirect($url);
        }

        return redirect()->route('notifications.index');
    }

    // Mark all of the user's notifications as read.
    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}

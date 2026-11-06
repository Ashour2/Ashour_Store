<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->adminNotifications()->latest()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function open(Request $request, AdminNotification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);
        $notification->update(['read_at' => now()]);
        return redirect($notification->url ?: route('admin.notifications.index'));
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->adminNotifications()->whereNull('read_at')->update(['read_at' => now()]);
        return back()->with('msg', 'تم تعليم جميع الإشعارات كمقروءة.')->with('type', 'success');
    }
}
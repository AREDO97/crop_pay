<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    //all notifications and count
    public function index(Request $request)
{
    $user = $request->user();

    return response()->json([
        'unread_count'  => $user->unreadNotifications()->count(), 
        'notifications' => $user->notifications()->latest()->get(),
    ]);
}

//mark all as read
public function markAllAsRead(Request $request)
{
    // current user
    $user = $request->user();
    $user->unreadNotifications->markAsRead();

    return response()->json([
        'message' => 'All notifications marked as read',
        'unread_count' => 0
    ]);
}
// delete notification
public function destroy(Request $request, string $id)
{
    // find user notifications
    $notification = $request->user()->notifications()->findOrFail($id);

    $notification->delete();

    return response()->json([
        'message' => 'Notification deleted successfully',
        'unread_count' => $request->user()->unreadNotifications()->count()
    ]);
}
}

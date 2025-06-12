<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class NotificationController extends BaseController
{


    public function index(Request $request)
    {
        $query = Notification::query();


        // Paginate results, 10 per page
        $notificationList = $query->orderBy('created_at', 'desc')->paginate(10);

        // Return a specific view when it's an AJAX request
        if ($request->ajax()) {
            return view('admin.notification.index', compact('notificationList'));
        }

        return view('admin.notification.index', compact('notificationList'));
    }



    public function markAllRead()
    {
        Notification::query()->update(['is_read_by_admin' => 1]);
        return response()->json(['success' => true]);
    }

    public function markRead(Request $request)
    {
        $notification = Notification::find($request->id);
        if ($notification) {
            $notification->update(['is_read_by_admin' => 1]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
}

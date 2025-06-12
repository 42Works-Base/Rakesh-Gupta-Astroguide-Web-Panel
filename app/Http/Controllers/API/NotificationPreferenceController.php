<?php

namespace App\Http\Controllers\API;

use App\Models\NotificationPreference;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class NotificationPreferenceController extends BaseController
{
    public function getPreferences($user_id)
    {
        $user = User::where('id', $user_id)->where('role', 'user')->first();

        if (!$user) {
            return $this->sendError('User not found.');
        }

        $preferences = $user->notificationPreferences;

        if (is_null($preferences)) {
            $preferences = $user->notificationPreferences()->create([
                'email_notifications' => 0,
                'sms_notifications' => 0,
                'push_notifications' => 1, // or default values
            ]);
        }

        $preferences = $preferences->makeHidden(['email_notifications', 'sms_notifications']);



        return $this->sendSuccessResponse($preferences, 'Notifications preferences.');
    }

    public function updatePreferences(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                // 'email_notifications' => 'required|boolean',
                // 'sms_notifications' => 'required|boolean',
                'user_id' => 'required|exists:users,id',
                'push_notifications' => 'required|boolean',
            ],
            []
        );

        if ($validator->fails()) {

            return $this->sendError($validator->errors()->first());
        }

        $user = User::where('id', $request->user_id)->where('role', 'user')->first();

        if (!$user) {
            return $this->sendError('User not found.');
        }

        // $preferences = $request->user()->notificationPreferences;

        // $email_notifications = $request->email_notifications;
        // $sms_notifications = $request->sms_notifications;
        // $push_notifications = $request->push_notifications;



        $email_notifications = false;
        $sms_notifications = false;
        $push_notifications = $request->push_notifications;

        NotificationPreference::updateOrCreate(
            [
                'user_id' => $user->id,

            ],
            [
                'email_notifications' => $email_notifications,
                'sms_notifications' => $sms_notifications,
                'push_notifications' => $push_notifications,
            ]
        );

        $status = $request->push_notifications == 1 ? 'enabled' : 'disabled';
        return $this->sendSuccessResponse('', "Push notifications have been $status successfully.");
    }

    public function userNotificationList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $user_id = $request->user_id;
        $user = User::where('id', $user_id)->where('role', 'user')->first();

        if (!$user) {
            return $this->sendError('User not found.');
        }

        $notificationList = $user->userNotification()
            ->orderBy('created_at', 'desc')
            ->get(['id','title', 'message', 'type', 'data', 'is_read', 'receiver_id', 'created_at']);



        $unreadCount = $notificationList->where('is_read', 0)->count();
        $data = [
                'notifications' => $notificationList,
                'unread_count' => $unreadCount,
        ];

        return $this->sendSuccessResponse($data, 'Notifications list.');
    }

    public function userNotificationMarkAsRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:notifications,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $notification_id = $request->id;
        $notification = Notification::where('id', $notification_id)->first();

        $notification->update([
            'is_read' => '1',
        ]);



        return $this->sendSuccessResponse('', 'Notifications status updated.');
    }

    public function userNotificationMarkAllAsRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $user_id = $request->user_id;
        $user = User::where('id', $user_id)->where('role', 'user')->first();

        if (!$user) {
            return $this->sendError('User not found.');
        }
        Notification::where('receiver_id', $user_id)->update([
            'is_read' => '1',
        ]);

        return $this->sendSuccessResponse('', 'Notifications status updated.');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Helpers\NotificationHelper;

class BaseController extends Controller
{
    protected function sendSuccessResponse($data, $message = 'Success', $status = true)
    {
        return response()->json([
            'data' => $data,
            'status' => $status,
            'message' => $message,
        ]);
    }

    protected function sendError($message)
    {
        return response()->json([
            'data' => [],
            'status' => false,
            'message' => $message,
        ]);
    }

    protected function storeOrUpdateDevice($user, $request)
    {
        $identifier = $request->input('device_identifier') ? $request->input('device_identifier') : null;
        $type = strtolower($request->input('device_type'));
        $device_id  = $request->input('device_id');
        $fcm_token  = $request->input('fcm_token') ? $request->input('fcm_token') : null;

        return UserDevice::updateOrCreate(
            [
                'user_id' => $user->id,
                'type' => strtolower($request->device_type)
            ],
            [
                'type' => $request->device_type,
                'device_id' => $request->device_id,
                'fcm_token' => $request->fcm_token
            ]
        );
    }

    protected function formatUserResponse($user)
    {
        // return [
        //     'id' => $user->id,
        //     'first_name' => $user->first_name,
        //     'last_name' => $user->last_name,
        //     'email' => $user->email,
        //     'phone' => $user->phone,
        //     'country_code' => $user->country_code,
        //     'gender' => $user->gender,
        //     'dob' => $user->dob,
        //     'dob_time' => $user->dob_time,
        //     'birth_city' => $user->birth_city,
        //     'birthplace_country' => $user->birthplace_country,
        //     'full_address' => $user->full_address,
        //     'latitude' => $user->latitude,
        //     'longitude' => $user->longitude,
        //     'timezone' => $user->timezone,
        //     'is_verified' => $user->is_verified,
        //     'status' => $user->status,
        //     'profile_picture' => $user->profile_picture ? url($user->profile_picture) : null,
        //     'created_at' => $user->created_at,
        //     'updated_at' => $user->updated_at,
        //     'token' => $user->token,

        //     // Exclude email_notifications and include device details
        //     // 'notification_preferences' => $user->notificationPreferences ? [
        //     //     'sms_notifications' => $user->notificationPreferences->sms_notifications,
        //     //     'push_notifications' => $user->notificationPreferences->push_notifications
        //     // ] : null,

        //     'device_details' => $user->latestDevice ? [
        //         'fcm_token' => $user->latestDevice->fcm_token,
        //         'device_id' => $user->latestDevice->device_id,
        //         'type' => $user->latestDevice->type

        //     ] : null,

        // ];

        $response = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'country_code' => $user->country_code,
            'gender' => $user->gender,
            'dob' => $user->dob,
            'dob_time' => $user->dob_time,
            'birth_city' => $user->birth_city,
            'birthplace_country' => $user->birthplace_country,
            'full_address' => $user->full_address,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'timezone' => $user->timezone,
            'is_verified' => $user->is_verified,
            'status' => $user->status,
            'profile_picture' => $user->profile_picture ? url($user->profile_picture) : null,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'device_details' => $user->latestDevice ? [
                'fcm_token' => $user->latestDevice->fcm_token,
                'device_id' => $user->latestDevice->device_id,
                'type' => $user->latestDevice->type
            ] : null,
        ];

        // Add token only if exists
        if (isset($user->token)) {
            $response['token'] = $user->token;
        }

        return $response;
    }



    protected function sendOtpPhone($user, $otp)
    {
        Mail::raw("Your OTP is: $otp", function ($message) use ($user) {
            $message->to($user->email)->subject('Verify Your Account');
        });
    }
}

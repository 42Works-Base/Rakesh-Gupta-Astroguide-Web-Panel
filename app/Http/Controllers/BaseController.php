<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\UserDevice;

class BaseController extends Controller
{

    function sendPushNotification($deviceToken, $title, $body)
    {
        $url = "https://fcm.googleapis.com/fcm/send";

        $data = [
            'to' => $deviceToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default'
            ],
            'data' => [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]
        ];

        $headers = [
            'Authorization' => 'key=' . env('FIREBASE_SERVER_KEY'),
            'Content-Type' => 'application/json',
        ];

        $response = Http::withHeaders($headers)->post($url, $data);
        return $response->json();
    }

    // Example Usage
    // sendPushNotification('user-device-token', 'New Alert', 'You have a new message!');

}

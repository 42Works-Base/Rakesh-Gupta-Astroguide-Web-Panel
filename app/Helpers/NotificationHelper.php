<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class NotificationHelper
{


    /*  public static function notifyUser($senderId, $receiverId, $title,  $message, $title_admin = null, $message_to_admin = null, $type = null, $data = [])
    {
        // Save in DB
        Notification::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'title' => $title,
            'message' => $message,
            'title_admin' => $title_admin,
            'message_to_admin' => $message_to_admin,
            'type' => $type,
            'data' => $data,
        ]);

        // Get user FCM token (assuming stored in DB)
        $user = User::find($receiverId);
        $notificationPreference = $user->notificationPreferences;
        $fcm_token = $user->latestDevice->fcm_token;
        // $fcm_token = 'elrGaANbtEwcqEVr8moIsj:APA91bFavF70519abErP37LR3mhRXtJvTnmrv-gi-eDPmpoqKsI7TqezMekBjuQad7s0IDU8MPxVvCqzCNNVHbrSuoF4_8rZ3tJ_aqS-rttCfLursIdIV_M';
        if ($user && $fcm_token &&
            (
                !$notificationPreference ||
                !isset($notificationPreference['push_notifications']) ||
                $notificationPreference['push_notifications'] == '1'
            )
        ) {
            $response = self::sendFCMNotificationV1($fcm_token, $title, $message, $data);
            return $response;
        }
    } */

    public static function notifyUser($senderId, $receiverId, $title, $message, $title_admin = null, $message_to_admin = null, $type = null, $data = [])
    {
        // Log::info('notifyUser called', [
        //     'sender_id' => $senderId,
        //     'receiver_id' => $receiverId,
        //     'title' => $title,
        //     'message' => $message,
        // ]);

        // Save in DB
        $notification = Notification::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'title' => $title,
            'message' => $message,
            'title_admin' => $title_admin,
            'message_to_admin' => $message_to_admin,
            'type' => $type,
            'data' => $data,
        ]);

       // Log::info('Notification created', ['id' => $notification->id]);

        $user = User::find($receiverId);

        if (!$user) {
            Log::warning('Receiver user not found', ['receiver_id' => $receiverId]);
            return;
        }

        $notificationPreference = $user->notificationPreferences;
        $fcm_token = optional($user->latestDevice)->fcm_token;

        if (
            $fcm_token &&
            (
                !$notificationPreference ||
                !isset($notificationPreference['push_notifications']) ||
                $notificationPreference['push_notifications'] == '1'
            )
        ) {
            // Log::info('Sending FCM notification', [
            //     'token' => $fcm_token,
            //     'title' => $title,
            //     'message' => $message,
            // ]);

            $response = self::sendFCMNotificationV1($fcm_token, $title, $message, $data);

           // Log::info('FCM response', $response);

            return $response;
        } else {
            Log::info('Notification not sent due to missing token or user preference', [
                'fcm_token' => $fcm_token,
                'notificationPreference' => $notificationPreference,
            ]);
        }
    }


    //woking perfect for ios but not android
    /* public static function sendFCMNotificationV1($token, $title, $body, $data)
    {
        // dd($token, $title, $body, $data);
        $credsPath = config('services.fcm.credentials_path');
        $projectId = config('services.fcm.project_id');

        $creds = json_decode(file_get_contents($credsPath), true);
        $now = time();
        $jwtHeader = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];

        $jwtClaim = [
            'iss' => $creds['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $jwtHeaderEncoded = rtrim(strtr(base64_encode(json_encode($jwtHeader)), '+/', '-_'), '=');
        $jwtClaimEncoded = rtrim(strtr(base64_encode(json_encode($jwtClaim)), '+/', '-_'), '=');
        $dataToSign = "$jwtHeaderEncoded.$jwtClaimEncoded";

        openssl_sign($dataToSign, $signature, $creds['private_key'], 'sha256');
        $signatureEncoded = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
        $jwt = "$dataToSign.$signatureEncoded";

        // Get access token
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        $accessToken = $response->json()['access_token'];

        // Send push
        $fcmUrl = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";


        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
            ]
        ];

        // Only include 'data' if it's a non-empty associative array
        if (!empty($data) && is_array($data) && array_keys($data) !== range(0, count($data) - 1)) {
            $payload['message']['data'] = $data;
        }


        return Http::withToken($accessToken)
            ->post($fcmUrl, $payload)
            ->json();
    } */

    public static function sendFCMNotificationV1($token, $title, $body, $data)
    {
        $credsPath = config('services.fcm.credentials_path');
        $projectId = config('services.fcm.project_id');

        $creds = json_decode(file_get_contents($credsPath), true);
        $now = time();

        $jwtHeader = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];

        $jwtClaim = [
            'iss' => $creds['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $jwtHeaderEncoded = rtrim(strtr(base64_encode(json_encode($jwtHeader)), '+/', '-_'), '=');
        $jwtClaimEncoded = rtrim(strtr(base64_encode(json_encode($jwtClaim)), '+/', '-_'), '=');
        $dataToSign = "$jwtHeaderEncoded.$jwtClaimEncoded";

        openssl_sign($dataToSign, $signature, $creds['private_key'], 'sha256');
        $signatureEncoded = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
        $jwt = "$dataToSign.$signatureEncoded";

        // Get access token
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        $accessToken = $response->json()['access_token'];

        // Prepare FCM payload
        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'android' => [
                    'notification' => [
                        'sound' => 'default',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'icon' => 'ic_notification'
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ],
                    ],
                ],
            ]
        ];

        // Sanitize and cast data to string
        if (!empty($data) && is_array($data) && array_keys($data) !== range(0, count($data) - 1)) {
            $data = array_map(function ($item) {
                return is_array($item) ? json_encode($item) : (string) $item;
            }, $data);

            $payload['message']['data'] = $data;
        }

        // Optional: log payload for debugging
        // \Log::info('FCM Payload', ['payload' => $payload]);

        $fcmUrl = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        return Http::withToken($accessToken)
            ->post($fcmUrl, $payload)
            ->json();
    }
}

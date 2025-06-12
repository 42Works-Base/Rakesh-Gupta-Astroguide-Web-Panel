<?php

namespace App\Helpers;

class JwtHelper
{
    public static function generateToken($payload, $secret = null)
    {
        $header = ['typ' => 'JWT', 'alg' => 'HS256'];
        $secret = $secret ?: env('JWT_SECRET', 'my_secret_key');

        $header_encoded = base64_encode(json_encode($header));
        $payload_encoded = base64_encode(json_encode($payload));

        $signature = hash_hmac('sha256', "$header_encoded.$payload_encoded", $secret, true);
        $signature_encoded = base64_encode($signature);

        return "$header_encoded.$payload_encoded.$signature_encoded";
    }

    public static function validateToken($token, $secret = null)
    {
        $secret = $secret ?: env('JWT_SECRET', 'my_secret_key');

        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;

        $valid_signature = base64_encode(
            hash_hmac('sha256', "$header.$payload", $secret, true)
        );

        if (!hash_equals($valid_signature, $signature)) {
            return null; // Signature mismatch
        }

        $payload_array = json_decode(base64_decode($payload), true);

        // Check for expiry
        if (isset($payload_array['exp']) && time() > $payload_array['exp']) {
            return null; // Token is expired
        }

        return $payload_array;
    }
}

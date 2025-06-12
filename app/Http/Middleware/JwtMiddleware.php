<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Helpers\JwtHelper;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(env('APP_ENV') == 'local'){
            return $next($request);
        }
        $token = $request->bearerToken() ?? $request->header('token');

        if (!$token) {
            return $this->sendError('Token not provided.');
        }

        $payload = JwtHelper::validateToken($token);


        if (!$payload || !isset($payload['user_id'])) {
            return $this->sendError('Invalid or expired token.');
        }

        $user = User::find($payload['user_id']);

        if (!$user || $user->status !== 'active') {
            return $this->sendError('Account inactive.');
        }

        // auth()->login($user);

        return $next($request);
    }

    protected function sendError($message)
    {
        return response()->json([
            'data' => [],
            'status' => false,
            'message' => $message,
        ]);
    }
}

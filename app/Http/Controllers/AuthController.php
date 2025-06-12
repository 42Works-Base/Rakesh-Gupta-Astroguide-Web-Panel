<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;


use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        // Get email, password, and remember inputs
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember'); // Check if 'remember' is checked

        // Attempt login with remember functionality
        if (Auth::attempt($credentials, $remember)) {
            // Check if user has a specific role
            if (Auth::user()->role == 'astrologer') {

                // Store email and password in cookies for 2 days if "remember" is checked
                    if ($remember) {
                        Cookie::queue('remembered_email', $request->email, 2880); // 2 days = 2880 minutes
                        Cookie::queue('remembered_password', $request->password, 2880); // Not secure, just for demo
                    } else {
                        Cookie::queue(Cookie::forget('remembered_email'));
                        Cookie::queue(Cookie::forget('remembered_password'));
                    }

                return redirect()->intended('/dashboard'); // Change to your desired page
            }
            return back()->withErrors(['error' => 'You are not authorized to access this page.']);
        }

        return back()->withErrors([
            'error' => 'Invalid credentials. Please check your email and password.',
        ]);
    }


    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // public function sendResetLink(Request $request)
    // {
    //     $request->validate(['email' => 'required|email']);

    //     // Check if email exists in users table
    //     $user = User::where('email', $request->email)->first();
    //     if (!$user) {
    //         return back()->withErrors(['email' => 'This email address is not registered in our system.']);
    //     }

    //     $status = Password::sendResetLink(
    //         $request->only('email')
    //     );

    //     return $status === Password::RESET_LINK_SENT
    //         ? back()->with(['success' => __($status)])
    //         : back()->withErrors(['email' => __($status)]);
    // }

    public function sendResetLink(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        // Check if email exists in users table
        $user = User::where('email', $request->email)->where('role', 'astrologer')->first();
        if (!$user) {
            return back()->withErrors(['error' => 'This email address is not registered.']);
        }

        // Generate plain token
        $token = Str::random(64);

        // Store token in database (without hashing)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        // Send reset link via email (if required)
        Mail::to($request->email)->send(new ResetPasswordMail($token));

        return back()->with(['success' => 'Password reset link sent successfully.']);
    }

    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }


    // public function resetPassword(Request $request)
    // {
    //     $request->validate([
    //         'token' => 'required',
    //         'email' => 'required|email',
    //         'password' => 'required|confirmed|min:6',
    //     ]);

    //     $status = Password::reset(
    //         $request->only('email', 'password', 'password_confirmation', 'token'),
    //         function (User $user, string $password) {
    //             $user->forceFill([
    //                 'password' => Hash::make($password),
    //                 'remember_token' => Str::random(60),
    //             ])->save();
    //         }
    //     );

    //     return $status === Password::PASSWORD_RESET
    //         ? redirect()->route('login')->with('success', __($status))
    //         : back()->withErrors(['email' => [__($status)]]);
    // }

    // public function resetPassword(Request $request)
    // {

    //     $request->validate([]);

    //     $validator = Validator::make($request->all(), [
    //         'token' => 'required',
    //         'password' => 'required|confirmed|min:6',
    //     ]);

    //     if ($validator->fails()) {
    //         return back()->withErrors(['error' => $validator->errors()->first()]);
    //     }

    //     // Find the reset record based on token
    //     $reset = DB::table('password_reset_tokens')->where('token', $request->token)->first();

    //     if (!$reset) {
    //         return back()->withErrors(['error' => 'Invalid or expired token.']);
    //     }

    //     // Find the user by email stored in password_resets
    //     $user = User::where('email', $reset->email)->first();

    //     if (!$user) {
    //         return back()->withErrors(['error' => 'User not found.']);
    //     }

    //     // Reset password
    //     $user->forceFill([
    //         'password' => Hash::make($request->password),
    //         'remember_token' => Str::random(60),
    //     ])->save();

    //     // Delete used reset token
    //     DB::table('password_reset_tokens')->where('email', $reset->email)->delete();

    //     return redirect()->route('login')->with('success', 'Password reset successfully.');
    // }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'token' => 'required',
                'password' => 'required|confirmed|min:6',
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }


        // Find reset request by token only
        $resetRecord = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['error' => 'Invalid or expired reset token.']);
        }

        // Get user by email (retrieved from reset record)
        $user = User::where('email', $resetRecord->email)->first();
        if (!$user) {
            return back()->withErrors(['error' => 'User not found.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete token after reset
        DB::table('password_reset_tokens')->where('token', $request->token)->delete();

        return redirect()->route('login')->with('success', 'Password reset successfully.');
    }



    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}

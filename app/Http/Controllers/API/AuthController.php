<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Models\User;
use App\Models\KundaliDetail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyOTPRequest;
use App\Http\Requests\ResendOTPRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\CreateNewPasswordRequest;
use App\Http\Requests\EditProfileRequest;
use App\Helpers\JwtHelper;
use App\Helpers\NotificationHelper;

use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordOtpMail;
use App\Mail\OtpMail;


class AuthController extends BaseController
{
    public function register(RegisterRequest $request)
    {

        // Handle profile picture upload
        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $profilePicturePath = $file->storeAs('profile_pictures', $filename, 'public');
        }

        $otp = rand(100000, 999999);
        // $otp = 123456;
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'country_code' => $request->country_code,
            'dob' => $request->dob,
            'dob_time' => $request->dob_time,
            'gender' => $request->gender,
            'birth_city' => $request->birth_city,
            'birthplace_country' => $request->birthplace_country,
            'full_address' => $request->full_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'timezone' => $request->timezone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp' => $otp,
            'role' => 'user',
            'profile_picture' => $profilePicturePath ? Storage::url($profilePicturePath) : null, // Store URL
        ]);

        //Send OTP via email
        Mail::to($user->email)->send(new OtpMail($user, $otp));


        // Store device details
        $deviceData = $this->storeOrUpdateDevice($user, $request);

        $payload = [
            'user_id' => $user->id,
            'email' => $user->email,
            // 'exp' => time() + 3600, // expires in 1 hour
            'exp' => time() + (24 * 60 * 60), // 24 hours from now
        ];

        $token = JwtHelper::generateToken($payload);
        $user['token'] = $token;

        // notification start

        $senderId = null;
        $receiverId = $user->id;
        $title = 'Onboarding';
        $message = 'OTP sent to email. Verify to activate account';

        $title_admin =  'New user onboarding today: ' . $user['first_name'] . ' ' . $user['last_name'] . ' has joined!';
        $message_to_admin = $user['first_name'] . ' ' . $user['last_name'] . ' has successfully onboarded today!';

        $type = "new_user_register";

        $firebaseResponse = NotificationHelper::notifyUser($senderId, $receiverId,  $title,  $message, $title_admin, $message_to_admin, $type);
        // notification end

        return $this->sendSuccessResponse($this->formatUserResponse($user), 'OTP sent to email. Verify to activate account.');
    }

    public function verifyOtp(VerifyOTPRequest $request)
    {

        $user = User::where('email', $request->email)->where('otp', $request->otp)->first();
        // $user = User::where('phone', $request->phone)->where('otp', $request->otp)->first();
        if (!$user) {
            return $this->sendError('Invalid OTP');
        }
        $user->is_verified = true;
        $user->otp = null;
        $user->save();

        $payload = [
            'user_id' => $user->id,
            'email' => $user->email,
            // 'exp' => time() + 3600, // expires in 1 hour
            'exp' => time() + (24 * 60 * 60), // 24 hours from now
        ];

        $token = JwtHelper::generateToken($payload);
        $user['token'] = $token;

        return $this->sendSuccessResponse($this->formatUserResponse($user), 'OTP Verified');
    }

    public function login(Request $request)
    {

        $user = User::where('email', $request->email)->first();
        // $user = User::where('phone', $request->phone)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {

            return $this->sendError('Invalid credentials');
        }
        if (!$user->is_verified) {
            return $this->sendError('User not verified. Please verify the user.');
        }

        if ($user->status !== 'active') {
            return $this->sendError('Account inactive.');
        }

        // Store device details
        $deviceData = $this->storeOrUpdateDevice($user, $request);

        $payload = [
            'user_id' => $user->id,
            'email' => $user->email,
            // 'exp' => time() + 3600, // expires in 1 hour
            'exp' => time() + (24 * 60 * 60), // 24 hours from now
        ];

        $token = JwtHelper::generateToken($payload);
        $responseMessage = 'Login successful';
        $user['token'] = $token;
        return $this->sendSuccessResponse($this->formatUserResponse($user), $responseMessage);
    }

    public function resendOtp(ResendOTPRequest $request)
    {


        $user = User::where('email', $request->email)->first();
        // $user = User::where('phone', $request->phone)->first();

        $otp = rand(100000, 999999);
        // $otp = 123456;

        $user->is_verified = false;
        $user->otp = $otp;
        $user->save();


        // Send OTP via email
        Mail::to($user->email)->send(new OtpMail($user, $otp));

        return $this->sendSuccessResponse($this->formatUserResponse($user), 'OTP sent to email. Verify to activate account.');
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {


        // $user = User::where('phone', $request->phone)->first();
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->sendError('User not found.');
        }
        $otp = rand(100000, 999999);
        // $otp = 123456;

        // $user->is_verified = false;
        $user->otp = $otp;
        $user->save();

        // // Send OTP via email
        Mail::to($user->email)->send(new ForgotPasswordOtpMail($user, $otp));



        return $this->sendSuccessResponse($this->formatUserResponse($user), 'OTP sent to email. Verify to change password.');
    }

    public function createNewPassword(CreateNewPasswordRequest $request)
    {

        // $user = User::where('phone', $request->phone)->first();
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->sendError('User not found.');
        }
        // $otp = rand(100000, 999999);

        $user->password = Hash::make($request->password);
        $user->is_verified = true;
        $user->otp = null;
        $user->save();


        return $this->sendSuccessResponse($this->formatUserResponse($user), 'New password created succesfully.');
    }

    public function editProfile(EditProfileRequest $request)
    {
        $user = User::where('id', $request['user_id'])->first();

        if (!$user) {
            return $this->sendError('User not found.');
        }

        $kundaliDetail = KundaliDetail::where('user_id', $user['id'])->first();

        if ($kundaliDetail) {
            $kundaliDetail->delete();
        }


        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete the old profile picture if it exists
            if ($user->profile_picture) {
                $oldPicturePath = str_replace(Storage::url(''), '', $user->profile_picture);
                Storage::disk('public')->delete($oldPicturePath);
            }

            // Store new profile picture
            $file = $request->file('profile_picture');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $profilePicturePath = $file->storeAs('profile_pictures', $filename, 'public');

            // Update the profile picture URL
            $user->profile_picture = Storage::url($profilePicturePath);
        }

        // Update allowed fields (excluding email & phone)
        $user->fill($request->except(['email', 'phone', 'profile_picture']));

        // Save the updated user data
        $user->save();

        return $this->sendSuccessResponse($this->formatUserResponse($user), 'Profile updated successfully.');
    }


    public function getProfile($user_Id)
    {
        $user = User::where('id', $user_Id)->first();

        if (!$user) {
            return $this->sendError('User not found.');
        }

        $payload = [
            'user_id' => $user->id,
            'email' => $user->email,
            // 'exp' => time() + 3600, // expires in 1 hour
            'exp' => time() + (24 * 60 * 60), // 24 hours from now
        ];

        $token = JwtHelper::generateToken($payload);
        $user['token'] = $token;

        return $this->sendSuccessResponse($this->formatUserResponse($user), 'Profile details.');
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|exists:users,id',
                'old_password' => 'required',
                'password' => 'required|min:6|confirmed',
            ],
            [
                'user_id.required' => 'ID is required.',
                'old_password.required' => 'Old password is required.',
                'password.required' => 'New password is required.',
                'password.min' => 'Password must be at least 6 characters long.',
                'password.confirmed' => 'Password confirmation does not match.',
            ]
        );

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $user = User::where('id', $request->user_id)->where('role', 'user')->first();

        if (!$user) {
            return $this->sendError('User not found.');
        }

        if (!Hash::check($request->old_password, $user->password)) {
            return $this->sendError('Old password is incorrect.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return $this->sendSuccessResponse($this->formatUserResponse($user), 'Password changed successfully.');
    }


    public function updateFcmToken(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|exists:users,id',
                'fcm_token' => 'required',
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

        $deviceData = $this->storeOrUpdateDevice($user, $request);

        return $this->sendSuccessResponse($this->formatUserResponse($user), 'FCM token updated succesfully.');
    }

    public function deleteAccount(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|exists:users,id',
            ]
        );

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $user = User::where('id', $request->user_id)
            ->where('role', 'user')
            ->first();

        if (!$user) {
            return $this->sendError('User not found.');
        }

        $user->delete(); // Soft delete

        return $this->sendSuccessResponse('', 'Account deleted successfully.');
    }
}

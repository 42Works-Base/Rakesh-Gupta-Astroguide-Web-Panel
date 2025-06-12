<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyOTPRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to use this request
    }

    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email',
            // 'phone' => 'required|exists:users,phone',
            'otp' => 'required|digits:6',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email address.',
            'email.exists' => 'This email is not registered.',
            // 'phone.required' => 'Phone is required.',
            // 'phone.exists' => 'This phone is not registered.',
            'otp.required' => 'OTP is required.',
            'otp.digits' => 'OTP must be a 6-digit code.',
        ];
    }

    // **Custom Validation Response**
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'data' => [],
            'status' => false,
            'message' => $validator->errors()->first(),
        ]));
    }
}

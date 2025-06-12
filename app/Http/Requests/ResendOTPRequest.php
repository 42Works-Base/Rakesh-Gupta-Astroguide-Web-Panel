<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResendOTPRequest extends FormRequest
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

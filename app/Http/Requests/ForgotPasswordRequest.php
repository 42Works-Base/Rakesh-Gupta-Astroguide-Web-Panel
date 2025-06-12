<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to use this request
    }

    public function rules()
    {
        return [
            // 'phone' => 'required|exists:users,phone',
            'email' => 'required|email|exists:users,email',
        ];
    }

    public function messages()
    {
        return [
            // 'phone.required' => 'Phone is required.',
            // 'phone.exists' => 'This phone is not registered.',

            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email address.',
            'email.exists' => 'This email is not registered.',
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

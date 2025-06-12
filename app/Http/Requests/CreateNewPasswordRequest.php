<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateNewPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'phone' => 'required|exists:users,phone',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
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

            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
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

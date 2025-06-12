<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;


class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to use this request
    }

    public function rules()
    {
        return [
            'first_name' => [
                'required',
                'string',
                'min:2',     // minimum 2 characters
                'max:20',    // maximum 50 characters
                'regex:/^[a-zA-Z\s\-]+$/'
            ],
            // 'phone' => 'required|unique:users,phone',
            // 'country_code' => 'required',

            'phone' => [
                'required',
                'digits_between:7,15', // Ensures valid phone number length
                'regex:/^\d+$/', // Allows only digits (no spaces or symbols)
                Rule::unique('users', 'phone'),
            ],
            'country_code' => [
                'required',
                'regex:/^\+\d{1,4}$/', // Must start with + and contain up to 4 digits
            ],

            'dob' => 'required|date_format:Y-m-d|before:today',
            'dob_time' => 'required|date_format:H:i:s',
            'gender' => 'required|in:male,female,other',
            'birthplace_country' => 'required|string',
            'birth_city' => 'required|string',
            // 'email' => 'required|email|unique:users,email',
            'email' => [
                'required',
                'email:rfc,dns', // Ensures proper email format and domain validation
                Rule::unique('users', 'email'),
            ],
            'password' => 'required|min:6|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'latitude' => 'required',
            'longitude' => 'required',
            'timezone' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'First name is required.',
            'first_name.min' => 'First name must be at least 2 characters.',
            'first_name.max' => 'First name may not be greater than 20 characters.',
            'first_name.regex' => 'The first name may only contain letters, spaces, or hyphens.',
            // 'phone.required' => 'Phone number is required.',
            // 'phone.unique' => 'This phone number is already registered.',
            // 'country_code.required' => 'Country code is required.',

            'phone.required' => 'Phone number is required.',
            'phone.digits_between' => 'Phone number must be between 7 to 15 digits.',
            'phone.regex' => 'Phone number must contain only digits.',
            'phone.unique' => 'This phone number is already registered.',

            'country_code.required' => 'Country code is required.',
            'country_code.regex' => 'Country code must start with + followed by 1 to 4 digits.',

            'dob.required' => 'The date of birth field is required.',
            'dob.date_format' => 'The date of birth must be in YYYY-MM-DD format.',
            'dob.before' => 'The date of birth must be a valid past date.',
            'dob_time.required' => 'Time of birth is required.',
            'dob_time.date_format' => 'The time of birth must be in HH:MM:SS format (24-hour).',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Gender must be male, female, or other.',
            'birthplace_country.required' => 'Birthplace country is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'latitude.required' => 'Latitude is required.',
            'longitude.required' => 'Longitude is required.',
            'timezone.required' => 'Timezone is required.',
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

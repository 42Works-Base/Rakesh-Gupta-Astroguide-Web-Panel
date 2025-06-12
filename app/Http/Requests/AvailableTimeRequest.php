<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;

class AvailableTimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => ['required', 'date', 'after_or_equal:today'], // Prevents past dates
            'price' => 'required|numeric|gt:0',
            'day_start_time' => 'required|date_format:H:i',
            'day_end_time'   => 'required|date_format:H:i|after:day_start_time',
            // 'start_time' => [
            //     'required',
            //     'date_format:H:i',
            //     function ($attribute, $value, $fail) {
            //         $endTime = Carbon::parse($value)->addMinutes(30)->format('H:i');
            //         request()->merge(['end_time' => $endTime]); // Auto set end_time
            //     },
            // ],
            // 'end_time' => 'required|date_format:H:i',
        ];
    }

    public function messages()
    {
        return [
            'date.required' => 'Please select a date.',
            'date.date' => 'The selected date is not valid.',
            'date.after_or_equal' => 'You cannot select a past date. Please choose today or a future date.',

            'price.required' => 'Please enter a price.',
            'price.numeric' => 'The price must be a valid number.',
            'price.gt' => 'The price must be greater than 0.',

            'day_start_time.required' => 'Please enter the start time of the day.',
            'day_start_time.date_format' => 'The start time must be in HH:MM format (e.g., 14:30 for 2:30 PM).',

            'day_end_time.required' => 'Please enter the end time of the day.',
            'day_end_time.date_format' => 'The end time must be in HH:MM format (e.g., 18:00 for 6:00 PM).',
            'day_end_time.after' => 'The end time must be later than the start time.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {

        $errorMessage = $validator->errors()->first();



        // For web requests, redirect back with an error message
        throw new HttpResponseException(
            redirect()->route('slot-management.create')->withInput()->with('error', $errorMessage)
        );
    }
}

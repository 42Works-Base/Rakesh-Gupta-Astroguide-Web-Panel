<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\AvailableTime;
use App\Models\ScheduleCall;
use Illuminate\Support\Facades\Config;

class CallHelper
{
    /**
     * Generate WhatsApp link with user's phone number
     */
    public static function getWhatsAppLink($call)
    {
        if (!$call || !$call->whatsapp_link) {
            return null; // No link available
        }

        $countryCode = $call->user->country_code ?? '+91'; // Default country code
        $userPhoneNumber = $call->user->phone ?? '0000000000'; // Default phone number
        $fullPhoneNumber = $countryCode . $userPhoneNumber;

        return str_replace('[replaceWithWhatsappnumber]', $fullPhoneNumber, $call->whatsapp_link);
    }

    /**
     * Check if the WhatsApp link should be enabled based on call timing
     */
    public static function isCallLinkEnabled($bookingId)
    {
        // $bookingId = '2';
        $bookedCall = ScheduleCall::where('id', $bookingId)->first();


        // Check if booked call exists
        if (!$bookedCall) {
            return false;
        }

        if ($bookedCall->is_call_canceled == '1') {
            return false;
        }

        if ($bookedCall->is_call_completed == '1') {
            return false;
        }

        // Fetch the associated available time using the available_time_id
        $availableTime = AvailableTime::find($bookedCall->available_time_id);
        if (!$availableTime) {
            return false;
        }

        $now = Carbon::now()->setTimezone(config('app.timezone'));

        // Ensure the current date matches the scheduled call date
        $callDate = Carbon::parse($availableTime->date)->toDateString();
        if ($now->toDateString() !== $callDate) {
            return false;
        }

        // Calculate 5 minutes before start time and 10 minutes after end time
        $startTime = Carbon::parse($availableTime->start_time)
            ->setTimezone(config('app.timezone'))
            ->subMinutes(5); // 5 minutes before start time
        // dd($startTime);

        $endTime = Carbon::parse($availableTime->end_time)
            ->setTimezone(config('app.timezone'))
            ->addMinutes(10); // 10 minutes after end time

        // Check if the current time is within the range of 5 minutes before the start and 10 minutes after the end time
        return $now->between($startTime, $endTime);
    }

    public static function canReschedule($bookingId)
    {
        // $bookingId = '2';
        $bookedCall = ScheduleCall::where('id', $bookingId)->first();


        // Check if booked call exists
        if (!$bookedCall) {
            return false;
        }

        if ($bookedCall->is_call_canceled == '1') {
            return false;
        }

        if ($bookedCall->is_call_completed == '1') {
            return false;
        }

        // Fetch the associated available time using the available_time_id
        $availableTime = AvailableTime::find($bookedCall->available_time_id);
        if (!$availableTime) {
            return false;
        }

        $now = Carbon::now()->setTimezone(config('app.timezone'));

        // Ensure the current date matches the scheduled call date
        $callDate = Carbon::parse($availableTime->date)->toDateString();

        // Allow reschedule only if call date is in the past
        if ($callDate < $now->toDateString()) {
            return false;
        }



        // Combine date with start and end times
        $startTime = Carbon::parse($availableTime->date . ' ' . $availableTime->start_time, config('app.timezone'));
        $endTime = Carbon::parse($availableTime->date . ' ' . $availableTime->end_time, config('app.timezone'));

        // Ensure that the current time is at least 1 hour before the start time
        if ($now->gt($startTime->subHour())) {
            return false;
        }

        // Ensure that the end time and date have not passed
        if ($now->gte($endTime)) {
            return false;
        }

        // If all checks pass, the call can be rescheduled
        return true;
    }
}

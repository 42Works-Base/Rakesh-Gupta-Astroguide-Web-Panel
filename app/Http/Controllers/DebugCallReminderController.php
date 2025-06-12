<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduleCall;
use App\Models\User;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DebugCallReminderController extends Controller
{
    public function testReminders()
    {
        $this->sendReminders(10, 'reminder_sent_before_10min'); // 10 minutes before
        $this->sendReminders(60, 'reminder_sent_before_1hr');   // 1 hour before

        return response()->json(['status' => 'success', 'message' => 'Reminders processed']);
    }

    private function sendReminders($minutesBefore, $reminderField)
    {
        $now = now(config('app.timezone')); // Ensure you're using the correct timezone
        $targetTime = $now->copy()->addMinutes($minutesBefore)->setSecond(0); // Remove seconds
        $targetTimeFormatted = $targetTime->format('Y-m-d H:i:00'); // Format to remove microseconds

        // Fetch calls with a specific reminder
        $bookedCalls = ScheduleCall::join('available_times', 'schedule_calls.available_time_id', '=', 'available_times.id')
            ->join('transactions', function ($join) {
                $join->on('schedule_calls.id', '=', 'transactions.schedule_call_id')
                    ->where('transactions.status', 'completed');
            })
            ->join('users', 'schedule_calls.user_id', '=', 'users.id')
            ->whereNull('users.deleted_at')
            ->where('users.status', 'active')
            ->where("schedule_calls.{$reminderField}", false)
            ->where(function ($query) use ($targetTimeFormatted) {
                // Convert start time into Carbon instance and adjust with timezone
                $query->whereRaw(
                    "STR_TO_DATE(CONCAT(available_times.date, ' ', available_times.start_time), '%Y-%m-%d %H:%i:%s') = ?",
                    [$targetTimeFormatted]
                );
            })
            ->select(
                'schedule_calls.*',
                'available_times.date',
                'available_times.start_time',
                'available_times.end_time',
                'users.first_name',
                'users.last_name',
                'users.id as user_id'
            )
            ->get();

        foreach ($bookedCalls as $call) {
            $astrologer = User::where('role', 'astrologer')->first();

            // Set reminder content
            if ($minutesBefore == 10) {
                $title = "⏰ Reminder: Your call is in 10 minutes!";
                $type = 'call_reminder_10_minutes';
            } else {
                $title = "⏰ Reminder: Your call is in 1 hour!";
                $type = 'call_reminder_1_hour';
            }

            $message = "Your consultation with {$astrologer->first_name} {$astrologer->last_name} starts at {$call->start_time} on {$call->date}.";
            $title_admin = "{$call->first_name} {$call->last_name}'s call starts soon.";
            $message_to_admin = "Session with {$call->first_name} {$call->last_name} starts at {$call->start_time} on {$call->date}.";
            $data = ['schedule_calls_id' => $call->id];

            // Send notification
            NotificationHelper::notifyUser(null, $call->user_id, $title, $message, $title_admin, $message_to_admin, $type, $data);

            // Mark as sent to avoid duplicates
            $call->update([$reminderField => true]);
        }

        if (app()->runningInConsole()) {
            $this->info("{$minutesBefore}-minute reminders sent successfully.");
        }
    }


    public function testMarkOldCallsAsCompleted()
    {
        $now = now(config('app.timezone'));


        $calls = ScheduleCall::join('available_times', 'schedule_calls.available_time_id', '=', 'available_times.id')
            ->where('schedule_calls.is_call_completed', '0')
            ->whereDate('available_times.date', $now->toDateString()) // 👈 restrict to today's calls only
            ->whereRaw(
                "DATE_ADD(STR_TO_DATE(CONCAT(available_times.date, ' ', available_times.end_time), '%Y-%m-%d %H:%i:%s'), INTERVAL 1 HOUR) <= ?",
                [$now->format('Y-m-d H:i:s')]
            )
            ->select('schedule_calls.id')
            ->get();


        ScheduleCall::whereIn('id', $calls->pluck('id'))->update(['is_call_completed' => '1']);
    }
}

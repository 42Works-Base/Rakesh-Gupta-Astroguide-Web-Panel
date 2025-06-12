<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduleCall;
use App\Models\User;
use App\Helpers\NotificationHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendUpcomingCallReminders extends Command
{
    protected $signature = 'calls:send-upcoming-reminders';
    protected $description = 'Send notifications to users whose call is starting within 10 minutes.';



    public function handle()
    {
        try {
            // $this->logInfo("Cron started");

            $this->sendReminders(10, 'reminder_sent_before_10min');
            $this->sendReminders(60, 'reminder_sent_before_1hr');

            // $this->logInfo("Cron finished successfully");
        } catch (\Exception $e) {
            $this->logError("Cron failed: " . $e->getMessage());
        }
    }

    /*  private function sendReminders($minutesBefore, $reminderField)
    {
        $now = now(config('app.timezone')); // Ensure you're using the correct timezone
        $targetTime = $now->copy()->addMinutes($minutesBefore)->setSecond(0); // Remove seconds
        $targetTimeFormatted = $targetTime->format('Y-m-d H:i:00'); // Format to remove microseconds

        // $this->logInfo("Sending {$minutesBefore}-minute reminders at {$targetTimeFormatted}");

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

        // $this->logInfo("Found " . count($bookedCalls) . " calls for {$minutesBefore}-minute reminder");

        foreach ($bookedCalls as $call) {
            $astrologer = User::where('role', 'astrologer')->first();

            $date = Carbon::parse($call->date)->format('d-m-Y');
            $startTime =  Carbon::parse($call->start_time)->format('h:i A');

            // Set reminder content
            if ($minutesBefore == 10) {
                $title = "Reminder: Your call is in 10 minutes!";
                $title_admin = "{$call->first_name} {$call->last_name}'s call starts in 10 minutes.";
                $type = 'call_reminder_10_minutes';
            } else {
                $title = "Reminder: Your call is in 1 hour!";
                $title_admin = "{$call->first_name} {$call->last_name}'s call starts in 1 hour.";
                $type = 'call_reminder_1_hour';
            }

            $message = "Your consultation with {$astrologer->first_name} {$astrologer->last_name} starts at {$startTime} on {$date}.";
            $message_to_admin = "Session with {$call->first_name} {$call->last_name} starts at {$startTime} on {$date}.";
            $data = ['schedule_calls_id' => $call->id];

            // Send notification
            NotificationHelper::notifyUser(null, $call->user_id, $title, $message, $title_admin, $message_to_admin, $type, $data);

            // Mark as sent to avoid duplicates
            $call->update([$reminderField => true]);

            // $this->logInfo("Reminder sent to user ID {$call->user_id} for schedule call ID {$call->id}");
        }

        // if (app()->runningInConsole()) {
        //     $this->info("{$minutesBefore}-minute reminders sent successfully.");
        // }
    } */

    private function sendReminders($minutesBefore, $reminderField)
    {
        $now = now(config('app.timezone')); // Ensure you're using the correct timezone
        $targetTime = $now->copy()->addMinutes($minutesBefore)->setSecond(0); // Remove seconds
        $targetTimeFormatted = $targetTime->format('Y-m-d H:i:00'); // Format to remove microseconds

        $start = $targetTime->copy()->subSeconds(30)->format('Y-m-d H:i:s');
        $end = $targetTime->copy()->addSeconds(30)->format('Y-m-d H:i:s');

        // $this->logInfo("Sending {$minutesBefore}-minute reminders at {$targetTimeFormatted}");

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
            ->where("schedule_calls.is_call_canceled", '0')
            ->where("available_times.is_available", 'yes')
            ->where(function ($query) use ($start, $end) {
                // Convert start time into Carbon instance and adjust with timezone
                $query->whereRaw(
                "STR_TO_DATE(CONCAT(available_times.date, ' ', available_times.start_time), '%Y-%m-%d %H:%i:%s') BETWEEN ? AND ?",
                [$start, $end]
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

        // $this->logInfo("Found " . count($bookedCalls) . " calls for {$minutesBefore}-minute reminder");

        foreach ($bookedCalls as $call) {
            $astrologer = User::where('role', 'astrologer')->first();

            $date = Carbon::parse($call->date)->format('d-m-Y');
            $startTime =  Carbon::parse($call->start_time)->format('h:i A');

            // Set reminder content
            if ($minutesBefore == 10) {
                $title = "Reminder: Your call is in 10 minutes!";
                $title_admin = "{$call->first_name} {$call->last_name}'s call starts in 10 minutes.";
                $type = 'call_reminder_10_minutes';
            } else {
                $title = "Reminder: Your call is in 1 hour!";
                $title_admin = "{$call->first_name} {$call->last_name}'s call starts in 1 hour.";
                $type = 'call_reminder_1_hour';
            }

            $message = "Your consultation with {$astrologer->first_name} {$astrologer->last_name} starts at {$startTime} on {$date}.";
            $message_to_admin = "Session with {$call->first_name} {$call->last_name} starts at {$startTime} on {$date}.";
            $data = ['schedule_calls_id' => $call->id];

            // Send notification
            NotificationHelper::notifyUser(null, $call->user_id, $title, $message, $title_admin, $message_to_admin, $type, $data);

            // Mark as sent to avoid duplicates
            $call->update([$reminderField => true]);

            // $this->logInfo("Reminder sent to user ID {$call->user_id} for schedule call ID {$call->id}");
        }

        // if (app()->runningInConsole()) {
        //     $this->info("{$minutesBefore}-minute reminders sent successfully.");
        // }
    }

    // Log helper methods
    private function logInfo($message)
    {
        //Log::channel('reminder_cron')->info($message);
    }

    private function logError($message)
    {
        //Log::channel('reminder_cron')->error($message);
    }
}

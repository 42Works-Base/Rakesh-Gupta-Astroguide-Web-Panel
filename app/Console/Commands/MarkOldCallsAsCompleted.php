<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduleCall;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MarkOldCallsAsCompleted extends Command
{
    protected $signature = 'calls:mark-old-complete';
    protected $description = 'Mark calls as completed if they ended more than 1 hour ago';

    public function handle()
    {
        try {
            $this->logInfo("Cron started at " . now());

            $now = now(config('app.timezone'));

            // $calls = ScheduleCall::join('available_times', 'schedule_calls.available_time_id', '=', 'available_times.id')
            //     ->where('schedule_calls.is_call_completed', '0')
            //     ->whereDate('available_times.date', $now->toDateString())
            //     ->whereRaw(
            //         "DATE_ADD(STR_TO_DATE(CONCAT(available_times.date, ' ', available_times.end_time), '%Y-%m-%d %H:%i:%s'), INTERVAL 1 HOUR) <= ?",
            //         [$now->format('Y-m-d H:i:s')]
            //     )
            //     ->select('schedule_calls.id')
            //     ->get();

            $calls = ScheduleCall::join('available_times', 'schedule_calls.available_time_id', '=', 'available_times.id')
                ->where('schedule_calls.is_call_canceled', '0')
                ->whereDate('available_times.date', now()->toDateString())
                ->select('schedule_calls.id')
                ->get();


            $count = $calls->count();

            if ($count > 0) {
                ScheduleCall::whereIn('id', $calls->pluck('id'))->update(['is_call_completed' => '1']);
                $this->logInfo("Marked {$count} old calls as completed.");
            } else {
                $this->logInfo("No old calls to mark as completed.");
            }

            if (app()->runningInConsole()) {
                $this->info("{$count} old calls marked as completed.");
            }

            $this->logInfo("Cron finished at " . now());
        } catch (\Exception $e) {
            $this->logError("Cron failed: " . $e->getMessage());
        }
    }

    // Logging methods
    private function logInfo($message)
    {
        //Log::channel('old_calls_cron')->info($message);
    }

    private function logError($message)
    {
        //Log::channel('old_calls_cron')->error($message);
    }
}

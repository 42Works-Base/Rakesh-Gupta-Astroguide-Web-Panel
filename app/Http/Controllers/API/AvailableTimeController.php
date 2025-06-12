<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Models\AvailableTime;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\AvailableTimeRequest;
use Carbon\Carbon;
use DB;

class AvailableTimeController extends BaseController
{


     public function index($date = null)
    {
        $now = Carbon::now(Config('app.timezone'));
        $nowDate = $now->toDateString();
        $nowTime = $now->format('H:i:s');

        // Step 1: Get all available times and remove past slots manually
        // $availableTimes = AvailableTime::where('is_available','yes')->all()->filter(function ($time) use ($nowDate, $nowTime) {
        //     $slotDateTime = Carbon::parse($time->date . ' ' . $time->start_time, Config('app.timezone'));
        //     return $slotDateTime->gte(Carbon::now(Config('app.timezone')));
        // });

        $availableTimes = AvailableTime::orderBy('start_time', 'asc')->where('is_available', 'yes')
            ->get()
            ->filter(function ($time) {
                $slotDateTime = Carbon::parse($time->date . ' ' . $time->start_time, config('app.timezone'));
                return $slotDateTime->gte(Carbon::now(config('app.timezone')));
            });


        // Step 2: Exclude times with valid transactions (completed, initiated, processing)
        $filteredTimes = $availableTimes->filter(function ($time) {
            foreach ($time->scheduleCalls as $call) {
                $hasCompletedTransaction = $call->transactions->where('status', 'completed')->isNotEmpty();
                $isNotCanceled = $call->is_call_canceled == '0';

                if ($hasCompletedTransaction && $isNotCanceled) {
                    return false; // exclude slot if there's a valid completed transaction and it's not canceled
                }
            }
            return true; // keep the slot
        });


        // Optional: Filter by date
        if ($date) {
            $filteredTimes = $filteredTimes->where('date', $date);
        }

        $filteredTimes = $filteredTimes->sortByDesc('date')->values(); // sort & reindex

        // if ($filteredTimes->isEmpty()) {
        //     return $this->sendError('No slots found.');
        // }
        $filteredTimes = $filteredTimes->map(function ($time) {
            return [
                'id' => $time->id,
                'date' => $time->date,
                'start_time' => $time->start_time,
                'end_time' => $time->end_time,
                'price' => $time->price,
                'is_available' => $time->is_available,
                'created_at' => $time->created_at,
                'updated_at' => $time->updated_at,
            ];
        });

        return $filteredTimes->isEmpty()
            ? $this->sendError('Please select an available time slot.')
            : $this->sendSuccessResponse($filteredTimes, 'Slot listing.');
    }


}

<?php

namespace App\Http\Controllers;


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


    // public function index(Request $request)
    // {
    //     // Fetch date from request
    //     $searchDate = $request->input('date');

    //     // Fetch available slots, optionally filtering by date
    //     $query = AvailableTime::orderBy('date', 'desc');

    //     if ($searchDate) {
    //         $query->whereDate('date', $searchDate);
    //     }

    //     $availableTimes = $query->get()->groupBy('date');

    //     return view('admin.available_time.index', compact('availableTimes', 'searchDate'));
    // }

    public function index(Request $request)
    {
        // Fetch date from request
        $searchDate = $request->input('date');

        // Fetch available slots, optionally filtering by date
        $query = AvailableTime::orderBy('date', 'desc')
            ->orderBy('start_time', 'asc'); // Add this line

        if ($searchDate) {
            $query->whereDate('date', $searchDate);
        }

        $availableTimes = $query->get()->groupBy('date');

        return view('admin.available_time.index', compact('availableTimes', 'searchDate'));
    }


    public function showCreateForm()
    {
        $availableTimes = AvailableTime::get();
        return view('admin.available_time.create', compact('availableTimes'));
    }




    public function store(AvailableTimeRequest $request)
    {
        // dd($request->all());
        // Convert request date to Carbon instance
        $selectedDate = Carbon::parse($request->date);
        $today = Carbon::today();

        // Prevent storing slots for past dates (excluding today)
        if ($selectedDate->lt($today)) {
            return back()->withInput()->with('error', 'You cannot add slots for past dates.');
        }

        // to prevent pased time start


        // Get current time in the application's timezone
        $now = Carbon::now('Asia/Kolkata'); // Use your configured timezone

        // Convert request date to Carbon instance (without modifying timezone)
        $selectedDate = Carbon::createFromFormat('Y-m-d', $request->date);

        // Convert start and end times correctly
        $startTime = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->day_start_time, 'Asia/Kolkata');
        $endTime = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->day_end_time, 'Asia/Kolkata');

        // Prevent past start times if the date is today
        if ($selectedDate->isToday() && $startTime->lt($now)) {
            return back()->withInput()->with('error', 'The selected time slot is in the past. Please choose a future time slot to proceed.');
        }

        // Ensure end time is after start time
        if ($endTime->lte($startTime)) {
            return back()->withInput()->with('error', 'End time must be after start time.');
        }


        // ----to prevent pased time end----

        $startTime = Carbon::parse($request->day_start_time);
        $endTime = Carbon::parse($request->day_end_time);


        //  1. Validate if the time range is perfectly divisible by 30 minutes
        $duration = $startTime->diffInMinutes($endTime);
        if ($duration % 30 !== 0) {
            return back()->withInput()->with('error', 'Time range must be in exact 30-minute slots.');
        }


        $slots = [];

        while ($startTime < $endTime) {
            $slotEndTime = $startTime->copy()->addMinutes(30);

            //  2. Check if this slot already exists
            $exists = DB::table('available_times')
                ->where('date', $request->date) // Check for same day
                ->where('start_time', $startTime->format('H:i:s'))
                ->exists();

            if ($exists) {
                return back()->withInput()->with('error', "Slot already exists for {$startTime->format('h:i A')} - {$slotEndTime->format('h:i A')}");
            }



            // Check for overlapping slots
            $overlapExists = AvailableTime::where('date', $request->date)
                ->where(function ($query) use ($startTime, $slotEndTime) {
                    $query->where(function ($q) use ($startTime, $slotEndTime) {
                        $q->where('start_time', '<', $slotEndTime->format('H:i:s'))
                            ->where('end_time', '>', $startTime->format('H:i:s'));
                    });
                })
                ->exists();

            if ($overlapExists) {
                return back()->withInput()->with('error', "The slot {$startTime->format('h:i A')} - {$slotEndTime->format('h:i A')} overlaps with an existing slot.");
            }



            // Ensure the new slot starts exactly at the end of the last slot (if any slot exists)
            // Find the last added slot for this date
            /* $lastSlot = AvailableTime::where('date', $request->date)
                ->orderBy('end_time', 'desc')
                ->first();

            if ($lastSlot) {
                $lastEndTime = Carbon::parse($request->date . ' ' . $lastSlot->end_time);
                if ($startTime->ne($lastEndTime)) {
                    return back()->withInput()->with('error', 'New slot must start exactly at ' . $lastEndTime->format('h:i A'));
                }
            } */



            // Add slot data
            $slots[] = [
                'date'          => $request->date,
                'day_start_time' => Carbon::parse($request->day_start_time)->format('H:i:s'),
                'day_end_time'   => Carbon::parse($request->day_end_time)->format('H:i:s'),

                'start_time'    => $startTime->format('H:i:s'),
                'end_time'      => $slotEndTime->format('H:i:s'),
                'price' => $request->price,

            ];

            // Move to the next slot
            $startTime = $slotEndTime;
        }

        // Insert all slots into the database
        if (!empty($slots)) {
            DB::table('available_times')->insert($slots);
        }

        return back()->with('success', 'Slots created successfully.');
    }


    public function showFormEdit($id)
    {
        $selectedAvailableTime = AvailableTime::find($id);
        return view('admin.available_time.edit', compact('selectedAvailableTime'));
    }

    /*  public function updateSlot(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'price' => 'required|numeric|min:1',
            ],
            []
        );

        if ($validator->fails()) {

            return back()->with('error', $validator->errors()->first());
        }

        $time = AvailableTime::find($request->id);
        if ($time) {
            $time->price = $request->price;
            $time->save();
            return redirect()->route('slot-management.index')->with('success', 'Slots updated successfully.');
        }
        return back()->with('error', 'Slot not found.');
    } */

    /* public function updateSlot(Request $request, $availableTimesId){
        // dd($request->all());
        // Validate input
        $validator = Validator::make($request->all(), [
            'id'         => 'required|exists:available_times,id',
            'date'       => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i:s', // Updated to match the input format
            'end_time'   => 'required|date_format:H:i:s|after:start_time',
            'price'      => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        // Fetch the slot to be updated
        $slot = AvailableTime::find($request->id);

        if (!$slot) {
            return back()->with('error', 'Slot not found.');
        }

        // Ensure time format consistency
        $startTime = Carbon::parse($request->start_time)->format('H:i:s');
        $endTime   = Carbon::parse($request->end_time)->format('H:i:s');

        // Check if another slot exists in the same date & overlapping time range
        $exists = AvailableTime::where('date', $request->date)
            ->where('id', '!=', $request->id) // Exclude current slot from check
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->exists();

        if ($exists) {
            return back()->with('error', 'Another slot already exists within this time range.');
        }

        // Update slot
        $slot->date       = $request->date;
        $slot->start_time = $startTime;
        $slot->end_time   = $endTime;
        $slot->price      = $request->price;
        $slot->save();

        return redirect()->route('slot-management.index')->with('success', 'Slot updated successfully.');
    } */



    public function updateAvailability(Request $request)
    {
        $time = AvailableTime::find($request->id);
        if ($time) {
            $time->is_available = $request->status;
            $time->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Slot not found']);
    }

    public function destroy(Request $request)
    {
        $time = AvailableTime::find($request->id);

        if ($time) {
            $time->delete(); // Permanently delete the slot
            return response()->json(['success' => true, 'message' => 'Slot deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Slot not found']);
    }
}

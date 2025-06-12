<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailableTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'price'
    ];

    // Relationship with ScheduleCall
    public function scheduleCalls()
    {
        return $this->hasMany(ScheduleCall::class);
    }

    // public function isBooked()
    // {
    //     return $this->hasOne(ScheduleCall::class, 'available_time_id')->exists();
    // }

    public function isBooked()
    {
        // Get the latest schedule call with related transactions
        $call = $this->hasOne(ScheduleCall::class, 'available_time_id')
            ->with('transactions')
            ->latest()
            ->first(); // Fetch the first (latest) record

        // If no call exists, return false (not booked)
        if (!$call) {
            return false;
        }

        // If the call is canceled, return true (canceled)
        if ($call->is_call_canceled == 1) {
            return false; // Call is canceled, so it's not booked
        }

        // Otherwise, check if there are completed transactions
        return $call->transactions->where('status', 'completed')->isNotEmpty();
    }
}

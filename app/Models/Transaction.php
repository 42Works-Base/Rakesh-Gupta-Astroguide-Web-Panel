<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'schedule_call_id',
        'transaction_id',
        'payment_id',
        'status',
        'amount',
        'payment_method',
        'response_data',
        'refund_status'
    ];

    protected $casts = [
        'response_data' => 'array',
    ];

    public function user()
    {
        // return $this->belongsTo(User::class);
        return $this->belongsTo(User::class)->withTrashed();
    }

    // public function schedule()
    // {
    //     return $this->belongsTo(Schedule::class);
    // }

    public function scheduleCall()
    {
        return $this->belongsTo(ScheduleCall::class);
    }
}

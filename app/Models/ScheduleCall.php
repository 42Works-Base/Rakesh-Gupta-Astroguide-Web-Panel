<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'available_time_id',
        'start_time',
        'end_time',
        'call_type',
        'agenda',
        'whatsapp_link',
        'is_call_completed',
        'is_call_canceled',
        'reminder_sent_before_10min',
        'reminder_sent_before_1hr'
    ];

    // Relationship with User
    public function user()
    {
        // return $this->belongsTo(User::class)->whereNull('deleted_at');
        return $this->belongsTo(User::class)->withTrashed();
    }

    // Relationship with AvailableTime
    public function availableTime()
    {
        return $this->belongsTo(AvailableTime::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'schedule_call_id');
    }

    public function transactionCompleted()
    {
        return $this->hasOne(Transaction::class, 'schedule_call_id')->where('status','completed');
    }

    public function kundaliDetail()
    {
        return $this->hasOne(KundaliDetail::class, 'user_id', 'user_id');
    }
}

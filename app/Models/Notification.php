<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'title',
        'message',
        'title_admin',
        'message_to_admin',
        'type',
        'data',
        'is_read',
        'is_read_by_admin'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    public function user()
    {

        return $this->belongsTo('App\Models\User', 'receiver_id', 'id');
    }
}

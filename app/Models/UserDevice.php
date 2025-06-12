<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = [

        'user_id', 'type', 'identifier', 'api_token', 'fcm_token'
        
    ];

    public function user()
    {

        return $this->belongsTo('App\Models\User', 'user_id', 'id');

    }
}

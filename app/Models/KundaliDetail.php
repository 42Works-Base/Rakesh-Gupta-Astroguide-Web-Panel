<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KundaliDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gana',
        'yoni',
        'vasya',
        'nadi',
        'varna',
        'paya',
        'tatva',
        'life_stone',
        'lucky_stone',
        'fortune_stone',
        'name_start',
        'ascendant_sign',
        'ascendant_nakshatra',
        'rasi',
        'rasi_lord',
        'nakshatra',
        'nakshatra_lord',
        'nakshatra_pada',
        'sun_sign',
        'tithi',
        'karana',
        'yoga'
    ];


    public function user()
    {

        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}

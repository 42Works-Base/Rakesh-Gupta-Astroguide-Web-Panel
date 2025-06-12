<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBankDetail extends Model
{
    use HasFactory;

    protected $table = 'user_bank_details';

    protected $fillable = [
        'user_id',
        'account_holder_name',
        'phone',
        'account_number',
        'bank_name',
        'ifsc_code',
        'upi_id',

    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

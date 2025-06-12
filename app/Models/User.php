<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'country_code',
        'dob',
        'dob_time',
        'gender',
        'birth_city',
        'birthplace_country',
        'password',
        'otp',
        'profile_picture',
        'role',
        'is_verified',
        'full_address',
        'latitude',
        'longitude',
        'timezone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function devices()
    {
        return $this->hasMany('App\Models\UserDevice', 'user_id', 'id');
    }

    public function latestDevice()
    {
        return $this->hasOne(UserDevice::class, 'user_id')->latest();
    }

    public function notificationPreferences()
    {
        return $this->hasOne(NotificationPreference::class, 'user_id');
    }

    public function userNotification()
    {
        return $this->hasMany(Notification::class, 'receiver_id', 'id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

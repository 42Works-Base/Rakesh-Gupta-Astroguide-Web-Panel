<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeederController extends Controller
{
    public function seedTestAdmins()
    {
        $admins = [
            [
                'email' => 'Jaspreetsingh@42works.net',
                'first_name' => 'Sh. Rakesh',
                'last_name' => 'Gupta',
                'phone' => '9855994779',
                'country_code' => '+91',
                'dob' => '1990-01-01',
                'dob_time' => '10:00:00',
                'gender' => 'male',
                'birth_city' => 'Mohali',
                'birthplace_country' => 'India',
                'full_address' => 'Mohali, India',
                'latitude' => '26.8467',
                'longitude' => '80.9462',
                'timezone' => '+05:30',
                'password' => '123qwe!@#',
                'role' => 'astrologer',
            ],
            [
                'email' => 'abdullah@42works.net',
                'first_name' => 'Test',
                'last_name' => 'AdminOne',
                'phone' => '8218811389',
                'country_code' => '+91',
                'dob' => '1990-01-01',
                'dob_time' => '10:00:00',
                'gender' => 'male',
                'birth_city' => 'Lucknow',
                'birthplace_country' => 'India',
                'full_address' => 'Lucknow, India',
                'latitude' => '26.8467',
                'longitude' => '80.9462',
                'timezone' => '+05:30',
                'password' => '123qwe!@#',
                'role' => 'astrologer',
            ],
        ];

        $created = 0;

        foreach ($admins as $admin) {
            if (!User::where('email', $admin['email'])->exists()) {
                User::create([
                    'first_name' => $admin['first_name'],
                    'last_name' => $admin['last_name'],
                    'phone' => $admin['phone'],
                    'country_code' => $admin['country_code'],
                    'dob' => $admin['dob'],
                    'dob_time' => $admin['dob_time'],
                    'gender' => $admin['gender'],
                    'birth_city' => $admin['birth_city'],
                    'birthplace_country' => $admin['birthplace_country'],
                    'full_address' => $admin['full_address'],
                    'latitude' => $admin['latitude'],
                    'longitude' => $admin['longitude'],
                    'timezone' => $admin['timezone'],
                    'email' => $admin['email'],
                    'password' => Hash::make($admin['password']),
                    'otp' => rand(100000, 999999),
                    'role' => $admin['role'],
                ]);
                $created++;
            }
        }

        return response()->json([
            'message' => "$created test admin(s) created successfully."
        ]);
    }
}

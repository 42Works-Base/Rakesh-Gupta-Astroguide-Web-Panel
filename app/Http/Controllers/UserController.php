<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserBankDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;


class UserController extends Controller
{
    //

    public function index()
    {
        $users = User::where('role', 'user')->latest()->get();
        return view('admin.user.index', compact('users'));
    }

    public function userDetails($userId)
    {

        // $users = User::findorfail($userId);
        $users = User::withTrashed()->findOrFail($userId);
        return view('admin.user.details', compact('users'));
    }

    public function blockUnblock($id, Request $request)
    {

        $user = User::findOrFail($id);

        if ($request->action === 'block') {
            $user->status = 'inactive';
        } else {
            $user->status = 'active';
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => "User successfully " . ($request->action === 'block' ? 'blocked' : 'unblocked')
        ]);
    }
    // admin profile start
    public function adminProfile()
    {
        return view('admin.admin_profile_update');
    }

    public function adminProfileUpdate(Request $request)
    {
        $user = Auth::user();

        // Validate input
        $validator = Validator::make($request->all(), [
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'         => 'required|digits_between:10,15|unique:users,phone,' . $user->id,
            'dob'           => 'required|date',
            'full_address'  => 'required|string|max:500',
            'gender'        => 'required|in:male,female,other',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            // For web requests, redirect back with an error message
            throw new HttpResponseException(
                redirect()->back()->withInput()->with('error', $errorMessage)
            );

        }

        // Handle profile picture
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                $oldPicturePath = str_replace(Storage::url(''), '', $user->profile_picture);
                Storage::disk('public')->delete($oldPicturePath);
            }

            $file = $request->file('profile_picture');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $profilePicturePath = $file->storeAs('profile_pictures', $filename, 'public');

            $user->profile_picture = Storage::url($profilePicturePath);
        }

        // Update other fields
        $user->first_name   = $request->first_name;
        $user->last_name    = $request->last_name;
        $user->email        = $request->email;
        $user->phone        = $request->phone;
        $user->dob          = $request->dob;
        $user->full_address = $request->full_address;
        $user->gender       = $request->gender;

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }


    public function adminPassword()
    {
        return view('admin.admin_password_update');
    }


    public function adminPasswordUpdate(Request $request)
    {
        // $request->validate([
        //     'current_password' => 'required',
        //     'new_password' => 'required|min:8|confirmed',
        // ]);

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            // For web requests, redirect back with an error message
            throw new HttpResponseException(
                redirect()->back()->withInput()->with('error', $errorMessage)
            );
        }

        $user = Auth::user();

        // Check if the current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            // throw ValidationException::withMessages(['current_password' => 'The current password is incorrect.']);

            throw new HttpResponseException(
                redirect()->back()->withInput()->with('error', 'The current password is incorrect.')
            );
        }

        // Update the password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }


    public function adminBank()
    {
        $userId = auth()->id();
        $bankDetails = UserBankDetail::where('user_id', $userId)->first();

        return view('admin.admin_bank_update', compact('bankDetails'));
    }


    public function adminBankUpdate(Request $request)
    {
        $user = Auth::user();

        // Validate input for bank details
        $validator = Validator::make($request->all(), [
            'account_holder_name' => 'required|string|max:255',
            'phone'          => 'nullable|digits_between:10,15',
            'account_number'      => 'required|numeric|digits_between:9,20',
            'bank_name'           => 'required|string|max:255',
            'ifsc_code'           => 'required|string',
            // 'upi_id'              => 'required|string|max:255|different:account_number',

        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            // For web requests, redirect back with an error message
            throw new HttpResponseException(
                redirect()->back()->withInput()->with('error', $errorMessage)
            );
            // return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update or create user bank details
        UserBankDetail::updateOrCreate(
            ['user_id' => $user->id],
            [
                'account_holder_name' => $request->account_holder_name,
                'bank_name'           => $request->bank_name,
                'account_number'      => $request->account_number,
                'ifsc_code'           => $request->ifsc_code,
                'upi_id'              => $request->upi_id,
                'phone'          => $request->phone,
            ]
        );

        return redirect()->back()->with('success', 'Bank details updated successfully!');
    }



    // admin profile end
}

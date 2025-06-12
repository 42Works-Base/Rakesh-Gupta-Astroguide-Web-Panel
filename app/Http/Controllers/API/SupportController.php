<?php

namespace App\Http\Controllers\API;

use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Mail\SupportMail;
use Illuminate\Support\Facades\Mail;

class SupportController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'issue' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $support = Support::create($request->all());

        // Send email to the admin
        $adminEmail = config('app.admin_email');
        Mail::to($adminEmail)->send(new SupportMail($support));

        return $this->sendSuccessResponse([], 'Support request submitted successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($email)
    {
        $supports = Support::where('email', $email)->get();

        if ($supports->isEmpty()) {
            return $this->sendError('No support requests found for this email.');
        }

        $supports =  $supports->map(function ($support) {
            return [
                'id' => $support->id,
                'issue' => $support->issue,
                'email' => $support->email,
                'message' => $support->message,
            ];
        })->toArray();

        return $this->sendSuccessResponse($supports, 'Your submitted support list!');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Support $support)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Support $support)
    {
        //
    }
}

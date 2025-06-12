<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\ScheduleCall;
use App\Models\User;
use App\Models\AvailableTime;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Helpers\NotificationHelper;
use Carbon\Carbon;

use App\Mail\WhatsAppLinkMail;
use Illuminate\Support\Facades\Mail;

class TransactionController extends BaseController
{

    // 1. Initiate a transaction (check under CallAppointmentController)
    /*  public function initiateTransaction(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|exists:users,id',
                'schedule_call_id' => 'required|exists:schedule_calls,id',
            ],
            []
        );

        if ($validator->fails()) {

            return $this->sendError($validator->errors()->first());
        }
        // dd($request->schedule_call_id, $request->user_id);

        $scheduleCalls = ScheduleCall::where('id', $request->schedule_call_id)->where('user_id', $request->user_id)->first();

        if (!$scheduleCalls) {
            return $this->sendError('Invalid Details.');
        }
        $availableTime = AvailableTime::find($scheduleCalls->available_time_id);

        if (!$availableTime) {
            return $this->sendError('Selected time slot is not found.');
        }

        $transaction = Transaction::create([
            'user_id' => $request->user_id,
            'schedule_call_id' => $request->schedule_call_id,
            'transaction_id' => Str::uuid(),
            'status' => 'initiated',
            'amount' => $availableTime->price,
        ]);


        return $this->sendSuccessResponse($transaction, 'Transaction initiated successfully.');
    } */

    // 2. Update transaction status (process or complete)
    public function updateTransaction(Request $request, $transaction_id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'status' => 'required|in:processing,completed,failed',
                'payment_method' => 'required|string',
                'response_data' => 'nullable|array',
                'payment_id' => 'required|unique:transactions,payment_id',
            ],
            []
        );

        if ($validator->fails()) {

            return $this->sendError($validator->errors()->first());
        }

        $transaction = Transaction::where('transaction_id', $transaction_id)->first();

        if (!$transaction) {
            return $this->sendError('Transaction not found.');
        }
        $userId = $transaction['user_id'];
        $schedule_call_id = $transaction['schedule_call_id'];
        $userExists = User::where('id', $userId)->first();

        if (!$userExists) {
            return $this->sendError('Invalid user ID. No such user found.');
        }
        $transaction->update([
            'status' => $request->status,
            'payment_method' => $request->payment_method ?? $transaction->payment_method,
            'response_data' => $request->response_data ?? $transaction->response_data,
            'payment_id' => $request->payment_id ?? $transaction->payment_id,
        ]);

        // Fetch booked call
        $bookedCalls = ScheduleCall::where('id', $schedule_call_id)->where('user_id', $userId)->first();
        $availableTime = AvailableTime::where('id', $bookedCalls->available_time_id)->first();
        $date = Carbon::parse($availableTime->date)->format('d-m-Y');
        $startTime =  Carbon::parse($availableTime->start_time)->format('h:i A');
        $end_time =  Carbon::parse($availableTime->end_time)->format('h:i A');

        // if transaction is completed then crate whatsappLink
        if ($request->status == 'completed') {
            // Generate WhatsApp Call Link Based on Call Type
            $whatsappLink = "https://wa.me/[replaceWithWhatsappnumber]?call";

            if ($bookedCalls->call_type === 'video') {
                $whatsappLink = "https://wa.me/[replaceWithWhatsappnumber]?video_call";
            }
            $bookedCalls->update([
                'whatsapp_link' => $whatsappLink,
            ]);

            // notification start
            $astrologer = User::where('role', 'astrologer')->first();
            $data = ['schedule_calls_id' => $bookedCalls['id']];
            $senderId = null;
            $receiverId = $userExists->id;
            $title = 'Slot Booking Confirmation';
            $message = 'Your consultation with ' . $astrologer['first_name'] . ' ' . $astrologer['last_name'] . ' is booked for ' . $date . ' at ' . $startTime . '!';

            $title_admin =  $userExists['first_name'] . ' ' . $userExists['last_name'] . ' has booked a slot for ' . $date . ' at ' . $startTime . '!';
            $message_to_admin = $userExists['first_name'] . ' ' . $userExists['last_name'] . ' has successfully booked a Consultation Type: ' . ucfirst($bookedCalls['call_type']) . ' session.
                            Date & Time: ' . $date . ' at ' . $startTime . '
                            Make sure the session is prepared and ready to go!';

            $type = "book_a_consultation";

            $firebaseResponse = NotificationHelper::notifyUser($senderId, $receiverId,  $title,  $message, $title_admin, $message_to_admin,$type, $data);
            // dd($firebaseResponse);
            // notification end

            // Send WhatsApp Message
            $this->sendWhatsAppEmail($userExists->email, $userExists->phone, $whatsappLink);
        }

        return $this->sendSuccessResponse($transaction, 'Transaction updated successfully.');
    }

    // 3. Get transaction details
    public function getTransaction($transaction_id)
    {
        $transaction = Transaction::where('transaction_id', $transaction_id)->first();

        if (!$transaction) {
            return $this->sendError('Transaction not found.');
        }

        return $this->sendSuccessResponse($transaction, 'Transaction details.');
    }



    public function sendWhatsAppEmail($userEmail, $phone, $secureLink)
    {
        // Generate WhatsApp link
        // $message = "Hello, your scheduled call is ready. Click here to join: {$secureLink}";
        // $encodedMessage = urlencode($message);
        // $whatsappLink = "https://api.whatsapp.com/send?phone={$phone}&text={$encodedMessage}";

        // Send email
        Mail::to($userEmail)->send(new WhatsAppLinkMail($secureLink));

        return "WhatsApp link email sent!";
    }


    /*   private function sendWhatsAppMessage($to, $message)
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_WHATSAPP_FROM');

        try {
            $client = new Client($sid, $token);
            $client->messages->create(
                "whatsapp:+" . $to, // User's WhatsApp Number
                [
                    'from' => $from,
                    'body' => $message
                ]
            );
        } catch (\Exception $e) {
            \Log::error("WhatsApp Message Failed: " . $e->getMessage());
        }
    } */

    /*  public function sendWhatsAppMessage($phone, $message)
    {
        $phone = '+918218811389';
        // Ensure phone number is in international format
        $phone = preg_replace('/\D/', '', $phone); // Remove non-numeric characters

        // Encode the message for URL
        $message = urlencode($message);

        // Construct the WhatsApp API URL
        $whatsappUrl = "https://api.whatsapp.com/send?phone={$phone}&text={$message}";

        // Redirect user to WhatsApp
        return redirect()->away($whatsappUrl);
    } */

    // public function sendWhatsAppMessage($phone, $message)
    // {
    //     $accessToken = 'YOUR_WHATSAPP_API_TOKEN'; // Get from Facebook Developer Console
    //     $phoneNumberId = 'YOUR_PHONE_NUMBER_ID'; // Get from Facebook Developer Console

    //     $data = [
    //         'messaging_product' => 'whatsapp',
    //         'to' => $phone,
    //         'type' => 'text',
    //         'text' => ['body' => $message],
    //     ];

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v17.0/{$phoneNumberId}/messages");
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_POST, 1);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //         "Authorization: Bearer $accessToken",
    //         'Content-Type: application/json',
    //     ]);

    //     $response = curl_exec($ch);
    //     curl_close($ch);

    //     return json_decode($response, true);
    // }
}

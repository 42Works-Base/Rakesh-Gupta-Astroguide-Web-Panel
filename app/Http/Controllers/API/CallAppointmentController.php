<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Models\AvailableTime;
use App\Models\ScheduleCall;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Mpdf\Mpdf;
use App\Helpers\CallHelper;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Storage;

class CallAppointmentController extends BaseController
{
    public function bookCall(Request $request)
    {
        // dd($request->all());
        // Validate request data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'available_time_id' => 'required|exists:available_times,id',
            'call_type' => 'required|string', //'audio', 'video')
            // 'agenda' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $user_id = $request->user_id;
        // Get the available time slot
        $availableTime = AvailableTime::find($request->available_time_id);

        if (!$availableTime) {
            return $this->sendError('Selected time slot is not found.');
        }

        if ($availableTime->is_available == 'no') {
            return $this->sendError('Selected time slot is not available.');
        }

        // Check if the call time is in the future
        $callDateTime = Carbon::parse($availableTime->date . ' ' . $availableTime->start_time);

        if ($callDateTime->isPast()) {
            return $this->sendError('You cannot schedule a call for a past date or time.');
        }


        $existingCall = ScheduleCall::where('user_id', $user_id)
            ->where('available_time_id', $request->available_time_id)
            ->where('is_call_canceled', '0')
            ->whereHas('transactions', function ($query) {
                $query->where('status', 'completed');
            })
            ->exists();

        if ($existingCall) {
            return $this->sendError('You have already scheduled a call at this time.');
        }

        // Create the scheduled call
        $scheduleCall = ScheduleCall::create([
            'user_id' => $user_id,
            'available_time_id' => $request->available_time_id,
            'call_type' => $request->call_type,
            'agenda' => $request->agenda,
        ]);

        $schedule_call_id = $scheduleCall->id;
        $price =  $availableTime->price;
        $initiateTransaction = $this->initiateTransaction($schedule_call_id, $user_id, $price);
        $data = [
            'scheduleCall' => $scheduleCall,
            'initiateTransaction' => $initiateTransaction
        ];

        return $this->sendSuccessResponse($data, 'Call scheduled successfully!');
    }

    public function initiateTransaction($schedule_call_id, $user_id, $price)
    {


        $transaction = Transaction::create([
            'user_id' => $user_id,
            'schedule_call_id' => $schedule_call_id,
            'transaction_id' => Str::uuid(),
            'status' => 'initiated',
            'amount' => $price,
        ]);


        return $transaction;
    }

    public function updateStatus(Request $request, $schedule_calls_id)
    {
        $validator = Validator::make($request->all(), [
            'is_call_completed' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $call = ScheduleCall::where('id', $schedule_calls_id)->first();
        if (!$call) {
            return $this->sendError('Invalid id.');
        }
        $call->is_call_completed = $request->is_call_completed;
        $call->save();

        return $this->sendSuccessResponse($call, 'Appointment status updated successfully!');
    }

    /*  public function updateScheduledCall(Request $request, $id)
    {
        $scheduleCall = ScheduleCall::findOrFail($id);

        if ($scheduleCall->call_type !== $request->call_type) {
            return response()->json(['error' => 'Call type cannot be changed after scheduling'], 400);
        }

        $scheduleCall->update($request->only(['agenda']));

        return response()->json(['message' => 'Schedule updated successfully', 'data' => $scheduleCall]);
    } */



    public function listBookedCallsByUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $userId = $request->user_id;

        $userExists = User::where('id', $userId)->where('role', 'user')->exists();

        if (!$userExists) {
            return $this->sendError('Invalid user ID. No such user found.');
        }

        $date = $request->date;
        $booking_list_type =  $request->booking_list_type;

        $booked_calls = ScheduleCall::join('available_times', 'schedule_calls.available_time_id', '=', 'available_times.id')
            ->join('transactions', function ($join) {
                $join->on('schedule_calls.id', '=', 'transactions.schedule_call_id')
                    ->where('transactions.status', 'completed');
            })
            ->where('schedule_calls.user_id', $userId)
            ->where('schedule_calls.is_call_canceled', '0')

            ->when($date, function ($query, $date) {
                $query->whereDate('available_times.date', $date);
            })
            // ->when($booking_list_type === 'past', function ($query) {
            //     $query->whereDate('available_times.date', '<', now()->toDateString());
            // })
            // ->when($booking_list_type === 'upcoming', function ($query) {
            //     $query->whereDate('available_times.date', '>=', now()->toDateString());
            // })

            ->when($booking_list_type === 'past', function ($query) {
                $query->whereRaw("STR_TO_DATE(CONCAT(available_times.date, ' ', available_times.start_time), '%Y-%m-%d %H:%i:%s') < ?", [now()]);
            })
            ->when($booking_list_type === 'upcoming', function ($query) {
                $query->whereRaw("STR_TO_DATE(CONCAT(available_times.date, ' ', available_times.start_time), '%Y-%m-%d %H:%i:%s') >= ?", [now()]);
            })

            ->orderBy('available_times.date', 'asc')
            ->select('schedule_calls.*', 'available_times.date', 'available_times.start_time', 'available_times.end_time')
            ->get();



        if ($booked_calls->isEmpty()) {
            return $this->sendError('No booked calls found for this user.');
        }
        /* $bookedCalls = $booked_calls->map(function ($call) {
            $fullPhoneNumber = config('app.admin_phone');
            $whatsapp_link = str_replace('[replaceWithWhatsappnumber]', $fullPhoneNumber, $call->whatsapp_link);
            return [
                'id' => $call->id,
                'date' => $call->date, // Fetched from available_times
                'start_time' => $call->start_time,
                'call_type' => $call->call_type,
                'agenda' => $call->agenda,
                'whatsapp_link' => $whatsapp_link,
                'is_call_completed' => $call->is_call_completed
            ];
        })->toArray(); */
        $bookedCalls = $booked_calls->map(function ($call) {
            // $description = config('app_message.Calldescription');

            if ($call->call_type == 'video') {
                $description = config('app_message.Calldescription.video');
            } elseif ($call->call_type == 'audio') {
                $description = config('app_message.Calldescription.audio');
            } elseif ($call->call_type == 'chat') {
                $description = config('app_message.Calldescription.chat');
            } else {
                $description = 'N/A';
            }

            $fullPhoneNumber = config('app.admin_phone');
            $whatsapp_link = str_replace('[replaceWithWhatsappnumber]', $fullPhoneNumber, $call->whatsapp_link);

            // Combine date and start_time to check whether it's in the past or upcoming
            /*  $callDateTime = \Carbon\Carbon::parse($call->date . ' ' . $call->start_time);
            $now = \Carbon\Carbon::now();

            $statusTag = $callDateTime->lt($now) ? 'past' : 'upcoming'; */

            // Combine date and start_time to a full datetime and apply app timezone
            $callDateTime = \Carbon\Carbon::parse($call->date . ' ' . $call->start_time)
                ->setTimezone(config('app.timezone'));

            $now = \Carbon\Carbon::now(config('app.timezone'));

            // Determine if the call is in the past or upcoming
            $statusTag = $callDateTime->lt($now) ? 'past' : 'upcoming';
            $isCallLinkEnabled = CallHelper::isCallLinkEnabled($call->id);
            return [
                'id' => $call->id,
                'admin_phone' => $fullPhoneNumber,
                'date' => $call->date,
                'start_time' => $call->start_time,
                'end_time' => $call->end_time,
                'call_type' => $call->call_type,
                'agenda' => $call->agenda,
                'whatsapp_link' => $whatsapp_link,
                'is_call_completed' => $call->is_call_completed,
                'is_call_canceled'=> $call->is_call_canceled,
                'isCallLinkEnabled' => $isCallLinkEnabled,
                'status' => $statusTag,
                'description' => $description,
            ];
        })->toArray();

        return $this->sendSuccessResponse($bookedCalls, 'List of booked calls!');
    }

    public function bookedCallDetails($bookingId)
    {

        $booked_call = ScheduleCall::join('available_times', 'schedule_calls.available_time_id', '=', 'available_times.id')
            ->join('transactions', function ($join) {
                $join->on('schedule_calls.id', '=', 'transactions.schedule_call_id')
                    ->where('transactions.status', 'completed');
            })
            ->where('schedule_calls.id', $bookingId) // Fully qualified to avoid ambiguity
            ->orderBy('available_times.date', 'asc')
            ->select('schedule_calls.*', 'available_times.date', 'available_times.start_time', 'available_times.end_time')
            ->first();



        if (!$booked_call) {
            return $this->sendError('No booked calls found.');
        }



        if ($booked_call->call_type == 'video') {
            $description = config('app_message.Calldescription.video');
        } elseif ($booked_call->call_type == 'audio') {
            $description = config('app_message.Calldescription.audio');
        } elseif ($booked_call->call_type == 'chat') {
            $description = config('app_message.Calldescription.chat');
        } else {
            $description = 'N/A';
        }

        // $fullPhoneNumber = config('app.admin_phone');
        $astrologer = User::where('role', 'astrologer')->first();
        $fullPhoneNumber = $astrologer['phone'];
        $whatsapp_link = str_replace('[replaceWithWhatsappnumber]', $fullPhoneNumber, $booked_call->whatsapp_link);

        // Combine date and start_time to a full datetime and apply app timezone
        $callDateTime = \Carbon\Carbon::parse($booked_call->date . ' ' . $booked_call->start_time)
            ->setTimezone(config('app.timezone'));

        $now = \Carbon\Carbon::now(config('app.timezone'));

        // Determine if the call is in the past or upcoming
        $statusTag = $callDateTime->lt($now) ? 'past' : 'upcoming';
        $isCallLinkEnabled = CallHelper::isCallLinkEnabled($booked_call->id);
        $canReschedule  = CallHelper::canReschedule($booked_call->id);
        $canCancel  = CallHelper::canReschedule($booked_call->id);
        $bookedCallDetails =  [
            'id' => $booked_call->id,
            'admin_phone' => $fullPhoneNumber,
            'date' => $booked_call->date,
            'start_time' => $booked_call->start_time,
            'end_time' => $booked_call->end_time,
            'call_type' => $booked_call->call_type,
            'agenda' => $booked_call->agenda,
            'whatsapp_link' => $whatsapp_link,
            'is_call_completed' => $booked_call->is_call_completed,
            'is_call_canceled' => $booked_call->is_call_canceled,
            'isCallLinkEnabled' => $isCallLinkEnabled,
            'canReschedule' => $canReschedule,
            'canCancel' => $canCancel,
            'status' => $statusTag,
            'description' => $description,
        ];


        return $this->sendSuccessResponse($bookedCallDetails, 'Booked call details!');
    }

    public function rescheduleCall(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'schedule_calls_id' => 'required|exists:schedule_calls,id',
            'available_time_id' => 'required|exists:available_times,id',
            'call_type' => 'required|string', // 'audio', 'video', 'chat'
            // 'agenda' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        // Check if user is allowed to reschedule
        $canReschedule = CallHelper::canReschedule($request->schedule_calls_id);
        if (!$canReschedule) {
            return $this->sendError('Rescheduling allowed up to 1 hour before start time.');
        }

        // Check if selected available time exists and is available
        $availableTime = AvailableTime::find($request->available_time_id);

        $date = Carbon::parse($availableTime->date)->format('d-m-Y');
        $startTime =  Carbon::parse($availableTime->start_time)->format('h:i A');

        if (!$availableTime || $availableTime->is_available == 'no') {
            return $this->sendError('Selected time slot is not available.');
        }

        // Check if selected time slot is not in the past
        $callDateTime = Carbon::parse($availableTime->date . ' ' . $availableTime->start_time, config('app.timezone'));
        if ($callDateTime->isPast()) {
            return $this->sendError('You cannot schedule a call for a past date or time.');
        }

        // Check if someone already booked this time and paid
        $existingCall = ScheduleCall::where('available_time_id', $request->available_time_id)
            ->whereHas('transactions', function ($query) {
                $query->where('status', 'completed');
            })
            ->exists();

        if ($existingCall) {
            return $this->sendError('This time slot is already booked.');
        }

        // Update the existing schedule call
        $scheduleCall = ScheduleCall::where('id', $request->schedule_calls_id)->first();
        $scheduleCall->update([
            'available_time_id' => $request->available_time_id,
            'call_type' => $request->call_type,
            'agenda' => $request->agenda,
        ]);

        // notification start
        $astrologer = User::where('role', 'astrologer')->first();
        $user = User::where('id', $scheduleCall['user_id'])->first();
        $data = ['schedule_calls_id' => $scheduleCall['id']];
        $senderId = null;
        $receiverId = $scheduleCall->user_id;
        $title = 'Reschedule Slot Confirmation';
        $message = 'Your consultation has been rescheduled to ' . $date . ' at ' . $startTime . '!';

        $title_admin =  $user['first_name'] . ' ' . $user['last_name'] . ' has rescheduled their call for ' . $date . ' at ' . $startTime . '!';
        $message_to_admin = $user['first_name'] . ' ' . $user['last_name'] . ' has successfully rescheduled their Consultation Type: ' . ucfirst($scheduleCall['call_type']) . ' session.
                            New Date & Time: '.$date.' at '. $startTime. '
                            Please update the schedule accordingly.';

        $type = "reschedule_consultation";

        $firebaseResponse = NotificationHelper::notifyUser($senderId, $receiverId,  $title,  $message, $title_admin, $message_to_admin, $type, $data);
        // dd($firebaseResponse);
        // notification end

        return $this->sendSuccessResponse([
            'scheduleCall' => $scheduleCall,
        ], 'Call rescheduled successfully!');
    }

    public function cancelCall(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'schedule_calls_id' => 'required|exists:schedule_calls,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $bookedCall = ScheduleCall::where('id', $request->schedule_calls_id)->first();
        if (!$bookedCall) {
            return $this->sendError('Invalid schudule call id.');
        }

        // Check if user is allowed to reschedule
        $canCancel = CallHelper::canReschedule($request->schedule_calls_id);
        if (!$canCancel) {
            return $this->sendError('You can not cancel the call.');
        }

        // Check if selected available time exists and is available
        $availableTime = AvailableTime::find($bookedCall->available_time_id);

        $date = Carbon::parse($availableTime->date)->format('d-m-Y');
        $startTime =  Carbon::parse($availableTime->start_time)->format('h:i A');


        // Check if selected time slot is not in the past
        $callDateTime = Carbon::parse($availableTime->date . ' ' . $availableTime->start_time, config('app.timezone'));
        if ($callDateTime->isPast()) {
            return $this->sendError('You cannot cancel a call for a past date or time.');
        }

        // Check if already canceled
        $existingCall = ScheduleCall::where('available_time_id', $availableTime->id)
            ->whereHas('transactions', function ($query) {
                $query->where('refund_status', 'completed');
            })
            ->exists();

        if ($existingCall) {
            return $this->sendError('This time slot is already canceled.');
        }

        // Update the existing schedule call
        $scheduleCall = ScheduleCall::where('id', $request->schedule_calls_id)->first();
        $scheduleCall->update([
            'is_call_canceled' => '1',
        ]);

        // $initiateRefundTransactuion = $this->initiateRefundTransactuion($request->schedule_calls_id);

        // notification start
        $astrologer = User::where('role', 'astrologer')->first();
        $user = User::where('id', $scheduleCall['user_id'])->first();

        $data = ['schedule_calls_id' => $scheduleCall['id']];
        $senderId = null;
        $receiverId = $bookedCall->user_id;
        $title = 'Cancel Consultation';
        $message = 'Your consultation on ' . $date . ' at ' . $startTime . ' has been canceled — refund will be processed soon.';

        $title_admin =  $user['first_name'] . ' ' . $user['last_name'] . ' has canceled their slot for ' . $date . ' at ' . $startTime . '!';
        $message_to_admin = $user['first_name'] . ' ' . $user['last_name'] . ' has canceled their Consultation Type: '. ucfirst($scheduleCall['call_type']).' session.
                            Original Date & Time: ' . $date . ' at ' . $startTime . '
                            Please take note and update the schedule accordingly.';

        $type = "cancel_consultation";

        $firebaseResponse = NotificationHelper::notifyUser($senderId,$receiverId,$title,$message,$title_admin, $message_to_admin,$type, $data);
        // dd($firebaseResponse);
        // notification end

        return $this->sendSuccessResponse($scheduleCall, 'Call canceled successfully!');
    }

    // public function initiateRefundTransactuion($schedule_calls_id)
    // {
    //     $transaction = Transaction::where('schedule_call_id', $schedule_calls_id)->first();
    //     $transaction->update([
    //         'refund_status' => 'initiated',
    //     ]);
    // }

    public function generateReceipt($schedule_calls_id)
    {
        $bookedCall = ScheduleCall::where('id', $schedule_calls_id)->first();

        if (!$bookedCall) {
            return $this->sendError('Invalid schedule call id.');
        }

        $userId =  $bookedCall['user_id'];
        $astrologer = User::where('role', 'astrologer')->first();
        $astrologerSpecialties = 'Prashana ,Vastu ,Vedic';

        $user =  User::find($userId);
        $availableTime = AvailableTime::where('id', $bookedCall['available_time_id'])->first();

        $data = [
            'user' => $user,
            'transaction' => Transaction::where('schedule_call_id', $schedule_calls_id)->first(),
            'scheduleCall' => $bookedCall,
            'availableTime' => $availableTime,
            'astrologer' => $astrologer,
            'astrologerSpecialties' => $astrologerSpecialties
        ];

        $html = view('pdf.receipt', $data)->render();

        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);

        // File name generation
        $fileName = 'receipts/receipt_' . $schedule_calls_id . '.pdf'; // Use schedule_calls_id for consistent file naming

        // Check if the file already exists and remove it if it does
        if (Storage::disk('public')->exists($fileName)) {
            Storage::disk('public')->delete($fileName); // Remove the old file if it exists
        }

        // Store the new file publicly
        Storage::disk('public')->put($fileName, $mpdf->Output('', 'S')); // 'S' = return as string

        // Generate public URL for the file
        $url = url('storage/' . $fileName);

        return $this->sendSuccessResponse($url, 'Pay slip!');
    }
}

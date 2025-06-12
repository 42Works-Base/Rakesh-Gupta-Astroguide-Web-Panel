<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\AvailableTime;
use App\Models\ScheduleCall;
use App\Models\Transaction;
use App\Models\User;
use App\Models\KundaliDetail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Helpers\NotificationHelper;
use App\Helpers\AstrologyHelper;
use Illuminate\Http\Exceptions\HttpResponseException;


class CallAppointmentController extends BaseController
{


    public function index(Request $request)
    {
        $query = ScheduleCall::with(['availableTime', 'transactions' => function ($q) {
            $q->where('status', 'completed');
        }])
            ->whereHas('transactions', function ($q) {
                $q->where('status', 'completed');
            });

        if ($request->filled('call_date')) {
            $query->whereHas('availableTime', function ($q) use ($request) {
                $q->whereDate('date', $request->call_date);
            });
        }

        $callAppointments = $query
            ->orderByDesc(
                AvailableTime::select('date')
                    ->whereColumn('available_times.id', 'schedule_calls.available_time_id')
                    ->limit(1)
            )
            ->orderByDesc(
                AvailableTime::select('start_time')
                    ->whereColumn('available_times.id', 'schedule_calls.available_time_id')
                    ->limit(1)
            )
            ->get();

        return view('admin.call_appointment.index', compact('callAppointments'));
    }







    public function details($schedule_call_id)
    {
        $callAppointment = ScheduleCall::with([
            'availableTime',    // Eager load available time
            'kundaliDetail',    // Eager load available time
            'transactions.user' // Eager load related transactions and users
        ])->findOrFail($schedule_call_id);


        return view('admin.call_appointment.details', compact('callAppointment'));
    }

    public function userChart($schedule_call_id)
    {
        $chartTypeArray = config('jyotishamastroapi.chart');
        // dd($chartTypeArray);
        $callAppointment = ScheduleCall::with([
            'user' // Eager load related transactions and users
        ])->findOrFail($schedule_call_id);

        $user = User::where('id', $callAppointment['user_id'])->where('role', 'user')->first();

        // Custome predictin start

        $lalKitabReport = '';

        $type = 'remedies';
        $lalLitabDataArray = AstrologyHelper::getLalKitabData($user, $type);
        $LalKitab_report = json_decode($lalLitabDataArray);


        if ($LalKitab_report->status == 200) {

            if ($LalKitab_report->response) {

                $lalKitabReport = [];

                foreach ($LalKitab_report->response as $key => $item) {
                    $lalKitabReport[] = [
                        'title' => $item->planet . ' ( घर :- '. $item->house.' )',
                        'text' => $item->effects,
                        'remedies' => isset($item->remedies) ? $item->remedies : [],
                    ];
                }
            }
        }



            // custome prdiction end

            $customPrediction = '';
        // $predictionType = 'ascendant';
        // $prediction = AstrologyHelper::getPrediction($user, $predictionType);
        // $prediction = json_decode($prediction);
        // if ($prediction->status == 200) {
        //     $rawPrediction = $prediction->response;

        //     if ($prediction->response) {
        //         $customPrediction = [

        //             [
        //                 'title' => 'Description',
        //                 'text' => $rawPrediction->explanation ?? '',
        //             ],
        //             [
        //                 'title' => 'Personality',
        //                 'text' => $rawPrediction->temp ?? '',
        //             ],
        //             [
        //                 'title' => 'Physical',
        //                 'text' => $rawPrediction->physical ?? '',
        //             ],
        //             [
        //                 'title' => 'Health',
        //                 'text' => $rawPrediction->health ?? '',
        //             ],

        //         ];
        //     }
        // }

        /////////////11111111111111/////////////////////////
        $customAscendantReport = '';
        // $type = 'ascendant-report';
        // $planetDataArray = AstrologyHelper::getPlanetDataDetails($user, $type);
        // $ascendant_report = json_decode($planetDataArray);

        // if ($ascendant_report->status == 200) {
        //     $rawAscendantReport = $ascendant_report->response[0];

        //     if ($ascendant_report->response) {
        //         $customAscendantReport = [

        //             [
        //                 // 'title' => 'General Prediction',
        //                 'title' => 'सामान्य पूर्वानुमान',
        //                 'text' => $rawAscendantReport->general_prediction ?? '',
        //             ],
        //             [
        //                 // 'title' => 'Personalised Prediction',
        //                 'title' => 'व्यक्तिगत पूर्वानुमान',
        //                 'text' => $rawAscendantReport->personalised_prediction ?? '',
        //             ],
        //             [
        //                 // 'title' => 'Verbal Location',
        //                 'title' => 'मौखिक स्थान / मौखिक विवरण',
        //                 'text' => $rawAscendantReport->verbal_location ?? '',
        //             ],
        //             [
        //                 // 'title' => 'Gayatri Mantra',
        //                 'title' => 'गायत्री मंत्र',
        //                 'text' => $rawAscendantReport->gayatri_mantra ?? '',
        //             ],
        //             [
        //                 // 'title' => 'Flagship Qualities',
        //                 'title' => 'प्रमुख गुण',
        //                 'text' => $rawAscendantReport->flagship_qualities ?? '',
        //             ],

        //             [
        //                 'title' => 'आध्यात्मिक सलाह',
        //                 'text' => $rawAscendantReport->spirituality_advice ?? '',
        //             ],

        //             [
        //                 // 'title' => 'Good Qualities',
        //                 'title' => 'अच्छे गुण',
        //                 'text' => $rawAscendantReport->good_qualities ?? '',
        //             ],

        //             [
        //                 'title' => 'बुरे गुण',
        //                 'text' => $rawAscendantReport->bad_qualities ?? '',
        //             ],

        //         ];
        //     }
        // }

        //////////////1111111111111111111////////////

        /////////////222222222222/////////////////////////
        $customNumerologyReport = '';

        // $prediction = AstrologyHelper::getPredictionNumerology($user);
        // $numerologyReport = json_decode($prediction);

        // if (isset($numerologyReport->error)) {
        //     throw new HttpResponseException(
        //         redirect()->back()->withInput()->with('error', $numerologyReport->error)
        //     );
        // }

        // if ($numerologyReport->status == 200) {

        //     if ($numerologyReport->response) {

        //         $customNumerologyReport = [];

        //         foreach ($numerologyReport->response as $key => $item) {

        //             $customNumerologyReport[] = [
        //                 'title' => $item->title,
        //                 'text' => $item->meaning,
        //             ];
        //         }
        //     }
        // }

        //////////////2222222222222222222222////////////



        return view('admin.call_appointment.userChart', compact('callAppointment', 'chartTypeArray', 'customPrediction', 'customAscendantReport', 'customNumerologyReport', 'lalKitabReport'));
    }

    public function userPredictions($schedule_call_id)
    {
        $predictionsTypeArray = config('jyotishamastroapi.predictions');
        // dd($chartTypeArray);
        $callAppointment = ScheduleCall::with([
            'user' // Eager load related transactions and users
        ])->findOrFail($schedule_call_id);

        $user = User::where('id', $callAppointment['user_id'])->where('role', 'user')->first();
        return view('admin.call_appointment.userPrediction', [
            'callAppointment' => $callAppointment,
            'predictionsTypeArray' => $predictionsTypeArray,
            'customPrediction' => null,
            'selectedPredictionType' => null,
            'customPredictionNew'=> null,
        ]);
    }

    public function handlePredictionType(Request $request, $schedule_call_id)
    {
        $request->validate([
            'predictions_type' => 'required|string'
        ]);
        $predictionType = $request->input('predictions_type');
        // dd($predictionType);

        $predictionsTypeArray = config('jyotishamastroapi.predictions');
        $callAppointment = ScheduleCall::with('user')->findOrFail($schedule_call_id);

        $user = $callAppointment->user;
        $customPrediction = [];
        $customPredictionNew = [];

        if($predictionType == 'ascendant_prediction'){

            $pType = 'ascendant';

            $prediction = AstrologyHelper::getPrediction($user, $pType);
            $prediction = json_decode($prediction);

            if (isset($prediction->error)) {
                throw new HttpResponseException(
                    redirect()->back()->withInput()->with('error', $prediction->error)
                );
            }

            if ($prediction && $prediction->status == 200 && $prediction->response) {
                $rawPrediction = $prediction->response;

                $customPrediction = [
                    ['title' => 'Description', 'text' => $rawPrediction->explanation ?? ''],
                    ['title' => 'Personality', 'text' => $rawPrediction->temp ?? ''],
                    ['title' => 'Physical', 'text' => $rawPrediction->physical ?? ''],
                    ['title' => 'Health', 'text' => $rawPrediction->health ?? ''],
                ];
            }
        }

        if ($predictionType == 'prediction_daily') {

            $day = 'today';
            $kundaliData = null;
            $kundaliData = KundaliDetail::select('rasi', 'ascendant_sign')->where('user_id', $user['id'])->first();

            $zodiacSigns = config('jyotishamastroapi.zodiac_signs');
            $zodicData = $zodiacSigns[strtolower($kundaliData['ascendant_sign'])];
            $zodiac = $zodicData['zodiac'];

            $prediction = AstrologyHelper::getHoroscopeData($zodiac, $day);
            $prediction = json_decode($prediction);

            if (isset($prediction->error)) {
                throw new HttpResponseException(
                    redirect()->back()->withInput()->with('error', $prediction->error)
                );
            }

            if ($prediction && $prediction->status == 200 && $prediction->response) {
                $rawPrediction = $prediction->response;
                $customPrediction = [
                    ['title' => 'Day - ' . $rawPrediction->date, 'text' =>  ''],
                    ['title' => 'Description', 'text' => $rawPrediction->horoscope_data ?? ''],
                ];
            }


            $day = 'daily';
            $zodiac = $kundaliData['ascendant_sign'];
            $predictionNew = AstrologyHelper::getHoroscopeDataNew($zodiac, $day);
            $predictionNew = json_decode($predictionNew);
            if ($predictionNew && $predictionNew->status == true && $predictionNew->prediction) {
                $rawPrediction = $predictionNew;
                $customPredictionNew = [
                    ['title' => 'Sign - ' . $rawPrediction->sun_sign, 'text' =>  ''],
                    ['title' => 'Day - ' . $rawPrediction->prediction_date, 'text' =>  ''],
                    ['title' => 'Description', 'text' => ''],
                    ['title' => 'Personal Life', 'text' => $rawPrediction->prediction->personal_life ?? ''],
                    ['title' => 'Profession', 'text' => $rawPrediction->prediction->profession ?? ''],
                    ['title' => 'Health', 'text' => $rawPrediction->prediction->health ?? ''],
                    ['title' => 'Emotions', 'text' => $rawPrediction->prediction->emotions ?? ''],
                    ['title' => 'travel', 'text' => $rawPrediction->prediction->travel ?? ''],
                    ['title' => 'Luck', 'text' => $rawPrediction->prediction->luck ?? ''],
                ];
            }
            // dd($customPredictionNew);
        }

        /* if ($predictionType == 'prediction_weekly') {

            if ($predictionType == 'prediction_weekly') {
                $pType = 'weekly';
            }


            $kundaliData = null;
            $kundaliData = KundaliDetail::select('rasi', 'ascendant_sign')->where('user_id', $user['id'])->first();

            $zodiacSigns = config('jyotishamastroapi.zodiac_signs');
            $zodicData = $zodiacSigns[strtolower($kundaliData['ascendant_sign'])];
            $zodiac = $zodicData['zodiac'];

            $prediction = AstrologyHelper::getHoroscopeWeekMonthsYearData($zodiac, $pType);
            $prediction = json_decode($prediction);

            if (isset($prediction->error)) {
                throw new HttpResponseException(
                    redirect()->back()->withInput()->with('error', $prediction->error)
                );
            }

            if ($prediction && $prediction->status == 200 && $prediction->response) {
                $rawPrediction = $prediction->response;

                $customPrediction = [
                    ['title' => 'Description', 'text' => $rawPrediction->explanation ?? ''],
                    ['title' => 'Personality', 'text' => $rawPrediction->temp ?? ''],
                    ['title' => 'Physical', 'text' => $rawPrediction->physical ?? ''],
                    ['title' => 'Health', 'text' => $rawPrediction->health ?? ''],
                ];
            }
        } */

        if ($predictionType == 'prediction_monthly') {

            $pType = 'monthly';

            $kundaliData = null;
            $kundaliData = KundaliDetail::select('rasi', 'ascendant_sign')->where('user_id', $user['id'])->first();

            $zodiacSigns = config('jyotishamastroapi.zodiac_signs');
            $zodicData = $zodiacSigns[strtolower($kundaliData['ascendant_sign'])];
            $zodiac = $zodicData['zodiac'];

            $prediction = AstrologyHelper::getHoroscopeWeekMonthsYearData($zodiac, $pType);
            $prediction = json_decode($prediction);

            if (isset($prediction->error)) {
                throw new HttpResponseException(
                    redirect()->back()->withInput()->with('error', $prediction->error)
                );
            }

            if ($prediction && $prediction->status == 200 && $prediction->response) {
                $rawPrediction = $prediction->response;
                $customPrediction = [
                    ['title' => 'Month - ' .$rawPrediction->month, 'text' =>  ''],
                    ['title' => 'Description', 'text' => $rawPrediction->horoscope_data ?? ''],
                ];
            }

            $day = 'monthly';
            $zodiac = $kundaliData['ascendant_sign'];
            $predictionNew = AstrologyHelper::getHoroscopeDataNewMonthly($zodiac, $day);
            $predictionNew = json_decode($predictionNew);
            // dd($predictionNew);
            if ($predictionNew && $predictionNew->status == true && $predictionNew->prediction) {
                $rawPrediction = $predictionNew;

                $customPredictionNew = [
                    ['title' => 'Sign - ' . $rawPrediction->sun_sign, 'text' => ''],
                    // ['title' => 'Month - ' . $rawPrediction->prediction_month, 'text' => ''],
                    ['title' => 'Description', 'text' => ''],
                ];

                if (!empty($rawPrediction->prediction) && is_array($rawPrediction->prediction)) {
                    foreach ($rawPrediction->prediction as $row) {
                        $customPredictionNew[] = [
                            'title' => '', // Optional formatting
                            'text' => $row ?? '',
                        ];
                    }
                }
            }
        }

        if ($predictionType == 'mangal_dosh' || $predictionType == 'kaalsarp_dosh') {

            if($predictionType == 'mangal_dosh'){
                $doshType = 'mangal_dosh';
            }elseif($predictionType == 'kaalsarp_dosh'){
                $doshType = 'kaalsarp-dosh';
            }




            $prediction = AstrologyHelper::getDoshData($user, $doshType);
            $prediction = json_decode($prediction);

            if (isset($prediction->error)) {
                throw new HttpResponseException(
                    redirect()->back()->withInput()->with('error', $prediction->error)
                );
            }

            if ($prediction && $prediction->status == 200 && $prediction->response) {
                $rawPrediction = $prediction->response;
                $customPrediction = [
                    ['title' => 'Description', 'text' => $rawPrediction->bot_response ?? ''],
                ];

                if ($predictionType == 'kaalsarp_dosh') {
                    $rawPredictionRemedies = $rawPrediction->remedies ?? null;

                    if ($rawPredictionRemedies) {
                        foreach ($rawPredictionRemedies as $key => $remedies) {
                            $customPrediction[] = [
                                'title' => '', // Optional formatting
                                'text' => $remedies ?? '',
                            ];
                        }
                    }
                }
            }
        }

        return view('admin.call_appointment.userPrediction', [
            'callAppointment' => $callAppointment,
            'predictionsTypeArray' => $predictionsTypeArray,
            'customPrediction' => $customPrediction,
            'customPredictionNew' => $customPredictionNew,
            'selectedPredictionType' => $predictionType,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $call = ScheduleCall::findOrFail($id);
        $call->is_call_completed = $request->is_call_completed;
        $call->save();

        return response()->json(['message' => 'Appointment status updated successfully!']);
    }

    public function markCallCancel(Request $request, $id)
    {
        $call = ScheduleCall::findOrFail($id);
        // $call->is_call_canceled = $request->is_call_canceled;
        $call->is_call_canceled = '1';
        $call->save();


        // notification start

        $astrologer = User::where('role', 'astrologer')->first();
        $user = User::where('id', $call['user_id'])->first();

        $availableTime = AvailableTime::find($call->available_time_id);

        $date = Carbon::parse($availableTime->date)->format('d-m-Y');
        $startTime =  Carbon::parse($availableTime->start_time)->format('h:i A');

        $data = ['schedule_calls_id' => $call['id']];
        $senderId = null;
        $receiverId = $call->user_id;
        $title = 'Cancel Consultation';
        $message = 'Your consultation on ' . $date . ' at ' . $startTime . ' has been canceled by Astrologer — refund will be processed soon.';

        $title_admin =  $user['first_name'] . ' ' . $user['last_name'] . '`s slot has canceled for ' . $date . ' at ' . $startTime . '!';
        $message_to_admin = $user['first_name'] . ' ' . $user['last_name'] . ' consultation has canceled for Consultation Type: ' . ucfirst($call['call_type']) . ' session.
                            Original Date & Time: ' . $date . ' at ' . $startTime . '
                            Please take note and update the schedule accordingly.';

        $type = "cancel_consultation_by_admin";

        $firebaseResponse = NotificationHelper::notifyUser($senderId, $receiverId, $title, $message, $title_admin, $message_to_admin, $type, $data);


        return response()->json(['message' => 'Appointment cancelled successfully!']);
    }

    // public function refund(Request $request, $id)
    // {
    //     $call = ScheduleCall::findOrFail($id);

    //     return response()->json(['message' => 'Refund initiated!']);
    // }

    // public function refund(Request $request, $id)
    // {
    //     $call = ScheduleCall::findOrFail($id);

    //     $paymentId = $call->payment_id; // e.g. pay_HHabcXYZ123456
    //     //pay_PE1i2E1p8HWQSi,pay_PE1eAJDW9t7GEf,pay_P4c0DHKzCKdBKs,pay_P5eGHOZwiNlN8x
    //     $paymentId = 'pay_PE1i2E1p8HWQSi';
    //     $key = config('services.razorpay.key');
    //     $secret = config('services.razorpay.secret');


    //     try {
    //         $url = "https://api.razorpay.com/v1/payments/{$paymentId}/refund";

    //         $response = Http::withBasicAuth($key, $secret)
    //             ->asForm() // Important: Razorpay accepts form-encoded data
    //             ->post($url);

    //         if ($response->successful()) {
    //             $refundData = $response->json();

    //             // Optionally save refund info
    //             $call->refund_status = 'completed';
    //             $call->refund_id = $refundData['id'] ?? null;
    //             $call->response_data = $refundData ?? null;
    //             $call->save();

    //             return response()->json([
    //                 'message' => 'Refund successful!',
    //                 'refund_id' => $refundData['id'] ?? null,
    //                 'refund_response' => $refundData
    //             ]);
    //         }

    //         return response()->json([
    //             'message' => 'Refund failed!',
    //             'error' => $response->json()
    //         ], $response->status());
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Refund failed!',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    /* public function refund(Request $request, $id)
    {
        $call = ScheduleCall::findOrFail($id);
        $paymentId = $call->payment_id;
        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');

        try {
            $url = "https://api.razorpay.com/v1/payments/{$paymentId}/refund";

            $response = Http::withBasicAuth($key, $secret)
                ->asForm()
                ->post($url);

            if ($response->successful()) {
                $refundData = $response->json();

                $call->refund_status = 'completed';
                $call->refund_id = $refundData['id'] ?? null;
                $call->response_data = $refundData ?? null;
                $call->save();

                return response()->json([
                    'message' => 'Refund successful!',
                    'refund_id' => $refundData['id'] ?? null,
                    'refund_response' => $refundData
                ]);
            } else {
                $error = $response->json()['error'] ?? ['description' => 'Unknown error'];

                // Detailed log with file and line number
                $logDetails = [
                    'message' => 'Refund API failed',
                    'file' => __FILE__,
                    'line' => __LINE__,
                    'payment_id' => $paymentId,
                    'error' => $error,
                    'timestamp' => now()->toDateTimeString(),
                ];

                Log::channel('razor')->error(json_encode($logDetails, JSON_PRETTY_PRINT));

                return response()->json([
                    'message' => 'Refund failed!',
                    'error' => $error['description'] ?? 'Unknown error'
                ], $response->status());
            }
        } catch (\Exception $e) {
            $logDetails = [
                'message' => 'Refund Exception',
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'error' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString(),
            ];

            Log::channel('razor')->error(json_encode($logDetails, JSON_PRETTY_PRINT));

            return response()->json([
                'message' => 'Refund failed!',
                'error' => $e->getMessage()
            ], 500);
        }
    } */

    public function refund(Request $request, $id)
    {
        $call = ScheduleCall::findOrFail($id);
        $transaction = Transaction::where('schedule_call_id', $call->id)->first();
        if(!$transaction){
            return response()->json(['message' => 'Payment capture failed', 'error' => 'Transaction not found.'], 400);
        }

        $user = User::where('id', $call->user_id)->first();
        if (!$user) {
            return response()->json(['message' => 'Payment capture failed', 'error' => 'User not found.'], 400);
        }

        $paymentId = $transaction->payment_id;

        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');

        try {
            // Step 1: Fetch Payment Details
            $paymentResponse = Http::withBasicAuth($key, $secret)
                ->get("https://api.razorpay.com/v1/payments/{$paymentId}");

            $paymentData = $paymentResponse->json();

            if (!isset($paymentData['status'])) {
                throw new \Exception("Unable to retrieve payment status.");
            }

            // Step 2: If authorized, capture the payment
            if ($paymentData['status'] === 'authorized') {
                $amountToCapture = $paymentData['amount']; // in paise

                $captureResponse = Http::withBasicAuth($key, $secret)
                    ->asForm()
                    ->post("https://api.razorpay.com/v1/payments/{$paymentId}/capture", [
                        'amount' => $amountToCapture,
                    ]);

                if (!$captureResponse->successful()) {
                    Log::channel('custom_razor')->error("Line " . __LINE__ . ": Capture Failed", [
                        'response' => $captureResponse->json(),
                    ]);
                    return response()->json(['message' => 'Payment capture failed', 'error' => $captureResponse->json()], 400);
                }
            }

            // Step 3: Attempt refund
            $refundResponse = Http::withBasicAuth($key, $secret)
                ->asForm()
                ->post("https://api.razorpay.com/v1/payments/{$paymentId}/refund");

            if ($refundResponse->successful()) {
                $refundData = $refundResponse->json();

                $call->refund_status = 'completed';
                $call->refund_id = $refundData['id'] ?? null;
                $call->response_data = $refundData;
                $call->save();

                return response()->json([
                    'message' => 'Refund successful!',
                    'refund_id' => $refundData['id'],
                    'refund_response' => $refundData
                ]);
            }

            Log::channel('custom_razor')->error("Line " . __LINE__ . ": Refund Failed", [
                'response' => $refundResponse->json(),
            ]);

            return response()->json(['message' => 'Refund failed!', 'error' => $refundResponse->json()], 400);
        } catch (\Exception $e) {
            Log::channel('custom_razor')->error("Line " . __LINE__ . ": Exception", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Exception occurred', 'error' => $e->getMessage()], 500);
        }
    }








    /*  public function redirectToWhatsApp($bookingId)
    {

        $bookedCall = ScheduleCall::find($bookingId);
        if (!$bookedCall) {
            return response()->json(['error' => 'Invalid booking Id.']);
        }

        $availableTime = AvailableTime::find($bookedCall->available_time_id);
        // dd($availableTime);
        $now = Carbon::now()->setTimezone(Config('app.timezone'));;


        // Validate date
        if ($now->toDateString() !== Carbon::parse($availableTime->date)->toDateString()) {
            return response()->json(['error' => 'Invalid date. Please check your booking.']);
        }

        // Check if link was already used
        if ($bookedCall->is_link_used) {
            return response()->json(['error' => 'This link has already been used.']);
        }

        $startTime = Carbon::parse($availableTime->start_time)->setTimezone(Config('app.timezone'));
        $endTime = Carbon::parse($availableTime->end_time)->setTimezone(Config('app.timezone'));

        // Check if user is clicking before the slot starts
        if ($now->lt($startTime)) {
            return response()->json([
                'error' => "Your call is scheduled for " . $startTime->format('h:i A') . ". Please wait until then."
            ]);
        }

        // Check if user is clicking after the slot ends
        if ($now->gt($endTime)) {
            return response()->json([
                'error' => 'Your call has ended. Please book a new slot.'
            ]);
        }

        // Mark link as used & redirect
        $bookedCall->update(['is_link_used' => '1']);
        return redirect($bookedCall->whatsapp_link);
    } */
}

<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\KundaliDetail;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AstrologyHelper;
use App\Helpers\NotificationHelper;

class DashboardController extends BaseController
{

    public function index(Request $request)
    {

        // $response = NotificationHelper::sendFCMNotificationV1(
        //     'e3ZFhU9fGkJevqcEWiPDLL:APA91bHNGmiAZRYRREEIJa1fD-IaDa88dqwX_tyodz2QPdeOgHFfo2oybGkC4lJOOWOHQBp_zRrRs0gZAFlbAVinv1_JdoziQDDKVNtebdcKfqCv6MpHjdo',
        //     "Booking Cancelled",
        //     "Your booking with ID #123 was cancelled.",
        //     ['booking_id' => '123']
        // );
        // $bookingDetails = ['booking_id' => '123'];
        // $senderId = '1';
        // $receiverId = '2';
        // $title = 'Booking Cancelled';
        // $message = 'User X has cancelled a booking.';
        // $type = "booking";
        // $data = $bookingDetails;
        // $firebaseResponse = NotificationHelper::notifyUser($senderId, $receiverId,  $title,  $message,   $type, $data);

        // dd($firebaseResponse);



        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $userId = $request->user_id;
        $user = User::where('id', $userId)->where('role', 'user')->first();

        if (!$user) {
            return $this->sendError('Invalid user ID. No such user found.');
        }
        // $chartType = 'd1';
        // $getChartData = $this->getChartData($user, $chartType);
        $kundaliDetail = KundaliDetail::where('user_id', $userId)->first();
        if (!$kundaliDetail) {
            $kundaliResponse = AstrologyHelper::getKundaliData($user);
            $kundaliDecoded = json_decode($kundaliResponse, true);

            if (isset($kundaliDecoded['response'])) {
                $data = $kundaliDecoded['response'];

                // Save new kundali data
                $kundaliDetail = KundaliDetail::create([
                    'user_id'             => $user->id,
                    'gana'                => $data['gana'] ?? null,
                    'yoni'                => $data['yoni'] ?? null,
                    'vasya'               => $data['vasya'] ?? null,
                    'nadi'                => $data['nadi'] ?? null,
                    'varna'               => $data['varna'] ?? null,
                    'paya'                => $data['paya'] ?? null,
                    'tatva'               => $data['tatva'] ?? null,
                    'life_stone'          => $data['life_stone'] ?? null,
                    'lucky_stone'         => $data['lucky_stone'] ?? null,
                    'fortune_stone'       => $data['fortune_stone'] ?? null,
                    'name_start'          => $data['name_start'] ?? null,
                    'ascendant_sign'      => $data['ascendant_sign'] ?? null,
                    'ascendant_nakshatra' => $data['ascendant_nakshatra'] ?? null,
                    'rasi'                => $data['rasi'] ?? null,
                    'rasi_lord'           => $data['rasi_lord'] ?? null,
                    'nakshatra'           => $data['nakshatra'] ?? null,
                    'nakshatra_lord'      => $data['nakshatra_lord'] ?? null,
                    'nakshatra_pada'      => $data['nakshatra_pada'] ?? null,
                    'sun_sign'            => $data['sun_sign'] ?? null,
                    'tithi'               => $data['tithi'] ?? null,
                    'karana'              => $data['karana'] ?? null,
                    'yoga'                => $data['yoga'] ?? null,
                ]);
            }
        }

        $kundaliData = null;
        $kundaliData = KundaliDetail::select('rasi', 'ascendant_sign')->where('user_id', $userId)->first();
        $zodiacSigns = config('jyotishamastroapi.zodiac_signs');
        // $zodicData = $zodiacSigns[strtolower($kundaliData['rasi'])];
        $zodicData = $zodiacSigns[strtolower($kundaliData['ascendant_sign'])];

        $image = url($zodicData['image']);
        $zodiac = $zodicData['zodiac'];
        // dd($image, $zodiac);

        $kundaliData['rasi'] =  $kundaliData['ascendant_sign']; // to over ride the data, actually we sending data in rasi key.
        $kundaliData['image'] =  $image;

        $day = 'today';
        $getChartData = AstrologyHelper::getHoroscopeData($zodiac, $day);
        $data = json_decode($getChartData);

        if (isset($data->message)) {
            return $this->sendError($data->message);
        }

        if (isset($data->error)) {
            return $this->sendError($data->error);
        }
        $horoscopeData = null;
        // $horoscopeData = 'Dummy text used for testing — not calling the API due to API limit being hit.';
        if ($data->status === 200) {
            $horoscopeData = $data->response->horoscope_data;
        } else {
            $horoscopeData = null;
        }
        $astrologer = User::where('role', 'astrologer')->first();


        $adminSpecialties = ['Prashana', 'Vastu', 'Vedic'];

        $remediesImage = url('storage/horoscopeData/remedies.png');
        $KundliImage = url('storage/horoscopeData/kundli.png');

        $astrologydata = [
            [
                'name' => 'General remedies',
                'image' => $remediesImage,
            ],
            [
                'name' => 'Kundli',
                'image' => $KundliImage
            ]
        ];
        $adminDetails =  [

            'first_name' => $astrologer->first_name,
            'last_name' => $astrologer->last_name,
            'email' => $astrologer->email,
            'phone' => $astrologer->phone,
            // 'country_code' => $astrologer->country_code,
            'gender' => $astrologer->gender,
            'dob' => $astrologer->dob,
            'dob_time' => $astrologer->dob_time,
            'birth_city' => $astrologer->birth_city,
            'birthplace_country' => $astrologer->birthplace_country,
            'full_address' => $astrologer->full_address,
            // 'latitude' => $astrologer->latitude,
            // 'longitude' => $astrologer->longitude,
            // 'timezone' => $astrologer->timezone,
            // 'is_verified' => $astrologer->is_verified,
            // 'status' => $astrologer->status,
            'profile_picture' => $astrologer->profile_picture ? url($astrologer->profile_picture) : null,
            // 'created_at' => $astrologer->created_at,
            // 'updated_at' => $astrologer->updated_at,
            'language' => 'Hindi and English',
            'price' => '2100',
            'duration' => '30 mins',
            'specialties' => $adminSpecialties
        ];

        $data = [
            'adminDetails' => $adminDetails,
            'astrologydata' => $astrologydata,
            'kundaliData' => $kundaliData,
            'horoscopeData' => $horoscopeData,

        ];
        return $this->sendSuccessResponse($data, 'Home page data!');
    }

}

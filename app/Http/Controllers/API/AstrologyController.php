<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\AstrologyHelper;
use App\Models\GeneralContent;
use Illuminate\Support\Facades\Validator;

class AstrologyController extends BaseController
{

    public function getKundaliData(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $userId = $request->user_id;
        $user = User::where('id', $userId)->where('role', 'user')->first();

        if (!$user) {
            return $this->sendError('Invalid user ID. No such user found.');
        }
        $laganAscendantChart = '';

        // $predictionType = 'ascendant';
        // $prediction = AstrologyHelper::getPrediction($user, $predictionType);
        // $prediction = json_decode($prediction);
        // if($prediction->status == 200){
        //     $rawPrediction = $prediction->response;

        //     if($prediction->response){
        //         $customPrediction = [

        //                 [
        //                     'title' => 'Description',
        //                     'text' => $rawPrediction->explanation ?? '',
        //                 ],
        //                 [
        //                     'title' => 'Personality',
        //                     'text' => $rawPrediction->temp ?? '',
        //                 ],
        //                 [
        //                     'title' => 'Physical',
        //                     'text' => $rawPrediction->physical ?? '',
        //                 ],
        //                 [
        //                     'title' => 'Health',
        //                     'text' => $rawPrediction->health ?? '',
        //                 ],

        //         ];

        //     }

        // }

        // $lalKitabReport = '';

        // $type = 'remedies';
        // $lalLitabDataArray = AstrologyHelper::getLalKitabData($user, $type);
        // $LalKitab_report = json_decode($lalLitabDataArray);
        // if ($LalKitab_report->status == 200) {

        //     if ($LalKitab_report->response) {

        //         $lalKitabReport = [];

        //         foreach ($LalKitab_report->response as $key => $item) {
        //             $lalKitabReport[] = [
        //                 'title' => $item->planet . ' ( घर :- ' . $item->house . ' )',
        //                 'text' => $item->effects,
        //                 'remedies' => isset($item->remedies) ? $item->remedies : [],
        //             ];
        //         }
        //     }
        // }

        $data = [
            'prediction' => $lalKitabReport,
        ];


        $type = $request->type;

        if ($type == 'lagan_ascendant_chart') {

            // Chart=> D1,	Varga=>Rasi / Lagan Chart	Purpose=>The main birth chart
            $chartType = 'd1';
            $laganAscendantChart = AstrologyHelper::getChartData($user, $chartType);
            $laganAscendantChart = json_decode($laganAscendantChart);

            if (isset($laganAscendantChart->error)) {
                return $this->sendError($laganAscendantChart->error);
            }

            // $data = [
            //     'laganAscendantChart' => $laganAscendantChart,
            // ];

            $data['laganAscendantChart']  = $laganAscendantChart;
            return $this->sendSuccessResponse($data, 'Lagan page data!');
        }

        if ($type == 'chandra_chart') {

            $chartType = 'moon';
            $chandraChart = AstrologyHelper::getChartData($user, $chartType);
            $chandraChart = json_decode($chandraChart);

            if (isset($chandraChart->error)) {
                return $this->sendError($chandraChart->error);
            }

            // $data = [
            //     'chandraChart' => $chandraChart,
            // ];

            $data['chandraChart'] = $chandraChart;
            return $this->sendSuccessResponse($data, 'Chandra chart!');
        }

        if ($type == 'nirayana_sidereal_zodiac_chart') {

            $chartType = 'd9';
            $nirayanaSiderealZodiacChart = AstrologyHelper::getChartData($user, $chartType);
            $nirayanaSiderealZodiacChart = json_decode($nirayanaSiderealZodiacChart);

            if (isset($nirayanaSiderealZodiacChart->error)) {
                return $this->sendError($nirayanaSiderealZodiacChart->error);
            }

            // $data = [
            //     'nirayanaSiderealZodiacChart' => $nirayanaSiderealZodiacChart,
            // ];
            $data['nirayanaSiderealZodiacChart'] = $nirayanaSiderealZodiacChart;

            return $this->sendSuccessResponse($data, 'Chandra chart!');
        }
    }


    public function getRemediesData(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $userId = $request->user_id;
        $user = User::where('id', $userId)->where('role', 'user')->first();

        if (!$user) {
            return $this->sendError('Invalid user ID. No such user found.');
        }
        $laganAscendantChart = '';
        $type = $request->type;

        $marriageImage = url('storage/horoscopeData/marriage.png');
        $relationshipImage = url('storage/horoscopeData/relationship.png');
        $educationImage = url('storage/horoscopeData/education.png');
        $carrerImage = url('storage/horoscopeData/carrer.jpg');
        $general_remedies = GeneralContent::where('sku', 'general-remedies')->first();
        $RemediesData = [
            [
                'name' => 'General Remedies',
                'image' => $carrerImage,
                /*                 'details'=> 'किस दिशा में वास्तु दोष हो तो उसका असर परिवार के किस सदस्य पर
                पड़ता है-
                1- उत्तर दिशा के वास्तु दोष का असर घर की महिलाओं
                एवं परिवार की आमदनी पर पड़ता है।
                2- ईशान कोण (उत्तर-पूर्व) के वास्तु दोष का प्रभाव घर के मालिक
                एवं वहां रहने वाले अन्य पुरुषों एवं उनकी संतानों पर
                पड़ता है। संतान में विशेषकर प्रथम पुत्र पर इसका प्रभाव
                ज्यादा पड़ता है।
                3- यदि पूर्व दिशा में वास्तु दोष है तो इसका असर
                भी संतान पर ही पड़ता है।
                4- आग्नेय कोण (दक्षिण-पूर्व) के वास्तु दोष का असर घर
                की स्त्रियों, बच्चों विशेषकर दूसरी संतान पर
                पड़ता है।
                5- दक्षिण दिशा के वास्तु दोष का प्रभाव घर
                की स्त्रियों पर विशेष रूप से पड़ता है।
                6- नैऋत्य कोण (पश्चिम-दक्षिण) के वास्तु दोष का असर परिवार के
                मुखिया व उनकी पत्नी एवं बड़े पुत्र पर
                पड़ता है।
                7- यदि पश्चिम दिशा में किसी प्रकार का वास्तु दोष है
                तो इसका इफेक्ट घर के पुरुषों पर पड़ता है।
                8- वायव्य कोण (पश्चिम-उत्तर) के वास्तु दोष का प्रभाव घर
                की महिलाओं एवं तीसरी संतान
                पर पड़ता है।' */

                'details' => $general_remedies['content'],

            ],
            // [
            //     'name' => 'Marriage',
            //     'image' => $marriageImage,
            // ],
            // [
            //     'name' => 'Relationship',
            //     'image' => $relationshipImage
            // ],
            // [
            //     'name' => 'Education',
            //     'image' => $educationImage
            // ],

        ];

        return $this->sendSuccessResponse($RemediesData, 'Remedies page data!');
    }


    public function getPlanetData(Request $request)
    {


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


        // $planetDataArray = '';

        // $type = 'planet-details';
        // $planetDataArray = AstrologyHelper::getPlanetDataDetails($user, $type);
        // $planetDataArray = json_decode($planetDataArray);


        // if (isset($data->error)) {
        //     return $this->sendError($data->error);
        // }



        $zodiacSigns = config('jyotishamastroapi.zodiac_signs');

        $planet = [];
        foreach ($zodiacSigns as $key => $sign) {
            $sign['key'] = $key;
            $sign['image'] = url($sign['image']);

            // Directly append each sign's data as an array with its key as the array key
            $planet[] = [
                'key' => $key,
                'name' => $sign['name'],
                'zodiac' => $sign['zodiac'],
                'image' => $sign['image'],
                'date_range' => $sign['date_range'],
                'element' => $sign['element'],
            ];
        }

        $data = [
            'planet' => $planet,
        ];

        return $this->sendSuccessResponse($data, 'Planet page data!');
    }

    public function getPlanetDetails(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }



        $day = 'today';
        $key = $request->key;

        $zodiacSigns = config('jyotishamastroapi.zodiac_signs');
        $zodicData = $zodiacSigns[$key];
        $zodiac = $zodicData['zodiac'];
        $heading = $zodicData['name'];
        $image = url($zodicData['image']);
        $date_range = $zodicData['date_range'];

        $getHoroscope = AstrologyHelper::getHoroscopeData($zodiac, $day);
        $data = json_decode($getHoroscope);

        if (isset($data->message)) {
            return $this->sendError($data->message);
        }
        if (isset($data->error)) {
            return $this->sendError($data->error);
        }

        $horoscopeData = null;
        //$horoscopeData = 'Dummy text used for testing — not calling the API due to API limit being hit.';
        if ($data->status === 200) {
            $horoscopeData = $data->response->horoscope_data;
        } else {
            $horoscopeData = null;
        }

        $data = [
            'heading' => $heading . " Today's Horoscope",
            'para' => $horoscopeData,
            'image' => $image,
            'date_range' => $date_range
        ];
        return $this->sendSuccessResponse($data, 'Planet details data!');
    }
}

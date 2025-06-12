<?php

namespace App\Helpers;

use Carbon\Carbon;

class AstrologyHelper
{

    // api used from https://www.jyotishamastroapi.com/
    public static function getChartData($user, $chartType)
    {

        $apiKey = config('jyotishamastroapi.jyotishamastroapi_key');
        $apiBaseUrl = 'api.jyotishamastroapi.com/api/chart_image';
        // dd($apiKey);
        // $date = $user->dob;
        // $date = str_replace('-', '/', $user->dob);
        $date = Carbon::parse($user->dob)->format('d/m/Y');
        $dob_time = date('H:i', strtotime($user->dob_time));
        $latitude = intval($user->latitude);
        $longitude = intval($user->longitude);
        $timezone = self::convertToDecimalTimezone($user->timezone);
        $lang = 'en';
        $style = 'north';

        $url = $apiBaseUrl . '/' . $chartType .
            '?date=' . $date .
            '&time=' . $dob_time .
            '&latitude=' . $latitude .
            '&longitude=' . $longitude .
            '&tz=' . $timezone .
            '&style=' . $style .
            '&lang=' . $lang .
            '&colored_planets=true&color=%23657798';

        // dd($url);
        // cURL setup
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'key: ' . $apiKey
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public static function getHoroscopeData($zodiac, $day)
    {
        $apiKey = config('jyotishamastroapi.jyotishamastroapi_key');
        $apiBaseUrl = 'api.jyotishamastroapi.com/api/prediction/daily';

        $day = $day;
        $zodiac = $zodiac;
        $lang = 'en';
        // $style = 'north';

        $url = $apiBaseUrl .
            '?zodiac=' . $zodiac .
            '&day=' . $day .
            '&lang=' . $lang;

        // dd($url);
        // cURL setup
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'key: ' . $apiKey
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public static function getChartDataNew($user, $chartType)
    {
        $chartId = '';
        if($chartType == 'd1'){
            $chartId = 'D1';
        }elseif($chartType == 'd9'){
            $chartId = 'D9';
        }elseif($chartType == 'moon'){
            $chartId = 'MOON';
        }

        $apiUrl = 'https://json.astrologyapi.com/v1/horo_chart_image/' . $chartId;
        // dd($apiUrl);

        // $latitude = intval($user->latitude);
        // $longitude = intval($user->longitude);
        $latitude = $user->latitude;
        $longitude = $user->longitude;
        $timezone = self::convertToDecimalTimezone($user->timezone);
        $lang = 'en';

        $dateObj = Carbon::parse($user->dob);
        $timeObj = Carbon::parse($user->dob_time); // optional if time is separate

        $payload = array(
            'day'   => (int) $dateObj->format('d'),
            'month' => (int) $dateObj->format('m'),
            'year'  => (int) $dateObj->format('Y'),
            'hour'  => (int) $timeObj->format('H'),
            'min'   => (int) $timeObj->format('i'),
            'lat'   => $latitude,
            'lon'   => $longitude,
            'tzone' => $timezone
        );

        // dd($payload);
        // $payload = array(
        //     'day' => 25,
        //     'month' => 12,
        //     'year' => 1988,
        //     'hour' => 4,
        //     'min' => 0,
        //     'lat' => 25.123,
        //     'lon' => 82.34,
        //     'tzone' => 5.5
        // );

        $username = '641311';
        $password = 'c3c0900ec0d8492217132257bff751cc7073ff3c';

        $auth = base64_encode($username . ':' . $password);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // Use POST method
        curl_setopt($ch, CURLOPT_POSTFIELDS,  json_encode($payload)); // Send empty body

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $auth,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public static function getHoroscopeDataNew($zodiac, $day)
    {
        $apiUrl = 'https://json.astrologyapi.com/v1/sun_sign_prediction/' . $day . '/' . $zodiac;

        $username = '641272';
        $password = '22da1d76ab26fe35c2196db1e067a0d7b5058296';

        $auth = base64_encode($username . ':' . $password);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // Use POST method
        curl_setopt($ch, CURLOPT_POSTFIELDS, ""); // Send empty body

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $auth,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public static function getHoroscopeDataNewMonthly($zodiac, $day)
    {
        $apiUrl = 'https://json.astrologyapi.com/v1/horoscope_prediction/' . $day . '/' . $zodiac;

        $username = '641272';
        $password = '22da1d76ab26fe35c2196db1e067a0d7b5058296';

        $auth = base64_encode($username . ':' . $password);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // Use POST method
        curl_setopt($ch, CURLOPT_POSTFIELDS, ""); // Send empty body

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $auth,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }


    public static function getPlanetDataDetails($user, $type)
    {
        $apiKey = config('jyotishamastroapi.jyotishamastroapi_key');
        $apiBaseUrl = 'api.jyotishamastroapi.com/api/horoscope';
        // dd($apiKey);
        // $date = $user->dob;
        // $date = str_replace('-', '/', $user->dob);
        $date = Carbon::parse($user->dob)->format('d/m/Y');
        $dob_time = date('H:i', strtotime($user->dob_time));
        $latitude = intval($user->latitude);
        $longitude = intval($user->longitude);
        $timezone = self::convertToDecimalTimezone($user->timezone);
        $lang = 'hi';
        $style = 'north';

        $url = $apiBaseUrl . '/' . $type .
            '?date=' . $date .
            '&time=' . $dob_time .
            '&latitude=' . $latitude .
            '&longitude=' . $longitude .
            '&tz=' . $timezone .
            '&style=' . $style .
            '&lang=' . $lang .
            '&colored_planets=true&color=%23657798';

        // dd($url);
        // cURL setup
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'key: ' . $apiKey
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public static function getKundaliData($user)
    {
        $type = 'extended_kundali';
        $apiKey = config('jyotishamastroapi.jyotishamastroapi_key');
        $apiBaseUrl = 'api.jyotishamastroapi.com/api/extended_horoscope';
        // dd($apiKey);
        // $date = $user->dob;
        // $date = str_replace('-', '/', $user->dob);
        $date = Carbon::parse($user->dob)->format('d/m/Y');
        $dob_time = date('H:i', strtotime($user->dob_time));
        $latitude = intval($user->latitude);
        $longitude = intval($user->longitude);
        $timezone = self::convertToDecimalTimezone($user->timezone);
        $lang = 'en';

        $url = $apiBaseUrl . '/' . $type .
            '?date=' . $date .
            '&time=' . $dob_time .
            '&latitude=' . $latitude .
            '&longitude=' . $longitude .
            '&tz=' . $timezone .
            '&lang=' . $lang;

        // dd($url);
        // cURL setup
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'key: ' . $apiKey
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public static function convertToDecimalTimezone($timezone)
    {
        // Match the format: +HH:MM or -HH:MM
        if (preg_match('/^([+-])(\d{2}):(\d{2})$/', $timezone, $matches)) {
            $sign = $matches[1];
            $hours = (int)$matches[2];
            $minutes = (int)$matches[3];

            // Convert minutes to decimal
            $decimal = $hours + ($minutes / 60);

            // Apply negative sign if needed, otherwise return as positive number
            return $sign === '-' ? -$decimal : $decimal;
        }

        return null; // Invalid format
    }


    public static function getPrediction($user, $predictionType)
    {

        $apiKey = config('jyotishamastroapi.jyotishamastroapi_key');
        $apiBaseUrl = 'api.jyotishamastroapi.com/api/prediction';
        // dd($apiKey);
        $date = Carbon::parse($user->dob)->format('d/m/Y');
        $dob_time = date('H:i', strtotime($user->dob_time));
        $latitude = intval($user->latitude);
        $longitude = intval($user->longitude);
        $timezone = self::convertToDecimalTimezone($user->timezone);
        $lang = 'en';

        $url = $apiBaseUrl . '/' . $predictionType .
            '?date=' . $date .
            '&time=' . $dob_time .
            '&latitude=' . $latitude .
            '&longitude=' . $longitude .
            '&tz=' . $timezone .
            '&lang=' . $lang ;

        // dd($url);
        // cURL setup
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'key: ' . $apiKey
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public static function getHoroscopeWeekMonthsYearData($zodiac, $type)
    {
        $apiKey = config('jyotishamastroapi.jyotishamastroapi_key');



        $apiBaseUrl = 'api.jyotishamastroapi.com/api/prediction/'. $type;

        $zodiac = $zodiac;
        $lang = 'en';
        // $style = 'north';

        if ($type == 'weekly' || $type == 'monthly') {
            $url = $apiBaseUrl .
                '?zodiac=' . $zodiac .
                '&lang=' . $lang;
        }



        // dd($url);
        // cURL setup
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'key: ' . $apiKey
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public static function getDoshData($user, $doshType)
    {

        $apiKey = config('jyotishamastroapi.jyotishamastroapi_key');
        $apiBaseUrl = 'api.jyotishamastroapi.com/api/dosha';
        // dd($apiKey);
        $date = Carbon::parse($user->dob)->format('d/m/Y');
        $dob_time = date('H:i', strtotime($user->dob_time));
        $latitude = intval($user->latitude);
        $longitude = intval($user->longitude);
        $timezone = self::convertToDecimalTimezone($user->timezone);
        $lang = 'en';

        $url = $apiBaseUrl . '/' . $doshType .
            '?date=' . $date .
            '&time=' . $dob_time .
            '&latitude=' . $latitude .
            '&longitude=' . $longitude .
            '&tz=' . $timezone .
            '&lang=' . $lang;

        // dd($url);
        // cURL setup
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'key: ' . $apiKey
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public static function getPredictionNumerology($user)
    {

        $apiKey = config('jyotishamastroapi.jyotishamastroapi_key');
        $apiBaseUrl = 'api.jyotishamastroapi.com/api/prediction';
        // dd($apiKey);
        $date = Carbon::parse($user->dob)->format('d/m/Y');
        $dob_time = date('H:i', strtotime($user->dob_time));
        $latitude = intval($user->latitude);
        $longitude = intval($user->longitude);
        $timezone = self::convertToDecimalTimezone($user->timezone);
        $name = $user->first_name;
        $lang = 'hi';

        $url = $apiBaseUrl . '/numerology'.
            '?date=' . $date .
            '&name=' . $name .
            '&lang=' . $lang;

        // dd($url);
        // cURL setup
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'key: ' . $apiKey
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public static function getLalKitabData($user, $type)
    {
        $apiKey = config('jyotishamastroapi.jyotishamastroapi_key');
        $apiBaseUrl = 'api.jyotishamastroapi.com/api/lalKitab';
        // dd($apiKey);
        // $date = $user->dob;
        // $date = str_replace('-', '/', $user->dob);
        $date = Carbon::parse($user->dob)->format('d/m/Y');
        $dob_time = date('H:i', strtotime($user->dob_time));
        $latitude = intval($user->latitude);
        $longitude = intval($user->longitude);
        $timezone = self::convertToDecimalTimezone($user->timezone);
        $lang = 'hi';
        $style = 'north';
        $url = $apiBaseUrl . '/' . $type .
            '?date=' . $date .
            '&time=' . $dob_time .
            '&latitude=' . $latitude .
            '&longitude=' . $longitude .
            '&tz=' . $timezone .
            // '&style=' . $style .
            '&lang=' . $lang;

        // dd($url);
        // cURL setup
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'key: ' . $apiKey
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}

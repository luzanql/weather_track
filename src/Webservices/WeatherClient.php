<?php

namespace App\Webservices;
use Psr\Http\Message\ResponseInterface as Response;

use function PHPSTORM_META\type;

class WeatherClient
{
    /**
     * @var string
     *
     */
    private $base_uri = 'https://community-open-weather-map.p.rapidapi.com';
    /**
     * @var string
     */

    /**
     *
     */
    public function getRequest(string $end_point, string $params): string
    {

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "{$this->base_uri}/{$end_point}?{$params}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "X-RapidAPI-Host: {$_ENV['RAPID_API_HOST']}",
                "X-RapidAPI-Key: {$_ENV['RAPID_API_KEY']}"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            return "ERROR={$err}";
        } else {
            return $response;
        }
    }

}
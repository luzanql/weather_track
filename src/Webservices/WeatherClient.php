<?php

namespace App\Webservices;
use Psr\Http\Message\ResponseInterface as Response;
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
     * Perform a merge request to the weather Api using curl
     */
    public function getRequest(string $end_point, string $params): array
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
            return ['error' => true, 'message' => json_decode($err, true)];
        } else {
            return ['data' => json_decode($response, true)];
        }
    }

    /**
     * Write a response
     *
     * @param array $call_response
     * @param Response $response
     *
     * @return void
     */
    public function writeReponse(array $call_response, Response $response): Response
    {
        if (isset($client_response['error']) &&  $client_response['error']) {
            $response->getBody()->write($client_response['message']);
            return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(500);
        } else {
            $response->getBody()->write(json_encode($call_response));
            return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
        }
    }

}
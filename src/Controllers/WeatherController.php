<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Webservices\WeatherClient as WeatherClient;


class WeatherController
{
    /**
     * Calls Weather api and store the request call
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getWeather(Request $request, Response $response, array $args): Response
    {
        $query_string = $request->getQueryParams();
        if (!$query_string['q']) {
            $response->getBody()->write("Not found");
            return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
        }

        $end_point = "weather";

        // The only param required and allowed for this API is the location.
        $params = "q={$query_string['q']}&units=metric";

        $weather_client = new WeatherClient();

         // Perform the request
        $client_response = $weather_client->getRequest($end_point, $params);

        if (str_starts_with($client_response, "ERROR=")) {
            $response->getBody()->write($client_response);
            return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(500);
        }
        $response->getBody()->write($client_response);
        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
    }
}
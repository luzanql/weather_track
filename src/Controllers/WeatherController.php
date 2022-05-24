<?php

declare(strict_types=1);

namespace App\Controllers;
use App\Models\RequestLog;
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
        $response = $weather_client->writeReponse($client_response, $response);
        // Save Request for Historical purpose
        $token = $request->getAttribute("jwt");
        $this->logRequest($token['id'], $client_response['data']['main']);

        return $response;
    }

    /**
    * Get a user historical data
    *
    * @param Request $request
    * @param Response $response
    * @param array $args
    * @return Response
    */
    public function getHistory(Request $request, Response $response, array $args): Response
    {
        $token  = $request->getAttribute("jwt");
        $page   = $request->getQueryParams()['page'] ?? 1;
        $page   = (intval($page) && $page > 0) ? $page : 1;
        $limit  = 10; // Number of history records on one page
        $skip   = ($page - 1) * $limit;
        $count  = RequestLog::where('user_id', $token['id'])->count();

        $history = RequestLog::where('user_id', $token['id'])
                    ->orderByDesc('created_at')
                    ->skip($skip)->take($limit)
                    ->get();

        $formatted_response = [
            "page_number" => $page,
            "page_size"   => $limit,
            "total_record_count"=> $count,
            "records" => []
        ];

        foreach($history as $entry) {
            $formatted_response['records'][] = array_merge(
                    ['date' => $entry['created_at']],
                    json_decode($entry['data'], true)
                );
        }
        $response->getBody()->write(json_encode($formatted_response));
        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
    }

    /**
     * Log request for historical purposes
     *
     * @param int $user_id
     * @param array $weather_data
     *
     * @return void
     */
    private function logRequest(int $user_id, array $weather_data): RequestLog
    {
        $log_data = [
            "user_id" => $user_id,
            "data"    => json_encode($weather_data)
        ];

        $log = new RequestLog($log_data);
        $log->save();

        return $log;
    }

}
<?php

namespace App\action\ws;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface as Response;

class StooqClientAction
{
    /**
     * @var string
     *
     */
    private $base_uri = 'https://stooq.com/';

    /**
     * @var GuzzleHttp\Client
     *
     */
    private $client = null;

    /**
     * StooqClientAction constructor. Instantiates Guzzle Client
     *
     */
    public function __construct()
    {
        if (!$this->client) {
            $this->client = new Client(
                [
                    'base_uri' => $this->base_uri
                ]
            );
        }
    }

    /**
     * Call a get request method to stooq api given uri and params.
     *
     * @param array $params
     * @param string $uri
     *
     * @return Response
     */
    public function getRequest(array $params, string $uri = 'q/l/'): Response
    {
        return $this->client->request('GET', $uri, $params);
    }

    /**
     * Format stock data response as associative array
     *
     * @param string $string_response separted by comma:
     * input format: sd2t2ohlcvn separated by coma, no header
     *
     * @return array
     */
    public function formatResponse(string $string_response): array
    {
        $response_array = explode(',', $string_response);
        return [
            "name"   => trim($response_array[8] ?? 'N/A'),
            "symbol" => $response_array[0] || !empty($response_array[0]) ? $response_array[0] : 'N/A',
            "open"   => $response_array[3] ?? 'N/A',
            "high"   => $response_array[4] ?? 'N/A',
            "low"    => $response_array[5] ?? 'N/A',
            "close"  => $response_array[6] ?? 'N/A',
        ];

    }
}


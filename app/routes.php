<?php

declare(strict_types=1);

use App\Controllers\HelloController;
use App\Controllers\AuthController;
use App\Controllers\WeatherController;
use App\CurrencyController;
use Slim\App;
use Slim\Exception\HttpUnauthorizedException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;



return function (App $app)
{
    $app->get('/hello/{name}', HelloController::class . ':hello');

    // User routes

    // Sign up user
    $app->post('/api/v1/user/create', AuthController::class . ':store');
    // Sign in a User
    $app->post('/api/v1/user/signin', AuthController::class . ':signin');

    // Manage Stock
    $app->get('/api/v1/weather', WeatherController::class . ':getWeather');

    // 2nd middleware to throw 401 with correct slim exception
    // Reformat when lin updates to v4, see: https://github.com/tuupola/slim-basic-auth/issues/95
    $app->add(function (Request $request, RequestHandler $handler) {
        $response = $handler->handle($request);
        $statusCode = $response->getStatusCode();

        if ($statusCode == 401) {
            throw new HttpUnauthorizedException($request);
        }

        return $response;
    });


};

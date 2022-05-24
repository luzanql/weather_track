### Weather Track API
This project is a REST API application in which a user can sign up and use it to track the historical data of its weather requests. This Project used the Open Weather Map API https://rapidapi.com/community/api/open-weather-map/
(WIP): A user can add mood  and a note to the request logs. This will be in order to collect data of how they feel
(Maybe in the future, users can realizes gray days are vibrant than sunny days)
## Requirements
`Weather Track API` uses at least PHP 7.4.28, and a Conexion to MySQL

## Installation
* Download last changes aquiceno/weather_track
* run # composer install
* Initiate MySQl
* Run SQL script `scripts.sql` to  create the tables: `users` and `request_logs`.
* Modify .env according to your configuration. There is a .env.sample to guide you.
* Run composer start


## Dependencies
Additional third-party libraries were used in order to achieve certain goals. Third-party installed were carefully chosen
taking account: community size, security, popularity and maintenance:

These libraries included in composer.json file are:

* illuminate/database "~5.1"
* tuupola/slim-jwt-auth
* vlucas/valitron
* slim/twig-view
* zircote/swagger-php

## Usage
Api documentation was generate using Swagger. To update documentation according to doc annotation please run at the root directory
 `./vendor/bin/openapi --output app/resources/weather_track.yaml ./src/`

To display documentation please navigate to route:
 `http://localhost:8080/docs/v1`

## Postman Collection
A postman collection is included in the project.

You can import weather_track.postman_collection.json file in postman.

`{{host}}` is local environment variable: http://localhost:8080

To start using it:

* Please first create a user according to documentation (name, password, and email)
* Use Signin endpoint to get token
* Copy and paste token in the /stock endpoint as the Autorization Bearer Token and Send Request
* Copy and paste token in the /history endpoint as the Autorization Bearer Token and Send Request


## Unit test
Run `composer test` to see the test suite result
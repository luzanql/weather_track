<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager AS Capsule;



return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
            'db' => function() {
                $capsule = New Capsule();
                $capsule->addConnection([
                    'driver'    => $_ENV['DB_CONNECTION'],
                    'host'      => $_ENV['DB_HOST'],
                    'database'  => $_ENV['DB_DATABASE'],
                    'username'  => $_ENV['DB_USERNAME'],
                    'password'  => $_ENV['DB_PASSWORD'],
                    'port'      => intval($_ENV['DB_PORT'])
                ]);

                $capsule->setAsGlobal();
                $capsule->bootEloquent();
                return $capsule;
            }
        ],
        [
            'view' => function() {
                return \Slim\Views\Twig::create('templates');
            }
        ]
    );

};

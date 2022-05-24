<?php

namespace App\action\docs;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\Yaml\Yaml;
use Psr\Container\ContainerInterface;


final class SwaggerUiAction
{
    /**
     * @var Twig
     */
    private $twig;

    public function __construct(ContainerInterface $container)
    {
        $this->twig = $container->get('view');;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        // Path to the yaml file
        $yamlFile = '../app/resources/weather_track.yaml';

        $viewData = [
            'spec' =>json_encode(Yaml::parseFile($yamlFile)),
        ];

        return $this->twig->render($response, 'docs/swagger.twig', $viewData);
    }
}

<?php

namespace App\Modules\Open\Home\Infrastructure\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as SlimResponse;

final readonly class HomeController
{
    public function __invoke(Request $httpRequest): Response
    {
        $response = new SlimResponse();
        $response->getBody()->write("im home");
        return $response;
    }
}
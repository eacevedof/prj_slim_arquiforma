<?php

namespace App\Modules\Open\Home\Infrastructure\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Psr7\Response as SlimResponse;

use App\Modules\Open\Home\Application\GetHomePage\GetHomePageService;

final readonly class HomeController
{
    public function __construct(
        private GetHomePageService $getHomePageService
    )
    {

    }

    public function __invoke(Request $httpRequest): Response
    {
        $queryParams = $httpRequest->getQueryParams();
        $response = new SlimResponse();
        $response->getBody()->write(
            json_encode($this->getHomePageService->__invoke())
        );
        return $response;
    }
}
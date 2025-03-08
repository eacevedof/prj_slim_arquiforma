<?php

namespace App\Modules\Open\Home\Infrastructure\Controllers;

use App\Modules\Open\Home\Application\GetHomePage\GetHomePageService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final readonly class HomeController
{
    public function __construct(
        private GetHomePageService $getHomePageService,
        private Twig $twig
    )
    {
    }

    public function __invoke(Request $httpRequest, Response $response): Response
    {
        $data = $this->getHomePageService->__invoke();
        return $this->twig->render(
            $response,
            "home.twig",
            $data
        );
    }
}
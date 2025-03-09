<?php

namespace App\Modules\Open\Home\Infrastructure\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as SlimResponse;

use App\Modules\Shared\Infrastructure\Components\TplReader;

use App\Modules\Open\Home\Application\GetHomePage\GetHomePageService;

final readonly class HomeController
{
    public function __construct(
        private GetHomePageService $getHomePageService,
        private TplReader $tplReader
    )
    {
        $this->tplReader->setViewFolderByController(HomeController::class);
    }

    public function __invoke(Request $httpRequest): Response
    {
        $data = $this->getHomePageService->__invoke();
        $response = new SlimResponse();
        $response->getBody()->write(
            $this->tplReader->getFileContent('home', ['data' => $data])
        );
        return $response;
    }
}
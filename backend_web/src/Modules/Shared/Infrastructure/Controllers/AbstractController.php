<?php

namespace App\Modules\Shared\Infrastructure\Controllers;

use App\Modules\Shared\Infrastructure\Components\TplReader;

use Slim\Psr7\Response as SlimResponse;

abstract class AbstractController
{
    protected TplReader $tplReader;

    protected function renderView(string $view, array $data = []): SlimResponse
    {
        $response = new SlimResponse();
        $response->getBody()->write(
            $this->tplReader->getFileContent($view, $data)
        );
        return $response;
    }
}
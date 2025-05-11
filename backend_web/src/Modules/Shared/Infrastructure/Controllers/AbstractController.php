<?php

namespace App\Modules\Shared\Infrastructure\Controllers;

use App\Modules\Shared\Infrastructure\Components\TplReader;

use Slim\Psr7\Response as SlimResponse;

use App\Modules\Shared\Infrastructure\Enums\ResponseCodeEnum;
use App\Modules\Shared\Infrastructure\Components\Sessioner;

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

    protected function redirect(string $locationUrl): SlimResponse
    {
        return (new SlimResponse())->withHeader("Location", $locationUrl)
            ->withStatus(ResponseCodeEnum::REDIRECTION);
    }

    protected function redirectWithPayload(string $locationUrl, array $payload): SlimResponse
    {
        $sessioner = Sessioner::getInstance();
        foreach ($payload as $key => $value) {
            $sessioner->add($key, $value);
        }
        //dd("session", $_SESSION);
        return (new SlimResponse())->withHeader("Location", $locationUrl)
            ->withStatus(ResponseCodeEnum::REDIRECTION);
    }

}
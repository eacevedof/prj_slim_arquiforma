<?php

declare(strict_types=1);

use App\Slim\Infrastructure\Middlewares\HeadersMiddleware;
use App\Slim\Infrastructure\Middlewares\SessionMiddleware;
use Slim\App;

return function (App $app) {
    $app->add(SessionMiddleware::class);
    $app->add(HeadersMiddleware::class);
};

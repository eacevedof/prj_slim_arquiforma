<?php

declare(strict_types=1);

use Slim\App;
use App\Slim\Application\Middleware\SessionMiddleware;
use App\Slim\Infrastructure\Middlewares\HeadersMiddleware;

return function (App $app) {
    $app->add(SessionMiddleware::class);
    $app->add(HeadersMiddleware::class);
};

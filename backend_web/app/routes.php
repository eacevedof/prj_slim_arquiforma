<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

use App\Slim\Application\Actions\User\ListUsersAction;
use App\Slim\Application\Actions\User\ViewUserAction;

use App\Modules\Shared\Infrastructure\Enums\RouteEnum;
use App\Modules\Open\Home\Infrastructure\Controllers\HomeController;
use App\Modules\Open\Home\Infrastructure\Controllers\ContactController;

return function (App $app) {
    $app->options("/{routes:.*}", function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->post(RouteEnum::CONTACT->value, [ContactController::class, "sendMessage"]);
    $app->get(RouteEnum::HOME->value, HomeController::class);

    $app->group("/users", function (Group $group) {
        $group->get("", ListUsersAction::class);
        $group->get("/{id}", ViewUserAction::class);
    });

};

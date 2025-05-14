<?php

declare(strict_types=1);

namespace App\Slim\Infrastructure\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final readonly class HeadersMiddleware implements Middleware
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        return $response->withHeader(
            "Strict-Transport-Security",
            "max-age=31536000; includeSubDomains; preload"
        );

    }
}
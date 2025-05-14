<?php

declare(strict_types=1);

namespace App\Slim\Infrastructure\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final class HeadersMiddleware implements Middleware
{
    private array $headers = [
        "Strict-Transport-Security" => "max-age=31536000; includeSubDomains; preload",
        "X-Frame-Options" => "SAMEORIGIN",
        "Set-Cookie" => "example_cookie=value; HttpOnly; SameSite=Strict",
    ];

    public function process(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);
        if ($request->getUri()->getScheme() === "https") {
            $this->headers["Set-Cookie"] = "example_cookie=value; Secure; HttpOnly; SameSite=Strict";
        }

        return $this->getResponseWithHeaders($response);
    }

    private function getResponseWithHeaders(Response $response): Response
    {
        foreach ($this->headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }
        return $response;
    }

}
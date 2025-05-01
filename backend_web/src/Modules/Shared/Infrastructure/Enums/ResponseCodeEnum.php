<?php

namespace App\Modules\Shared\Infrastructure\Enums;

final class ResponseCodeEnum
{
    public const int OK = 200;
    public const int CREATED = 201;
    public const int ACCEPTED = 202;
    public const int NO_CONTENT = 204;
    public const int MOVED_PERMANENTLY = 301;
    public const int REDIRECTION = 302;
    public const int BAD_REQUEST = 400;
    public const int UNAUTHORIZED = 401;
    public const int FORBIDDEN = 403;
    public const int NOT_FOUND = 404;
    public const int METHOD_NOT_ALLOWED = 405;
    public const int CONFLICT = 409;
    public const int TOO_MANY_REQUESTS = 429;
    public const int INTERNAL_SERVER_ERROR = 500;
    public const int SERVICE_UNAVAILABLE = 503;
    public const int GATEWAY_TIMEOUT = 504;

    private function __construct() {}

}

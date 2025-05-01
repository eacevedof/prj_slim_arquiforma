<?php

namespace App\Modules\Open\Home\Domain\Exceptions;

use Exception;

use App\Modules\Shared\Infrastructure\Enums\ResponseCodeEnum;

final class HomeException extends Exception
{
    public static function unexpectedErrorOnRequest(string $message): self
    {
        throw new self($message, ResponseCodeEnum::INTERNAL_SERVER_ERROR);
    }

    public static function badRequest(string $message): self
    {
        throw new self($message, ResponseCodeEnum::BAD_REQUEST);
    }

    public static function unauthorized(string $message): self
    {
        throw new self($message, ResponseCodeEnum::UNAUTHORIZED);
    }

}
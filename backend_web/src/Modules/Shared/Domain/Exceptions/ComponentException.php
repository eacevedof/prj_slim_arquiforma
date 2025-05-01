<?php

namespace App\Modules\Shared\Domain\Exceptions;

use Exception;
use App\Modules\Shared\Infrastructure\Enums\ResponseCodeEnum;

final class ComponentException extends Exception
{
    public static function unexpectedErrorOnRequest(string $message): self
    {
        throw new self($message, ResponseCodeEnum::INTERNAL_SERVER_ERROR);
    }

}
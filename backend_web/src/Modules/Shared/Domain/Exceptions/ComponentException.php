<?php

namespace App\Modules\Shared\Domain\Exceptions;

use Exception;

final class ComponentException extends Exception
{
    public static function unexpectedErrorOnRequest(string $message): self
    {
        throw new self($message, 500);
    }

}
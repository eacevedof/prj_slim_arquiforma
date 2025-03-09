<?php

namespace App\Modules\Open\Home\Domain\Exceptions;

use Exception;

final class HomeException extends Exception
{
    public static function unexpectedErrorOnRequest(string $message): self
    {
        throw new self($message, 500);
    }

}
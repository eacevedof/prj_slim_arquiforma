<?php

declare(strict_types=1);

namespace App\Slim\Domain\User;

use App\Slim\Domain\DomainException\DomainRecordNotFoundException;

class UserNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The user you requested does not exist.';
}

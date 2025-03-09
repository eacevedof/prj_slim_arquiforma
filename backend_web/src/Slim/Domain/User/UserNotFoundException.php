<?php

declare(strict_types=1);

namespace Domain\User;

use Domain\DomainException\DomainRecordNotFoundException;

class UserNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The user you requested does not exist.';
}

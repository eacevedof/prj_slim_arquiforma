<?php

declare(strict_types=1);

namespace App\Slim\Application\Actions\User;

use App\Slim\Application\Actions\Action;
use Domain\User\UserRepository;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{
    protected UserRepository $userRepository;

    public function __construct(LoggerInterface $logger, \Domain\User\UserRepository $userRepository)
    {
        parent::__construct($logger);
        $this->userRepository = $userRepository;
    }
}

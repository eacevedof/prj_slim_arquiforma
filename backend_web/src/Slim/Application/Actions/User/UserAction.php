<?php

declare(strict_types=1);

namespace App\Slim\Application\Actions\User;

use Psr\Log\LoggerInterface;
use App\Slim\Application\Actions\Action;
use App\Slim\Domain\User\UserRepositoryInterface;

abstract class UserAction extends Action
{
    protected UserRepositoryInterface $userRepositoryInterface;

    public function __construct(LoggerInterface $logger, UserRepositoryInterface $userRepository)
    {
        parent::__construct($logger);
        $this->userRepositoryInterface = $userRepository;
    }
}

<?php

declare(strict_types=1);

use DI\autowire;
use DI\ContainerBuilder;

use App\Slim\Domain\User\UserRepositoryInterface;
use App\Slim\Infrastructure\Persistence\User\InMemoryUserRepository;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([
        UserRepositoryInterface::class => \DI\autowire(InMemoryUserRepository::class),
    ]);
};

<?php

declare(strict_types=1);

use DI\ContainerBuilder;

use App\Slim\Infrastructure\Persistence\User\InMemoryUserRepository;
use App\Slim\Domain\User\UserRepository;


return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(InMemoryUserRepository::class),
    ]);
};

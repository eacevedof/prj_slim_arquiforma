<?php

declare(strict_types=1);

use DI\ContainerBuilder;

use Infrastructure\Persistence\User\InMemoryUserRepository;
use Domain\User\UserRepository;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(InMemoryUserRepository::class),
    ]);
};

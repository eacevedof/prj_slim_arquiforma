<?php

declare(strict_types=1);

use Monolog\Logger;
use DI\ContainerBuilder;

use App\Slim\Application\Settings\Settings;
use App\Slim\Application\Settings\SettingsInterface;

//@eaf
return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                "displayErrorDetails" => false,
                "logError"            => false,
                "logErrorDetails"     => false,
                "logger" => [
                    "name" => "slim-app",
                    "path" => isset($_ENV["docker"]) ? "php://stdout" : __DIR__ . "/../logs/app.log",
                    "level" => Logger::DEBUG,
                ],
            ]);
        }
    ]);
};

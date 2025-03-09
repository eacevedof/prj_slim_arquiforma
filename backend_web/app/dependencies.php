<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use Monolog\Logger;

use DI\ContainerBuilder;

use App\Slim\Application\Settings\SettingsInterface;
use App\Modules\Shared\Infrastructure\Components\TplReader;


return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([

        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        TplReader::class => function (ContainerInterface $c) {
            $views = __DIR__ . '/../src/Modules/Views';
            $cache = __DIR__ . '/../cache';
            return TplReader::fromPrimitives([
                "pathViews" => $views,
                "pathCache" => $cache,
            ]);
        },

    ]);

};

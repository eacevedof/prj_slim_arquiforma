<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

use App\Slim\Application\Handlers\HttpErrorHandler;
use App\Slim\Application\Handlers\ShutdownHandler;
use App\Slim\Application\ResponseEmitter\ResponseEmitter;
use App\Slim\Application\Settings\SettingsInterface;

$slimRoot = __DIR__. "/../";
$slimRoot = realpath($slimRoot);
define("PATH_ROOT", $slimRoot);

function load_dotenv($path): void
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), "#") === 0) {
            continue;
        }

        list($name, $value) = explode("=", $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf("%s=%s", $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

load_dotenv(PATH_ROOT . "/.env");
getenv("APP_ENV") ?: putenv("APP_ENV=development");
$debug = getenv("APP_ENV") === "development";
error_reporting(0);
if ($debug) {
    ini_set("display_errors", "1");
    ini_set("display_startup_errors", "1");
    error_reporting(E_ALL);
}

require PATH_ROOT . "/vendor/autoload.php";
$containerBuilder = new ContainerBuilder();

if ($debug) { // Should be set to true in production
	$containerBuilder->enableCompilation(PATH_ROOT . '/var/cache');
}

// Set up settings
$settings = require PATH_ROOT . '/app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require PATH_ROOT . '/app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
$repositories = require PATH_ROOT . '/app/repositories.php';
$repositories($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

// Register middleware
$middleware = require PATH_ROOT . '/app/middleware.php';
$middleware($app);

// Register routes
$routes = require PATH_ROOT . '/app/routes.php';
$routes($app);

/** @var \App\Slim\Application\Settings\SettingsInterface $settings */
$settings = $container->get(SettingsInterface::class);

$displayErrorDetails = $settings->get('displayErrorDetails');
$logError = $settings->get('logError');
$logErrorDetails = $settings->get('logErrorDetails');

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Create Error Handler
$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

// Create Shutdown Handler
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Body Parsing Middleware
$app->addBodyParsingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);

<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

use App\Slim\Application\Handlers\HttpErrorHandler;
use App\Slim\Application\Handlers\ShutdownHandler;
use App\Slim\Application\ResponseEmitter\ResponseEmitter;
use App\Slim\Application\Settings\SettingsInterface;

$pathRootFolder = __DIR__. "/../";
$pathRootFolder = realpath($pathRootFolder);
define("PATH_ROOT", $pathRootFolder);

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

load_dotenv("$pathRootFolder/.env");
getenv("APP_ENV") ?: putenv("APP_ENV=production");

$debugEnabled = getenv("APP_ENV") !== "production";
error_reporting(0);
if ($debugEnabled) {
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(E_ALL);
}

require "$pathRootFolder/vendor/autoload.php";
$containerBuilder = new ContainerBuilder();

if ($debugEnabled) { // Should be set to true in production
	$containerBuilder->enableCompilation("$pathRootFolder/var/cache");
}

// Set up settings
$settings = require "$pathRootFolder/app/settings.php";
$settings($containerBuilder);

// Set up dependencies
$dependencies = require "$pathRootFolder/app/dependencies.php";
$dependencies($containerBuilder);

// Set up repositories
$repositories = require "$pathRootFolder/app/repositories.php";
$repositories($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

// Register middleware
$middleware = require "$pathRootFolder/app/middleware.php";
$middleware($app);

// Register routes
$routes = require "$pathRootFolder/app/routes.php";
$routes($app);

/** @var SettingsInterface $settings */
$settings = $container->get(SettingsInterface::class);

$displayErrorDetails = $settings->get("displayErrorDetails");
$logError = $settings->get("logError");
$logErrorDetails = $settings->get("logErrorDetails");

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

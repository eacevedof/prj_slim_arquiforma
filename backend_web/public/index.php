<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

use App\Slim\Application\ResponseEmitter\ResponseEmitter;
use App\Slim\Application\Settings\SettingsInterface;
use App\Slim\Application\Handlers\HttpErrorHandler;
use App\Slim\Application\Handlers\ShutdownHandler;

$pathRootFolder = __DIR__. "/../";
$pathRootFolder = realpath($pathRootFolder);
define("PATH_ROOT", $pathRootFolder);

function slimLog($message): void
{
    $today = date("Y-m-d");
    $logFile = PATH_ROOT . "/logs/slim-$today.log";
    $now = date("H:i:s");
    $content = "[$now][{$_SERVER["REMOTE_ADDR"]}]\n" . print_r($message, true) . "\n";
    file_put_contents($logFile, $content, FILE_APPEND);
}

register_shutdown_function(function () {
    $error = error_get_last();
    if (!$error) return;
    slimLog($error);
});

function load_dotenv($path): void
{
    if (!file_exists($path)) return;

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
$environment = getenv("APP_ENV");

$debugEnabled = ($environment !== "production");
error_reporting(0);
if ($debugEnabled) {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
}

try {
    require "$pathRootFolder/vendor/autoload.php";

    $containerBuilder = new ContainerBuilder();
    if ($environment === "production") { // Should be set to true in production
        $containerBuilder->enableCompilation("$pathRootFolder/cache");
    }

    // Set up settings
    $fnSettings = require "$pathRootFolder/app/settings.php";
    $fnSettings($containerBuilder);

    // Set up dependencies
    $fnDependencies = require "$pathRootFolder/app/dependencies.php";
    $fnDependencies($containerBuilder);

    // Set up repositories
    $fnRepositories = require "$pathRootFolder/app/repositories.php";
    $fnRepositories($containerBuilder);

    $diContainer = $containerBuilder->build();
    AppFactory::setContainer($diContainer);
    $app = AppFactory::create();

    // Register middleware
    $fnMiddleware = require "$pathRootFolder/app/middleware.php";
    $fnMiddleware($app);

    // Register routes
    $fnRoutes = require "$pathRootFolder/app/routes.php";
    $fnRoutes($app);

    // Create Error Handler
    $callableResolver = $app->getCallableResolver();
    $responseFactory = $app->getResponseFactory();
    $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

    // Create Shutdown Handler
    $fnSettings = $diContainer->get(SettingsInterface::class);
    $displayErrorDetails = $fnSettings->get("displayErrorDetails");

    // Create Request object from globals
    $serverRequestCreator = ServerRequestCreatorFactory::create();
    $serverRequest = $serverRequestCreator->createServerRequestFromGlobals();
    $shutdownHandler = new ShutdownHandler($serverRequest, $errorHandler, $displayErrorDetails);
    register_shutdown_function($shutdownHandler);

    // Add Routing Middleware
    $app->addRoutingMiddleware();

    // Add Body Parsing Middleware
    $app->addBodyParsingMiddleware();

    // Add Error Middleware
    $logError = $fnSettings->get("logError");
    $logErrorDetails = $fnSettings->get("logErrorDetails");
    $errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);
    $errorMiddleware->setDefaultErrorHandler($errorHandler);

    // Run App & Emit Response
    $serverResponse = $app->handle($serverRequest);
    (new ResponseEmitter())->emit($serverResponse);

}
catch (Throwable $ex) {
    slimLog($ex);
}




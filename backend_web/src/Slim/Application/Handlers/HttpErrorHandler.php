<?php

declare(strict_types=1);

namespace App\Slim\Application\Handlers;


use Throwable;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;

use App\Slim\Application\Actions\ActionError;
use App\Slim\Application\Actions\ActionPayload;

use App\Modules\Shared\Infrastructure\Traits\LogTrait;

final class HttpErrorHandler extends SlimErrorHandler
{
    use LogTrait;

    /**
     * @inheritdoc
     */
    protected function respond(): Response
    {
        $exception = $this->exception;
        $errorStatusCode = 500;
        $actionError = new ActionError(
            ActionError::SERVER_ERROR,
            "An internal error has occurred while processing your request."
        );

        if ($exception instanceof HttpException) {
            $errorStatusCode = $exception->getCode();
            $actionError->setDescription($exception->getMessage());

            if ($exception instanceof HttpNotFoundException) {
                $actionError->setType(ActionError::RESOURCE_NOT_FOUND);
            } elseif ($exception instanceof HttpMethodNotAllowedException) {
                // Verificar si la ruta realmente existe
                $routeContext = $this->request->getAttribute('route');
                if ($routeContext === null) {
                    // Si no existe, tratar como 404
                    $errorStatusCode = 404;
                    $actionError->setType(ActionError::RESOURCE_NOT_FOUND);
                    $actionError->setDescription("Ruta no encontrada");
                } else {
                    $actionError->setType(ActionError::NOT_ALLOWED);
                }
            } elseif ($exception instanceof HttpUnauthorizedException) {
                $actionError->setType(ActionError::UNAUTHENTICATED);
            } elseif ($exception instanceof HttpForbiddenException) {
                $actionError->setType(ActionError::INSUFFICIENT_PRIVILEGES);
            } elseif ($exception instanceof HttpBadRequestException) {
                $actionError->setType(ActionError::BAD_REQUEST);
            } elseif ($exception instanceof HttpNotImplementedException) {
                $actionError->setType(ActionError::NOT_IMPLEMENTED);
            }
        }

        if (
            !($exception instanceof HttpException)
            && $exception instanceof Throwable
            && $this->displayErrorDetails
        ) {
            $actionError->setDescription($exception->getMessage());
        }

        $httpResponse = $this->responseFactory->createResponse($errorStatusCode);

        $actionPayload = new ActionPayload($errorStatusCode, null, $actionError);
        $encodedPayload = json_encode($actionPayload, JSON_PRETTY_PRINT);
        $httpResponse->getBody()->write($encodedPayload);

        return $httpResponse->withHeader("Content-Type", "application/json");
    }
}

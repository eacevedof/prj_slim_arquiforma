<?php

namespace App\Modules\Open\Home\Infrastructure\Controllers;

use Throwable;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Modules\Shared\Infrastructure\Enums\RouteEnum;
use App\Modules\Shared\Infrastructure\Components\Sessioner;
use App\Modules\Shared\Infrastructure\Components\TplReader;
use App\Modules\Shared\Infrastructure\Components\Texter;
use App\Modules\Shared\Infrastructure\Traits\LogTrait;
use App\Modules\Shared\Infrastructure\Controllers\AbstractController;

use App\Modules\Open\Home\Application\GetHomePage\GetHomePageService;
use App\Modules\Open\Home\Domain\Exceptions\HomeException;

use App\Modules\Open\Home\Application\SendContactMessage\SendContactMessageDto;
use App\Modules\Open\Home\Application\SendContactMessage\SendContactMessageService;

final class ContactController extends AbstractController
{
    use LogTrait;

    public function __construct(
        protected TplReader                        $tplReader,
        private readonly Texter                    $texter,
        private readonly Sessioner                 $sessioner,
        private readonly GetHomePageService        $getHomePageService,
        private readonly SendContactMessageService $sendContactMessageService,
    )
    {
        $this->tplReader->setViewFolderByController(
            ContactController::class
        );
    }

    public function __invoke(Request $httpRequest): Response
    {
        try {
            $seo = $this->getHomePageService->__invoke();
            return $this->renderView(
                "view-Home",
                ["seo" => $seo]
            );
        }
        catch (HomeException $e) {
            return $this->renderView(
                "view-Home",
                ["error" => $e->getMessage()]
            );
        }
        catch (Throwable $e) {
            $this->logException($e);
            return $this->renderView(
                "view-error",
                ["error" => $e->getMessage()]
            );
        }
    }

    public function sendMessage(Request $httpRequest): Response
    {
        $formInput = $this->texter->getSanitizedPrimitives($httpRequest->getParsedBody());
        try {
            $sendContactMessageDto = SendContactMessageDto::fromPrimitives($formInput);
            $this->sendContactMessageService->__invoke($sendContactMessageDto);
            return $this->redirectWithPayload(RouteEnum::HOME, [
                "success" => "El mensaje ha sido enviado",
            ]);
        }
        catch (HomeException $e) {
            return $this->redirectWithPayload(RouteEnum::HOME, [
                "error" => $e->getMessage(),
                "form" => $formInput,
            ]);
        }
        catch (Throwable $e) {
            $this->logException($e);
            return $this->redirectWithPayload(RouteEnum::HOME, [
                "error" => $e->getMessage(),
                "form" => $formInput,
            ]);
        }
    }

}
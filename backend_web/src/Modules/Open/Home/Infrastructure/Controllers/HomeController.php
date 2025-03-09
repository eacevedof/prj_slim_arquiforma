<?php

namespace App\Modules\Open\Home\Infrastructure\Controllers;

use Throwable;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Modules\Shared\Infrastructure\Traits\LogTrait;
use App\Modules\Shared\Infrastructure\Components\TplReader;
use App\Modules\Shared\Infrastructure\Controllers\AbstractController;
use App\Modules\Open\Home\Application\GetHomePage\GetHomePageService;
use App\Modules\Open\Home\Domain\Exceptions\HomeException;

final class HomeController extends AbstractController
{
    use LogTrait;

    public function __construct(
        protected TplReader                 $tplReader,
        private readonly GetHomePageService $getHomePageService,
    )
    {
        $this->tplReader->setViewFolderByController(
            HomeController::class
        );
    }

    public function __invoke(Request $httpRequest): Response
    {
        try {
            $seo = $this->getHomePageService->__invoke();
            return $this->renderView(
                "view-home",
                ["seo" => $seo]
            );
        }
        catch (HomeException $e) {
            return $this->renderView(
                "view-home",
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
}
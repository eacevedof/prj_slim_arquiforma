<?php

namespace App\Modules\Open\Home\Application\GetHomePage;

use App\Modules\Open\Home\Infrastructure\Repositories\SeoRawReaderRepository;

final readonly class GetHomePageService
{
    public function __invoke(): array
    {
        return [
            "message" => "im home"
        ];
    }
}
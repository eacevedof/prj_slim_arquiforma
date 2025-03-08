<?php

namespace App\Modules\Open\Home\Application\GetHomePage;

final readonly class GetHomePageService
{
    public function __invoke(): array
    {
        return [
            "message" => "im home"
        ];
    }
}
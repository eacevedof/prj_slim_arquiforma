<?php

namespace App\Modules\Open\Home\Application\GetHomePage;

use App\Modules\Open\Home\Infrastructure\Repositories\SeoRawReaderRepository;

final readonly class GetHomePageService
{
    public function __invoke(): array
    {
        return [
            "message" => "ARQUIFORMA Y OBRAS EN GENERAL SL
                        B-56883473
                        C/ GENERAL RICARDOS 105
                        28019 Madrid - España
                        ",
        ];
    }
}
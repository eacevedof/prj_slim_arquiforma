<?php

namespace App\Modules\Open\Home\Application\GetHomePage;

use App\Modules\Open\Home\Infrastructure\Repositories\SeoRawReaderRepository;

final readonly class GetHomePageService
{
    public function __invoke(): array
    {
        return [
            "title" => "ARQUIFORMA Y OBRAS EN GENERAL SL",
            "info" => "
            ARQUIFORMA Y OBRAS EN GENERAL SL
            
            arquiforma.es
            
            C/ GENERAL RICARDOS 105                        
            28019 Madrid - España
            ",
            "meta_description" => "ARQUIFORMA Y OBRAS EN GENERAL SL is a company based in Madrid, Spain, specializing in general construction works.",
            "meta_keywords" => "trabajos de construcción en general, Madrid, Spain",
            "meta_author" => "Arquiforma y Obras en General SL",
        ];
    }
}
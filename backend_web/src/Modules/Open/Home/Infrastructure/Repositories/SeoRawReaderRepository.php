<?php

namespace App\Modules\Open\Home\Infrastructure\Repositories;

final readonly class SeoRawReaderRepository
{
    public function getMetaSeoBySlug(string $slug): array
    {
        return [
            "title" => "title",
            "description" => "description",
            "keywords" => "keywords",
            "image" => "image",
        ];
    }
}
<?php

namespace App\Modules\Shared\Infrastructure\Components;

final class Slugger
{
    public static function getInstance(): self
    {
        return new self();
    }

    public function getSluggedText(string $text): string
    {
        $slug = strtolower(trim($text));
        $slug = preg_replace("/[^a-z0-9]+/", "-", $slug);
        $slug = preg_replace("/^-+|-+$/", "", $slug);
        return $slug;
    }
}
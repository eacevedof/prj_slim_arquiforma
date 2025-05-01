<?php

namespace App\Modules\Shared\Infrastructure\Components;

final class Htmler
{
    public static function getInstance(): self
    {
        return new self();
    }

    public function getHtmlForIBPW(string $rawText): string
    {
        $html = str_replace("'", "&#39;", $rawText);
        $html = htmlentities($html);
        $html = htmlspecialchars($html);
        return trim($html);
    }

}
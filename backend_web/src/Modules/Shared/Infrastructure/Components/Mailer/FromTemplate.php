<?php

namespace App\Modules\Shared\Infrastructure\Components\Mailer;

final class FromTemplate
{
    public static function getFileContent(string $filename, array $vars): string
    {
        ob_start();
        foreach ($vars as $name => $value) {
            $$name = $value;
        }
        include $filename;
        return ob_get_clean();
    }
}

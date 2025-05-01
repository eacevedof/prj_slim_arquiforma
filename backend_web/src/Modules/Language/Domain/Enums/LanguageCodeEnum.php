<?php

namespace App\Modules\Language\Domain\Enums;

abstract class LanguageCodeEnum
{
    public const ES = "es";
    public const EN = "en";
    public const IT = "it";
    public const PT = "pt";

    public static function getCodeByLiteral(string $literal): string
    {
        switch ($literal) {
            case LanguageLiteralEnum::ENGLISH:
                return self::EN;
            case LanguageLiteralEnum::ITALIAN:
                return self::IT;
            case LanguageLiteralEnum::PORTUGUESE:
                return self::PT;
            case LanguageLiteralEnum::SPANISH:
            default:
                return self::ES;
        }
    }

}


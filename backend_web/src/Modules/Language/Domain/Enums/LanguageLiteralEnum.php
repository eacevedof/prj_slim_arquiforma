<?php

namespace application\Modules\Language\Enums;

abstract class LanguageLiteralEnum
{
    public const ENGLISH = "english";
    public const SPANISH = "spanish";
    public const PORTUGUESE = "portuguese";
    public const ITALIAN = "italian";

    public static function isInLanguages(string $language): bool
    {
        return in_array($language, [
            self::ENGLISH,
            self::ITALIAN,
            self::PORTUGUESE,
            self::SPANISH
        ]);
    }

}


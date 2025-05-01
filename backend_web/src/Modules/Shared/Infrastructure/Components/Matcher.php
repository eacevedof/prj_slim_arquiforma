<?php

namespace App\Modules\Shared\Infrastructure\Components;

final class Matcher
{
    public static function getInstance(): self
    {
        return new self();
    }

    /**
     * @uses \App\Modules\Shared\Infrastructure\Enums\MatchEnum;
     */
    public function doesMatch(string $value, string $pattern): bool
    {
        $pattern = "/{$pattern}/";
        return preg_match($pattern, $value) === 1;
    }

}

<?php

namespace Yog\Checkers;

use Yog\Http\HttpRequest;
use Yog\Xml\HtmlOutput;

final class Php
{
    public static function getInstance(): self
    {
        return new self();
    }

    public function isPhpVersionOver4_3(): bool
    {
        $strPhpVersion = $this->getPhpVersion();
        $versionParts = explode(".", $strPhpVersion, 2);

        $major = (int) $versionParts[0];
        $minor = (int) $versionParts[1];
        /* We dont support v4.3.0 */
        if (
            $major < 4 ||
            ($major == 4 && $minor < 3)
        )
            return false;
        return true;
    }

    public function getPhpVersion(): string
    {
        return phpversion();
    }
}
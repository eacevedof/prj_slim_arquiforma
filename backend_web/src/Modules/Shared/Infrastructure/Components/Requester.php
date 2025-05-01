<?php

namespace App\Modules\Shared\Infrastructure\Components;

final class Requester
{
    public static function getInstance(): self
    {
        return new self();
    }

    public function getRequestIP(): string
    {
        if (php_sapi_name() === "cli")
            return "cli-".gethostbyname(gethostname());

        return $_SERVER["HTTP_CLIENT_IP"]
            ?? $_SERVER["HTTP_X_FORWARDED_FOR"]
            ?? $_SERVER["REMOTE_ADDR"]
            ?? "unknown";
    }

    public function getLanguage(): string
    {
        return $_SERVER["HTTP_ACCEPT_LANGUAGE"] ?? "unknown";
    }

    public function getOS(): string
    {
        return $_SERVER["HTTP_SEC_CH_UA_PLATFORM"] ?? "unknown";
    }

    public function getBrowser(): string
    {
        return $_SERVER["HTTP_USER_AGENT"] ?? "unknown";
    }

    public function getBrowserVersion(): string
    {
        return $_SERVER["HTTP_SEC_CH_UA"] ?? "unknown";
    }

    public function getBearToken(): string
    {
        return $_SERVER["HTTP_AUTHORIZATION"] ?? "";
    }

}
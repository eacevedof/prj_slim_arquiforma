<?php

namespace App\Modules\Shared\Infrastructure\Components;

use App\Modules\Language\Domain\Enums\LanguageCodeEnum;

abstract class AbstractInputDto
{
    protected string $requestIpAddress;
    protected string $requestBrowser;
    protected string $requestBrowserVersion;
    protected string $requestOs;
    protected string $requestTimeZone;
    protected string $requestLanguage;
    protected string $requestAuthToken;

    public function __construct(array $primitives)
    {
        $requester = Requester::getInstance();

        $this->requestIpAddress = trim((string)($primitives["requestIpAddress"] ?? $requester->getRequestIP()));

        $requestLanguage = $primitives["requestLanguage"] ?? $requester->getLanguage();
        if (!$requestLanguage) $requestLanguage = LanguageCodeEnum::ES;
        $this->requestLanguage = trim((string) $requestLanguage);

        $this->requestOs = trim((string)($primitives["requestOs"] ?? $requester->getOS()));
        $this->requestBrowser = trim((string)($primitives["requestBrowser"] ?? $requester->getBrowser()));
        $this->requestBrowserVersion = trim((string)($primitives["requestBrowserVersion"] ?? $requester->getBrowserVersion()));

        $this->requestTimeZone = trim((string)($primitives["requestTimeZone"] ?? $_POST["_timezone"] ?? $_SERVER["HTTP_TIMEZONE"] ?? "unknown"));
        $this->requestAuthToken = trim((string)($primitives["requestAuthToken"] ?? $_POST["_token"] ?? $requester->getBearToken()));

    }

    public function requestIpAddress(): string
    {
        return $this->requestIpAddress;
    }

    public function getRequestBrowser(): string
    {
        return $this->requestBrowser;
    }

    public function getRequestBrowserVersion(): string
    {
        return $this->requestBrowserVersion;
    }

    public function getRequestOs(): string
    {
        return $this->requestOs;
    }

    public function getRequestTimeZone(): string
    {
        return $this->requestTimeZone;
    }

    public function getRequestLanguage(): string
    {
        return $this->requestLanguage;
    }

    public function getRequestAuthToken(): string
    {
        return $this->requestAuthToken;
    }

}

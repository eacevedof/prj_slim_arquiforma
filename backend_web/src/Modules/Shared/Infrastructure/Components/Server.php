<?php

namespace App\Modules\Shared\Infrastructure\Components;

final class Server
{
    public static function getInstance(): self
    {
        return new self();
    }

    public function getServerName(): string
    {
        return gethostname() ?? "";
    }

    public function getServerIp(): string
    {
        return gethostbyname($this->getServerName()) ?? "";
    }

}
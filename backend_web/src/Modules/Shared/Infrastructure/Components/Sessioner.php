<?php

namespace App\Modules\Shared\Infrastructure\Components;

final class Sessioner
{
    private static bool $isStarted = false;

    public function __construct()
    {
        if (self::$isStarted) return;
        self::$isStarted = session_start();
    }

    public static function getInstance(): self
    {
        return new self();
    }

    public function add(string $sessionKey, mixed $sessionValue): self
    {
        $_SESSION[$sessionKey] = $sessionValue;
        return $this;
    }

    public function get(string $sessionKey): mixed
    {
        return $_SESSION[$sessionKey] ?? null;
    }

    public function getOnce(string $sessionKey): mixed
    {
        $value = $_SESSION[$sessionKey] ?? null;
        unset($_SESSION[$sessionKey]);
        return $value;
    }

    public function remove(string $sessionKey): self
    {
        unset($_SESSION[$sessionKey]);
        return $this;
    }

    public function clear(): self
    {
        $_SESSION = [];
        return $this;
    }
}
<?php

namespace Yog\Http;


final class HttpRequest
{
    public static function getInstance(): self
    {
        return new self();
    }

    public function getGET(string $key, $default = null): ?string
    {
        return $_GET[$key] ?? $default;
    }

    /*
    in special case, sqlyog just sends garbage data with query string to check for tunnel version.
    we need to process that now
    */
    private function isGETApp(): bool
    {
        return isset($_GET["app"]);
    }

    public function isGarbageTestFromApp(): bool
    {
        return $this->isGETApp();
    }

    public function getPOST(string $key, $default = null): ?string
    {
        return $_POST[$key] ?? $default;
    }

    public function getPhpInput(): string
    {
        return file_get_contents("php://input");
    }

    public function getRequest(): array
    {
        return $_REQUEST ?? [];
    }


    public function logRequest(): void
    {
        $request = [
            "get" => $_GET ?? [],
            "post" => $_POST ?? [],
            "input" => file_get_contents("php://input"),
            "request" => $_REQUEST ?? [],
        ];
        yogLog($request, "logRequest");
    }
}
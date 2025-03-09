<?php

namespace App\Modules\Shared\Infrastructure\Traits;

use Throwable;

trait LogTrait
{
    protected function logDebug($mixed, string $title = ""): void
    {
        $now = date("Y-m-d H:i:s");
        $content = [
            "host_ip" => $this->getServerClientIP()
        ];
        if ($title) $content[] = $title;

        $content[] = is_string($mixed) ? $mixed : var_export($mixed, true);
        $content = implode("\n", $content);
        $content = "$now [DEBUG] $content";
        $fileName = $this->getFileName("debug");
        $this->filePutContent($fileName, $content);
    }

    protected function logSql(string $sql, string $title = ""): void
    {
        $now = date("Y-m-d H:i:s");
        $content = [
            "host_ip" => $this->getServerClientIP()
        ];
        if ($title) $content[] = $title;

        $content[] = $sql;
        $content = implode("\n", $content);
        $content = "$now [SQL] $content";
        $fileName = $this->getFileName("sql", "sql");
        $this->filePutContent($fileName, $content);
    }

    protected function logError($mixed, string $title = ""): void
    {
        $now = date("Y-m-d H:i:s");
        $content = [
            "host_ip" => $this->getServerClientIP()
        ];
        if ($title) $content[] = $title;

        $content[] = is_string($mixed) ? $mixed : var_export($mixed, true);
        $content = implode("\n", $content);
        $content = "$now [ERROR] $content";
        $fileName = $this->getFileName("error");
        $this->filePutContent($fileName, $content);
    }

    private function logException(Throwable $throwable, $title = "ERROR"): void
    {
        $now = date("Y-m-d H:i:s");
        $content = [
            "host_ip" => $this->getServerClientIP()
        ];
        if ($title) $content[] = $title;

        $content["file"] = "ex file:\n\t" . $throwable->getFile();
        $content["line"] = "ex line:\n\t" . $throwable->getLine();
        $content["code"] = "ex code:\n\t" . $throwable->getCode();
        $content["message"] = "ex message:\n\t" . $throwable->getMessage();
        $content["trace"] = "ex trace:\n\t" . $throwable->getTraceAsString();
        $content = implode("\n", $content);
        $content = "$now [EXCEPTION] $content";
        $fileName = $this->getFileName("error");
        $this->filePutContent($fileName, $content);
    }

    private function getServerClientIP(): string
    {
        if (php_sapi_name() === "cli")
            return "cli-".gethostbyname(gethostname());

        return $_SERVER["HTTP_CLIENT_IP"]
            ?? $_SERVER["HTTP_X_FORWARDED_FOR"]
            ?? $_SERVER["REMOTE_ADDR"]
            ?? "unknown";
    }

    private function filePutContent(string $fileName, string $content): void
    {
        $pathLogs = PATH_ROOT . "/logs";
        if (!is_dir($pathLogs)) mkdir($pathLogs, 0777, true);
        file_put_contents("$pathLogs/$fileName", $content);
    }

    private function getFileName(string $fileName, string $ext = "log"): string
    {
        $today = date("Y-m-d");
        return "$fileName-$today.$ext";
    }

}
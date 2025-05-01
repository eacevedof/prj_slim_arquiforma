<?php

namespace App\Modules\Shared\Infrastructure\Components;

final class Systemer
{
    public static function getInstance(): self
    {
        return new self();
    }

    public function runCommand(string $command): array
    {
        $output = [];
        $isError = 0;
        exec($command, $output, $isError);
        return [
            "command" => $command,
            "result" => $output,
            "is_error" => $isError
        ];
    }

    public function runCommandNohup(string $command): array
    {
        $cmdAsync = "nohup $command > /dev/null 2>&1 &";
        $output = [];
        $isError = 0;
        exec($cmdAsync, $output, $isError);
        return [
            "command" => $command,
            "output" => $output,
            "is_error" => $isError
        ];
    }
}
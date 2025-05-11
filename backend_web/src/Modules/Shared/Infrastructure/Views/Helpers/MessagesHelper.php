<?php

namespace App\Modules\Shared\Infrastructure\Views\Helpers;

use App\Modules\Shared\Infrastructure\Components\Sessioner;

final readonly class MessagesHelper
{
    private array $errors;
    private array $success;
    private array $warnings;
    private array $infos;

    public function __construct(array $primitives)
    {
        $this->errors = $this->getMessagesByMessageType("error", $primitives);
        $this->success = $this->getMessagesByMessageType("success", $primitives);
        $this->warnings = $this->getMessagesByMessageType("warning", $primitives);
        $this->infos = $this->getMessagesByMessageType("info", $primitives);
    }

    public static function fromPrimitives(array $primitives): self
    {
        return new self($primitives);
    }

    public function getMessages(): string
    {
        $message = [];

        if ($this->errors)
            $message[] = "<h3 class=\"font-bold text-center mb-12\" style=\"color: red\">{$this->errors[0]}</h3>";

        if ($this->success)
            $message[] = "<h3 class=\"font-bold text-center mb-12\" style=\"color: green\">{$this->success[0]}</h3>";

        if ($this->warnings)
            $message[] = "<h3 class=\"font-bold text-center mb-12\" style=\"color: orange\">{$this->warnings[0]}</h3>";

        if ($this->infos)
            $message[] = "<h3 class=\"font-bold text-center mb-12\" style=\"color: dodgerblue\">{$this->infos[0]}</h3>";

        return implode("\n", $message);
    }

    public function hasMessages(): bool
    {
        return ($this->errors || $this->success || $this->warnings || $this->infos);
    }

    private function getMessagesByMessageType(string $messageType, array $primitives): array
    {
        $temp = $this->getNotEmpty($primitives["{$messageType}s"] ?? []);
        $session = Sessioner::getInstance()->getOnce($messageType);
        if ($session) $temp[] = $session;
        return $temp;
    }

    private function getNotEmpty(array $array): array
    {
        if (!$array) return [];
        return array_filter($array, function ($value) {
            $value = trim($value);
            return (($value !== "") && ($value !== null));
        });
    }

}
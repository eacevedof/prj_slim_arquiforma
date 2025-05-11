<?php

namespace App\Modules\Open\Home\Application\SendContactMessage;

use App\Modules\Shared\Infrastructure\Components\AbstractInputDto;

final class SendContactMessageDto extends AbstractInputDto
{
    private string $name;
    private string $email;
    private string $subject;
    private string $phone;
    private string $message;

    public function __construct(array $primitives)
    {
        parent::__construct($primitives);

        $this->name = trim((string) ($primitives["name"] ?? ""));
        $this->email = trim((string) ($primitives["email"] ?? ""));
        $this->subject = trim((string) ($primitives["subject"] ?? ""));
        $this->phone = trim((string) ($primitives["phone"] ?? ""));
        $this->message = trim((string) ($primitives["message"] ?? ""));

    }

    public static function fromPrimitives(array $primitives): self
    {
        return new self($primitives);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function toArray(): array
    {
        return [
            "name" => $this->name,
            "email" => $this->email,
            "subject" => $this->subject,
            "phone" => $this->phone,
            "message" => $this->message,
        ];
    }

}
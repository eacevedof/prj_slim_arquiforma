<?php

namespace App\Modules\Shared\Infrastructure\Components\Mailer;

abstract class AbstractMailer implements MailerInterface
{
    protected bool $isHtmlBody = false;
    protected array $headers = [];
    protected string $emailFrom = "";
    protected string $emailFromName = "";
    protected array $emailsTo = [];
    protected array $emailsCc = [];
    protected array $emailsBcc = [];
    protected array $attachments = [];

    protected string $subject = "";
    protected string $content = "";

    protected array $errors = [];
    protected bool $isError = false;

    protected function addError(?string $error): self
    {
        if (!$error) return $this;

        $this->isError = true;
        $this->errors[] = $error;
        return $this;
    }

    protected function addErrors(?array $errors): self
    {
        if (!$errors) return $this;
        $this->isError = true;
        foreach ($errors as $error) {
            $this->errors[] = $error;
        }
        return $this;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function setEmailFrom(string $emailFrom): self
    {
        $this->emailFrom = $emailFrom;
        return $this;
    }

    public function setEmailFromName(string $emailFromName): self
    {
        $this->emailFromName = $emailFromName;
        return $this;
    }

    public function addEmailTo(string $emailTo): self
    {
        $this->emailsTo[] = $emailTo;
        return $this;
    }

    public function addEmailCc(string $emailCc): self
    {
        $this->emailsCc[] = $emailCc;
        return $this;
    }

    public function addEmailBcc(string $emailBcc): self
    {
        $this->emailsBcc[] = $emailBcc;
        return $this;
    }

    public function setBodyHtml(mixed $mxContent): self
    {
        $this->isHtmlBody = true;
        $this->content = is_array($mxContent)
            ? implode(PHP_EOL, $mxContent)
            : $mxContent;

        return $this;
    }

    public function setBodyPlain(mixed $mxContent): self
    {
        $this->isHtmlBody = false;
        $this->content = is_array($mxContent)
            ? implode(PHP_EOL, $mxContent)
            : $mxContent;

        return $this;
    }

    public function addAttachment(array $attachment = ["path" => "", "filename" => "", "mime" => ""]): self
    {
        $this->attachments[] = $attachment;
        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

}

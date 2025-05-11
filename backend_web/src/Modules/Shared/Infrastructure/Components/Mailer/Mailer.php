<?php

namespace App\Modules\Shared\Infrastructure\Components\Mailer;

use App\Modules\Shared\Infrastructure\Traits\LogTrait;
use Exception;

final class Mailer extends AbstractMailer
{
    use LogTrait;

    private string $hashBoundary = "";

    public static function getInstance(): self
    {
        return new self();
    }

    public function send(): self
    {
        try {
            if (!($this->emailFrom && $this->emailsTo)) {
                $this->addError("No target emails!");
                return $this;
            }

            $strHeaders = $this->loadHashBoundary()
                ->addFromHeaders()
                ->addMimeHeaders()
                ->addCcHeader()
                ->addBccHeader()
                ->getHeadersAsString()
            ;

            $emailBody = $this->getMultiPartBoundaryString().PHP_EOL;
            $emailBody .= $this->content.PHP_EOL;

            foreach ($this->attachments as $attachment) {
                $attachmentContent = $this->getAttachmentAsString($attachment);
                $emailBody .= $attachmentContent ?: "";
            }

            $emailsTo = implode(", ", $this->emailsTo);
            $isSent = mail($emailsTo, $this->subject, $emailBody, $strHeaders);

            if (!$isSent) {
                $this->addError("error sending email. May be headers are wrong. Content-Type: text/plain or text/html");
                $this->addErrors(error_get_last());
                $this->addErrors($r = ["to" => $emailsTo, "subject" => $this->subject, "email_body"=> $emailBody, "headers" => $strHeaders]);
            }
        }
        catch (Exception $e) {
            $this->addError($e->getMessage());
        }
        return $this;
    }

    private function loadHashBoundary(): self
    {
        if ($this->attachments) {
            $this->hashBoundary = md5(uniqid());
        }
        return $this;
    }

    private function addFromHeaders(): self
    {
        $this->headers[] = "From: $this->emailFromName <$this->emailFrom>";
        $this->headers[] = "Return-Path: <$this->emailFrom>";
        $this->headers[] = "X-Sender: $this->emailFrom";
        return $this;
    }

    private function addMimeHeaders(): self
    {
        $headers = [
            "MIME-Version: 1.0",
            "Content-Type: text/plain; charset=\"UTF-8\"",
            "Content-Transfer-Encoding: 8bit",
        ];

        if ($this->isHtmlBody) {
            $headers = [
                "MIME-Version: 1.0",
                "Content-Type: text/html; charset=\"UTF-8\"",
                "Content-Transfer-Encoding: 8bit",
            ];
        }

        if ($this->hashBoundary) {
            $headers = [
                "MIME-Version: 1.0",
                "Content-Type: multipart/mixed; boundary=\"$this->hashBoundary\"",
                "Content-Transfer-Encoding: 7bit",
                "This is a MIME encoded message."
            ];
        }
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    private function addCcHeader(): self
    {
        if ($this->emailsCc) {
            $this->headers[] = "cc: ".implode(", ", $this->emailsCc);
        }
        return $this;
    }

    private function addBccHeader(): self
    {
        if ($this->emailsBcc) {
            $this->headers[] = "bcc: ".implode(", ", $this->emailsBcc);
        }
        return $this;
    }

    private function getHeadersAsString(): string
    {
        $header = implode(PHP_EOL, $this->headers);
        return $header;
    }

    private function getMultiPartBoundaryString(): string
    {
        if (!$this->hashBoundary) {
            return "";
        }
        $content[] = "--$this->hashBoundary";
        $content[] = "Content-Type: text/html; charset=UTF-8";
        $content[] = "Content-Transfer-Encoding: 8bit";
        return implode(PHP_EOL, $content);
    }

    private function getAttachmentAsString(array $attachment): string
    {
        //https://stackoverflow.com/questions/12301358/send-attachments-with-php-mail
        $pathfile = $attachment["path"];
        if (!is_file($pathfile)) return "";

        $mime = $attachment["mime"] ?? "application/octet-stream";
        $alias = $attachment["filename"] ?? basename($pathfile);

        $content = file_get_contents($pathfile);
        if (!$content) return "";

        $content = chunk_split(base64_encode($content));
        $separator = $this->hashBoundary;

        $body[] = "";
        $body[] = "--$separator";
        $body[] = "Content-Type: $mime; name=\"$alias\"";
        $body[] = "Content-Transfer-Encoding: base64";
        $body[] = "Content-Disposition: attachment; ";
        $body[] = $content;
        $body[] = "--$separator--";
        $body[] = "";

        return implode(PHP_EOL, $body);
    }

}

<?php

namespace App\Modules\Shared\Infrastructure\Components\Mailer;

interface MailerInterface
{
    public function send(): object;
}

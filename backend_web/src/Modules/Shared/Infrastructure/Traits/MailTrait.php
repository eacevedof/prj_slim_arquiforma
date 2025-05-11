<?php

namespace App\Modules\Shared\Infrastructure\Components\Traits;

use App\Modules\Shared\Infrastructure\Components\Server;
use App\Modules\Shared\Infrastructure\Components\Mailer\Mailer;
use App\Modules\Shared\Infrastructure\Repositories\Configuration\EnvironmentRawRepository;

trait MailTrait
{
    private function sendEmailOnError(
        string $message,
        string $emailTo = "development@lazarus.es"
    ): object
    {
        $environmentRepository = EnvironmentRawRepository::getInstance();

        $appName = $environmentRepository->getAppName();
        $baseUrl = $environmentRepository->getBaseUrl();
        $baseUrl = "<a href=\"$baseUrl\" target=\"_blank\">$baseUrl</a>";

        $env = $environmentRepository->getEnvironment();
        if ($env !== "production") $emailTo = "eacevedo@lazarus.es";

        $server = Server::getInstance();
        $serverInfo = "{$server->getServerName()} ({$server->getServerIp()})";
        $sentResult = Mailer::getInstance()
            ->setEmailTo($emailTo)
            ->setEmailFromName("$appName ($env)")
            ->setSubject($subject = "[alert]: ($env) - ".self::class)
            ->setDefaultTplVars([
                "subject" => "$serverInfo $subject",
                "body" => "$baseUrl<br/>$message",
            ])
            ->sendSynchronousByTpl()->getResult();
        return $sentResult;
    }
}
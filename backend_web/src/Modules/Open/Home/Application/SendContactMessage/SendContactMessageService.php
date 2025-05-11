<?php

namespace App\Modules\Open\Home\Application\SendContactMessage;

use App\Modules\Shared\Infrastructure\Components\Hasher\Hasher;
use App\Modules\Shared\Infrastructure\Components\Mailer\Mailer;
use App\Modules\Shared\Infrastructure\Traits\LogTrait;

use App\Modules\Open\Home\Domain\Exceptions\HomeException;

final readonly class SendContactMessageService
{
    use LogTrait;

    private SendContactMessageDto $sendContactMessageDto;

    public function __construct(
        private Mailer $mailer,
        private Hasher $hasher,
    ) {

    }

    /**
     * @throws HomeException
     */
    public function __invoke(SendContactMessageDto $sendContactMessageDto): void
    {
        $this->sendContactMessageDto = $sendContactMessageDto;
        $this->failIfWrongInput();
        $this->failIfWrongToken();
        $this->sendMessage();
    }

    private function failIfWrongInput(): void
    {
        if (!$emailTo = $this->sendContactMessageDto->getEmail())
            HomeException::badRequest("Email: vacio");

        if (!filter_var($emailTo, FILTER_VALIDATE_EMAIL))
            HomeException::badRequest("Email: formato incorrecto");

        if (!$this->sendContactMessageDto->getName())
            HomeException::badRequest("Nombre: vacio");

        if (!$this->sendContactMessageDto->getSubject())
            HomeException::badRequest("Asunto: vacio");

        if (!$this->sendContactMessageDto->getMessage())
            HomeException::badRequest("Mensaje: vacio");

    }

    private function failIfWrongToken(): void
    {
        if (!$authToken = $this->sendContactMessageDto->getRequestAuthToken())
            HomeException::unauthorized("Action is invalid (1)");

        $token = $this->hasher->getUnpackedToken($authToken);
        if (!$token)
            HomeException::unauthorized("Action is invalid (2)");

        if (
            $token["ip"] !== $this->sendContactMessageDto->requestIpAddress() ||
            $token["os"] !== $this->sendContactMessageDto->getRequestOs() ||
            $token["browser"] !== $this->sendContactMessageDto->getRequestBrowser() ||
            $token["browser_version"] !== $this->sendContactMessageDto->getRequestBrowserVersion() ||
            $token["date"] !== date("Y-m-d")
        )
            HomeException::unauthorized("Action is invalid (3)");

        $tokenDate = $token["date"];
        $tokenTime = $token["time"];

        $tokenDateTime = new \DateTime("$tokenDate $tokenTime");
        $currentDateTime = new \DateTime();
        $tokenTimeAgo = $tokenDateTime->diff($currentDateTime);
        if ($tokenTimeAgo->h > 2)
            HomeException::unauthorized("Refresh page and try again");

    }

    private function sendMessage(): void
    {
        $errors = $this->mailer->setSubject($this->sendContactMessageDto->getSubject())
            ->setEmailFrom("tfwnoreply@gmail.com")
            //->addEmailTo($this->sendContactMessageDto->getEmail())
            ->addEmailTo("eacevedof@gmail.com")
            ->setSubject($this->sendContactMessageDto->getSubject())
            ->setBodyPlain($this->sendContactMessageDto->getMessage())
            ->send()
            ->getErrors();

        if ($errors) {
            $this->logError(implode(", ", $errors), self::class);
            $this->logError($this->sendContactMessageDto->toArray(), self::class);
            HomeException::unexpectedErrorOnRequest("some error occurred");
        }
    }

}
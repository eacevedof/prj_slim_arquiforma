<?php

namespace App\Modules\Shared\Infrastructure\Components;

use DateTime;
use Exception;
use App\Modules\Shared\Infrastructure\Components\Traits\LogTrait;

final class DateTimer
{
    use LogTrait;

    public static function getInstance(): self
    {
        return new self();
    }

    public function getNow(): string
    {
        return date("Y-m-d H:i:s");
    }

    public function getToday(): string
    {
        return date("Y-m-d");
    }

    public function getDatetime(string $dateTime): string
    {
        if (!$dateTime) return "";
        if (!$dateTime = trim($dateTime)) return "";
        $dateTime = $this->tryToGetDateObject($dateTime);
        if (!$dateTime) return "";

        return $dateTime->format("Y-m-d H:i:s");
    }

    public function getMinutesBetweenTwoDates(string $startDate, string $endDate=""): int
    {
        if (!$startDate = trim($startDate)) return 0;
        if (!$endDate = trim($endDate)) $endDate = $this->getNow();

        $startDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);

        $interval = $startDate->diff($endDate);
        return ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
    }

    public function getDateFromFormatAsYmd(string $anyDate, string $format="Y-m-d"): ?string
    {
        if (!$dateTmp = DateTime::createFromFormat($format, $anyDate))
            return null;
        return $dateTmp->format("Y-m-d");
    }

    public function getDateAsYmd(?string $anyDate): ?string
    {
        if (!$anyDate) return null;
        if (!$dateTmp = $this->tryToGetDateObject($anyDate))
            return null;
        return $dateTmp->format("Y-m-d");
    }

    private function tryToGetDateObject(?string $anyDateTime): ?DateTime
    {
        if (!$anyDateTime) return null;
        try {
            return (new DateTime($anyDateTime));
        }
        catch (Exception $e) {
            $this->logError("tryToGetDateObject $anyDateTime", $e->getMessage());
            return null;
        }
    }


}

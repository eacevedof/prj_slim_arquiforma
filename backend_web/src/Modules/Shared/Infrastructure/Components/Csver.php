<?php

namespace App\Modules\Shared\Infrastructure\Components;

final class Csver
{
    public static function getInstance(): self
    {
        return new self();
    }

    public function getTmpCsvFile(array $headers, array $rows): string
    {
        $now = date("Ymd_His");
        $uuid = uniqid();
        $filename = "/tmp/csv-$now-$uuid.csv";
        $tmpCsvFile = fopen($filename, "w");

        fputcsv($tmpCsvFile, $headers);
        foreach ($rows as $row) {
            fputcsv($tmpCsvFile, $row);
        }

        fclose($tmpCsvFile);
        return $filename;
    }

}
<?php

namespace App\Modules\Shared\Infrastructure\Components;

use DOMDocument;
use App\Modules\Shared\Infrastructure\Components\Traits\LogTrait;

final class Crawler
{
    use LogTrait;

    private const DEFAULT_TIMEOUT = 10;
    private const USER_AGENT   = "Mozilla/5.0 (compatible; Infusionbot/2.1; +https://www.google.com/bot.html)";

    public static function getInstance(): self
    {
        return new self();
    }

    public function getHtmlContentFromUrl(string $url): array
    {
        $curlObj = curl_init();
        curl_setopt_array($curlObj, [
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => self::USER_AGENT,
            CURLOPT_CONNECTTIMEOUT => self::DEFAULT_TIMEOUT,
            CURLOPT_TIMEOUT => self::DEFAULT_TIMEOUT,
            CURLOPT_ENCODING => "UTF-8",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true, // Follow redirects
        ]);

        $jsonResponse = curl_exec($curlObj);
        $error = curl_error($curlObj);
        $statusCode = (int) curl_getinfo($curlObj, CURLINFO_HTTP_CODE);
        curl_close($curlObj);

        if ($statusCode !== 200) {
            $this->logError("warning $url \n$statusCode: $error", self::class);
        }

        return [
            "status_code" => $statusCode,
            "error" => $error,
            "response" => $jsonResponse,
        ];
    }

    public function getWordsCountResume(
        string $htmlContent,
        array $parentTags,
        string $childTag,
        int $numOfWords
    ): array
    {
        $domDocument = new DOMDocument;
        @$domDocument->loadHTML($htmlContent);

        $wordsCount = [];
        foreach ($parentTags as $parentTag) {
            foreach ($domDocument->getElementsByTagName($parentTag) as $parentElement) {
                foreach ($parentElement->getElementsByTagName($childTag) as $htmlElement) {
                    $innerText = self::getTextWithOneBlankSpacePerWord($htmlElement->nodeValue);
                    $innerText = strip_tags($innerText);
                    if (($numWords = str_word_count($innerText)) > $numOfWords){
                        $wordsCount[] = [
                            'texto' => $innerText,
                            'palabras' => $numWords
                        ];
                    }
                }
            }
        }

        return $this->getUniqueValues($wordsCount);
    }

    private function getUniqueValues(array $texts): array
    {
        $uniqueValues = [];
        foreach ($texts as $i => $text){
            if (!in_array($text, $uniqueValues)){
                $uniqueValues[$i] = $text;
            }
        }

        $keys = array_keys($uniqueValues);
        foreach ($texts as $i => $text){
           if (!in_array($i, $keys))
               unset($texts[$i]);
        }

        return array_values($texts);
    }

    private function getTextWithOneBlankSpacePerWord(?string $text): string
    {
        if (!$text) return "";
        $text = preg_replace('/\s+/', " ", $text);
        return trim($text);
    }

}
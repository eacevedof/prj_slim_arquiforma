<?php

namespace App\Modules\Elastic\Infrastructure\Repositories;

use DateTime;
use DateTimeZone;

use App\Modules\Shared\Infrastructure\Components\Slugger;
use App\Modules\Shared\Infrastructure\Repositories\Configuration\EnvironmentRawRepository;

use App\Modules\Elastic\Domain\Enums\LogLevelEnum;

/*
curl --location 'https://someuser:somepassword@elk.somedomain.com:443/mi_index/_doc' \
--header 'Content-Type: application/json' \
--data-raw '{
	"status" : "start",
	"scope" : "local",
    "@timestamp" : "2025-04-07T15:11:02+00:00"
}'
*/

/**
 * @info no usar LogTrait porque se hariamos referencias circulares
 */
final class ElasticApiWriterRepository
{
    private string $elasticApiUrl = "https://someuser:somepassword@elk.somedomain.com:443";
    private static ?self $instance = null;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance) return self::$instance;
        self::$instance = new self();
        return self::$instance;
    }

    public function logError(string $content): array
    {
        $this->substring10000($content);
        $postPayload = $this->getElasticDocument($content, LogLevelEnum::ERROR);
        return $this->httpPostByPhpMode($postPayload);
    }

    public function logDebug(string $content): array
    {
        $this->substring10000($content);
        $postPayload = $this->getElasticDocument($content, LogLevelEnum::DEBUG);
        return $this->httpPostByPhpMode($postPayload);
    }

    public function logSql(string $content): array
    {
        $postPayload = $this->getElasticDocument($content, LogLevelEnum::SQL);
        return $this->httpPostByPhpMode($postPayload);
    }

    public function logWarning(string $content): array
    {
        $this->substring10000($content);
        $postPayload = $this->getElasticDocument($content, LogLevelEnum::WARNING);
        return $this->httpPostByPhpMode($postPayload);
    }

    private function getElasticDocument(string $content, string $logLevel): array
    {
        $environmentRawRepository = EnvironmentRawRepository::getInstance();
        return [
            "domain" => $environmentRawRepository->getBaseUrl(),
            "environment" => $environmentRawRepository->getEnvironment(),
            "level" => $logLevel,
            "date_time" => date("Y-m-d H:i:s"),
            "remote_ip" => $this->getServerClientIP(),
            "log_content" => $content,
            "@timestamp" => $this->getTimestampInUTC(),
        ];
    }

    private function getServerClientIP(): string
    {
        if (php_sapi_name() === "cli")
            return "cli-".gethostbyname(gethostname());

        return $_SERVER["HTTP_CLIENT_IP"]
            ?? $_SERVER["HTTP_X_FORWARDED_FOR"]
            ?? $_SERVER["REMOTE_ADDR"]
            ?? "unknown";
    }

    private function getTimestampInUTC(): string
    {
        return (new DateTime("now", new DateTimeZone("UTC")))->format("Y-m-d\TH:i:sP");
    }

    private function httpPostByPhpMode(array $postPayload): array
    {
        if (php_sapi_name() === "cli") {
            return $this->getPostRequest($postPayload);
        }
        return $this->getPostRequestAsync($postPayload);
    }

    private function getPostRequestAsync(array $postPayload): array
    {
        $logFolder = EnvironmentRawRepository::getInstance()->getLogPath();

        $today = date("Y-m-d");
        $logFolder = rtrim($logFolder, "/");
        $logElk = "{$logFolder}/elk-$today.log";

        $databaseName = $this->getDatabaseName();
        $url = "{$this->elasticApiUrl}/$databaseName/_doc";

        $jsonPayload = json_encode($postPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $curlCommand = <<<BASH
        nohup curl --silent --location --request POST \\
            --max-time 60 \\
            --url "$url" \\
            --header "Content-Type: application/json" \\
            --data-binary '$jsonPayload' \\
            >> $logElk 2>&1 </dev/null &
        BASH;
        exec($curlCommand);
        $this->logElk($curlCommand, __FILE__. " " . __LINE__);

        return [
            "url" => $url,
            "status_code" => 0,
            "error" => "",
            "response" => $logElk,
        ];
    }

    private function getPostRequest(array $postPayload): array
    {
        if (!$postPayload) return [];

        $databaseName = $this->getDatabaseName();
        $url = "{$this->elasticApiUrl}/$databaseName/_doc";

        $curlObj = curl_init();
        curl_setopt_array($curlObj, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($postPayload),
        ]);

        $jsonResponse = curl_exec($curlObj);
        $error = curl_error($curlObj);
        $statusCode = (int) curl_getinfo($curlObj, CURLINFO_HTTP_CODE);
        curl_close($curlObj);

        return [
            "url" => $url,
            "status_code" => $statusCode,
            "error" => $error,
            "response" => $jsonResponse,
        ];

    }

    private function getDatabaseName(): string
    {
        $environmentRawRepository = EnvironmentRawRepository::getInstance();
        $appName = $environmentRawRepository->getAppName();
        $appEnv = $environmentRawRepository->getEnvironment();

        $dbName = "{{$appEnv}-$appName";
        $dbSlug = Slugger::getInstance()->getSluggedText($dbName);
        return substr($dbSlug, 0, 250);
    }

    private function logElk($mixed, string $title=""): void
    {
        $logFolder = EnvironmentRawRepository::getInstance()->getLogPath();
        $today = date("Y-m-d");
        $logFolder = rtrim($logFolder, "/");
        $fileName = "$logFolder/elk-$today.log";

        $now = date("Y-m-d H:i:s");
        $content = "[$now]";
        if ($title) $content .= " $title\n\t";

        if (!is_string($mixed)) {
            $mixed = var_export($mixed, true);
        }
        $content .= $mixed . PHP_EOL;
        @file_put_contents($fileName, $content , FILE_APPEND);
    }

    private function substring10000(string &$string): void
    {
        $maxLen = 100000;
        if (strlen($string) <= $maxLen) return;

        $string = substr($string, 0, $maxLen). "... [string truncated to $maxLen chars]";
    }

}
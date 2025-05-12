<?php

namespace App\Modules\Shared\Infrastructure\Repositories\Configuration;

use App\Modules\Shared\Infrastructure\Enums\EnvironmentEnum;

final class EnvironmentRawRepository
{
    private readonly array $envVars;
    private static ?self $instance = null;

    public function __construct()
    {
         $this->envVars = [
             "app_key" => getenv("APP_KEY") ?: "Example151e28a26375030c37414ad8e499aeb7b0a3e88a",
             "app_name" => getenv("APP_NAME") ?: "App Name",
             "environment" => getenv("APP_ENV") ?: EnvironmentEnum::PRODUCTION,
             "timezone" => getenv("APP_TIMEZONE") ?: "UTC",
             "base_url" => getenv("APP_BASE_URL") ?: "http://localhost",
             "domain" => getenv("APP_DOMAIN") ?: "localhost",

             "db_host" => getenv("APP_DB_HOST") ?: "localhost",
             "db_user" => getenv("APP_DB_USER") ?: "root",
             "db_pass" => getenv("APP_DB_PASS") ?: "root",
             "db_name" => getenv("APP_DB_NAME") ?: "db_xxx",

             "cookie_name" => getenv("APP_COOKIE_NAME") ?: "app_cookie_name",
             "cookie_secure" => filter_var(getenv("APP_COOKIE_SECURE"), FILTER_VALIDATE_BOOLEAN),
             "cookie_httponly" => filter_var(getenv("APP_COOKIE_HTTPONLY"), FILTER_VALIDATE_BOOLEAN),

             "log_threshold" => (int)(getenv("LOG_THRESHOLD") ?: 4),
             "log_path" => getenv("APP_LOG_PATH") ?: "",

             "system_path" => getenv("APP_SYSTEM_PATH") ?: "",
             "error_views_path" => getenv("APP_ERROR_VIEWS_PATH") ?: "",

            "email_from1" => getenv("APP_EMAIL_FROM1") ?: "",
            "email_from2" => getenv("APP_EMAIL_FROM2") ?: "",
            "email_to1" => getenv("APP_EMAIL_TO1") ?: "",
            "email_to2" => getenv("APP_EMAIL_TO2") ?: "",
         ];
    }

    public static function getInstance(): self
    {
        if (self::$instance) return self::$instance;
        self::$instance = new self();
        return self::$instance;
    }

    public function getAppKey(): string
    {
        return $this->envVars["app_key"] ?? "";
    }

    public function getAppName(): string
    {
        return $this->envVars["app_name"] ?? "";
    }

    public function getEnvironment(): string
    {
        return $this->envVars["environment"];
    }

    public function getBaseUrl(): string
    {
        return $this->envVars["base_url"] ?? "";
    }

    public function getLogPath(): string
    {
        return $this->envVars["log_path"] ?? "";
    }


    public function getEmailFrom1(): string
    {
        return $this->envVars["email_from1"] ?? "";
    }

    public function getEmailTo1(): string
    {
        return $this->envVars["email_to1"] ?? "";
    }

    public function getEmailTo2(): string
    {
        return $this->envVars["email_to2"] ?? "";
    }



    public function getTimezone(): string
    {
        return $this->envVars["timezone"] ?? "";
    }

    public function getDbName(): string
    {
        return $this->envVars["db_name"] ?? "";
    }

    public function isLocal(): bool
    {
        return $this->getEnvironment() === EnvironmentEnum::LOCAL;
    }

    public function isProduction(): bool
    {
        return $this->getEnvironment() === EnvironmentEnum::PRODUCTION;
    }

    public function isLocalOrDevelopment(): bool
    {
        return $this->getEnvironment() === EnvironmentEnum::LOCAL ||
            $this->getEnvironment() === EnvironmentEnum::DEVELOPMENT;
    }

    public function getDomain(): string
    {
        return $this->envVars["domain"] ?? "";
    }

    public function getDbHost(): string
    {
        return $this->envVars["db_host"] ?? "";
    }

    public function getDbUser(): string
    {
        return $this->envVars["db_user"] ?? "";
    }

    public function getDbPass(): string
    {
        return $this->envVars["db_pass"] ?? "";
    }

    public function getCookieName(): string
    {
        return $this->envVars["cookie_name"] ?? "";
    }

    public function getCookieSecure(): bool
    {
        return $this->envVars["cookie_secure"] ?? false;
    }

    public function getCookieHttpOnly(): bool
    {
        return $this->envVars["cookie_httponly"] ?? false;
    }

    public function getLogThreshold(): int
    {
        return $this->envVars["log_threshold"] ?? 0;
    }

    public function getSystemPath(): string
    {
        return $this->envVars["system_path"] ?? "";
    }

    public function getErrorViewsPath(): string
    {
        return $this->envVars["error_views_path"] ?? "";
    }

}
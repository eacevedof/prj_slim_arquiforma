<?php

namespace App\Modules\Shared\Infrastructure\Repositories\Configuration;

use App\Modules\Shared\Infrastructure\Enums\EnvironmentEnum;

final class EnvironmentRawRepository
{
    private readonly array $envVars;
    private static ?self $instance = null;

    private function __construct()
    {
         $this->envVars = [
             "app_name" => getenv("APP_NAME") ?: "",
             "environment" => getenv("APP_ENV") ?: EnvironmentEnum::PRODUCTION,
             "timezone" => getenv("APP_TIMEZONE") ?: "",
             "base_url" => getenv("APP_BASE_URL") ?: "",
             "domain" => getenv("APP_DOMAIN") ?: "",

             "db_host" => getenv("DB_HOST") ?: "",
             "db_user" => getenv("DB_USER") ?: "",
             "db_pass" => getenv("DB_PASS") ?: "",
             "db_name" => getenv("DB_NAME") ?: "",

             "cookie_name" => getenv("COOKIE_NAME") ?: "",
             "cookie_secure" => filter_var(getenv("COOKIE_SECURE"), FILTER_VALIDATE_BOOLEAN),
             "cookie_httponly" => filter_var(getenv("COOKIE_HTTPONLY"), FILTER_VALIDATE_BOOLEAN),

             "log_threshold" => (int)(getenv("LOG_THRESHOLD") ?: 0),
             "log_path" => getenv("LOG_PATH") ?: "",

             "system_path" => getenv("SYSTEM_PATH") ?: "",
             "error_views_path" => getenv("ERROR_VIEWS_PATH") ?: "",

         ];
    }

    public static function getInstance(): self
    {
        if (self::$instance) return self::$instance;
        self::$instance = new self();
        return self::$instance;
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

    public function getAppName(): string
    {
        return $this->envVars["app_name"] ?? "";
    }

    public function getEnvironment(): string
    {
        return $this->envVars["environment"];
    }

    public function getTimezone(): string
    {
        return $this->envVars["timezone"] ?? "";
    }

    public function getBaseUrl(): string
    {
        return $this->envVars["base_url"] ?? "";
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

    public function getDbName(): string
    {
        return $this->envVars["db_name"] ?? "";
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

    public function getLogPath(): string
    {
        return $this->envVars["log_path"] ?? "";
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
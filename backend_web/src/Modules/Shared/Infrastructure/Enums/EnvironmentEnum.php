<?php

namespace App\Modules\Shared\Infrastructure\Enums;

final class EnvironmentEnum
{
    public const string LOCAL = "local";
    public const string DEVELOPMENT = "development";
    public const string TESTING = "testing";

    public const string PRODUCTION = "production";

    private function __construct() {}

}
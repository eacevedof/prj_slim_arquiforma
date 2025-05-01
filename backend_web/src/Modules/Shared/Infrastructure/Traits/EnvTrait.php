<?php

namespace App\Modules\Shared\Infrastructure\Traits;

use App\Modules\Shared\Infrastructure\Repositories\Configuration\EnvironmentRawRepository;

trait EnvTrait
{
    private function getEnvEnvironment(): string
    {
        return EnvironmentRawRepository::getInstance()->getEnvironment();
    }

}
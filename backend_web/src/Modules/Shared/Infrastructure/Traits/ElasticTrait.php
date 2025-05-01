<?php

namespace App\Modules\Shared\Infrastructure\Components\Traits;

use App\Modules\Elastic\Infrastructure\Repositories\ElasticApiWriterRepository;

trait ElasticTrait
{
    protected function logElkDebug(string $content): void
    {
        ElasticApiWriterRepository::getInstance()->logDebug($content);
    }

    protected function logElkSql(string $sql): void
    {
        ElasticApiWriterRepository::getInstance()->logSql($sql);
    }

    protected function logElkError(string $content): void
    {
        ElasticApiWriterRepository::getInstance()->logError($content);
    }

}
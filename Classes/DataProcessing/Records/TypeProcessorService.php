<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Records;

use GeorgRinger\News\Domain\Model\News;

class TypeProcessorService implements NewsProcessorInterface
{
    public function canHandle(string $processStatement): bool
    {
        return $processStatement === 'type';
    }

    /**
     * @return mixed
     */
    public function render(News $newsRecord, array $configuration = [], array $processorConfiguration = [])
    {
        return NewsProcessorService::NEWS_TYPE[$newsRecord->getType()] ?? 'default';
    }
}

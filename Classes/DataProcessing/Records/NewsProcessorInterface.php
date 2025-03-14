<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Records;

use GeorgRinger\News\Domain\Model\News;

interface NewsProcessorInterface
{
    public function canHandle(string $processStatement): bool;

    /**
     * @return mixed
     */
    public function render(News $newsRecord, array $configuration = [], array $processorConfiguration = []): mixed;
}

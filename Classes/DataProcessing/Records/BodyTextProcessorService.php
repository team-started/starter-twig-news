<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Records;

use GeorgRinger\News\Domain\Model\News;
use PrototypeIntegration\PrototypeIntegration\Processor\RichtextProcessor;

class BodyTextProcessorService implements NewsProcessorInterface
{
    private RichtextProcessor $richTextProcessor;

    public function __construct(RichtextProcessor $richTextProcessor)
    {
        $this->richTextProcessor = $richTextProcessor;
    }

    public function canHandle(string $processStatement): bool
    {
        return $processStatement === 'bodyText';
    }

    /**
     * @return mixed
     */
    public function render(News $newsRecord, array $configuration = [], array $processorConfiguration = [])
    {
        return $this->richTextProcessor->processRteText($newsRecord->getBodytext());
    }
}

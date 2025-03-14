<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Records;

use GeorgRinger\News\Domain\Model\News;
use Override;
use PrototypeIntegration\PrototypeIntegration\Processor\RichtextProcessor;

class BodyTextProcessorService implements NewsProcessorInterface
{
    public function __construct(
        private readonly RichtextProcessor $richTextProcessor,
    ) {
    }

    #[Override]
    public function canHandle(string $processStatement): bool
    {
        return $processStatement === 'bodyText';
    }

    /**
     * @return mixed
     */
    #[Override]
    public function render(News $newsRecord, array $configuration = [], array $processorConfiguration = []): mixed
    {
        return $this->richTextProcessor->processRteText($newsRecord->getBodytext());
    }
}

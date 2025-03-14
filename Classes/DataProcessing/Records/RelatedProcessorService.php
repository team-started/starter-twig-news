<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Records;

use GeorgRinger\News\Domain\Model\News;
use Override;

class RelatedProcessorService implements NewsProcessorInterface
{
    protected array $processorConfiguration = [
        'previewOnly' => true,
        'preferConfigurationIndex' => 'list',
    ];

    protected array $processedFields = [
        'listImage' => true,
        'teaser' => true,
        'link' => true,
    ];

    public function __construct(private readonly NewsProcessorService $newsProcessorService)
    {
    }

    #[Override]
    public function canHandle(string $processStatement): bool
    {
        return $processStatement === 'relatedItems';
    }

    /**
     * @return mixed
     */
    #[Override]
    public function render(News $newsRecord, array $configuration = [], array $processorConfiguration = []): mixed
    {
        if (!is_iterable($newsRecord->getRelated())) {
            return null;
        }

        $relatedItems = null;
        foreach ($newsRecord->getRelated() as $relatedNewsItem) {
            $relatedItems[] = $this->newsProcessorService->process(
                $relatedNewsItem,
                $this->processedFields,
                $configuration,
                $this->processorConfiguration
            );
        }

        return $relatedItems;
    }
}

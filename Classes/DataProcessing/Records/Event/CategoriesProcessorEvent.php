<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Records\Event;

use GeorgRinger\News\Domain\Model\News;
use StarterTeam\StarterTwigNews\DataProcessing\Records\CategoriesProcessorService;

final class CategoriesProcessorEvent
{
    private CategoriesProcessorService $processorService;

    private News $newsRecord;

    private array $renderedItemData;

    private array $configuration;

    private array $processorConfiguration;

    public function __construct(CategoriesProcessorService $processorService, News $newsRecord, array $renderedItemData, array $configuration, array $processorConfiguration)
    {
        $this->processorService = $processorService;
        $this->newsRecord = $newsRecord;
        $this->renderedItemData = $renderedItemData;
        $this->configuration = $configuration;
        $this->processorConfiguration = $processorConfiguration;
    }

    public function getProcessorService(): CategoriesProcessorService
    {
        return $this->processorService;
    }

    public function getNewsRecord(): News
    {
        return $this->newsRecord;
    }

    public function getRenderedItemData(): array
    {
        return $this->renderedItemData;
    }

    public function setRenderedItemData(array $itemData): void
    {
        $this->renderedItemData = $itemData;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function getProcessorConfiguration(): array
    {
        return $this->processorConfiguration;
    }
}

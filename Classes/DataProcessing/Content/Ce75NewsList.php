<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Content;

use GeorgRinger\News\Domain\Model\Category;
use GeorgRinger\News\Domain\Model\News;
use PrototypeIntegration\PrototypeIntegration\Processor\PtiDataProcessor;
use StarterTeam\StarterTwigNews\DataProcessing\Records\NewsProcessorService;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class Ce75NewsList implements PtiDataProcessor
{
    protected NewsProcessorService $newsProcessorService;

    protected array $processorConfiguration = [
        'previewOnly' => true,
        'preferConfigurationIndex' => 'list',
    ];

    protected array $processDataStatements = [
        'listImage' => true,
        'teaser' => true,
        'link' => true,
    ];

    public function __construct(NewsProcessorService $newsProcessorService)
    {
        $this->newsProcessorService = $newsProcessorService;
    }

    public function process(array $data, array $configuration): ?array
    {
        $renderedNewsRecordItems = $this->renderNewsItems($data['news'], $configuration, $this->processDataStatements, $this->processorConfiguration);

        return [
            'uid' => $data['contentObjectData']['uid'],
            'space_before_class' => $data['contentObjectData']['space_before_class'],
            'space_after_class' => $data['contentObjectData']['space_after_class'],
            'tx_starter_visibility' => $data['contentObjectData']['tx_starter_visibility'],
            'tx_starter_backgroundcolor' => $data['contentObjectData']['tx_starter_backgroundcolor'],
            'tx_starter_background_fluid' => (bool)$data['contentObjectData']['tx_starter_background_fluid'],
            'tx_starter_container' => $data['contentObjectData']['tx_starter_width'],
            'txNews' => [
                'templateLayout' => (string)$configuration['templateLayout'],
                'itemsCategories' => null,
                'items' => $renderedNewsRecordItems,
                'pagination' => null,
            ],
        ];
    }

    /**
     * @param QueryResult<News> $items
     */
    protected function renderNewsItems(QueryResult $items, array $configuration, array $processedFields, array $processorConfiguration): ?array
    {
        $renderedItems = null;

        foreach ($items as $item) {
            if ($item instanceof News) {
                $renderedItems[] = $this->newsProcessorService->process(
                    $item,
                    $processedFields,
                    $configuration,
                    $processorConfiguration
                );
            }
        }

        return $renderedItems;
    }

    /**
     * @param QueryResult<News> $newsItems
     */
    protected function getCategoryElements(QueryResult $newsItems, bool $appendAllCategory = false): array
    {
        $categories = [];

        if ($appendAllCategory) {
            $categories = [
                [
                    'uid' => null,
                    'label' => LocalizationUtility::translate(
                        'categories.label.all',
                        'customerSitepackage'
                    ),
                ],
            ];
        }

        foreach ($newsItems as $newsItem) {
            if (!$newsItem instanceof News) {
                continue;
            }

            $itemCategories = $newsItem->getCategories();
            if (is_iterable($itemCategories)) {
                foreach ($itemCategories as $itemCategory) {
                    if ($itemCategory instanceof Category) {
                        $categories[] = [
                            'uid' => $itemCategory->getUid(),
                            'title' => $itemCategory->getTitle(),
                        ];
                    }
                }
            }
        }

        return array_unique($categories, SORT_REGULAR);
    }
}

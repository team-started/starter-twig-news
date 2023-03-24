<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Records;

use GeorgRinger\News\Domain\Model\Category;
use GeorgRinger\News\Domain\Model\News;
use Psr\EventDispatcher\EventDispatcherInterface;
use StarterTeam\StarterTwigNews\DataProcessing\Records\Event\CategoriesProcessorEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class CategoriesProcessorService implements NewsProcessorInterface
{
    private ContentObjectRenderer $contentObjectRenderer;

    private EventDispatcherInterface $eventDispatcher;

    private array $properties = ['uid', 'title', 'description'];

    public function __construct(ContentObjectRenderer $contentObjectRenderer, EventDispatcherInterface $eventDispatcher)
    {
        $this->contentObjectRenderer = $contentObjectRenderer;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function canHandle(string $processStatement): bool
    {
        return $processStatement === 'categories';
    }

    /**
     * @return mixed
     */
    public function render(News $newsRecord, array $configuration = [], array $processorConfiguration = [])
    {
        $categories = $newsRecord->getCategories();
        if (is_null($categories)) {
            return null;
        }

        $categoriesData = null;
        foreach ($categories as $category) {
            if ($category instanceof Category) {
                $itemData = [];
                foreach ($this->properties as $property) {
                    $itemData[$property] = $category->_getProperty($property);
                }

                $typoLinkConfiguration = $this->getLinkConfiguration($category->getTitle(), $category->getUid(), $category->getSinglePid(), $configuration);
                $this->addLink($itemData, $typoLinkConfiguration);

                $itemData['showTitle'] = (bool)$configuration['list']['displayCategoryTitle'];

                $event = $this->eventDispatcher->dispatch(new CategoriesProcessorEvent($this, $newsRecord, $itemData, $configuration, $processorConfiguration));
                $categoriesData[] = $event->getRenderedItemData();
            }
        }

        return $categoriesData;
    }

    private function addLink(array &$itemData, array $linkConfiguration): void
    {
        $itemData['link'] = $this->contentObjectRenderer->typoLink_URL($linkConfiguration);
    }

    private function getLinkConfiguration(string $linkTitle, ?int $uid, int $singlePid, array $configuration): array
    {
        if (is_null($uid)) {
            return [];
        }

        return [
            'title' => $linkTitle,
            'class' => (string)$configuration['categoryLinkClass'],
            'parameter' => $singlePid,
            'target' => '_self',
            'additionalParams' => '&' . GeneralUtility::implodeArrayForUrl(
                'tx_news_pi1',
                [
                    'overwriteDemand' => [
                        'categories' => $uid,
                    ],
                    'controller' => 'Category',
                    'action' => 'list',
                ]
            ),
        ];
    }
}

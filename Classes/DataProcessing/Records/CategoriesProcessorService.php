<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Records;

use GeorgRinger\News\Domain\Model\Category;
use GeorgRinger\News\Domain\Model\News;
use Override;
use Psr\EventDispatcher\EventDispatcherInterface;
use StarterTeam\StarterTwigNews\DataProcessing\Records\Event\CategoriesProcessorEvent;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class CategoriesProcessorService implements NewsProcessorInterface
{
    private array $properties = ['uid', 'title', 'description'];

    public function __construct(private readonly ContentObjectRenderer $contentObjectRenderer, private readonly EventDispatcherInterface $eventDispatcher)
    {
    }

    #[Override]
    public function canHandle(string $processStatement): bool
    {
        return $processStatement === 'categories';
    }

    /**
     * @return mixed
     */
    #[Override]
    public function render(News $newsRecord, array $configuration = [], array $processorConfiguration = []): mixed
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

                $typoLinkConfiguration = $this->getLinkConfiguration($category->getTitle(), $category->getUid(), $category->getShortcut(), $configuration);
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
        $itemData['link'] = null;
        if ($linkConfiguration !== []) {
            $uri = $this->contentObjectRenderer->typoLink_URL($linkConfiguration);

            if ($uri !== '') {
                $itemData['link'] = [
                    'config' => [
                        'uri' => $uri,
                        'target' => $linkConfiguration['target'],
                        'class' => $linkConfiguration['class'],
                        'title' => $linkConfiguration['title'],
                    ],
                ];
            }
        }
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
        ];
    }
}

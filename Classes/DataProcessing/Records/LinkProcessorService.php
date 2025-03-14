<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Records;

use GeorgRinger\News\Domain\Model\Category;
use GeorgRinger\News\Domain\Model\News;
use Override;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class LinkProcessorService implements NewsProcessorInterface
{
    protected array $detailPidDeterminationCallbacks = [
        'flexform' => 'getDetailPidFromFlexForm',
        'categories' => 'getDetailPidFromCategories',
        'default' => 'getDetailPidFromDefaultDetailPid',
    ];

    public function __construct(private readonly ContentObjectRenderer $contentObjectRenderer)
    {
    }

    #[Override]
    public function canHandle(string $processStatement): bool
    {
        return $processStatement === 'link';
    }

    /**
     * @return mixed
     */
    #[Override]
    public function render(News $newsRecord, array $configuration = [], array $processorConfiguration = []): mixed
    {
        $basicLinkConfig = [
            'class' => '',
            'title' => $newsRecord->getTitle(),
        ];

        $typeLinkConfig = $this->getLinkConfigurationByNewsType($newsRecord, $configuration);
        $typoLinkConfig = array_merge($typeLinkConfig, $basicLinkConfig);
        $uri = $this->contentObjectRenderer->typoLink_URL($typoLinkConfig);
        if ($uri === '') {
            return null;
        }

        return [
            'config' => [
                'uri' => $uri,
                'target' => $typoLinkConfig['target'],
                'title' => $typoLinkConfig['title'],
                'label' => $newsRecord->getTitle(),
            ],
        ];
    }

    private function getLinkConfigurationByNewsType(News $newsRecord, array $configuration): array
    {
        $typoLinkConfig = match (NewsProcessorService::NEWS_TYPE[$newsRecord->getType()]) {
            'internal' => [
                'parameter' => $newsRecord->getInternalurl(),
                'target' => '_self',
            ],
            'external' => [
                'parameter' => $newsRecord->getExternalurl(),
                'target' => '_blank',
            ],
            default => $this->getLinkToNewsItem($newsRecord, $configuration),
        };

        return $typoLinkConfig;
    }

    protected function getLinkToNewsItem(News $newsRecord, array $configuration): array
    {
        $typoLinkParameter = isset($configuration['detailPid']) ? (int)$configuration['detailPid'] : 0;

        if ($typoLinkParameter === 0) {
            if (!isset($configuration['detailPidDetermination'])) {
                $detailPidDeterminationMethods = ['flexform'];
            } else {
                $detailPidDeterminationMethods = GeneralUtility::trimExplode(
                    ',',
                    $configuration['detailPidDetermination'],
                    true
                );
            }

            foreach ($detailPidDeterminationMethods as $determinationMethod) {
                if ($callback = $this->detailPidDeterminationCallbacks[$determinationMethod]) {
                    $typoLinkParameter = call_user_func($callback, $newsRecord, $configuration);
                    if (is_int($typoLinkParameter)) {
                        break;
                    }
                }
            }

            if (!$typoLinkParameter && isset($GLOBALS['TSFE'])) {
                $typoLinkParameter = $GLOBALS['TSFE']->id;
            }
        }

        return [
            'parameter' => $typoLinkParameter,
            'target' => '_self',
            'additionalParams' => '&' . GeneralUtility::implodeArrayForUrl(
                'tx_news_pi1',
                [
                    'news' => $newsRecord->getUid(),
                    'controller' => 'News',
                    'action' => 'detail',
                ]
            ),
        ];
    }

    /**
     * Gets detailPid from categories of the given news item. First will be return.
     */
    protected function getDetailPidFromCategories(News $newsRecord, array $configuration): int
    {
        $detailPid = 0;
        if ($newsRecord->getCategories() instanceof ObjectStorage) {
            foreach ($newsRecord->getCategories() as $category) {
                if ($category instanceof Category && ($detailPid = $category->getSinglePid())) {
                    break;
                }
            }
        }
        return $detailPid;
    }

    /**
     * Gets detailPid from defaultDetailPid setting
     */
    protected function getDetailPidFromDefaultDetailPid(News $newsItem, array $configuration): int
    {
        return isset($configuration['defaultDetailPid']) ? (int)$configuration['defaultDetailPid'] : 0;
    }

    /**
     * Gets detailPid from flexform of current plugin.
     */
    protected function getDetailPidFromFlexForm(News $newsItem, array $configuration): int
    {
        return isset($configuration['detailPid']) ? (int)$configuration['detailPid'] : 0;
    }
}

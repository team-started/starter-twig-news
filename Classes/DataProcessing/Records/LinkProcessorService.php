<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Records;

use GeorgRinger\News\Domain\Model\News;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class LinkProcessorService implements NewsProcessorInterface
{
    private ContentObjectRenderer $contentObjectRenderer;

    public function __construct(ContentObjectRenderer $contentObjectRenderer)
    {
        $this->contentObjectRenderer = $contentObjectRenderer;
    }

    public function canHandle(string $processStatement): bool
    {
        return $processStatement === 'link';
    }

    /**
     * @return mixed
     */
    public function render(News $newsRecord, array $configuration = [], array $processorConfiguration = [])
    {
        $basicLinkConfig = [
            'class' => '',
            'title' => $newsRecord->getTitle(),
        ];

        $typeLinkConfig = $this->getLinkConfigurationByNewsType($newsRecord, (int)$configuration['detailPid']);
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

    private function getLinkConfigurationByNewsType(News $newsRecord, int $defaultDetailPid): array
    {
        switch (NewsProcessorService::NEWS_TYPE[$newsRecord->getType()]) {
            case 'internal':
                $typoLinkConfig = [
                    'parameter' => $newsRecord->getInternalurl(),
                    'target' => '_self',
                ];
                break;
            case 'external':
                $typoLinkConfig = [
                    'parameter' => $newsRecord->getExternalurl(),
                    'target' => '_blank',
                ];
                break;
            default:
                $typoLinkConfig = [
                    'parameter' => $defaultDetailPid,
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

        return $typoLinkConfig;
    }
}

<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Content;

use Override;
use PrototypeIntegration\PrototypeIntegration\Formatter\DateTimeFormatter;
use PrototypeIntegration\PrototypeIntegration\Processor\PtiDataProcessor;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class Ce77NewsDateMenu implements PtiDataProcessor
{
    protected array $configuration = [];

    public function __construct(
        protected ContentObjectRenderer $contentObjectRenderer,
        protected DateTimeFormatter $dateTimeFormatter,
    ) {
    }

    #[Override]
    public function process(array $data, array $configuration): ?array
    {
        $this->configuration = $configuration;
        $processedDateMenu = $this->processData($data['data']['single']);

        return [
            'uid' => $data['contentObjectData']['uid'],
            'space_before_class' => $data['contentObjectData']['space_before_class'],
            'space_after_class' => $data['contentObjectData']['space_after_class'],
            'tx_starter_visibility' => $data['contentObjectData']['tx_starter_visibility'],
            'tx_starter_backgroundcolor' => $data['contentObjectData']['tx_starter_backgroundcolor'],
            'tx_starter_background_fluid' => (bool)$data['contentObjectData']['tx_starter_background_fluid'],
            'tx_starter_container' => $data['contentObjectData']['tx_starter_width'],
            'txNews' => [
                'templateLayout' => (string)$this->configuration['templateLayout'],
                'items' => $processedDateMenu,
            ],
        ];
    }

    protected function processData(array $data): array
    {
        $result = [];

        foreach ($data as $year => $months) {
            foreach ($months as $month => $entriesPerMonth) {
                $title = $this->dateTimeFormatter->formatWithPattern(
                    [
                        'tm_mday' => 1,
                        'tm_mon' => (int)$month - 1,
                        'tm_year' => $year - 1900,
                    ],
                    'MMMM yyyy'
                );

                $result[$year][] = [
                    'items' => $entriesPerMonth,
                    'title' => $title,
                    'month' => (int)$month,
                    'link' => $this->getLink((int)$this->configuration['listPid'], (int)$month, (int)$year),
                    'link_label' => LocalizationUtility::translate(
                        $entriesPerMonth == 1 ? 'article.entry' : 'article.entries',
                        'customerSitepackage'
                    ),
                ];
            }
        }

        return $result;
    }

    protected function getLink(int $pageUid, int $month, int $year): ?array
    {
        $typoLinkConfig = [
            'parameter' => $pageUid,
            'target' => '_self',
            'additionalParams' => '&' . GeneralUtility::implodeArrayForUrl(
                'tx_news_pi1',
                [
                    'controller' => 'News',
                    'action' => 'list',
                    'overwriteDemand' => [
                        'year' => $year,
                        'month' => $month,
                    ],
                ]
            ),
        ];

        $uri = $this->contentObjectRenderer->typoLink_URL($typoLinkConfig);
        if ($uri !== '') {
            return null;
        }

        return [
            'config' => [
                'uri' => $uri,
                'target' => $typoLinkConfig['target'],
                'title' => '',
            ],
        ];
    }
}

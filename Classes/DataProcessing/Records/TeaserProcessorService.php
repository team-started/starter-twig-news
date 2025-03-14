<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Records;

use GeorgRinger\News\Domain\Model\News;
use Override;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class TeaserProcessorService implements NewsProcessorInterface
{
    public function __construct(private readonly ContentObjectRenderer $contentObjectRenderer)
    {
    }

    #[Override]
    public function canHandle(string $processStatement): bool
    {
        return $processStatement === 'teaser';
    }

    /**
     * @return mixed
     */
    #[Override]
    public function render(News $newsRecord, array $configuration = [], array $processorConfiguration = []): mixed
    {
        $cropConfiguration = $configuration['list']['cropping'];
        $cropSetting = $this->getCropSettings($cropConfiguration);

        $teaserData = $newsRecord->getTeaser();
        $stringToTruncate = trim($teaserData) === '' ? $newsRecord->getBodytext() : $teaserData;

        if ($cropConfiguration['respectHtml']) {
            $content = $this->contentObjectRenderer->cropHTML($stringToTruncate, $cropSetting);
        } else {
            $stringToTruncate = strip_tags($stringToTruncate);
            $content = $this->contentObjectRenderer->crop($stringToTruncate, $cropSetting);
        }

        return $content;
    }

    private function getCropSettings(array $cropConfiguration = []): string
    {
        if ($cropConfiguration === []) {
            return '';
        }

        return $cropConfiguration['maxCharacters']
            . '|' . $cropConfiguration['append']
            . '|' . $cropConfiguration['respectWordBoundaries'];
    }
}

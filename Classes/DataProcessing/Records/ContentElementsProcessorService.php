<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Records;

use GeorgRinger\News\Domain\Model\News;
use GeorgRinger\News\Domain\Model\TtContent;
use Override;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\Exception\ContentRenderingException;

class ContentElementsProcessorService implements NewsProcessorInterface
{
    public function __construct(private readonly ContentObjectRenderer $contentObjectRenderer)
    {
    }

    #[Override]
    public function canHandle(string $processStatement): bool
    {
        return $processStatement === 'contentElements';
    }

    /**
     * @return mixed
     * @throws ContentRenderingException
     */
    #[Override]
    public function render(News $newsRecord, array $configuration = [], array $processorConfiguration = []): mixed
    {
        $contentElements = $newsRecord->getContentElements();
        if (is_null($contentElements)) {
            return null;
        }

        $contentItems = [];
        foreach ($contentElements as $contentElement) {
            if ($contentElement instanceof TtContent) {
                $recordContentObject = $this->contentObjectRenderer->getContentObject('RECORDS');
                if (is_null($recordContentObject)) {
                    continue;
                }

                $contentItems[] = $recordContentObject->render([
                    'tables' => 'tt_content',
                    'source' => $contentElement->getUid(),
                    'source.current' => 1,
                    'dontCheckPid' => 1,
                ]);
            }
        }

        return $contentItems;
    }
}

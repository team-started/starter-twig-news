<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Records;

use GeorgRinger\News\Domain\Model\News;
use StarterTeam\StarterTwigNews\Exception\InvalidNewsProcessorException;
use TYPO3\CMS\Core\Utility\ArrayUtility;

class NewsProcessorService
{
    public const NEWS_TYPE = [
        '0' => 'default',
        '1' => 'internal',
        '2' => 'external',
    ];

    protected array $defaultProcessData = [
        'uid' => true,
        'title' => true,
    ];

    protected array $processData = [
        'type' => true,
        'isTopNews' => true,
        'date' => true,
        'categories' => true,
    ];

    public function __construct(
        /**
         * @var iterable<NewsProcessorInterface> $newsProcessors
         */
        protected iterable $newsProcessors
    ) {
    }

    public function process(News $newsRecord, array $processDataStatements, array $configuration = [], array $processorConfiguration = []): array
    {
        $defaultProcessData = $this->modifyDefaultProcessFields(
            $this->defaultProcessData,
            $configuration['modifyDefaultProcessData'] ?? []
        );
        $processedTwigViewData = $this->processDefaultProperties($newsRecord, $defaultProcessData);
        ArrayUtility::mergeRecursiveWithOverrule($processDataStatements, $this->processData);

        foreach ($processDataStatements as $processDataStatement => $useProcessDataStatement) {
            if (!$useProcessDataStatement) {
                continue;
            }

            foreach ($this->newsProcessors as $processor) {
                if ($processor->canHandle($processDataStatement)) {
                    try {
                        $processedTwigViewData[$processDataStatement] = $processor->render($newsRecord, $configuration, $processorConfiguration);
                    } catch (InvalidNewsProcessorException) {
                        trigger_error(
                            sprintf('News record with uid "%s" could not processed', $newsRecord->getUid()),
                            E_USER_ERROR
                        );
                    }
                }
            }
        }

        return $processedTwigViewData;
    }

    private function processDefaultProperties(News $newsRecord, array $properties): array
    {
        $processedData = [];

        foreach ($properties as $property => $useProperty) {
            if (!$useProperty) {
                continue;
            }

            $propertyGetter = 'get' . ucfirst($property);
            if (method_exists($newsRecord, $propertyGetter)) {
                $processedData[$property] = $newsRecord->$propertyGetter();
            }
        }

        return $processedData;
    }

    private function modifyDefaultProcessFields(array $defaultProcessFields, array $customProcessFields = []): array
    {
        if ($customProcessFields === []) {
            return $defaultProcessFields;
        }

        return array_merge($defaultProcessFields, $customProcessFields);
    }
}

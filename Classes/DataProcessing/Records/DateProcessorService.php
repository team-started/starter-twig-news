<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Records;

use GeorgRinger\News\Domain\Model\News;
use Override;
use StarterTeam\StarterTwig\Processor\DateDataProcessor;

class DateProcessorService implements NewsProcessorInterface
{
    public function __construct(private readonly DateDataProcessor $dateDataProcessor)
    {
    }

    #[Override]
    public function canHandle(string $processStatement): bool
    {
        return $processStatement === 'date';
    }

    /**
     * @return mixed
     */
    #[Override]
    public function render(News $newsRecord, array $configuration = [], array $processorConfiguration = []): mixed
    {
        return $this->dateDataProcessor->process($newsRecord->getDatetime(), $this->getDateFormatSettings($configuration));
    }

    private function getDateFormatSettings(array $configuration): array
    {
        return [
            'dateFormat' => $configuration['dateFormat'] ?? DateDataProcessor::DEFAULT_DATE_FORMAT,
            'calendarFormat' => [
                'day' => $configuration['dateFormat']['day'] ?? DateDataProcessor::DEFAULT_CALENDER_FORMAT['day'],
                'dayOfWeek' => $configuration['calendarFormat']['dayOfWeek'] ?? DateDataProcessor::DEFAULT_CALENDER_FORMAT['dayOfWeek'],
                'month' => $configuration['calendarFormat']['month'] ?? DateDataProcessor::DEFAULT_CALENDER_FORMAT['month'],
                'monthOfYear' => $configuration['calendarFormat']['monthOfYear'] ?? DateDataProcessor::DEFAULT_CALENDER_FORMAT['monthOfYear'],
                'year' => $configuration['calendarFormat']['year'] ?? DateDataProcessor::DEFAULT_CALENDER_FORMAT['year'],
            ],
        ];
    }
}

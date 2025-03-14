<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Content;

use Override;
use PrototypeIntegration\PrototypeIntegration\Processor\PtiDataProcessor;
use StarterTeam\StarterTwigNews\DataProcessing\Records\NewsProcessorService;

class Ce76NewsDetail implements PtiDataProcessor
{
    private array $defaultProcessorOptions = [
        'previewOnly' => false,
        'preferConfigurationIndex' => 'detail',
    ];

    protected array $processorTags = [
        'bodyText' => true,
        'contentElements' => true,
        'relatedItems' => true,
        'media' => true,
    ];

    public function __construct(
        protected NewsProcessorService $newsProcessorService,
    ) {
    }

    #[Override]
    public function process(array $data, array $configuration): ?array
    {
        $processedRecord = $this->newsProcessorService->process(
            $data['newsItem'],
            $this->processorTags,
            $configuration,
            $this->defaultProcessorOptions
        );

        return [
            'uid' => $data['contentObjectData']['uid'],
            'space_before_class' => $data['contentObjectData']['space_before_class'],
            'space_after_class' => $data['contentObjectData']['space_after_class'],
            'tx_starter_visibility' => $data['contentObjectData']['tx_starter_visibility'],
            'tx_starter_backgroundcolor' => $data['contentObjectData']['tx_starter_backgroundcolor'],
            'tx_starter_background_fluid' => (bool)$data['contentObjectData']['tx_starter_background_fluid'],
            'tx_starter_container' => $data['contentObjectData']['tx_starter_width'],
            'txNews' => $processedRecord,
        ];
    }
}

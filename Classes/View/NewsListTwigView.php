<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\View;

use PrototypeIntegration\PrototypeIntegration\View\ExtbaseViewAdapter;
use RuntimeException;
use StarterTeam\StarterTwigNews\DataProcessing\Content\Ce75NewsList;

class NewsListTwigView extends ExtbaseViewAdapter
{
    private Ce75NewsList $dataProcessor;

    public function __construct(Ce75NewsList $dataProcessor)
    {
        $this->dataProcessor = $dataProcessor;
    }

    public function render(): string
    {
        if (empty($this->settings['list']['templateName'])) {
            throw new RuntimeException('No template file defined for Ce75-NewsList');
        }

        $this->template = $this->settings['list']['templateName'];

        return parent::render();
    }

    public function convertVariables(array $variables): array
    {
        $variables['settings']['list']['cropping']['maxCharacters'] = (int)$variables['settings']['cropMaxCharacters'];
        return $this->dataProcessor->process($variables, $variables['settings']) ?? [];
    }
}

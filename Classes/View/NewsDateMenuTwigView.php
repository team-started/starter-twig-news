<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\View;

use PrototypeIntegration\PrototypeIntegration\View\ExtbaseViewAdapter;
use RuntimeException;
use StarterTeam\StarterTwigNews\DataProcessing\Content\Ce77NewsDateMenu;

class NewsDateMenuTwigView extends ExtbaseViewAdapter
{
    private Ce77NewsDateMenu $dataProcessor;

    public function __construct(Ce77NewsDateMenu $dataProcessor)
    {
        $this->dataProcessor = $dataProcessor;
    }

    public function render(): string
    {
        if (empty($this->settings['dateMenu']['templateName'])) {
            throw new RuntimeException('No template file defined for Ce77-NewsDateMenu');
        }

        $this->template = $this->settings['dateMenu']['templateName'];

        return parent::render();
    }

    public function convertVariables(array $variables): array
    {
        return $this->dataProcessor->process($variables, $variables['settings']) ?? [];
    }
}

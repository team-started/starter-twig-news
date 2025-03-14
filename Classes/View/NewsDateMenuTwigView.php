<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\View;

use Override;
use PrototypeIntegration\PrototypeIntegration\View\ExtbaseViewAdapter;
use RuntimeException;
use StarterTeam\StarterTwigNews\DataProcessing\Content\Ce77NewsDateMenu;

class NewsDateMenuTwigView extends ExtbaseViewAdapter
{
    public function __construct(
        private readonly Ce77NewsDateMenu $dataProcessor,
    ) {
    }

    #[Override]
    public function render(): string
    {
        if (empty($this->settings['dateMenu']['templateName'])) {
            throw new RuntimeException('No template file defined for Ce77-NewsDateMenu', 3014042619);
        }

        $this->template = $this->settings['dateMenu']['templateName'];

        return parent::render();
    }

    #[Override]
    public function convertVariables(array $variables): array
    {
        return $this->dataProcessor->process($variables, $variables['settings']) ?? [];
    }
}

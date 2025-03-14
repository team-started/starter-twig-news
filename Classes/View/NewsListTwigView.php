<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\View;

use Override;
use PrototypeIntegration\PrototypeIntegration\View\ExtbaseViewAdapter;
use RuntimeException;
use StarterTeam\StarterTwigNews\DataProcessing\Content\Ce75NewsList;

class NewsListTwigView extends ExtbaseViewAdapter
{
    public function __construct(
        private readonly Ce75NewsList $dataProcessor,
    ) {
    }

    #[Override]
    public function render(): string
    {
        if (empty($this->settings['list']['templateName'])) {
            throw new RuntimeException('No template file defined for Ce75-NewsList', 7597179001);
        }

        $this->template = $this->settings['list']['templateName'];

        return parent::render();
    }

    #[Override]
    public function convertVariables(array $variables): array
    {
        $variables['settings']['list']['cropping']['maxCharacters'] = (int)$variables['settings']['cropMaxCharacters'];
        return $this->dataProcessor->process($variables, $variables['settings']) ?? [];
    }
}

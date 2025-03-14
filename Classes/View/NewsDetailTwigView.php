<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\View;

use Override;
use PrototypeIntegration\PrototypeIntegration\View\ExtbaseViewAdapter;
use RuntimeException;
use StarterTeam\StarterTwigNews\DataProcessing\Content\Ce76NewsDetail;

class NewsDetailTwigView extends ExtbaseViewAdapter
{
    public function __construct(
        private readonly Ce76NewsDetail $dataProcessor,
    ) {
    }

    #[Override]
    public function render(): string
    {
        if (empty($this->settings['detail']['templateName'])) {
            throw new RuntimeException('No template file defined article detail view', 7793196134);
        }
        $this->template = $this->settings['detail']['templateName'];

        return parent::render();
    }

    #[Override]
    public function convertVariables(array $variables): array
    {
        $variables['settings']['list']['cropping']['maxCharacters'] = (int)$variables['settings']['cropMaxCharacters'];
        return $this->dataProcessor->process($variables, $variables['settings']) ?? [];
    }
}

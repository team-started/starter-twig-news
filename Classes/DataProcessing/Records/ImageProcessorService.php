<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\DataProcessing\Records;

use GeorgRinger\News\Domain\Model\FileReference;
use GeorgRinger\News\Domain\Model\News;
use Override;
use PrototypeIntegration\PrototypeIntegration\Processor\ImageProcessor;
use StarterTeam\StarterTwig\Service\RenderMediaService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ImageProcessorService implements NewsProcessorInterface
{
    private array $defaultProcessorOptions = [
        'previewOnly' => true,
        'preferConfigurationIndex' => 'list',
    ];

    private array $imageConfig = [];

    private array $imagePlaceHolderConfig = [];

    private bool $previewOnly = true;

    private bool $useDummyImage = false;

    private string $dummyImage = '';

    public function __construct(
        protected RenderMediaService $renderMediaService,
        protected ImageProcessor $imageProcessor,
    ) {
    }

    #[Override]
    public function canHandle(string $processStatement): bool
    {
        return GeneralUtility::inList('listImage,media', $processStatement);
    }

    /**
     * $processorConfiguration = [
     *      'previewOnly' => true//false,
     *      'preferConfigurationIndex' => 'list'//'detail'
     * ]
     *
     * @return mixed
     */
    #[Override]
    public function render(News $newsRecord, array $configuration = [], array $processorConfiguration = []): mixed
    {
        $this->setConfiguration($configuration, $processorConfiguration);

        $mediaElements = null;
        $mediaData = $this->previewOnly ? $newsRecord->getFalMediaPreviews() : $newsRecord->getFalMediaNonPreviews();
        $mediaData = $this->previewOnly ? $this->searchForPreviewAsset($mediaData) : $mediaData;

        if ($mediaData !== null && $mediaData !== []) {
            foreach ($mediaData as $mediaFile) {
                $mediaElement = $this->renderMediaService->processMediaElement(
                    $mediaFile->getOriginalResource(),
                    $this->imageConfig,
                    $this->imagePlaceHolderConfig
                );

                if (!is_null($mediaElement)) {
                    $mediaElements[] = $this->getCorrectAssetType($mediaElement);
                }
            }
        }

        if (is_null($mediaElements) && $this->previewOnly && $this->useDummyImage && $this->dummyImage !== '') {
            $mediaElements[] = [
                'image' => [
                    'uid' => null,
                    'default' => $this->imageProcessor->renderImage($this->dummyImage, $this->imageConfig),
                    'placeholder' => $this->imageProcessor->renderImage($this->dummyImage, $this->imagePlaceHolderConfig),
                    'tx_starter_show_small' => true,
                    'tx_starter_show_medium' => true,
                    'tx_starter_show_large' => true,
                ],
            ];
        }

        return $mediaElements;
    }

    private function getCorrectAssetType(array $mediaItem): array
    {
        $resultMedia = [];

        if ($mediaItem['type'] == 'image') {
            $resultMedia['image'] = $mediaItem['image'];
            $resultMedia['image']['uid'] = $mediaItem['uid'];
            $resultMedia['image']['placeholder'] =
                isset($mediaItem['thumbnail']) ? $mediaItem['thumbnail']['default'] : null;
        } else {
            $resultMedia['video'] = $mediaItem['video'];
            $resultMedia['video']['uid'] = $mediaItem['uid'];
        }

        return $resultMedia;
    }

    private function searchForPreviewAsset(array $mediaItems, int $previewOption = FileReference::VIEW_LIST_ONLY): ?array
    {
        $assetForPreview = null;

        foreach ($mediaItems as $mediaItem) {
            /**@var FileReference $mediaItem*/
            if ($mediaItem->getShowinpreview() === $previewOption) {
                $assetForPreview[] = $mediaItem;
                break;
            }
        }

        if (is_null($assetForPreview) && $previewOption === FileReference::VIEW_LIST_ONLY) {
            $assetForPreview = $this->searchForPreviewAsset($mediaItems, FileReference::VIEW_LIST_AND_DETAIL);
        }

        return $assetForPreview;
    }

    private function setConfiguration(array $configuration, array $customProcessorConfiguration): void
    {
        $processorOptions = $this->defaultProcessorOptions;
        if ($customProcessorConfiguration !== []) {
            ArrayUtility::mergeRecursiveWithOverrule($processorOptions, $customProcessorConfiguration);
        }

        $this->previewOnly = $processorOptions['previewOnly'];
        $this->imageConfig = $configuration[$processorOptions['preferConfigurationIndex']]['media']['defaultVariant']['imageConfig'] ?? [];
        $this->imagePlaceHolderConfig = $configuration[$processorOptions['preferConfigurationIndex']]['media']['defaultVariant']['imageConfigPreLoad'] ?? [];
        $this->useDummyImage = (bool)$configuration[$processorOptions['preferConfigurationIndex']]['media']['defaultVariant']['displayDummyIfNoMedia'];
        $this->dummyImage = $configuration[$processorOptions['preferConfigurationIndex']]['media']['defaultVariant']['dummyImage'] ?? '';
    }
}

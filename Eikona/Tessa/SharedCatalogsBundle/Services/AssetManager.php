<?php

namespace Eikona\Tessa\SharedCatalogsBundle\Services;

use Akeneo\AssetManager\Application\Asset\ComputeTransformationsAssets\EventAggregatorInterface as ComputeTransformationEventAggregatorInterface;
use Akeneo\AssetManager\Application\Asset\CreateAsset\CreateAssetCommand;
use Akeneo\AssetManager\Application\Asset\CreateAsset\CreateAssetHandler;
use Akeneo\AssetManager\Application\Asset\DeleteAsset\DeleteAssetCommand;
use Akeneo\AssetManager\Application\Asset\DeleteAsset\DeleteAssetHandler;
use Akeneo\AssetManager\Application\Asset\EditAsset\CommandFactory\EditAssetCommandFactory;
use Akeneo\AssetManager\Application\Asset\EditAsset\EditAssetHandler;
use Akeneo\AssetManager\Domain\Model\Asset\AssetCode;
use Akeneo\AssetManager\Domain\Model\Asset\Value\ChannelReference;
use Akeneo\AssetManager\Domain\Model\Asset\Value\LocaleReference;
use Akeneo\AssetManager\Domain\Model\AssetFamily\AssetFamilyIdentifier;
use Akeneo\AssetManager\Domain\Model\Attribute\AbstractAttribute;
use Akeneo\AssetManager\Domain\Model\Attribute\AttributeCode;
use Akeneo\AssetManager\Domain\Model\Attribute\AttributeIdentifier;
use Akeneo\AssetManager\Domain\Query\Attribute\ValueKey;
use Akeneo\AssetManager\Domain\Repository\AssetNotFoundException;
use Akeneo\AssetManager\Domain\Repository\AssetRepositoryInterface;
use Akeneo\AssetManager\Domain\Repository\AttributeRepositoryInterface as AssetAttributeRepositoryInterface;
use Akeneo\AssetManager\Infrastructure\Filesystem\PreviewGenerator\PreviewGeneratorInterface;
use Akeneo\AssetManager\Infrastructure\Filesystem\PreviewGenerator\PreviewGeneratorRegistry;
use Akeneo\AssetManager\Infrastructure\Search\Elasticsearch\Asset\EventAggregatorInterface;
use Akeneo\Pim\Enrichment\AssetManager\Component\Value\AssetCollectionValue;
use Akeneo\Pim\Enrichment\Bundle\Doctrine\Common\Saver\ProductSaver;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;
use Akeneo\Pim\Structure\Component\Repository\AttributeRepositoryInterface;
use Akeneo\Tool\Component\FileStorage\Exception\FileRemovalException;
use Akeneo\Tool\Component\FileStorage\Exception\FileTransferException;
use Eikona\Tessa\ConnectorBundle\Tessa;
use Eikona\Tessa\ConnectorBundle\Utilities\LinkGenerator;

class AssetManager
{
    protected Tessa $tessa;
    protected LinkGenerator $tessaLinkGenerator;
    protected CreateAssetHandler $createAssetHandler;
    protected EditAssetHandler $editAssetHandler;
    protected EditAssetCommandFactory $editAssetCommandFactory;
    protected DeleteAssetHandler $deleteAssetHandler;
    protected AssetAttributeRepositoryInterface $assetAttributeRepository;
    protected AttributeRepositoryInterface $attributeRepository;
    protected AssetRepositoryInterface $assetRepository;
    protected PreviewGeneratorInterface $previewGenerator;
    protected EventAggregatorInterface $indexAssetEventAggregator;
    protected ComputeTransformationEventAggregatorInterface $computeTransformationEventAggregator;
    protected ProductSaver $productSaver;
    protected bool $isSyncEnabled;
    protected string $assetFamilyCode;
    protected string $assetTessaLinkAttributeCode;
    protected string $attributeAffix;

    protected ?AbstractAttribute $linkAttribute = null;

    /**
     * @param Tessa                                         $tessa
     * @param LinkGenerator                                 $tessaLinkGenerator
     * @param CreateAssetHandler                            $createAssetHandler
     * @param EditAssetHandler                              $editAssetHandler
     * @param EditAssetCommandFactory                       $editAssetCommandFactory
     * @param DeleteAssetHandler                            $deleteAssetHandler
     * @param AssetAttributeRepositoryInterface             $assetAttributeRepository
     * @param AttributeRepositoryInterface                  $attributeRepository
     * @param AssetRepositoryInterface                      $assetRepository
     * @param PreviewGeneratorInterface                     $previewGenerator
     * @param EventAggregatorInterface                      $indexAssetEventAggregator
     * @param ComputeTransformationEventAggregatorInterface $computeTransformationEventAggregator
     * @param ProductSaver                                  $productSaver
     * @param bool                                          $isSyncEnabled
     * @param string                                        $assetFamilyCode
     * @param string                                        $assetTessaLinkAttributeCode
     * @param string                                        $attributeAffix
     */
    public function __construct(
        Tessa $tessa,
        LinkGenerator $tessaLinkGenerator,
        CreateAssetHandler $createAssetHandler,
        EditAssetHandler $editAssetHandler,
        EditAssetCommandFactory $editAssetCommandFactory,
        DeleteAssetHandler $deleteAssetHandler,
        AssetAttributeRepositoryInterface $assetAttributeRepository,
        AttributeRepositoryInterface $attributeRepository,
        AssetRepositoryInterface $assetRepository,
        PreviewGeneratorInterface $previewGenerator,
        EventAggregatorInterface $indexAssetEventAggregator,
        ComputeTransformationEventAggregatorInterface $computeTransformationEventAggregator,
        ProductSaver $productSaver,
        bool $isSyncEnabled,
        string $assetFamilyCode,
        string $assetTessaLinkAttributeCode,
        string $attributeAffix
    ) {
        $this->tessa = $tessa;
        $this->tessaLinkGenerator = $tessaLinkGenerator;
        $this->createAssetHandler = $createAssetHandler;
        $this->editAssetHandler = $editAssetHandler;
        $this->editAssetCommandFactory = $editAssetCommandFactory;
        $this->deleteAssetHandler = $deleteAssetHandler;
        $this->assetAttributeRepository = $assetAttributeRepository;
        $this->attributeRepository = $attributeRepository;
        $this->assetRepository = $assetRepository;
        $this->previewGenerator = $previewGenerator;
        $this->indexAssetEventAggregator = $indexAssetEventAggregator;
        $this->computeTransformationEventAggregator = $computeTransformationEventAggregator;
        $this->productSaver = $productSaver;
        $this->isSyncEnabled = $isSyncEnabled;
        $this->assetFamilyCode = $assetFamilyCode;
        $this->assetTessaLinkAttributeCode = $assetTessaLinkAttributeCode;
        $this->attributeAffix = $attributeAffix;
    }

    /**
     * @return bool
     */
    public function isSyncEnabled(): bool
    {
        return $this->isSyncEnabled;
    }

    /**
     * @return string
     */
    public function getAssetFamilyCode(): string
    {
        return $this->assetFamilyCode;
    }

    /**
     * @param string $attributeCode
     *
     * @return string
     */
    public function getLinkedAttributeCode(string $attributeCode): string
    {
        return $attributeCode . $this->attributeAffix;
    }

    /**
     * @param string $tessaAssetId
     *
     * @return string
     * @throws FileRemovalException
     * @throws FileTransferException
     */
    public function createAsset(
        string $tessaAssetId
    ): string {
        try {
            $existingAsset = $this->assetRepository->getByAssetFamilyAndCode(
                AssetFamilyIdentifier::fromString($this->assetFamilyCode),
                AssetCode::fromString($tessaAssetId)
            );
            $tessaLinkValue = $existingAsset->getValues()->findValue(
                ValueKey::create(
                    AttributeIdentifier::fromString($this->getLinkAttributeIdentifier()),
                    ChannelReference::noReference(),
                    LocaleReference::noReference()
                )
            );

            if ($tessaLinkValue) {
                return $tessaLinkValue->getData()->normalize();
            }
        } catch (AssetNotFoundException $e) {
        }

        $createAssetCommand = new CreateAssetCommand(
            $this->assetFamilyCode,
            $tessaAssetId,
            []
        );

        ($this->createAssetHandler)($createAssetCommand);

        $tessaAssetLink = $this->tessaLinkGenerator->getAssetTessaDownloadUrl($tessaAssetId);

        $editAssetCommand = $this->editAssetCommandFactory->create([
           'asset_family_identifier' => $this->assetFamilyCode,
           'code' => $tessaAssetId,
           'values' => [
               'tessa_url' => [
                   'attribute' => $this->getLinkAttributeIdentifier(
                   ),
                   'locale' => null,
                   'channel' => null,
                   'data' => $tessaAssetLink
               ]
           ]
       ]);

        ($this->editAssetHandler)($editAssetCommand);

        return $tessaAssetLink;
    }

    /**
     * @param string $tessaAssetId
     *
     * @return bool
     * @throws FileRemovalException
     * @throws FileTransferException
     */
    public function updateLink(
        string $tessaAssetId
    ): bool {
        $tessaAssetLink = $this->tessaLinkGenerator->getAssetTessaDownloadUrl($tessaAssetId);

        $existingAsset = $this->assetRepository->getByAssetFamilyAndCode(
            AssetFamilyIdentifier::fromString($this->assetFamilyCode),
            AssetCode::fromString($tessaAssetId)
        );
        $tessaLinkValue = $existingAsset->getValues()->findValue(
            ValueKey::create(
                AttributeIdentifier::fromString($this->getLinkAttributeIdentifier()),
                ChannelReference::noReference(),
                LocaleReference::noReference()
            )
        );
        if ($tessaLinkValue) {
            $existingTessaAssetLink = $tessaLinkValue->getData()->normalize();
            if ($existingTessaAssetLink === $tessaAssetLink) {
                return false;
            }
        }

        $editAssetCommand = $this->editAssetCommandFactory->create([
           'asset_family_identifier' => $this->assetFamilyCode,
           'code' => $tessaAssetId,
           'values' => [
               'tessa_url' => [
                   'attribute' => $this->getLinkAttributeIdentifier(),
                   'locale' => null,
                   'channel' => null,
                   'data' => $tessaAssetLink
               ]
           ]
       ]);

        ($this->editAssetHandler)($editAssetCommand);
        return true;
    }

    /**
     * @param string $tessaAssetId
     *
     * @throws AssetNotFoundException
     */
    public function deleteAsset(
        string $tessaAssetId
    ): void {
        $deleteCommand = new DeleteAssetCommand($tessaAssetId, $this->assetFamilyCode);
        ($this->deleteAssetHandler)($deleteCommand);
    }

    /**
     * @param string $tessaAssetId
     */
    public function regeneratePreviews(
        string $tessaAssetId
    ): void {
        $tessaAssetLink = $this->tessaLinkGenerator->getAssetTessaDownloadUrl($tessaAssetId);
        $tessaAssetData = base64_encode($tessaAssetLink);

        $linkAttribute = $this->getLinkAttribute();
        foreach (
            [
                PreviewGeneratorRegistry::PREVIEW_TYPE,
                PreviewGeneratorRegistry::THUMBNAIL_TYPE,
                PreviewGeneratorRegistry::THUMBNAIL_SMALL_TYPE
            ] as $type
        ) {
            $this->previewGenerator->remove($tessaAssetData, $linkAttribute, $type);
            $this->previewGenerator->generate($tessaAssetData, $linkAttribute, $type);
        }
    }

    /**
     * @param string $tessaAssetId
     */
    public function removePreviews(
        string $tessaAssetId
    ): void {
        $tessaAssetLink = $this->tessaLinkGenerator->getAssetTessaDownloadUrl($tessaAssetId);
        $tessaAssetData = base64_encode($tessaAssetLink);

        $linkAttribute = $this->getLinkAttribute();
        foreach (
            [
                PreviewGeneratorRegistry::PREVIEW_TYPE,
                PreviewGeneratorRegistry::THUMBNAIL_TYPE,
                PreviewGeneratorRegistry::THUMBNAIL_SMALL_TYPE
            ] as $type
        ) {
            $this->previewGenerator->remove($tessaAssetData, $linkAttribute, $type);
        }
    }

    /**
     * @param ProductInterface|ProductModelInterface $productOrProductModel
     * @param string                                 $attributeCode
     * @param string|null                            $channelCode
     * @param string|null                            $localeCode
     * @param array                                  $tessaAssetIds
     */
    public function linkAssets(
        $productOrProductModel,
        string $attributeCode,
        ?string $channelCode,
        ?string $localeCode,
        array $tessaAssetIds
    ): void {
        /** @var AssetCollectionValue|null $existingValue */
        $existingValue = $productOrProductModel->getValue($attributeCode, $localeCode, $channelCode);

        // Both empty -> skip
        if (empty($tessaAssetIds) && (!$existingValue || empty($existingValue->getData()))) {
            return;
        }

        // Both filled and the same -> skip
        if ($existingValue && !empty($existingValue->getData())) {
            $assetCodes = array_map(
                static fn(AssetCode $assetCode) => $assetCode->normalize(),
                $existingValue->getData()
            );
            if ($tessaAssetIds === $assetCodes) {
                return;
            }
        }

        // Remove old linkings
        $productOrProductModel->removeValue($existingValue);

        // No new linkings -> skip (linkings already removed)
        if (empty($tessaAssetIds)) {
            return;
        }

        // New linkings -> set value
        $assetCodes = array_map(static fn($tessaAssetId) => AssetCode::fromString($tessaAssetId), $tessaAssetIds);

        if ($channelCode !== null && $localeCode !== null) {
            $value = AssetCollectionValue::scopableLocalizableValue(
                $attributeCode,
                $assetCodes,
                $channelCode,
                $localeCode
            );
        } elseif ($channelCode) {
            $value = AssetCollectionValue::scopableValue($attributeCode, $assetCodes, $channelCode);
        } elseif ($localeCode) {
            $value = AssetCollectionValue::localizableValue($attributeCode, $assetCodes, $localeCode);
        } else {
            $value = AssetCollectionValue::value($attributeCode, $assetCodes);
        }

        $productOrProductModel->addValue($value);
    }

    public function indexAndTransform(): void
    {
        $this->indexAssetEventAggregator->flushEvents();
        if (null !== $this->computeTransformationEventAggregator) {
            $this->computeTransformationEventAggregator->flushEvents();
        }
    }

    /**
     * @return AbstractAttribute
     */
    protected function getLinkAttribute(): AbstractAttribute
    {
        if ($this->linkAttribute === null) {
            $this->linkAttribute = $this->assetAttributeRepository->getByCodeAndAssetFamilyIdentifier(
                AttributeCode::fromString($this->assetTessaLinkAttributeCode),
                AssetFamilyIdentifier::fromString($this->assetFamilyCode)
            );
        }
        return $this->linkAttribute;
    }

    /**
     * @return string
     */
    protected function getLinkAttributeIdentifier(): string
    {
        return $this->getLinkAttribute()->getIdentifier()->stringValue();
    }
}

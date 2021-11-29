<?php

namespace Eikona\Tessa\SharedCatalogsBundle\Services;

use Akeneo\Channel\Component\Repository\ChannelRepositoryInterface;
use Akeneo\Channel\Component\Repository\LocaleRepositoryInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ValueInterface;
use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Akeneo\Pim\Structure\Component\Repository\AttributeRepositoryInterface;
use Akeneo\Pim\WorkOrganization\Workflow\Component\Model\PublishedProductInterface;
use Akeneo\Tool\Component\FileStorage\Exception\FileRemovalException;
use Akeneo\Tool\Component\FileStorage\Exception\FileTransferException;
use Eikona\Tessa\ConnectorBundle\AttributeType\AttributeTypes;
use Eikona\Tessa\SharedCatalogsBundle\Exceptions\LinkedAttributeDifferentConfigException;
use Eikona\Tessa\SharedCatalogsBundle\Exceptions\LinkedAttributeOnWrongLevelException;
use InvalidArgumentException;

class ProductAndProductModelProcessor
{
    protected AssetManager $assetManager;
    protected AttributeRepositoryInterface $attributeRepository;
    protected ChannelRepositoryInterface $channelRepository;
    protected LocaleRepositoryInterface $localeRepository;

    /**
     * @param AssetManager $assetManager
     * @param AttributeRepositoryInterface $attributeRepository
     * @param ChannelRepositoryInterface $channelRepository
     * @param LocaleRepositoryInterface $localeRepository
     */
    public function __construct(
        AssetManager $assetManager,
        AttributeRepositoryInterface $attributeRepository,
        ChannelRepositoryInterface $channelRepository,
        LocaleRepositoryInterface $localeRepository
    ) {
        $this->assetManager = $assetManager;
        $this->attributeRepository = $attributeRepository;
        $this->channelRepository = $channelRepository;
        $this->localeRepository = $localeRepository;
    }

    /**
     * @param ProductInterface $product
     *
     * @throws FileRemovalException
     * @throws FileTransferException
     * @throws LinkedAttributeDifferentConfigException
     * @throws LinkedAttributeOnWrongLevelException
     * @throws InvalidArgumentException
     */
    public function processProduct(ProductInterface $product): void
    {
        if ($product instanceof PublishedProductInterface) {
            throw new InvalidArgumentException('Published products cant be processed');
        }

        if ($product->isVariant() && $product->getFamilyVariant()) {
            $attributeSet = $product
                ->getFamilyVariant()
                ->getVariantAttributeSet($product->getVariationLevel());

            $attributes = $attributeSet ? $attributeSet->getAttributes()->getValues() : [];
        } elseif ($product->getFamily()) {
            $attributes = $product
                ->getFamily()
                ->getAttributes()
                ->getValues();
        } else {
            $attributeCodes = $product->getUsedAttributeCodes();
            $attributes = $this->attributeRepository->findBy(['code' => $attributeCodes]);
        }

        $this->processProductOrProductModel($product, $attributes);
    }

    /**
     * @param ProductModelInterface $productModel
     *
     * @throws FileRemovalException
     * @throws FileTransferException
     * @throws LinkedAttributeDifferentConfigException
     * @throws LinkedAttributeOnWrongLevelException
     */
    public function processProductModel(ProductModelInterface $productModel): void
    {
        if ($productModel->getFamilyVariant()) {
            if ($productModel->isRoot()) {
                $attributes =  $productModel->getFamilyVariant()->getCommonAttributes()->getValues();
            } else {
                $attributeSet = $productModel
                    ->getFamilyVariant()
                    ->getVariantAttributeSet($productModel->getVariationLevel());

                $attributes = $attributeSet ? $attributeSet ->getAttributes()->getValues() : [];
            }
        } else {
            $attributes = [];
        }

        $this->processProductOrProductModel($productModel, $attributes);
    }

    /**
     * @param ProductInterface|ProductModelInterface $productOrProductModel
     * @param AttributeInterface[]                   $attributes
     *
     * @throws FileRemovalException
     * @throws FileTransferException
     * @throws LinkedAttributeDifferentConfigException
     * @throws LinkedAttributeOnWrongLevelException
     */
    private function processProductOrProductModel(
        $productOrProductModel,
        array $attributes
    ): void {
        $attributeCodes = array_map(static fn(AttributeInterface $attribute) => $attribute->getCode(), $attributes);
        $tessaAttributes = array_filter(
            $attributes,
            static fn(AttributeInterface $attribute) => $attribute->getType() === AttributeTypes::TESSA
        );

        foreach ($tessaAttributes as $tessaAttribute) {
            $linkedTessaAssetAttributeCode = $this->assetManager->getLinkedAttributeCode(
                $tessaAttribute->getCode()
            );

            /** @var AttributeInterface|null $linkedTessaAssetAttribute */
            $linkedTessaAssetAttribute = $this->attributeRepository->findOneByIdentifier(
                $linkedTessaAssetAttributeCode
            );

            // Linked asset attribute does not exist -> skip
            if ($linkedTessaAssetAttribute === null) {
                continue;
            }

            // Linked asset attribute is on wrong level -> error
            if (!in_array($linkedTessaAssetAttributeCode, $attributeCodes)) {
                throw new LinkedAttributeOnWrongLevelException(sprintf(
                    'Linked attribute "%s" is not on the same variation level as the tessa attribute "%s"',
                    $linkedTessaAssetAttribute->getCode(),
                    $tessaAttribute->getCode()
                ));
            }

            // Linked asset attribute differs from tessa attribute (scopeable, localizable) -> error
            if (
                ($tessaAttribute->isScopable() !== $linkedTessaAssetAttribute->isScopable())
                || ($tessaAttribute->isLocalizable() !== $linkedTessaAssetAttribute->isLocalizable())
            ) {
                throw new LinkedAttributeDifferentConfigException(sprintf(
                    'Linked attribute "%s" and tessa attribute "%s" have different configurations for scopeable and/o localizable',
                    $linkedTessaAssetAttribute->getCode(),
                    $tessaAttribute->getCode()
                ));
            }

            // Get all possible channel/locale combinations
            $channelLocaleCombinations = $this->getChannelLocaleCombinations($tessaAttribute);

            // Create assets
            foreach ($channelLocaleCombinations as [$channelCode, $localeCode]) {

                // Skip if linked asset attribute is locale specific and does not include this locale
                if ($linkedTessaAssetAttribute->isLocalizable()
                    && $linkedTessaAssetAttribute->isLocaleSpecific()
                    && !in_array($localeCode, $linkedTessaAssetAttribute->getAvailableLocaleCodes(), true)) {
                    continue;
                }

                /** @var ValueInterface|null $tessaAssetIdsValue */
                $tessaAssetIdsValue = $productOrProductModel->getValue(
                    $tessaAttribute->getCode(),
                    $localeCode,
                    $channelCode
                );

                $tessaAssetIds = ($tessaAssetIdsValue !== null && $tessaAssetIdsValue->getData())
                    ? explode(',', $tessaAssetIdsValue->getData())
                    : [];

                // Create asset (if not exists) for each asset id
                foreach ($tessaAssetIds as $tessaAssetId) {
                    $this->assetManager->createAsset($tessaAssetId);
                }

                // Link assets to product/product-model
                $this->assetManager->linkAssets(
                    $productOrProductModel,
                    $linkedTessaAssetAttribute->getCode(),
                    $channelCode,
                    $localeCode,
                    $tessaAssetIds
                );
            }
        }

        // Index and transform (new) assets
        $this->assetManager->indexAndTransform();
    }

    /**
     * @param AttributeInterface $attribute
     *
     * @return array
     */
    private function getChannelLocaleCombinations(AttributeInterface $attribute): array
    {
        if ($attribute->isScopable() && $attribute->isLocalizable()) {
            $combos = [];
            $availableChannels = $this->channelRepository->getFullChannels();
            foreach ($availableChannels as $availableChannel) {
                foreach ($availableChannel->getLocaleCodes() as $localeCode) {
                    $combos[] = [$availableChannel->getCode(), $localeCode];
                }
            }
            return $combos;
        }

        if ($attribute->isScopable() && !$attribute->isLocalizable()) {
            $availableChannelCodes = $this->channelRepository->getChannelCodes();
            return array_map(static function (string $channelCode) {
                return [$channelCode, null];
            }, $availableChannelCodes);
        }

        if (!$attribute->isScopable() && $attribute->isLocalizable()) {
            if ($attribute->isLocaleSpecific()) {
                return array_map(static function (string $localeCode) {
                    return [null, $localeCode];
                }, $attribute->getAvailableLocaleCodes());
            }

            $availableLocaleCodes = $this->localeRepository->getActivatedLocaleCodes();
            return array_map(static function (string $localeCode) {
                return [null, $localeCode];
            }, $availableLocaleCodes);
        }

        return [[null, null]];
    }
}

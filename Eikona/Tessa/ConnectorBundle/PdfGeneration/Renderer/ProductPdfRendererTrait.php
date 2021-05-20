<?php
/**
 * ProductPdfRendererTrait.php
 *
 * @author      Felix Hack <f.hack@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\PdfGeneration\Renderer;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Eikona\Tessa\ConnectorBundle\AttributeType\AttributeTypes;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Eikona\Tessa\ConnectorBundle\Security\AuthGuard;
use Eikona\Tessa\ConnectorBundle\Tessa;

trait ProductPdfRendererTrait
{
    /** @var Tessa  */
    protected $tessa;

    /** @var AuthGuard  */
    protected $authGuard;

    /**
     * {@inheritdoc}
     */
    public function render($object, $format, array $context = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $imagePaths = $this->getImagePaths($object, $context['locale'], $context['scope']);
        $optionLabels = $this->getOptionLabels($object, $context['locale'], $context['scope']);
        $tessaAssets = $this->resolveTessaAssets($object, $context['locale'], $context['scope']);
        $params = array_merge(
            $context,
            [
                'product'           => $object,
                'groupedAttributes' => $this->getGroupedAttributes($object),
                'imagePaths'        => $imagePaths,
                'customFont'        => $this->customFont,
                'optionLabels'      => $optionLabels,
            ]
        );

        $params = $resolver->resolve($params);

        $params['tessaAssets'] = $tessaAssets;

        $this->generateThumbnailsCache($imagePaths, $params['filter']);

        return $this->pdfBuilder->buildPdfOutput(
            $this->templating->render($this->template, $params)
        );
    }

    protected function resolveTessaAssets(ProductInterface $product, $locale, $scope)
    {
        $tessaAssets = [];

        foreach ($this->getAttributeCodes($product) as $attributeCode) {
            $attribute = $this->attributeRepository->findOneByIdentifier($attributeCode);

            if (null !== $attribute && AttributeTypes::TESSA === $attribute->getType()) {
                $assetsValue = $product->getValue(
                    $attribute->getCode(),
                    $attribute->isLocalizable() ? $locale : null,
                    $attribute->isScopable() ? $scope : null
                );

                $assetIds = explode(',', $assetsValue);
                $assets = [];
                foreach ($assetIds as $assetId) {
                    $assets[$assetId] = $this->getTessaAssetUrl($assetId);
                }

                $tessaAssets[$attributeCode] = $assets;
            }
        }

        return $tessaAssets;
    }

    protected function getTessaAssetUrl($assetId) {
        $authGuard = $this->authGuard;
        $downloadToken = $authGuard->getDownloadAuthToken($assetId, 'bild');
        return $this->tessa->getBaseUrl()
            . '/ui/bild.php'
            . '?asset_system_id=' . $assetId
            . '&type=preview'
            . '&key=' . $downloadToken;
    }
}

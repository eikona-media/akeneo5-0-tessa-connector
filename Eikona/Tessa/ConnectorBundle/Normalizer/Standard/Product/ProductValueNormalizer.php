<?php

namespace Eikona\Tessa\ConnectorBundle\Normalizer\Standard\Product;

use Akeneo\Pim\Structure\Component\Query\PublicApi\AttributeType\GetAttributes;
use Eikona\Tessa\ConnectorBundle\AttributeType\Value\TessaAssetsValue;
use Eikona\Tessa\ConnectorBundle\Utilities\LinkGenerator;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProductValueNormalizer extends \Akeneo\Pim\Enrichment\Component\Product\Normalizer\Standard\Product\ProductValueNormalizer
{
    protected LinkGenerator $linkGenerator;

    /**
     * ProductValueNormalizer constructor.
     *
     * @param NormalizerInterface $normalizer
     * @param GetAttributes       $getAttributes
     * @param LinkGenerator       $linkGenerator
     */
    public function __construct(
        NormalizerInterface $normalizer,
        GetAttributes $getAttributes,
        LinkGenerator $linkGenerator
    )
    {
        parent::__construct($normalizer, $getAttributes);
        $this->linkGenerator = $linkGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($entity, $format = null, array $context = []): array
    {
        if ($entity instanceof TessaAssetsValue) {
            $data = $entity->getData();
            $assetIds = empty($data) ? [] : explode(',', $data);

            $assetUrls = array_map(function($assetId) use ($entity) {
                return $this->linkGenerator->getAssetExportUrl($assetId, $entity->getAttributeCode(), $entity->getScopeCode())
                    ?? $this->linkGenerator->getAssetTessaDownloadUrl($assetId, $entity->getScopeCode());
            }, $assetIds);

            return [
                'locale' => $entity->getLocaleCode(),
                'scope' => $entity->getScopeCode(),
                'data' => $data,
                '_links' => $assetUrls
            ];
        }

        return parent::normalize($entity, $format, $context);
    }
}

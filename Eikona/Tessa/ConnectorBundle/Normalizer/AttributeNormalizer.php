<?php
/**
 * AttributeNormalizer.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2020 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Normalizer;

use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Eikona\Tessa\ConnectorBundle\AttributeType\TessaType;
use Eikona\Tessa\ConnectorBundle\Tessa;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class AttributeNormalizer
 *
 * @package Eikona\Tessa\ConnectorBundle\Normalizer
 */
class AttributeNormalizer implements NormalizerInterface
{
    /** @var NormalizerInterface */
    protected $normalizer;

    /** @var Tessa */
    protected $tessa;

    /**
     * @param NormalizerInterface $normalizer
     */
    public function __construct(
        NormalizerInterface $normalizer
    )
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param Tessa $tessa
     */
    public function setTessa(Tessa $tessa)
    {
        $this->tessa = $tessa;
    }

    /**
     * @param AttributeInterface $attribute
     * @param null|string        $format
     * @param array              $context
     *
     * @return array
     */
    public function normalize($attribute, $format = null, array $context = [])
    {
        $normalizedAttribute = $this->normalizer->normalize($attribute, $format, $context);

        $exportUrl = $attribute->getProperty(TessaType::ATTRIBUTE_EXPORT_URL);
        $normalizedAttribute[TessaType::ATTRIBUTE_EXPORT_URL] = $exportUrl;
        $maxDisplayedAssets = $attribute->getProperty(TessaType::ATTRIBUTE_MAX_DISPLAYED_ASSETS);
        $normalizedAttribute[TessaType::ATTRIBUTE_MAX_DISPLAYED_ASSETS] = $maxDisplayedAssets;

        if ($format === 'internal_api' || $format === 'standard') {
            $normalizedAttribute['meta']['canEditAssetsInAkeneoUi'] = !$this->tessa->isAssetEditingInAkeneoUiDisabled();
        }

        return $normalizedAttribute;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $this->normalizer->supportsNormalization($data, $format);
    }
}

<?php
/**
 * ProductModelNotificationNormalizer.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Modification;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;
use Eikona\Tessa\ConnectorBundle\Tessa;
use Eikona\Tessa\ConnectorBundle\Utilities\IdPrefixer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductModelNotificationNormalizer
 *
 * @package Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Modification
 */
class ProductModelNotificationNormalizer implements NotificationNormalizerInterface
{
    /**
     * @var IdPrefixer
     */
    protected $idPrefixer;

    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * ProductModelNotificationNormalizer constructor.
     *
     * @param IdPrefixer          $idPrefixer
     * @param NormalizerInterface $normalizer
     */
    public function __construct(
        IdPrefixer $idPrefixer,
        NormalizerInterface $normalizer
    )
    {
        $this->idPrefixer = $idPrefixer;
        $this->normalizer = $normalizer;
    }

    /**
     * @param ProductModelInterface $entity
     *
     * @return array
     */
    public function normalize($entity): array
    {
        return [
            'id' => $this->idPrefixer->getPrefixedId($entity),
            'code' => $entity->getCode(),
            'type' => Tessa::TYPE_PRODUCT,
            'context' => Tessa::CONTEXT_UPDATE,
            'data' => $this->normalizer->normalize($entity, 'standard'),
        ];
    }

    public function supports($entity): bool
    {
        return $entity instanceof ProductModelInterface;
    }
}

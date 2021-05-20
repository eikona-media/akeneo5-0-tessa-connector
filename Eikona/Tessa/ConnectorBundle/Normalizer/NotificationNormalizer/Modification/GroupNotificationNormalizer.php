<?php
/**
 * GroupNoficationNormalizer.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Modification;

use Akeneo\Pim\Enrichment\Component\Product\Model\GroupInterface;
use Eikona\Tessa\ConnectorBundle\Tessa;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class GroupNotificationNormalizer
 *
 * @package Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Modification
 */
class GroupNotificationNormalizer implements NotificationNormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * GroupNotificationNormalizer constructor.
     *
     * @param NormalizerInterface $normalizer
     */
    public function __construct(
        NormalizerInterface $normalizer
    )
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param GroupInterface $entity
     *
     * @return array
     */
    public function normalize($entity): array
    {
        return [
            'id' => $entity->getId(),
            'code' => $entity->getCode(),
            'type' => Tessa::TYPE_GROUP,
            'context' => Tessa::CONTEXT_UPDATE,
            'data' => $this->normalizer->normalize($entity, 'standard'),
        ];
    }

    public function supports($entity): bool
    {
        return $entity instanceof GroupInterface;
    }
}

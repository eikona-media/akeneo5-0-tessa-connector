<?php
/**
 * ChannelNotificationNormalizer.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Modification;

use Akeneo\Channel\Component\Model\ChannelInterface;
use Eikona\Tessa\ConnectorBundle\Tessa;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ChannelNotificationNormalizer
 *
 * @package Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Modification
 */
class ChannelNotificationNormalizer implements NotificationNormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * ChannelNotificationNormalizer constructor.
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
     * @param ChannelInterface $entity
     *
     * @return array
     */
    public function normalize($entity): array
    {
        return [
            'id' => $entity->getId(),
            'code' => $entity->getCode(),
            'type' => Tessa::TYPE_CHANNEL,
            'context' => Tessa::CONTEXT_UPDATE,
            'data' => $this->normalizer->normalize($entity, 'standard'),
        ];
    }

    public function supports($entity): bool
    {
        return $entity instanceof ChannelInterface;
    }
}

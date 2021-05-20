<?php
/**
 * ChannelNotificationNormalizer.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Deletion;

use Eikona\Tessa\ConnectorBundle\Tessa;

/**
 * Class ChannelNotificationNormalizer
 *
 * @package Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Deletion
 */
class ChannelNotificationNormalizer implements NotificationNormalizerInterface
{
    public function normalize($type, $id, $identifier): array
    {
        return [
            'id' => $id,
            'code' => $identifier,
            'type' => $type,
            'context' => Tessa::CONTEXT_DELETE,
            'resourceName' => Tessa::RESOURCE_NAME_CHANNEL,
        ];
    }

    public function supports($type): bool
    {
        return $type === Tessa::TYPE_CHANNEL;
    }
}

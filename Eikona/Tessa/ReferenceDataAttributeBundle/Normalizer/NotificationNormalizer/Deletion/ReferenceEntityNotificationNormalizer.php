<?php
/**
 * ReferenceEntityNotificationNormalizer.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Normalizer\NotificationNormalizer\Deletion;

use Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Deletion\NotificationNormalizerInterface;
use Eikona\Tessa\ConnectorBundle\Tessa;

/**
 * Class ReferenceEntityNotificationNormalizer
 *
 * @package Eikona\Tessa\ReferenceDataAttributeBundle\Normalizer\NotificationNormalizer\Deletion
 */
class ReferenceEntityNotificationNormalizer implements NotificationNormalizerInterface
{
    public function normalize($type, $id, $identifier): array
    {
        return [
            'id' => $id,
            'code' => $identifier,
            'type' => $type,
            'context' => Tessa::CONTEXT_DELETE,
            'resourceName' => Tessa::RESOURCE_NAME_ENTITY_RECORD,
        ];
    }

    public function supports($type): bool
    {
        return $type === Tessa::TYPE_ENTITY_RECORD;
    }
}

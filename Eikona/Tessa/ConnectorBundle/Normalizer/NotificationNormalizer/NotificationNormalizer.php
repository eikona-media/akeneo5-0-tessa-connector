<?php
/**
 * NotificationNormalizer.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer;

use Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Deletion\NotificationNormalizerInterface as DeletionNotificationNormalizerInterface;
use Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Modification\NotificationNormalizerInterface as ModificationNotificationNormalizerInterface;

/**
 * Class NotificationNormalizer
 *
 * @package Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer
 */
class NotificationNormalizer
{
    /** @var ModificationNotificationNormalizerInterface[] */
    private $modificationNormalizers = [];

    /** @var DeletionNotificationNormalizerInterface[] */
    private $deletionNormalizers = [];

    /**
     * NotificationNormalizer constructor.
     *
     * @param iterable $modificationNormalizers
     * @param iterable $deletionNormalizers
     */
    public function __construct(
        iterable $modificationNormalizers,
        iterable $deletionNormalizers
    )
    {
        foreach ($modificationNormalizers as $modificationNormalizer) {
            $this->modificationNormalizers[] = $modificationNormalizer;
        }
        foreach ($deletionNormalizers as $deletionNormalizer) {
            $this->deletionNormalizers[] = $deletionNormalizer;
        }
    }

    /**
     * @param $entity
     *
     * @return array
     */
    public function normalizeModification($entity): array
    {
        foreach ($this->modificationNormalizers as $modificationNormalizer) {
            if ($modificationNormalizer->supports($entity)) {
                return $modificationNormalizer->normalize($entity);
            }
        }

        throw new \RuntimeException('No normalizer found');
    }

    /**
     * @param $type
     * @param $id
     * @param $identifier
     *
     * @return array
     */
    public function normalizeDeletion($type, $id, $identifier): array
    {
        foreach ($this->deletionNormalizers as $deletionNormalizer) {
            if ($deletionNormalizer->supports($type)) {
                return $deletionNormalizer->normalize($type, $id, $identifier);
            }
        }

        throw new \RuntimeException('No normalizer found');
    }
}

<?php
/**
 * NotificationNormalizerInterface.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Deletion;

/**
 * Interface NotificationNormalizerInterface
 *
 * @package Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Deletion
 */
interface NotificationNormalizerInterface
{
    public function normalize($type, $id, $identifier): array;
    public function supports($type): bool;
}

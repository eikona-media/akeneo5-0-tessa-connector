<?php
/**
 * NotificationNormalizerInterface.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Modification;

/**
 * Interface NotificationNormalizerInterface
 *
 * @package Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Modification
 */
interface NotificationNormalizerInterface
{
    public function normalize($entity): array;
    public function supports($entity): bool;
}

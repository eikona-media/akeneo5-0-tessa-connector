<?php
/**
 * ProductNotificationNormalizer.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Deletion;

use Eikona\Tessa\ConnectorBundle\Tessa;
use Eikona\Tessa\ConnectorBundle\Utilities\IdPrefixer;

/**
 * Class ProductNotificationNormalizer
 *
 * @package Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Deletion
 */
class ProductNotificationNormalizer implements NotificationNormalizerInterface
{
    /**
     * @var IdPrefixer
     */
    protected $idPrefixer;

    /**
     * ProductNotificationNormalizer constructor.
     *
     * @param IdPrefixer $idPrefixer
     */
    public function __construct(
        IdPrefixer $idPrefixer
    )
    {
        $this->idPrefixer = $idPrefixer;
    }

    public function normalize($type, $id, $identifier): array
    {
        return [
            'id' => $this->idPrefixer->prefixProductId($identifier),
            'code' => $identifier,
            'type' => $type,
            'context' => Tessa::CONTEXT_DELETE,
            'resourceName' => Tessa::RESOURCE_NAME_PRODUCT,
        ];
    }

    public function supports($type): bool
    {
        return $type === Tessa::TYPE_PRODUCT;
    }
}

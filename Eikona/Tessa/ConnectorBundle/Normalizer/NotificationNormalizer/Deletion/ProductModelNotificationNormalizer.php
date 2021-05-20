<?php
/**
 * ProductModelNotificationNormalizer.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Deletion;

use Eikona\Tessa\ConnectorBundle\Tessa;
use Eikona\Tessa\ConnectorBundle\Utilities\IdPrefixer;

/**
 * Class ProductModelNotificationNormalizer
 *
 * @package Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Deletion
 */
class ProductModelNotificationNormalizer implements NotificationNormalizerInterface
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
            'id' => $this->idPrefixer->prefixProductModelId($id),
            'code' => $identifier,
            'type' => Tessa::TYPE_PRODUCT, // Tessa unterscheidet nicht zwischen Product und ProductModel
            'context' => Tessa::CONTEXT_DELETE,
            'resourceName' => Tessa::RESOURCE_NAME_PRODUCT_MODEL,
        ];
    }

    public function supports($type): bool
    {
        return $type === Tessa::TYPE_PRODUCT_MODEL;
    }
}

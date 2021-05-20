<?php
/**
 * IdPrefixer.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2018 Eikona AG (http://eikona.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Utilities;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;

class IdPrefixer
{
    /**
     * Gibt die ID eines Produkts oder Produkt-Modells mit einem
     * Prefix für Tessa zurück
     *
     * @param ProductInterface|ProductModelInterface $product
     * @param mixed|null $id
     * @return string
     */
    public function getPrefixedId($product, $id = null)
    {
        if ($product instanceof ProductInterface) {
            return $this->prefixProductId($id !== null ? $id : $product->getId());
        }

        if ($product instanceof ProductModelInterface) {
            return $this->prefixProductModelId($id !== null ? $id : $product->getId());
        }

        throw new \InvalidArgumentException('Invalid type for parameter $product');
    }

    /**
     * @param $id
     * @return string|int
     */
    public function prefixProductId($id)
    {
        return 'P-' . (string)$id;
    }

    /**
     * @param $id
     * @return string|int
     */
    public function prefixProductModelId($id)
    {
        return 'PM-' . (string)$id;
    }
}

<?php
/**
 * TessaMaskItemGenerator.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2020 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Product\Completeness\MaskItemGenerator;

use Akeneo\Pim\Enrichment\Component\Product\Completeness\MaskItemGenerator\DefaultMaskItemGenerator;
use Eikona\Tessa\ConnectorBundle\AttributeType\AttributeTypes;

/**
 * Class TessaMaskItemGenerator
 *
 * @package Eikona\Tessa\ConnectorBundle\Product\Completeness\MaskItemGenerator
 */
class TessaMaskItemGenerator extends DefaultMaskItemGenerator
{
    public function supportedAttributeTypes(): array
    {
        return [
            AttributeTypes::TESSA,
        ];
    }
}

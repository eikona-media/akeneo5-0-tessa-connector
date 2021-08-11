<?php
/**
 * TessaType.php
 *
 * @author    Matthias Mahler <m.mahler@eikona.de>
 * @copyright 2017 Eikona AG (http://www.eikona.de)
 */

namespace Eikona\Tessa\ConnectorBundle\AttributeType;

use Akeneo\Pim\Structure\Component\AttributeType\AbstractAttributeType;

class TessaType extends AbstractAttributeType
{
    const ATTRIBUTE_EXPORT_URL = 'tessa_export_url';
    const ATTRIBUTE_MAX_DISPLAYED_ASSETS = 'max_displayed_assets';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return AttributeTypes::TESSA;
    }
}

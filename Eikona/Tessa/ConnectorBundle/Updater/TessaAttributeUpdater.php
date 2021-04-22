<?php
/**
 * TessaAttributeUpdater.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2020 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Updater;

use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Akeneo\Tool\Component\StorageUtils\Updater\ObjectUpdaterInterface;

/**
 * Class TessaAttributeUpdater
 *
 * @package Eikona\Tessa\ConnectorBundle\Updater
 */
class TessaAttributeUpdater implements ObjectUpdaterInterface
{
    /** @var ObjectUpdaterInterface */
    protected $attributeUpdater;

    /** @var array */
    protected $properties;

    /**
     * @param ObjectUpdaterInterface $objectUpdater
     * @param array                  $properties
     */
    public function __construct(ObjectUpdaterInterface $objectUpdater, array $properties)
    {
        $this->attributeUpdater = $objectUpdater;
        $this->properties = $properties;
    }

    /**
     * {@inheritdoc}
     *
     * @param AttributeInterface $attribute
     */
    public function update($attribute, array $data, array $options = [])
    {
        $filteredData = [];
        foreach ($data as $field => $value) {
            if (in_array($field, $this->properties)) {
                $attribute->setProperty($field, $value);
            } else {
                $filteredData[$field] = $value;
            }
        }

        $this->attributeUpdater->update($attribute, $filteredData, $options);

        return $this;
    }
}

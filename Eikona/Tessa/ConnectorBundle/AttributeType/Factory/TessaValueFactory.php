<?php

namespace Eikona\Tessa\ConnectorBundle\AttributeType\Factory;

use Akeneo\Pim\Enrichment\Component\Product\Factory\Value\ScalarValueFactory;
use Akeneo\Pim\Enrichment\Component\Product\Model\ValueInterface;
use Akeneo\Pim\Structure\Component\Query\PublicApi\AttributeType\Attribute;
use Akeneo\Tool\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use Eikona\Tessa\ConnectorBundle\AttributeType\AttributeTypes;

class TessaValueFactory extends ScalarValueFactory
{
    /**
     * {@inheritDoc}
     */
    public function createByCheckingData(Attribute $attribute, ?string $channelCode, ?string $localeCode, $data): ValueInterface
    {
        if (!is_scalar($data) || (is_string($data) && '' === trim($data))) {
            throw InvalidPropertyTypeException::stringExpected(
                $attribute->code(),
                static::class,
                $data
            );
        }

        return parent::createWithoutCheckingData($attribute, $channelCode, $localeCode, $data);
    }

    public function supportedAttributeType(): string
    {
        return AttributeTypes::TESSA;
    }
}

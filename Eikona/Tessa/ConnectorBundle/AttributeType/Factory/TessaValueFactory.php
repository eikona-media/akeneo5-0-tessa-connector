<?php

namespace Eikona\Tessa\ConnectorBundle\AttributeType\Factory;

use Akeneo\Pim\Enrichment\Component\Product\Factory\Value\ValueFactory;
use Akeneo\Pim\Enrichment\Component\Product\Model\ValueInterface;
use Akeneo\Pim\Structure\Component\Query\PublicApi\AttributeType\Attribute;
use Akeneo\Tool\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use Eikona\Tessa\ConnectorBundle\AttributeType\AttributeTypes;
use Eikona\Tessa\ConnectorBundle\AttributeType\Value\TessaAssetsValue;

class TessaValueFactory implements ValueFactory
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

        return $this->createWithoutCheckingData($attribute, $channelCode, $localeCode, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function createWithoutCheckingData(
        Attribute $attribute,
        ?string $channelCode,
        ?string $localeCode,
        $data
    ): ValueInterface {

        $attributeCode = $attribute->code();

        if ($attribute->isLocalizableAndScopable()) {
            return TessaAssetsValue::scopableLocalizableValue($attributeCode, $data, $channelCode, $localeCode);
        }

        if ($attribute->isScopable()) {
            return TessaAssetsValue::scopableValue($attributeCode, $data, $channelCode);
        }

        if ($attribute->isLocalizable()) {
            return TessaAssetsValue::localizableValue($attributeCode, $data, $localeCode);
        }

        return TessaAssetsValue::value($attributeCode, $data);
    }

    public function supportedAttributeType(): string
    {
        return AttributeTypes::TESSA;
    }
}

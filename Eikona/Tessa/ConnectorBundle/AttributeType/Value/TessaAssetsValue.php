<?php

namespace Eikona\Tessa\ConnectorBundle\AttributeType\Value;

use Akeneo\Pim\Enrichment\Component\Product\Model\AbstractValue;
use Akeneo\Pim\Enrichment\Component\Product\Model\ValueInterface;

class TessaAssetsValue extends AbstractValue implements ValueInterface
{
    /** @var mixed */
    protected $data;

    /**
     * {@inheritdoc}
     */
    protected function __construct(string $attributeCode, $data = null, ?string $scopeCode, ?string $localeCode)
    {
        parent::__construct($attributeCode, $data, $scopeCode, $localeCode);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return (string) $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function isEqual(ValueInterface $value): bool
    {
        if (!$value instanceof TessaAssetsValue) {
            return false;
        }

        return $this->getScopeCode() === $value->getScopeCode() &&
            $this->getLocaleCode() === $value->getLocaleCode() &&
            $value->getData() === $this->getData();
    }
}

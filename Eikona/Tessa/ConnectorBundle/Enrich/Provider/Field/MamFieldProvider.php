<?php

namespace Eikona\Tessa\ConnectorBundle\Enrich\Provider\Field;

use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Akeneo\Platform\Bundle\UIBundle\Provider\Field\FieldProviderInterface;

class MamFieldProvider implements FieldProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getField($attribute)
    {
        return 'eikona-tessa-field';
    }

    /**
     * {@inheritdoc}
     */
    public function supports($element)
    {
        /* @var $element AttributeInterface */
        return $element instanceof AttributeInterface &&
            $element->getType() === 'eikona_catalog_tessa';
    }
}

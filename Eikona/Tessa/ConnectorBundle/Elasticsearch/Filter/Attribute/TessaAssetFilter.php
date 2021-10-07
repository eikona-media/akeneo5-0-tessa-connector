<?php

namespace Eikona\Tessa\ConnectorBundle\Elasticsearch\Filter\Attribute;

use Akeneo\Pim\Enrichment\Bundle\Elasticsearch\Filter\Attribute\AbstractAttributeFilter;
use Akeneo\Pim\Enrichment\Component\Product\Exception\InvalidOperatorException;
use Akeneo\Pim\Enrichment\Component\Product\Query\Filter\AttributeFilterInterface;
use Akeneo\Pim\Enrichment\Component\Product\Query\Filter\Operators;
use Akeneo\Pim\Enrichment\Component\Product\Validator\ElasticsearchFilterValidator;
use Akeneo\Pim\Structure\Component\Model\AttributeInterface;

/**
 * TessaAssetFilter.php
 *
 * Wandelt Filter-Anweisungen in Elasticsearch-Query-Regeln um
 *
 * @author    Timo Müller <t.mueller@eikona-media.de>
 * @copyright 2018 Eikona AG (http://www.eikona.de)
 */
class TessaAssetFilter extends AbstractAttributeFilter implements AttributeFilterInterface
{

    /**
     * @param ElasticsearchFilterValidator $filterValidator
     * @param array $supportedAttributeTypes
     * @param array $supportedOperators
     */
    public function __construct(
        ElasticsearchFilterValidator $filterValidator,
        array $supportedAttributeTypes = [],
        array $supportedOperators = []
    )
    {
        $this->filterValidator = $filterValidator;
        $this->supportedAttributeTypes = $supportedAttributeTypes;
        $this->supportedOperators = $supportedOperators;
    }

    /**
     * Add an attribute to filter
     *
     * @param AttributeInterface $attribute the attribute
     * @param string $operator the used operator
     * @param string|array $value the value(s) to filter
     * @param string $locale the locale
     * @param string $channel the channel
     * @param array $options the filter options
     *
     * @return AttributeFilterInterface
     */
    public function addAttributeFilter(
        AttributeInterface $attribute,
        $operator,
        $value,
        $locale = null,
        $channel = null,
        $options = []
    )
    {
        if (null === $this->searchQueryBuilder) {
            throw new \LogicException('The search query builder is not initialized in the filter.');
        }

        $this->checkLocaleAndChannel($attribute, $locale, $channel);

        $attributePath = $this->getAttributePath($attribute, $locale, $channel);

        switch ($operator) {
            case Operators::IS_EMPTY:
                $clause = [
                    'exists' => [
                        'field' => $attributePath,
                    ],
                ];
                $this->searchQueryBuilder->addMustNot($clause);
                break;

            case Operators::IS_NOT_EMPTY:
                $clause = [
                    'exists' => [
                        'field' => $attributePath,
                    ],
                ];
                $this->searchQueryBuilder->addFilter($clause);
                break;

            default:
                throw InvalidOperatorException::notSupported($operator, static::class);
        }

        return $this;
    }
}

<?php

namespace Eikona\Tessa\ConnectorBundle\DataGrid\Filter\ProductValue;

use Akeneo\Pim\Enrichment\Component\Product\Query\Filter\Operators;
use Oro\Bundle\FilterBundle\Datasource\FilterDatasourceAdapterInterface;
use Oro\Bundle\FilterBundle\Filter\BooleanFilter;
use Oro\Bundle\FilterBundle\Form\Type\Filter\BooleanFilterType;
use Oro\Bundle\PimFilterBundle\Filter\ProductFilterUtility;

/**
 * TessaBooleanFilter.php
 *
 * @author    Timo Müller <t.mueller@eikona-media.de>
 * @copyright 2018 Eikona AG (http://www.eikona.de)
 */
class TessaBooleanFilter extends BooleanFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply(FilterDatasourceAdapterInterface $ds, $data)
    {
        $data = $this->parseData($data);
        if (!$data) {
            return false;
        }

        switch ($data['value']) {
            case BooleanFilterType::TYPE_YES:
                $this->util->applyFilter($ds, $this->get(ProductFilterUtility::DATA_NAME_KEY), Operators::IS_NOT_EMPTY, null);
                break;
            case BooleanFilterType::TYPE_NO:
                $this->util->applyFilter($ds, $this->get(ProductFilterUtility::DATA_NAME_KEY), Operators::IS_EMPTY, null);
                break;
        }

        return true;
    }
}

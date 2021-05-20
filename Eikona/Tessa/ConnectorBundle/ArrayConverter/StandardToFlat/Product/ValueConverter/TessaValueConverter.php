<?php
/**
 * TessaValueConverter.php
 *
 * @author    Matthias Mahler <m.mahler@eikona.de>
 * @copyright 2017 Eikona AG (http://www.eikona.de)
 */

namespace Eikona\Tessa\ConnectorBundle\ArrayConverter\StandardToFlat\Product\ValueConverter;

use Akeneo\Pim\Enrichment\Component\Product\Connector\ArrayConverter\FlatToStandard\AttributeColumnsResolver;
use Akeneo\Pim\Enrichment\Component\Product\Connector\ArrayConverter\StandardToFlat\Product\ValueConverter\AbstractValueConverter;
use Eikona\Tessa\ConnectorBundle\Utilities\LinkGenerator;

class TessaValueConverter extends AbstractValueConverter
{
    /**
     * @var LinkGenerator
     */
    private $linkGenerator;

    /**
     * TessaValueConverter constructor.
     *
     * @param AttributeColumnsResolver $columnsResolver
     * @param LinkGenerator            $linkGenerator
     */
    public function __construct(
        AttributeColumnsResolver $columnsResolver,
        LinkGenerator $linkGenerator
    )
    {
        $this->linkGenerator = $linkGenerator;
        parent::__construct($columnsResolver, ['eikona_catalog_tessa']);
    }

    /**
     * Converts a value
     *
     * @param string $attributeCode
     * @param mixed  $data
     *
     * @return array
     */
    public function convert($attributeCode, $data)
    {
        $convertedItem = [];

        foreach ($data as $value) {
            $flatName = $this->columnsResolver->resolveFlatAttributeName(
                $attributeCode,
                $value['locale'],
                $value['scope']
            );

            $convertedItem[$flatName] = $this->convertAssetIdsToUrls(
                $attributeCode,
                $value['data'],
                $value['scope']
            );
        }

        return $convertedItem;
    }

    /**
     * @param        $attributeCode
     * @param string $data
     * @param string $scope
     *
     * @return string
     */
    private function convertAssetIdsToUrls($attributeCode, $data, $scope)
    {
        if (trim($data) === '') {
            return '';
        }

        $assetIds = explode(',', $data);
        $assetUrls = array_map(function ($assetId) use ($attributeCode, $scope) {
            return $this->linkGenerator->getAssetExportUrl($assetId, $attributeCode, $scope)
                ?? $this->linkGenerator->getAssetTessaDownloadUrl($assetId, $scope);
        }, $assetIds);


        return implode(';', $assetUrls);
    }
}

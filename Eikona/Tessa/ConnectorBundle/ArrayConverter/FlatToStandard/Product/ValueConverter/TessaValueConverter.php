<?php
/**
 * TessaValueConverter.php
 *
 * @author    Matthias Mahler <m.mahler@eikona.de>
 * @copyright 2017 Eikona AG (http://www.eikona.de)
 */

namespace Eikona\Tessa\ConnectorBundle\ArrayConverter\FlatToStandard\Product\ValueConverter;

use Akeneo\Pim\Enrichment\Component\Product\Connector\ArrayConverter\FlatToStandard\FieldSplitter;
use Akeneo\Pim\Enrichment\Component\Product\Connector\ArrayConverter\FlatToStandard\ValueConverter\AbstractValueConverter;
use Eikona\Tessa\ConnectorBundle\Utilities\LinkParser;

class TessaValueConverter extends AbstractValueConverter
{
    protected $supportedFieldType = ['eikona_catalog_tessa'];

    /**
     * @var LinkParser
     */
    protected $linkParser;

    /**
     * TessaValueConverter constructor.
     *
     * @param FieldSplitter $fieldSplitter
     * @param LinkParser    $linkParser
     */
    public function __construct(
        FieldSplitter $fieldSplitter,
        LinkParser $linkParser
    )
    {
        parent::__construct($fieldSplitter);
        $this->linkParser = $linkParser;
    }

    /**
     * Converts a value
     *
     * @param array $attributeFieldInfo
     * @param $value
     * @return array
     */
    public function convert(array $attributeFieldInfo, $value): array
    {
        if ($value === '' || trim((string)$value) === '') {
            $data = null;
        } else {
            $data = $this->convertUrlsToAssetIds(
                trim((string)$value)
            );
        }

        return [$attributeFieldInfo['attribute']->getCode() => [[
            'locale' => $attributeFieldInfo['locale_code'],
            'scope'  => $attributeFieldInfo['scope_code'],
            'data'   => $data,
        ]]];
    }

    /**
     * @param string $data
     *
     * @return string
     */
    private function convertUrlsToAssetIds($data): string
    {
        $assetUrls = explode(';', $data);
        $assetIds = array_map(function ($assetUrl) {
            return  $this->linkParser->getAssetIdFromTessaUrl($assetUrl);
        }, $assetUrls);

        return implode(',', $assetIds);
    }
}

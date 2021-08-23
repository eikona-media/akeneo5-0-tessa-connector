<?php
/**
 * TessaValueTransformer.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Record;

use Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute;
use Akeneo\ReferenceEntity\Infrastructure\Persistence\Sql\Record\Hydrator\Transformer\ConnectorValueTransformerInterface;
use Eikona\Tessa\ConnectorBundle\Utilities\LinkGenerator;
use Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\TessaAttribute;
use Webmozart\Assert\Assert;

class TessaValueTransformer implements ConnectorValueTransformerInterface
{
    protected LinkGenerator $linkGenerator;

    /**
     * TessaValueTransformer constructor.
     *
     * @param LinkGenerator $linkGenerator
     */
    public function __construct(
        LinkGenerator $linkGenerator
    )
    {
        $this->linkGenerator = $linkGenerator;
    }

    public function supports(AbstractAttribute $attribute): bool
    {
        return $attribute instanceof TessaAttribute;
    }

    public function transform(array $normalizedValue, AbstractAttribute $attribute): array
    {
        Assert::true($this->supports($attribute));

        $data = $normalizedValue['data'];
        $assetIds = empty($data) ? [] : $data;

        $assetUrls = array_map(function($assetId) use ($normalizedValue) {
            return $this->linkGenerator->getAssetTessaDownloadUrl($assetId, $normalizedValue['channel']);
        }, $assetIds);

        return [
            'locale'  => $normalizedValue['locale'],
            'channel' => $normalizedValue['channel'],
            'data'    => $normalizedValue['data'],
            '_links'  => $assetUrls,
        ];
    }
}

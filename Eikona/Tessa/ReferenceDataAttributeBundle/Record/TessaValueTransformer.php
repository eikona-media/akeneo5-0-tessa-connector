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
use Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\TessaAttribute;
use Webmozart\Assert\Assert;

class TessaValueTransformer implements ConnectorValueTransformerInterface
{
    public function supports(AbstractAttribute $attribute): bool
    {
        return $attribute instanceof TessaAttribute;
    }

    public function transform(array $normalizedValue, AbstractAttribute $attribute): array
    {
        Assert::true($this->supports($attribute));

        return [
            'locale'  => $normalizedValue['locale'],
            'channel' => $normalizedValue['channel'],
            'data'    => $normalizedValue['data'],
        ];
    }
}

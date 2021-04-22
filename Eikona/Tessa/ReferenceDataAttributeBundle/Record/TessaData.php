<?php
/**
 * TessaData.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Record;

use Akeneo\ReferenceEntity\Domain\Model\Record\Value\ValueDataInterface;
use Webmozart\Assert\Assert;

class TessaData implements ValueDataInterface
{
    /** @var string[] */
    private $tessaValue;

    private function __construct(array $tessaValue)
    {
        Assert::notEmpty($tessaValue, 'Tessa value should be a non empty array');

        $this->tessaValue = $tessaValue;
    }

    /**
     * @return string[]
     */
    public function normalize()
    {
        return $this->tessaValue;
    }

    public static function createFromNormalize($normalizedData): ValueDataInterface
    {
        Assert::isArray($normalizedData, 'Normalized data should be an array');
        Assert::allString($normalizedData, 'Normalized data should be an array of string');

        return new self($normalizedData);
    }
}

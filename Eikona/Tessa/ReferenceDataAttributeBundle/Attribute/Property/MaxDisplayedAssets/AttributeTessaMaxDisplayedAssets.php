<?php
/**
 * AttributeTessaMaxDisplayedAssets.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\Property\MaxDisplayedAssets;

use Webmozart\Assert\Assert;

class AttributeTessaMaxDisplayedAssets
{
    /** @var null|int */
    private $maxDisplayedAssets;

    private function __construct(?int $maxDisplayedAssets)
    {
        if ($maxDisplayedAssets !== null) {
            Assert::numeric($maxDisplayedAssets, sprintf('Max assets must be numeric, %d given', $maxDisplayedAssets));
            $maxAssets = (int)$maxDisplayedAssets;
            Assert::natural($maxDisplayedAssets, sprintf('Max assets cannot be negative, %d given', $maxDisplayedAssets));
        }
        $this->maxDisplayedAssets = $maxDisplayedAssets;
    }

    public static function fromInteger(?int $maxAssets): self
    {
        return new self($maxAssets);
    }

    public function intValue(): ?int
    {
        return $this->maxDisplayedAssets;
    }

    public function normalize(): ?int
    {
        return $this->maxDisplayedAssets;
    }
}

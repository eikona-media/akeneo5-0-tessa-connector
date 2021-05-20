<?php
/**
 * AttributeTessaMaxAssets.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\Property\MaxAssets;

use Webmozart\Assert\Assert;

class AttributeTessaMaxAssets
{
    public const NO_LIMIT = null;

    /** @var null|int */
    private $maxAssets;

    private function __construct(?int $maxAssets)
    {
        if (self::NO_LIMIT !== $maxAssets) {
            Assert::numeric($maxAssets, sprintf('Max assets must be numeric, %d given', $maxAssets));
            $maxAssets = (int)$maxAssets;
            Assert::natural($maxAssets, sprintf('Max assets cannot be negative, %d given', $maxAssets));
        }
        $this->maxAssets = $maxAssets;
    }

    public static function fromInteger(?int $maxAssets): self
    {
        return new self($maxAssets);
    }

    public static function noLimit(): self
    {
        return new self(self::NO_LIMIT);
    }

    public function intValue(): ?int
    {
        return $this->maxAssets;
    }

    public function normalize(): ?int
    {
        return $this->maxAssets;
    }
}

<?php
/**
 * CreateTessaAttributeCommand.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Attribute;

use Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\AbstractCreateAttributeCommand;

class CreateTessaAttributeCommand extends AbstractCreateAttributeCommand
{
    /** @var null|int */
    public $maxAssets;

    /** @var array */
    public $allowedExtensions;

    /** @var null|int */
    public $maxDisplayedAssets;

    public function __construct(
        string $referenceEntityIdentifier,
        string $code,
        array $labels,
        bool $isRequired,
        bool $valuePerChannel,
        bool $valuePerLocale,
        ?int $maxAssets,
        array $allowedExtensions,
        ?int $maxDisplayedAssets
    )
    {
        parent::__construct(
            $referenceEntityIdentifier,
            $code,
            $labels,
            $isRequired,
            $valuePerChannel,
            $valuePerLocale
        );

        $this->maxAssets = $maxAssets;
        $this->allowedExtensions = $allowedExtensions;
        $this->maxDisplayedAssets = $maxDisplayedAssets;
    }
}

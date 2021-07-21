<?php
/**
 * EditTessaMaxDisplayedAssetsCommand.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\Property\MaxDisplayedAssets;

use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\AbstractEditAttributeCommand;

class EditTessaMaxDisplayedAssetsCommand extends AbstractEditAttributeCommand
{
    /** @var int|null */
    public $maxDisplayedAssets;

    public function __construct(string $identifier, ?int $newMaxDisplayedAssets)
    {
        parent::__construct($identifier);

        $this->maxDisplayedAssets = $newMaxDisplayedAssets;
    }
}

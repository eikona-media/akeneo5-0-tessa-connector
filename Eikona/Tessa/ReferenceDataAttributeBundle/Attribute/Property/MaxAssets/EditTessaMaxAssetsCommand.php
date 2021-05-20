<?php
/**
 * EditTessaMaxAssetsCommand.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\Property\MaxAssets;

use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\AbstractEditAttributeCommand;

class EditTessaMaxAssetsCommand extends AbstractEditAttributeCommand
{
    /** @var int|null */
    public $maxAssets;

    public function __construct(string $identifier, ?int $newMaxAssets)
    {
        parent::__construct($identifier);

        $this->maxAssets = $newMaxAssets;
    }
}

<?php
/**
 * EditTessaMaxAssetsCommandFactory.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\Property\MaxAssets;

use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\AbstractEditAttributeCommand;
use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\EditAttributeCommandFactoryInterface;
use RuntimeException;

class EditTessaMaxAssetsCommandFactory implements EditAttributeCommandFactoryInterface
{
    public function supports(array $normalizedCommand): bool
    {
        return array_key_exists('max_assets', $normalizedCommand)
            && array_key_exists('identifier', $normalizedCommand);
    }

    public function create(array $normalizedCommand): AbstractEditAttributeCommand
    {
        if (!$this->supports($normalizedCommand)) {
            throw new RuntimeException('Impossible to create an edit maxAssets property command.');
        }

        $command = new EditTessaMaxAssetsCommand(
            $normalizedCommand['identifier'],
            $normalizedCommand['max_assets']
        );

        return $command;
    }
}

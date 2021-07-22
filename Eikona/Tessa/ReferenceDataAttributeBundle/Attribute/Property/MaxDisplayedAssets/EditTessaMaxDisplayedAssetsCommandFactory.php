<?php
/**
 * EditTessaMaxDisplayedAssetsCommandFactory.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\Property\MaxDisplayedAssets;

use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\AbstractEditAttributeCommand;
use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\EditAttributeCommandFactoryInterface;
use RuntimeException;

class EditTessaMaxDisplayedAssetsCommandFactory implements EditAttributeCommandFactoryInterface
{
    public function supports(array $normalizedCommand): bool
    {
        return array_key_exists('max_displayed_assets', $normalizedCommand)
            && array_key_exists('identifier', $normalizedCommand);
    }

    public function create(array $normalizedCommand): AbstractEditAttributeCommand
    {
        if (!$this->supports($normalizedCommand)) {
            throw new RuntimeException('Impossible to create an edit maxDisplayedAssets property command.');
        }

        $command = new EditTessaMaxDisplayedAssetsCommand(
            $normalizedCommand['identifier'],
            $normalizedCommand['max_displayed_assets']
        );

        return $command;
    }
}

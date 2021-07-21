<?php
/**
 * CreateTessaAttributeCommandFactory.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Attribute;

use Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\AbstractCreateAttributeCommand;
use Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\CommandFactory\AbstractCreateAttributeCommandFactory;
use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeAllowedExtensions;
use Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\Property\MaxAssets\AttributeTessaMaxAssets;

class CreateTessaAttributeCommandFactory extends AbstractCreateAttributeCommandFactory
{

    public function supports(array $normalizedCommand): bool
    {
         return isset($normalizedCommand['type']) && TessaAttribute::ATTRIBUTE_TYPE === $normalizedCommand['type'];
    }

    public function create(array $normalizedCommand): AbstractCreateAttributeCommand
    {
        $this->checkCommonProperties($normalizedCommand);

        $maxAssets = isset($normalizedCommand['max_assets'])
            ? (int)$normalizedCommand['max_assets']
            : AttributeTessaMaxAssets::NO_LIMIT;

        $allowedExtensions = isset($normalizedCommand['allowed_extensions'])
            ? $normalizedCommand['allowed_extensions']
            : AttributeAllowedExtensions::ALL_ALLOWED;

        $maxDisplayedAssets = isset($normalizedCommand['max_displayed_assets'])
            ? (int)$normalizedCommand['max_displayed_assets']
            : null;

        $command = new CreateTessaAttributeCommand(
            $normalizedCommand['reference_entity_identifier'],
            $normalizedCommand['code'],
            $normalizedCommand['labels'] ?? [],
            $normalizedCommand['is_required'] ?? false,
            $normalizedCommand['value_per_channel'],
            $normalizedCommand['value_per_locale'],
            $maxAssets,
            $allowedExtensions,
            $maxDisplayedAssets
        );

        return $command;
    }
}

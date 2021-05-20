<?php
/**
 * TessaMaxAssetsUpdater.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\Property\MaxAssets;

use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\AttributeUpdater\AttributeUpdaterInterface;
use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\AbstractEditAttributeCommand;
use Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute;
use Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\TessaAttribute;

class TessaMaxAssetsUpdater implements AttributeUpdaterInterface
{
    public function supports(AbstractAttribute $attribute, AbstractEditAttributeCommand $command): bool
    {
        return $command instanceof EditTessaMaxAssetsCommand && $attribute instanceof TessaAttribute;
    }

    public function __invoke(AbstractAttribute $attribute, AbstractEditAttributeCommand $command): AbstractAttribute
    {
        if (!$command instanceof EditTessaMaxAssetsCommand) {
            throw new \RuntimeException(
                sprintf(
                    'Expected command of type "%s", "%s" given',
                    EditTessaMaxAssetsCommand::class,
                    get_class($command)
                )
            );
        }

        /** @var TessaAttribute $attribute */
        if (AttributeTessaMaxAssets::NO_LIMIT === $command->maxAssets) {
            $attribute->setMaxAssets(AttributeTessaMaxAssets::noLimit());
        } else {
            $attribute->setMaxAssets(AttributeTessaMaxAssets::fromInteger($command->maxAssets));
        }

        return $attribute;
    }
}

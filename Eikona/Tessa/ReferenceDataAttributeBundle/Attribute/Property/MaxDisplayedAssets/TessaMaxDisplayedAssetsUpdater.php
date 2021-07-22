<?php
/**
 * TessaMaxDisplayedAssetsUpdater.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\Property\MaxDisplayedAssets;

use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\AttributeUpdater\AttributeUpdaterInterface;
use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\AbstractEditAttributeCommand;
use Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute;
use Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\TessaAttribute;

class TessaMaxDisplayedAssetsUpdater implements AttributeUpdaterInterface
{
    public function supports(AbstractAttribute $attribute, AbstractEditAttributeCommand $command): bool
    {
        return $command instanceof EditTessaMaxDisplayedAssetsCommand && $attribute instanceof TessaAttribute;
    }

    public function __invoke(AbstractAttribute $attribute, AbstractEditAttributeCommand $command): AbstractAttribute
    {
        if (!$command instanceof EditTessaMaxDisplayedAssetsCommand) {
            throw new \RuntimeException(
                sprintf(
                    'Expected command of type "%s", "%s" given',
                    EditTessaMaxDisplayedAssetsCommand::class,
                    get_class($command)
                )
            );
        }

        /** @var TessaAttribute $attribute */
        $attribute->setMaxDisplayedAssets(AttributeTessaMaxDisplayedAssets::fromInteger($command->maxDisplayedAssets));

        return $attribute;
    }
}

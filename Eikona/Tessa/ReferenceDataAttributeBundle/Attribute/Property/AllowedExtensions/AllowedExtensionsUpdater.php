<?php
/**
 * AllowedExtensionsUpdater.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\Property\AllowedExtensions;

use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\AbstractEditAttributeCommand;
use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\EditAllowedExtensionsCommand;
use Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute;
use Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\TessaAttribute;

class AllowedExtensionsUpdater extends \Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\AttributeUpdater\AllowedExtensionsUpdater
{
    public function supports(AbstractAttribute $attribute, AbstractEditAttributeCommand $command): bool
    {
        return $attribute instanceof TessaAttribute && $command instanceof EditAllowedExtensionsCommand;
    }
}

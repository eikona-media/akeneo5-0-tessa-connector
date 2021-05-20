<?php
/**
 * EditTessaValueCommand.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Record;

use Akeneo\ReferenceEntity\Application\Record\EditRecord\CommandFactory\AbstractEditValueCommand;
use Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute;

class EditTessaValueCommand extends AbstractEditValueCommand
{
    /** @var string[] */
    public $tessaValue;

    public function __construct(AbstractAttribute $attribute, ?string $channel, ?string $locale, array $tessaValue)
    {
        parent::__construct($attribute, $channel, $locale);

        $this->tessaValue = $tessaValue;
    }
}

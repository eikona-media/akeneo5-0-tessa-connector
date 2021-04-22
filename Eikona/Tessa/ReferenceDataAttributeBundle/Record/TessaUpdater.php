<?php
/**
 * TessaUpdater.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Record;

use Akeneo\ReferenceEntity\Application\Record\EditRecord\CommandFactory\AbstractEditValueCommand;
use Akeneo\ReferenceEntity\Application\Record\EditRecord\ValueUpdater\ValueUpdaterInterface;
use Akeneo\ReferenceEntity\Domain\Model\ChannelIdentifier;
use Akeneo\ReferenceEntity\Domain\Model\LocaleIdentifier;
use Akeneo\ReferenceEntity\Domain\Model\Record\Record;
use Akeneo\ReferenceEntity\Domain\Model\Record\Value\ChannelReference;
use Akeneo\ReferenceEntity\Domain\Model\Record\Value\LocaleReference;
use Akeneo\ReferenceEntity\Domain\Model\Record\Value\Value;
use RuntimeException;

class TessaUpdater implements ValueUpdaterInterface
{
    public function supports(AbstractEditValueCommand $command): bool
    {
        return $command instanceof EditTessaValueCommand;
    }

    public function __invoke(Record $record, AbstractEditValueCommand $command): void
    {
        if (!$this->supports($command)) {
            throw new RuntimeException('Impossible to update the value of the record with the given command.');
        }

        /** @var EditTessaValueCommand $command */

        $attribute = $command->attribute->getIdentifier();
        $channelReference = (null !== $command->channel) ?
            ChannelReference::fromChannelIdentifier(ChannelIdentifier::fromCode($command->channel)) :
            ChannelReference::noReference();
        $localeReference = (null !== $command->locale) ?
            LocaleReference::fromLocaleIdentifier(LocaleIdentifier::fromCode($command->locale)) :
            LocaleReference::noReference();

        $tessaValue = TessaData::createFromNormalize($command->tessaValue);

        $value = Value::create($attribute, $channelReference, $localeReference, $tessaValue);
        $record->setValue($value);
    }
}

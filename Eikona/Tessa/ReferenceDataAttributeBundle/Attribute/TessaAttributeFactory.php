<?php
/**
 * TessaAttributeFactory.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Attribute;

use Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\AbstractCreateAttributeCommand;
use Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\AttributeFactory\AttributeFactoryInterface;
use Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute;
use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeAllowedExtensions;
use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeCode;
use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeIdentifier;
use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeIsRequired;
use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeOrder;
use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeValuePerChannel;
use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeValuePerLocale;
use Akeneo\ReferenceEntity\Domain\Model\LabelCollection;
use Akeneo\ReferenceEntity\Domain\Model\ReferenceEntity\ReferenceEntityIdentifier;
use Eikona\Tessa\ConnectorBundle\Tessa;
use Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\Property\MaxAssets\AttributeTessaMaxAssets;
use Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\Property\MaxDisplayedAssets\AttributeTessaMaxDisplayedAssets;

class TessaAttributeFactory implements AttributeFactoryInterface
{
    /**
     * @var Tessa
     */
    protected $tessa;

    /**
     * TessaAttributeFactory constructor.
     *
     * @param Tessa $tessa
     */
    public function __construct(Tessa $tessa)
    {
        $this->tessa = $tessa;
    }

    public function supports(AbstractCreateAttributeCommand $command): bool
    {
        return $command instanceof CreateTessaAttributeCommand;
    }

    public function create(
        AbstractCreateAttributeCommand $command,
        AttributeIdentifier $identifier,
        AttributeOrder $order
    ): AbstractAttribute {
        if (!$this->supports($command)) {
            throw new \RuntimeException(
                sprintf(
                    'Expected command of type "%s", "%s" given',
                    CreateTessaAttributeCommand::class,
                    get_class($command)
                )
            );
        }

        /** @var CreateTessaAttributeCommand $command */
        $attribute = TessaAttribute::createTessa(
            $identifier,
            ReferenceEntityIdentifier::fromString($command->referenceEntityIdentifier),
            AttributeCode::fromString($command->code),
            LabelCollection::fromArray($command->labels),
            $order,
            AttributeIsRequired::fromBoolean($command->isRequired),
            AttributeValuePerChannel::fromBoolean($command->valuePerChannel),
            AttributeValuePerLocale::fromBoolean($command->valuePerLocale),
            AttributeTessaMaxAssets::fromInteger($command->maxAssets),
            AttributeAllowedExtensions::fromList($command->allowedExtensions),
            AttributeTessaMaxDisplayedAssets::fromInteger($command->maxDisplayedAssets)
        );

        $attribute->setTessa($this->tessa);
        return $attribute;
    }
}

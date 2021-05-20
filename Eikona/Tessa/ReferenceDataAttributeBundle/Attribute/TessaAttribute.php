<?php
/**
 * TessaAttribute.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Attribute;

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

class TessaAttribute extends AbstractAttribute
{
    const ATTRIBUTE_TYPE = 'tessa';

    /** @var Tessa|null */
    private $tessa = null;

    /** @var AttributeTessaMaxAssets */
    private $maxAssets;

    /** @var  AttributeAllowedExtensions */
    private $allowedExtensions;

    protected function __construct(
        AttributeIdentifier $identifier,
        ReferenceEntityIdentifier $referenceEntityIdentifier,
        AttributeCode $code,
        LabelCollection $labelCollection,
        AttributeOrder $order,
        AttributeIsRequired $isRequired,
        AttributeValuePerChannel $valuePerChannel,
        AttributeValuePerLocale $valuePerLocale,
        AttributeTessaMaxAssets $maxAssets,
        AttributeAllowedExtensions $allowedExtensions
    )
    {
        parent::__construct(
            $identifier,
            $referenceEntityIdentifier,
            $code,
            $labelCollection,
            $order,
            $isRequired,
            $valuePerChannel,
            $valuePerLocale
        );

        $this->maxAssets = $maxAssets;
        $this->allowedExtensions = $allowedExtensions;
    }

    public static function createTessa(
        AttributeIdentifier $identifier,
        ReferenceEntityIdentifier $referenceEntityIdentifier,
        AttributeCode $code,
        LabelCollection $labelCollection,
        AttributeOrder $order,
        AttributeIsRequired $isRequired,
        AttributeValuePerChannel $valuePerChannel,
        AttributeValuePerLocale $valuePerLocale,
        AttributeTessaMaxAssets $maxAssets,
        AttributeAllowedExtensions $allowedExtensions
    )
    {
        return new self(
            $identifier,
            $referenceEntityIdentifier,
            $code,
            $labelCollection,
            $order,
            $isRequired,
            $valuePerChannel,
            $valuePerLocale,
            $maxAssets,
            $allowedExtensions
        );
    }

    public function setTessa(Tessa $tessa)
    {
        $this->tessa = $tessa;
    }

    public function setMaxAssets(AttributeTessaMaxAssets $maxAssets): void
    {
        $this->maxAssets = $maxAssets;
    }

    public function setAllowedExtensions(AttributeAllowedExtensions $allowedExtensions): void
    {
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): string
    {
        return self::ATTRIBUTE_TYPE;
    }

    public function normalize(): array
    {
        $canEditAssetsInAkeneoUi = $this->tessa !== null
            ? !$this->tessa->isAssetEditingInAkeneoUiDisabled()
            : true;

        return array_merge(
            parent::normalize(),
            [
                'max_assets' => $this->maxAssets->normalize(),
                'allowed_extensions' => $this->allowedExtensions->normalize(),
                'canEditAssetsInAkeneoUi' => $canEditAssetsInAkeneoUi,
            ]
        );
    }
}

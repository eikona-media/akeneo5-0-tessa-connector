<?php
/**
 * ReferenceEntityNotificationNormalizer.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Normalizer\NotificationNormalizer\Modification;

use Akeneo\ReferenceEntity\Domain\Model\Record\Record;
use Akeneo\ReferenceEntity\Domain\Query\Record\Connector\FindConnectorRecordByReferenceEntityAndCodeInterface;
use Akeneo\ReferenceEntity\Domain\Query\Record\FindRecordDetailsInterface;
use Akeneo\ReferenceEntity\Domain\Query\ReferenceEntity\FindReferenceEntityDetailsInterface;
use Akeneo\ReferenceEntity\Domain\Repository\ReferenceEntityRepositoryInterface;
use Eikona\Tessa\ConnectorBundle\Normalizer\NotificationNormalizer\Modification\NotificationNormalizerInterface;
use Eikona\Tessa\ConnectorBundle\Tessa;
use Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\TessaAttribute;

/**
 * Class ReferenceEntityNotificationNormalizer
 *
 * @package Eikona\Tessa\ReferenceDataAttributeBundle\Normalizer\NotificationNormalizer\Modification
 */
class ReferenceEntityNotificationNormalizer implements NotificationNormalizerInterface
{
    /**
     * @var FindConnectorRecordByReferenceEntityAndCodeInterface
     */
    protected $findConnectorRecord;

    /**
     * @var FindReferenceEntityDetailsInterface
     */
    protected $findReferenceEntityDetails;

    /**
     * ReferenceEntityNotificationNormalizer constructor.
     *
     * @param FindConnectorRecordByReferenceEntityAndCodeInterface $findConnectorRecord
     * @param FindReferenceEntityDetailsInterface                  $findReferenceEntityDetails
     */
    public function __construct(
        FindConnectorRecordByReferenceEntityAndCodeInterface $findConnectorRecord,
        FindReferenceEntityDetailsInterface $findReferenceEntityDetails
    )
    {
        $this->findConnectorRecord = $findConnectorRecord;
        $this->findReferenceEntityDetails = $findReferenceEntityDetails;
    }

    /**
     * @param Record $entity
     *
     * @return array
     */
    public function normalize($entity): array
    {
        $connectorRecord = $this->findConnectorRecord->find(
            $entity->getReferenceEntityIdentifier(),
            $entity->getCode()
        );

        // Fetch tessa attribute codes from reference entity definition
        $tessaAttributeCodes = [];
        $referenceEntityDetails = $this->findReferenceEntityDetails->find($entity->getReferenceEntityIdentifier());
        foreach ($referenceEntityDetails->attributes as $attribute) {
            if ($attribute->type === TessaAttribute::ATTRIBUTE_TYPE) {
                $tessaAttributeCodes[] = $attribute->code;
            }
        }

        // Normalize record and append "tessaAttributeCodes"
        $normalizedConnectorRecord = $connectorRecord->normalize();
        $normalizedConnectorRecord['tessaAttributeCodes'] = $tessaAttributeCodes;

        return [
            'id' => (string)$entity->getIdentifier(),
            'code' => (string)$entity->getIdentifier(),
            'parentId' => (string)$entity->getReferenceEntityIdentifier(),
            'type' => Tessa::TYPE_ENTITY_RECORD,
            'context' => Tessa::CONTEXT_UPDATE,
            'data' => $normalizedConnectorRecord
        ];
    }

    public function supports($entity): bool
    {
        return $entity instanceof Record;
    }
}

<?php
/**
 * EikonaTessaAttributeConstraintValidator.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2020 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Validator\Constraints;

use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Eikona\Tessa\ConnectorBundle\AttributeType\AttributeTypes;
use Eikona\Tessa\ConnectorBundle\AttributeType\TessaType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class EikonaTessaAttributeConstraintValidator
 *
 * @package Eikona\Tessa\ConnectorBundle\Validator\Constraints
 */
class EikonaTessaAttributeConstraintValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     * @param EikonaTessaAttributeConstraint $constraint
     */
    public function validate($attribute, Constraint $constraint)
    {
        if (!$constraint instanceof EikonaTessaAttributeConstraint) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\EikonaTessaAttributeConstraint');
        }

        if ($attribute instanceof AttributeInterface &&
            ($attribute->getType() === AttributeTypes::TESSA)) {

            $exportUrl = $attribute->getProperty(TessaType::ATTRIBUTE_EXPORT_URL);

            if ($exportUrl === null) {
                return;
            }

            if (!preg_match_all('/({\w+})/', $exportUrl, $matches)) {
                return;
            }

            $allowedPlaceholders = ['{ASSET_ID}', '{SCOPE}'];
            $placeholders = array_values(array_unique($matches[1]));
            $unknownPlaceholders = array_diff($placeholders, $allowedPlaceholders);
            $isScopePlaceholderUsed = in_array('{SCOPE}', $placeholders);

            if (!empty($unknownPlaceholders)) {
                $this->context->buildViolation($constraint->invalidPlaceholder)
                    ->atPath(TessaType::ATTRIBUTE_EXPORT_URL)
                    ->setParameter('{{invalidPlaceholders}}', implode(', ', $unknownPlaceholders))
                    ->setParameter('{{allowedPlaceholders}}', implode(' & ', $allowedPlaceholders))
                    ->addViolation();
            }

            if (!$attribute->isScopable() && $isScopePlaceholderUsed) {
                $this->context->buildViolation($constraint->cannotUseScopePlaceholder)
                    ->atPath(TessaType::ATTRIBUTE_EXPORT_URL)
                    ->addViolation();
            }
        }
    }
}

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

            $this->checkExportUrl($attribute, $constraint);
            $this->checkMaxDisplayedAssets($attribute, $constraint);
        }
    }

    private function checkExportUrl($attribute, Constraint $constraint)
    {
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

    private function checkMaxDisplayedAssets($attribute, Constraint $constraint)
    {
        $maxDisplayedAssets = $attribute->getProperty(TessaType::ATTRIBUTE_MAX_DISPLAYED_ASSETS);

        if ($maxDisplayedAssets === null || $maxDisplayedAssets === '') {
            return;
        }

        if (!preg_match('/^(0|[1-9][0-9]*)$/', $maxDisplayedAssets)) {
            $this->context->buildViolation($constraint->invalidMaxDisplayedAssets)
                ->atPath(TessaType::ATTRIBUTE_MAX_DISPLAYED_ASSETS)
                ->addViolation();
        }
    }
}

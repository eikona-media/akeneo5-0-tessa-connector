<?php
/**
 * TessaMaxDisplayedAssetsValidator.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Validation\Attribute;

use Akeneo\ReferenceEntity\Infrastructure\Validation\Attribute\MaxLength;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validation;

class TessaMaxDisplayedAssetsValidator extends ConstraintValidator
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof TessaMaxDisplayedAssets) {
            throw new UnexpectedTypeException($constraint, self::class);
        }

        $validator = Validation::createValidator();
        $violations = $validator->validate($value, [
                new Constraints\Callback(function ($value, ExecutionContextInterface $context, $payload) {
                    if (null !== $value && !is_int($value)) {
                        $context->buildViolation(MaxLength::MESSAGE_SHOULD_BE_AN_INTEGER)
                            ->addViolation();
                    }
                }),
                new Constraints\GreaterThan(0)
            ]
        );

        if ($violations->count() > 0) {
            foreach ($violations as $violation) {
                $this->context->addViolation(
                    $violation->getMessage(),
                    $violation->getParameters()
                );
            }
        }
    }
}

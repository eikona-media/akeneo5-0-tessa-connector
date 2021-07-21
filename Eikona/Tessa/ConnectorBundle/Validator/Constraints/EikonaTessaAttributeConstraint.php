<?php
/**
 * EikonaTessaAttributeConstraint.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2020 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class EikonaTessaAttributeConstraint
 *
 * @package Eikona\Tessa\ConnectorBundle\Validator\Constraints
 */
class EikonaTessaAttributeConstraint extends Constraint
{
    public $cannotUseScopePlaceholder = 'The placeholder "{SCOPE}" is not allowed because the attribute is not scopable.';
    public $invalidPlaceholder = 'Invalid placeholder(s): "{{invalidPlaceholders}}". Allowed placeholders are: {{allowedPlaceholders}}.';
    public $invalidMaxDisplayedAssets = 'Invalid amount. Please provide a number or leave empty.';

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'eikona_tessa_validator_constraint_validator_attribute';
    }
}

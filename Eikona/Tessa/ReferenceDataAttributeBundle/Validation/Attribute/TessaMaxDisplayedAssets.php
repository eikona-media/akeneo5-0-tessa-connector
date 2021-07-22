<?php
/**
 * TessaMaxAssets.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2021 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Validation\Attribute;

use Symfony\Component\Validator\Constraint;

class TessaMaxDisplayedAssets extends Constraint
{
    public const MESSAGE_SHOULD_BE_AN_INTEGER = 'pim_reference_entity.attribute.validation.tessa.max_displayed_assets.should_be_an_integer';
}

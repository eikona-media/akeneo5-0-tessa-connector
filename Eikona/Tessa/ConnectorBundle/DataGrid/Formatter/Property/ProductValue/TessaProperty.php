<?php

namespace Eikona\Tessa\ConnectorBundle\DataGrid\Formatter\Property\ProductValue;

use Eikona\Tessa\ConnectorBundle\Tessa;
use Oro\Bundle\PimDataGridBundle\Extension\Formatter\Property\ProductValue\TwigProperty;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Field property, able to render majority of product attribute values
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TessaProperty extends TwigProperty
{
    const TEMPLATE_KEY = 'EikonaTessaConnectorBundle:Property:image.html.twig';

    /**
     * @var Tessa
     */
    protected $tessa;

    public function __construct(
        \Twig_Environment $environment,
        TranslatorInterface $translator,
        Tessa $tessa
    )
    {
        $this->translator = $translator;
        $this->tessa = $tessa;
        parent::__construct($environment);
    }

    /**
     * {@inheritdoc}
     */
    protected function convertValue($value)
    {
        $result = $value['data'] ?? null;

        if (null === $result) {
            return null;
        }

        $result = explode(',', $result);

        $assetId = array_shift($result);

        return $this->getTemplate()->render(
            [
                'count' => count($result),
                'assetId' => $assetId,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplate()
    {
        return $this->environment->loadTemplate(self::TEMPLATE_KEY);
    }
}

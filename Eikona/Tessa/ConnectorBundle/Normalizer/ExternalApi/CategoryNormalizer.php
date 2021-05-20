<?php
/**
 * CategoryNormalizer.php
 *
 * @author    Matthias Mahler <m.mahler@eikona.de>
 * @copyright 2017 Eikona AG (http://www.eikona.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Normalizer\ExternalApi;

use Akeneo\Tool\Component\Classification\Model\CategoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CategoryNormalizer extends \Akeneo\Pim\Enrichment\Component\Category\Normalizer\ExternalApi\CategoryNormalizer
{
    /**
     * @var null|Request
     */
    private $request;

    /**
     * @param NormalizerInterface $stdNormalizer
     * @param RequestStack $requestStack
     */
    public function __construct(
        NormalizerInterface $stdNormalizer,
        RequestStack $requestStack
    )
    {
        parent::__construct($stdNormalizer);

        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var CategoryInterface $object */
        return array_merge(
            parent::normalize($object, $format, $context),
            [
                'id' => $object->getId()
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        if ($this->request === null) {
            return false;
        }

        $isTessaApiRequest = (boolean)json_decode(strtolower($this->request->get('tessa')));

        return $data instanceof CategoryInterface
            && $format === 'external_api'
            && $isTessaApiRequest;
    }

}

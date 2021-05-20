<?php
/**
 * ProductModelNormalizer.php
 *
 * @author    Matthias Mahler <m.mahler@eikona.de>
 * @copyright 2017 Eikona AG (http://www.eikona.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Normalizer\ExternalApi;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;
use Akeneo\Pim\Structure\Component\Repository\ExternalApi\AttributeRepositoryInterface;
use Eikona\Tessa\ConnectorBundle\Utilities\IdPrefixer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProductModelNormalizer extends \Akeneo\Pim\Enrichment\Component\Product\Normalizer\ExternalApi\ProductModelNormalizer
{
    /**
     * @var null|Request
     */
    protected $request;

    /**
     * @var IdPrefixer
     */
    protected $idPrefixer;

    /**
     * @param NormalizerInterface $productNormalizer
     * @param AttributeRepositoryInterface $attributeRepository
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     * @param IdPrefixer $idPrefixer
     */
    public function __construct(
        NormalizerInterface $productNormalizer,
        AttributeRepositoryInterface $attributeRepository,
        RouterInterface $router,
        RequestStack $requestStack,
        IdPrefixer $idPrefixer
    )
    {
        parent::__construct($productNormalizer, $attributeRepository, $router);

        $this->request = $requestStack->getCurrentRequest();
        $this->idPrefixer = $idPrefixer;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        /** @var ProductModelInterface $object */
        $result = array_merge(
            parent::normalize($object, $format, $context),
            [
                'id' => $this->idPrefixer->getPrefixedId($object)
            ]
        );

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        if ($this->request === null) {
            return false;
        }

        $isTessaApiRequest = (boolean)json_decode(strtolower($this->request->get('tessa')));

        return $data instanceof ProductModelInterface
            && 'external_api' === $format
            && $isTessaApiRequest;
    }
}

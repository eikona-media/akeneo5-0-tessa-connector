<?php
/**
 * AssetsCollectionPresenter.php
 *
 * @author      Felix Hack <f.hack@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Presenter;

use Akeneo\AssetManager\Domain\Repository\AssetRepositoryInterface;
use Akeneo\Pim\WorkOrganization\Workflow\Bundle\Presenter\PresenterInterface;
use Akeneo\Tool\Component\StorageUtils\Repository\IdentifiableObjectRepositoryInterface;
use Eikona\Tessa\ConnectorBundle\AttributeType\AttributeTypes;
use Symfony\Component\Routing\RouterInterface;

class AssetsCollectionPresenter implements PresenterInterface
{
    /** @var AssetRepositoryInterface */
    protected $repository;

    /** @var RouterInterface */
    protected $router;

    /** @var IdentifiableObjectRepositoryInterface */
    protected $attributeRepository;

    public function __construct(
        AssetRepositoryInterface $repository,
        IdentifiableObjectRepositoryInterface $attributeRepository,
        RouterInterface $router
    ) {
        $this->repository = $repository;
        $this->attributeRepository = $attributeRepository;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(string $attributeType, string $referenceDataName = null): bool
    {
        return AttributeTypes::TESSA === $attributeType;
    }

    /**
     * @param mixed $data
     * @param array $change
     * @return array|string
     */
    public function present($data, array $change)
    {
        return [
            'before' => $this->prepareTessaAssets($data),
            'after'  => $this->prepareTessaAssets($change['data'])
        ];
    }

    public function prepareTessaAssets($assetCodes) {
        if (null === $assetCodes) {
            return null;
        }

        $result = '';

        $assets = explode(',', $assetCodes);

        foreach ($assets as $asset) {
            /** @noinspection CssUnknownTarget */
            $result .= sprintf(
                '<div class="AknThumbnail EikonAssetThumbnail" style="background-image: url(\'%s\')">' .
                '<span class="AknThumbnail-label">%s</span>' .
                '</div>',
                $this->router->generate('eikona_tessa_media_preview', [
                    'assetId' => $asset
                ]),
                $asset
            );
        }

        return $result;
    }
}

<?php
/**
 * LinkGenerator.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2020 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Utilities;

use Akeneo\Tool\Component\StorageUtils\Repository\CachedObjectRepositoryInterface;
use Eikona\Tessa\ConnectorBundle\AttributeType\TessaType;
use Eikona\Tessa\ConnectorBundle\Security\AuthGuard;
use Eikona\Tessa\ConnectorBundle\Tessa;

/**
 * Class LinkGenerator
 *
 * @package Eikona\Tessa\ConnectorBundle\Utilities
 */
class LinkGenerator
{
    /**
     * @var AuthGuard
     */
    protected $authGuard;

    /**
     * @var Tessa
     */
    protected $tessa;

    /**
     * @var CachedObjectRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * LinkGenerator constructor.
     *
     * @param AuthGuard                       $authGuard
     * @param Tessa                           $tessa
     * @param CachedObjectRepositoryInterface $attributeRepository
     */
    public function __construct(
        AuthGuard $authGuard,
        Tessa $tessa,
        CachedObjectRepositoryInterface $attributeRepository
    )
    {
        $this->authGuard = $authGuard;
        $this->tessa = $tessa;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @param $assetId
     * @param $attributeCode
     * @param $scopeCode
     *
     * @return string|null
     */
    public function getAssetExportUrl($assetId, $attributeCode, $scopeCode): ?string
    {
        $attribute = $this->attributeRepository->findOneByIdentifier($attributeCode);
        $exportUrl = $attribute->getProperty(TessaType::ATTRIBUTE_EXPORT_URL);

        if (!empty($exportUrl)) {
            return str_replace(
                ['{ASSET_ID}', '{SCOPE}'],
                [$assetId, $scopeCode],
                $exportUrl
            );
        }

        return null;
    }

    /**
     * @param $assetId
     * @param $scopeCode
     *
     * @return string
     */
    public function getAssetTessaDownloadUrl($assetId, $scopeCode): string
    {
        $key = $this->authGuard->getDownloadAuthToken($assetId, 'download');

        return $this->tessa->getBaseUrl()
            . '/ui/download.php'
            . '?asset_system_id=' . $assetId
            . '&kanal=' . $scopeCode
            . '&key=' . $key;
    }
}

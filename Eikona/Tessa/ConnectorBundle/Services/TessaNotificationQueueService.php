<?php
/**
 * TessaNotificationQueueService.php
 *
 * @author      Felix Hack <f.hack@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Services;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;
use Akeneo\Pim\Enrichment\Component\Product\Repository\ProductModelRepositoryInterface;
use Akeneo\Pim\Enrichment\Component\Product\Repository\ProductRepositoryInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Eikona\Tessa\ConnectorBundle\Entity\TessaNotificationQueue;
use Eikona\Tessa\ConnectorBundle\Tessa;
use Symfony\Component\Console\Style\SymfonyStyle;

class TessaNotificationQueueService
{
    const TYPE_PRODUCT = 'product';
    const TYPE_PRODUCT_MODEL = 'product_model';
    const TYPE_REFERENCE_ENTITIY = 'reference_entitiy';
    const ACTION_MODIFIED = 'modified';
    const ACTION_REMOVED = 'removed';

    /** @var EntityManagerInterface */
    protected $em;

    /** @var ObjectRepository */
    protected $repo;

    /** @var Tessa */
    protected $tessa;

    /**
     * TessaNotificationQueueService constructor.
     * @param EntityManagerInterface $em
     * @param Tessa $tessa
     */
    public function __construct(
        EntityManagerInterface $em,
        Tessa $tessa
    )
    {
        $this->em = $em;
        $this->repo = $this->em->getRepository(TessaNotificationQueue::class);
        $this->tessa = $tessa;
    }

    /**
     * @param string $code
     * @param string $type
     * @param string $action
     */
    public function addToQueue(string $code, string $type, string $action)
    {
        if ($this->repo->findOneBy(['code' => $code, 'type' => $type]) !== null) {
            return;
        }

        // $action wird hier nur gesetzt, aber noch nicht ausgewertet
        $item = new TessaNotificationQueue();
        $item->setCode($code);
        $item->setType($type);
        $item->setAction($action);

        $this->em->persist($item);
        $this->em->flush();
    }

    /**
     * Send items from queue to tessa and remove them afterwards
     */
    public function syncQueue()
    {
        /** @var TessaNotificationQueue[] $items */
        $items = $this->repo->findAll();

        /** @var ProductRepositoryInterface $productRepo */
        $productRepo = $this->em->getRepository(ProductInterface::class);

        /** @var ProductModelRepositoryInterface $productModelRepo */
        $productModelRepo = $this->em->getRepository(ProductModelInterface::class);

        $entities = [];

        foreach ($items as $item) {
            if ($item->getType() == self::TYPE_PRODUCT) {
                $product = $productRepo->findOneByIdentifier($item->getCode());
                if ($product !== null) {
                    $entities[] = $product;
                }
                $this->em->remove($item);
            }
            if ($item->getType() == self::TYPE_PRODUCT_MODEL) {
                $productModel = $productModelRepo->findOneByIdentifier($item->getCode());
                if ($productModel !== null) {
                    $entities[] = $productModel;
                }
                $this->em->remove($item);
            }
        }

        $this->tessa->notifyBulkModification($entities);

        $this->em->flush();
    }
}

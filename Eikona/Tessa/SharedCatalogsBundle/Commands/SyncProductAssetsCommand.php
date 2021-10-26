<?php

namespace Eikona\Tessa\SharedCatalogsBundle\Commands;

use Akeneo\Pim\Enrichment\Bundle\Doctrine\Common\Saver\ProductModelSaver;
use Akeneo\Pim\Enrichment\Bundle\Doctrine\Common\Saver\ProductSaver;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;
use Akeneo\Pim\Enrichment\Component\Product\Repository\ProductModelRepositoryInterface;
use Akeneo\Pim\Enrichment\Component\Product\Repository\ProductRepositoryInterface;
use Akeneo\Tool\Component\FileStorage\Exception\FileRemovalException;
use Akeneo\Tool\Component\FileStorage\Exception\FileTransferException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Eikona\Tessa\SharedCatalogsBundle\Exceptions\LinkedAttributeDifferentConfigException;
use Eikona\Tessa\SharedCatalogsBundle\Exceptions\LinkedAttributeOnWrongLevelException;
use Eikona\Tessa\SharedCatalogsBundle\Services\ProductAndProductModelProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SyncProductAssetsCommand extends Command
{
    protected static $defaultName = 'eikona_media:tessa:sync-shared-catalogs-assets';

    protected ProductAndProductModelProcessor $processor;
    protected ProductRepositoryInterface $productRepository;
    protected ProductModelRepositoryInterface $productModelRepository;
    protected EntityManagerInterface $em;
    protected ProductSaver $productSaver;
    protected ProductModelSaver $productModelSaver;

    /**
     * @param ProductAndProductModelProcessor $processor
     * @param ProductRepositoryInterface      $productRepository
     * @param ProductModelRepositoryInterface $productModelRepository
     * @param EntityManagerInterface          $em
     * @param ProductSaver                    $productSaver
     * @param ProductModelSaver               $productModelSaver
     */
    public function __construct(
        ProductAndProductModelProcessor $processor,
        ProductRepositoryInterface $productRepository,
        ProductModelRepositoryInterface $productModelRepository,
        EntityManagerInterface $em,
        ProductSaver $productSaver,
        ProductModelSaver $productModelSaver
    )
    {
        parent::__construct();
        $this->processor = $processor;
        $this->productRepository = $productRepository;
        $this->productModelRepository = $productModelRepository;
        $this->em = $em;
        $this->productSaver = $productSaver;
        $this->productModelSaver = $productModelSaver;
    }

    /**
     * {@inheritDoc}
     *
     * @throws FileRemovalException
     * @throws FileTransferException
     * @throws LinkedAttributeDifferentConfigException
     * @throws LinkedAttributeOnWrongLevelException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->syncProducts($io);
        $this->syncProductModels($io);
        return 0;
    }

    /**
     * @param SymfonyStyle $io
     *
     * @throws FileRemovalException
     * @throws FileTransferException
     * @throws LinkedAttributeDifferentConfigException
     * @throws LinkedAttributeOnWrongLevelException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    protected function syncProducts(SymfonyStyle $io): void
    {
        $io->writeln('Syncing products');

        $productCount = (int)$this->em
            ->createQuery('SELECT COUNT(p) FROM ' . ProductInterface::class . ' p')
            ->getSingleScalarResult();

        $io->progressStart($productCount);

        $q = $this->em->createQuery('SELECT p FROM ' . ProductInterface::class . ' p');

        /** @var ProductInterface $product */
        foreach ($q->iterate() as [$product]) {
            $this->processor->processProduct($product);
            if ($product->isDirty()) {
                $this->productSaver->save($product, ['skip_tessa_assets_sync' => true]);
            }
            $this->em->clear();
            $io->progressAdvance();
        }

        $io->progressFinish();
    }

    /**
     * @param SymfonyStyle $io
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws FileRemovalException
     * @throws FileTransferException
     * @throws LinkedAttributeDifferentConfigException
     * @throws LinkedAttributeOnWrongLevelException
     */
    protected function syncProductModels(SymfonyStyle $io): void
    {
        $io->writeln('Syncing products models');

        $productModelCount = (int)$this->em
            ->createQuery('SELECT COUNT(pm) FROM ' . ProductModelInterface::class . ' pm')
            ->getSingleScalarResult();

        $io->progressStart($productModelCount);

        $q = $this->em->createQuery('SELECT pm FROM ' . ProductModelInterface::class . ' pm');

        /** @var ProductModelInterface $productModel */
        foreach ($q->iterate() as [$productModel]) {
            $this->processor->processProductModel($productModel);
            if ($productModel->isDirty()) {
                $this->productModelSaver->save($productModel, ['skip_tessa_assets_sync' => true]);
            }
            $this->em->clear();
            $io->progressAdvance();
        }

        $io->progressFinish();
    }
}

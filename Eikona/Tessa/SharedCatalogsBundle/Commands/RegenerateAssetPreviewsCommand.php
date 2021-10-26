<?php

namespace Eikona\Tessa\SharedCatalogsBundle\Commands;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Eikona\Tessa\SharedCatalogsBundle\Services\AssetManager;
use Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RegenerateAssetPreviewsCommand extends Command
{
    protected static $defaultName = 'eikona_media:tessa:regenerate-asset-previews';

    protected EntityManagerInterface $em;
    protected AssetManager $assetManager;

    /**
     * @param EntityManagerInterface $em
     * @param AssetManager           $assetManager
     */
    public function __construct(
        EntityManagerInterface $em,
        AssetManager $assetManager
    )
    {
        parent::__construct();
        $this->em = $em;
        $this->assetManager = $assetManager;
    }

    /**
     * {@inheritDoc}
     * @throws DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln('Regenerating assets');
        $assetCount = $this->getAssetCount();
        $io->progressStart($assetCount);

        foreach ($this->getAssetCodes() as $code) {
            $this->assetManager->regeneratePreviews($code);
            $io->progressAdvance();
        }

        $io->progressFinish();
        return 0;
    }

    /**
     * @return int
     * @throws DBALException
     */
    private function getAssetCount(): int
    {
        $sqlQuery = <<<SQL
        SELECT COUNT(*)
        FROM akeneo_asset_manager_asset AS asset
        WHERE asset.asset_family_identifier = :family_identifier;
SQL;

        $statement = $this->em->getConnection()
            ->executeQuery($sqlQuery, ['family_identifier' => $this->assetManager->getAssetFamilyCode()]);
        return (int)$statement->fetchColumn(0);
    }

    /**
     * @return Generator
     * @throws DBALException
     */
    private function getAssetCodes(): Generator
    {
        $sqlQuery = <<<SQL
        SELECT asset.code
        FROM akeneo_asset_manager_asset AS asset
        WHERE asset.asset_family_identifier = :family_identifier;
SQL;

        $statement = $this->em->getConnection()
            ->executeQuery($sqlQuery, ['family_identifier' => $this->assetManager->getAssetFamilyCode()]);

        foreach ($statement->fetchAll() as ['code' => $assetCode]) {
            yield $assetCode;
        }
    }
}

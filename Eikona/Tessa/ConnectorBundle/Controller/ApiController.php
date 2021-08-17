<?php


namespace Eikona\Tessa\ConnectorBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ApiController constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getProductIds(): Response
    {

        $sql = 'SELECT id,identifier FROM `pim_catalog_product`';

        return new JsonResponse($this->executeSql($sql));
    }

    /**
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getProductModelIds(): Response
    {

        $sql = 'SELECT id,code FROM `pim_catalog_product_model`';

        return new JsonResponse($this->executeSql($sql));
    }

    /**
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCategoryIds(): Response
    {

        $sql = 'SELECT id,code FROM `pim_catalog_category`';

        return new JsonResponse($this->executeSql($sql));
    }

    /**
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getChannelIds(): Response
    {

        $sql = 'SELECT id,code FROM `pim_catalog_channel`';

        return new JsonResponse($this->executeSql($sql));
    }


    /**
     * @param string $sql
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function executeSql(string $sql, array $data = array()): array
    {

        $conn = $this->entityManager->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($data);
        return ($stmt->fetchAll());

    }

}
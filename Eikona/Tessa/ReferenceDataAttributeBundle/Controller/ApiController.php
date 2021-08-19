<?php


namespace Eikona\Tessa\ReferenceDataAttributeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends \Eikona\Tessa\ConnectorBundle\Controller\ApiController
{


    /**
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getReferenceEntityRecordIds(): Response
    {

        $sql = 'select distinct identifier as refid from `akeneo_reference_entity_reference_entity`';
        $res = $this->executeSql($sql);
        if (count($res) === 0) {
            return new JsonResponse(array());
        }
        return new JsonResponse($this->getReferenceEntityRecords($res));
    }

    /**
     * @param string $refid
     * @return JsonResponse
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getSingleReferenceEntityRecordIds(string $refid): JsonResponse
    {

        return new JsonResponse($this->getReferenceEntityRecords(array(array('refid' => $refid))));

    }

    /**
     * @param array $refIds
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getReferenceEntityRecords(array $refIds): array
    {
        $data = array();

        foreach ($refIds as $value) {
            $sql = 'SELECT code,identifier FROM `akeneo_reference_entity_record` where `reference_entity_identifier`= :refid';
            $subRes = $this->executeSql($sql, array('refid' => $value['refid']));
            if (count($subRes) > 0) {
                $data[$value['refid']] = $subRes;
            } else {
                $data[$value['refid']] = array();
            }

        }
        return $data;
    }

}
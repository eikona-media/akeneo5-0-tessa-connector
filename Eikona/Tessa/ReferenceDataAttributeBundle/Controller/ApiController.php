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

        $sql = 'select distinct reference_entity_identifier as refid from `akeneo_reference_entity_record`';
        $res = $this->executeSql($sql);
        if (count($res) === 0) {
            return new JsonResponse(array());
        }
        $data = array();

        foreach ($res as $value) {
            $sql = 'SELECT code,identifier FROM `akeneo_reference_entity_record` where `reference_entity_identifier`= :refid';
            $subRes = $this->executeSql($sql, array('refid' => $value['refid']));
            if (count($subRes) > 0) {
                $data[$value['refid']] = $subRes;
            }

        }
        return new JsonResponse($data);
    }

}
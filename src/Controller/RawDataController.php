<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\RawData;
use Exception;


class RawDataController extends AbstractController
{
    private $_repository;
    private $_response;

    /**
     * RawDataController constructor.
     */
    public function __construct()
    {
        $this->_response = new Response();

        $this->_response->headers->set('Content-type', 'application/json');
    }

    /**
     * Возвращает запись с сырыми данными по id
     *
     * @param int $id id записи
     *
     * @return Response
     * @throws Exception
     */
    public function getRawData(int $id): Response
    {
        $this->_repository = $this
            ->getDoctrine()
            ->getRepository(RawData::class);
        $rawData = $this->_repository->find($id);
        if ($rawData) {
            return $this->_setResponseContent($this->_response, $rawData);
        } else {
            throw new Exception("raw_data element with id:".$id." is missing");
        }
    }

    /**
     * Метод возвращает все записи из таблицы raw_data
     *
     * @return Response
     *
     * @throws Exception
     */
    public function getAll(): Response
    {
        $this->_repository = $this
            ->getDoctrine()
            ->getRepository(rawData::class);
        $rawDataList = $this->_repository->findAll();
        if ($rawDataList) {
            $items = [];
            foreach ($rawDataList as $rawData) {
                $item = [
                    'id' => $rawData->getId(),
                    'searchParamsId' => $rawData->getSearchParams()->getId(),
                    'offerText' => $rawData->getOfferText(),
                    'isParsed' => $rawData->getIsParsed(),
                    'createdAt' => $rawData->getCreatedAt()
                ];
                $items[] = $item;
            }
            return $this->_response->setContent(json_encode($items));
        } else {
            throw new Exception("There are no elements in table raw_data");
        }
    }

    /**
     * Упаковка данных из записи в объект Response
     *
     * @param Response $response объект Response
     * @param RawData  $rawData  объект RawData
     *
     * @return Response
     */
    private function _setResponseContent(
        Response $response,
        RawData $rawData
    ): Response {
        $response->setContent(
            json_encode(
                [
                    'id' => $rawData->getId(),
                    'searchParamsId' => $rawData->getSearchParams()->getId(),
                    'offerText' => $rawData->getOfferText(),
                    'isParsed' => $rawData->getIsParsed(),
                    'createdAt' => $rawData->getCreatedAt()
                ]
            )
        );
        return $response;
    }
}
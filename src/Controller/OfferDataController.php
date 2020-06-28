<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\OfferData;
use Exception;


class OfferDataController extends AbstractController
{
    private $_repository;
    private $_response;

    /**
     * OfferDataController constructor.
     */
    public function __construct()
    {
        $this->_response = new Response();

        $this->_response->headers->set('Content-type', 'application/json');
    }

    /**
     * Возвращает запись с информацией о предложении по id
     *
     * @param int $id id записи
     *
     * @return Response
     * @throws Exception
     */
    public function getOfferData(int $id): Response
    {
        $this->_repository = $this
            ->getDoctrine()
            ->getRepository(OfferData::class);
        $offerData = $this->_repository->find($id);
        if ($offerData) {
            return $this->_setResponseContent($this->_response, $offerData);
        } else {
            throw new Exception("offer_data element with id:".$id." is missing");
        }
    }

    /**
     * Метод возвращает все записи из таблицы offer_data
     *
     * @return Response
     *
     * @throws Exception
     */
    public function getAll(): Response
    {
        $this->_repository = $this
            ->getDoctrine()
            ->getRepository(offerData::class);
        $offerDataList = $this->_repository->findAll();
        if ($offerDataList) {
            $items = [];
            foreach ($offerDataList as $offerData) {
                $item = [
                    'id' => $offerData->getId(),
                    'rawDataId' => $offerData->getRawData()->getId(),
                    'akassaPrice' => $offerData->getAkassaPrice(),
                    'buttonPrice' => $offerData->getButtonPrice(),
                    'akassaHref' => $offerData->getAkassaHref(),
                    'baggage' => $offerData->getBaggage(),
                    'departurePoint' => $offerData->getDeparturePoint(),
                    'arrivalPoint' => $offerData->getArrivalPoint(),
                    'departureDatetime' => $offerData->getDepartureDatetime(),
                    'arrivalDatetime' => $offerData->getArrivalDatetime(),
                    'transferTime' => $offerData->getTransferTime(),
                    'createdAt' => $offerData->getCreatedAt()
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
     * @param Response  $response  объект Response
     * @param OfferData $offerData объект RawData
     *
     * @return Response
     */
    private function _setResponseContent(
        Response $response,
        OfferData $offerData
    ): Response {
        $response->setContent(
            json_encode(
                [
                    'id' => $offerData->getId(),
                    'rawDataId' => $offerData->getRawData()->getId(),
                    'akassaPrice' => $offerData->getAkassaPrice(),
                    'buttonPrice' => $offerData->getButtonPrice(),
                    'akassaHref' => $offerData->getAkassaHref(),
                    'baggage' => $offerData->getBaggage(),
                    'departurePoint' => $offerData->getDeparturePoint(),
                    'arrivalPoint' => $offerData->getArrivalPoint(),
                    'departureDatetime' => $offerData->getDepartureDatetime(),
                    'arrivalDatetime' => $offerData->getArrivalDatetime(),
                    'transferTime' => $offerData->getTransferTime(),
                    'createdAt' => $offerData->getCreatedAt()
                ]
            )
        );
        return $response;
    }
}
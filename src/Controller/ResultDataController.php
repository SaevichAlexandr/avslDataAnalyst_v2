<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\OfferData;
use App\Entity\SuppliersPrice;
use App\Entity\Supplier;
use App\Entity\OfferDataFlight;
use App\Entity\Flight;
use DateTime;
use Exception;

class ResultDataController extends AbstractController
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
    public function getFullOfferInfo(int $id): Response
    {
        $mainOfferInfo = $this->_findOfferData($id);
        $pricesOfferInfo = $this->_findPricesInfo($id);
        $resultList = [
            'offerData' => [
                $mainOfferInfo,
                'suppliersPrices' => $pricesOfferInfo
            ]
        ];
        return $this->_response->setContent(json_encode($resultList));
    }

    /**
     * Возвращает запись с информацией о предложении по id
     *
     * @param int $id id записи
     *
     * @return Response
     * @throws Exception
     */
    public function getFullOfferInfoAll(): Response
    {
        $this->_repository = $this
            ->getDoctrine()
            ->getRepository(OfferData::class);
        $offerDataList = $this->_repository->findAll();
        $resultList = [];
        foreach ($offerDataList as $offerData) {
            $mainOfferInfo = $this->_setOfferDataContent($offerData);
            $pricesOfferInfo = $this->_findPricesInfo($offerData->getId());
            $resultList[] = [
                $mainOfferInfo,
                'suppliersPrices' => $pricesOfferInfo
            ];
        }
        return $this->_response->setContent(json_encode($resultList));
    }

    private function _findPricesInfo(int $offerDataId): array
    {
        $this->_repository = $this
            ->getDoctrine()
            ->getRepository(SuppliersPrice::class);

        $suppliersPriceList = $this->_repository
            ->findBy(['offerData' => $offerDataId]);

        $this->_repository = $this
            ->getDoctrine()
            ->getRepository(Supplier::class);
        $list = [];
        foreach ($suppliersPriceList as $supplierPrice) {
            $supplier = $this->_repository
                ->find($supplierPrice->getSupplier()->getId());
            $list[] = [
                'supplierName' => $supplier->getName(),
                'supplierPrice' => $supplierPrice->getPrice()
            ];
        }
        return $list;
    }

    private function _findOfferData(int $offerDataId): array
    {
        $this->_repository = $this
            ->getDoctrine()
            ->getRepository(OfferData::class);
        $offerData = $this->_repository->find($offerDataId);
        if ($offerData) {
            return $this->_setOfferDataContent($offerData);
        } else {
            throw new Exception(
                "offer_data element with id:".
                $offerDataId." is missing"
            );
        }
    }

    /**
     * Упаковка данных из записи в объект Response
     *
     * @param OfferData $offerData объект offerData
     *
     * @return array
     */
    private function _setOfferDataContent(
        OfferData $offerData
    ): array {
        return
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
            ];
    }
}
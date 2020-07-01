<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\RawData;
use App\Entity\OfferData;
use App\Entity\SuppliersPrice;
use App\Entity\Supplier;
use DateTime;
use Exception;


class DataParserController extends AbstractController
{
    private $_datetimeFormat = 'Y-m-d H:i:s';

    /**
     * Входной метод
     *
     * @return Response
     *
     * @throws Exception
     */
    public function parsingQueueOrganizer()
    {
        $response = new Response();
        $response->headers->set('Content-type', 'application/json');

        $repository = $this->getDoctrine()->getRepository(RawData::class);
        $rawDataList = $repository->findBy(['isParsed' => false]);

        if ($rawDataList) {
            $completeList = [];
            foreach ($rawDataList as $rawData) {
                // TODO: сюда вставить ызов функции парсинга
                try {
                    $completeList[] = $this->_parseData($rawData)->getId();
                }
                catch (Exception $ex)
                {
                    $response->setContent(json_encode($ex));
                    return $response;
                }

            }
            $response->setContent(json_encode($completeList));
            return $response;
        } else {
            $response->setContent('There are no unchecked rawData');
            return $response;
        }
    }

    /**
     * Разбиение строки на части
     *
     * @param RawData $rawData строка с сырыми данными
     *
     * @return OfferData
     *
     * @throws Exception
     */
    private function _parseData(RawData $rawData): OfferData
    {
        //TODO: в этом методе надо будет возвращать скпомпонованный
        // объект (или json) с предложением, ценами и перелётами
        $splitResult = preg_split('/\r\n|\r|\n/', $rawData->getOfferText());
        $offerData = new OfferData();
        $offerData->setRawData($rawData);
        try {
            $offerData = $this->_setOfferData($splitResult, $offerData);
//            $offerDataId = $offerData->getId();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($offerData);
            $entityManager->flush();

            $this->_suppliersPricesHandler(
                $splitResult, $offerData
            );
        }
        catch (Exception $ex) {
//            throw new Exception($ex->getMessage().'rawDataId: '.$rawData->getId());
            throw $ex;
        }



        $rawData->setIsParsed(true);
        $entityManager->persist($rawData);
        $entityManager->flush();

        //TODO: весь сбор инфы по OfferData отсюда
        // нужно перенести в функцию parseOfferInfo
        return $offerData;
    }

    /**
     * Получение данных для таблицы OfferData
     *
     * @param array     $splitResult массив с строками сырых
     *                               данных в качестве элементов
     * @param OfferData $offerData   объект OfferData
     *                               с уже готовой ссылкой на rawData
     *
     * @return OfferData
     *
     * @throws Exception
     */
    private function _setOfferData(
        array $splitResult,
        OfferData $offerData
    ): OfferData {
        try {
            $offerData->setBaggage($this->_parseBaggage($splitResult[0]));
            $buyForIndex = $this->_buyFor($splitResult);
            $buttonPrice = $this->_parseButtonPrice($splitResult, $buyForIndex);
            $offerData->setButtonPrice($buttonPrice);
            $suppliersPrices = $this->_parseSuppliersPrices(
                $splitResult,
                $buyForIndex,
                $buttonPrice
            );
            $offerData->setAkassaPrice($this->_findAkassaPrice($suppliersPrices));
            $offerData->setAkassaHref(
                $this->_parseAkassaHref($splitResult[(count($splitResult) - 1)])
            );
            for ($i = 0; $i < count($splitResult); $i++) {
                if (preg_match('([0-9][0-9]:[0-9][0-9])', $splitResult[$i])) {
                    $currentElement = $i;
                    $offerData->setDeparturePoint(
                        $splitResult[($currentElement + 4)]
                    );
                    $offerData->setArrivalPoint(
                        $splitResult[($currentElement + 5)]
                    );
                    $offerData->setTransferTime(
                        $this->_parseTransferTime(
                            $splitResult[($currentElement + 3)]
                        )
                    );
                    $departureTime = $splitResult[$currentElement];
                    $departureDate = $splitResult[($currentElement + 2)];
                    $arrivalTime = $splitResult[($currentElement + 6)];
                    $arrivalDate = $splitResult[($currentElement + 8)];
                    $offerData->setDepartureDatetime(
                        $this->_parseOfferDate(
                            $departureDate,
                            $departureTime
                        )
                    );
                    $offerData->setArrivalDatetime(
                        $this->_parseOfferDate(
                            $arrivalDate,
                            $arrivalTime
                        )
                    );
                    $offerData->setCreatedAt(
                        DateTime::createFromFormat(
                            $this->_datetimeFormat, date('Y-m-d H:i:s')
                        )
                    );
                    return $offerData;
                }
            }
        }
        catch (Exception $ex) {
            throw $ex;
        }
        throw new Exception(
            'Произошла непредвиденная ошибка при заполнении объекта OfferData'
        );
    }

    private function _suppliersPricesHandler(
        array $splitResult,
        OfferData $offerData
    ): void {
        try {
            $buyForIndex = $this->_buyFor($splitResult);
            $buttonPrice = $this->_parseButtonPrice($splitResult, $buyForIndex);
            $suppliersPrices = $this->_parseSuppliersPrices(
                $splitResult,
                $buyForIndex,
                $buttonPrice
            );
            foreach ($suppliersPrices as $suppliersPrice) {
                $this->_setSuppliersPrice($suppliersPrice, $offerData);
            }
        }
        catch (Exception $ex) {
            throw $ex;
        }
        return;
    }

    //TODO: хорошая мысль всё-таки сделать кастомные классы ошибок
    // чтобы в них передавать id ресурса, на котором произошёл ахтунг

    /**
     * Метод записывает данные в таблицу с ценой каждого поставщика
     *
     * @param array     $supplierPrice массив хранящий название поставщика и его цену
     * @param OfferData $offerData     объект OfferData
     *
     * @return SuppliersPrice
     */
    private function _setSuppliersPrice(
        array $supplierPrice,
        OfferData $offerData
    ): SuppliersPrice {
        $repository = $this->getDoctrine()->getRepository(Supplier::class);
        $entityManager = $this->getDoctrine()->getManager();

        $supplier = $repository->findOneBy(
            ['name' => $supplierPrice['supplierName']]
        );
        if (!is_object($supplier)) {
            $supplier = new Supplier();
            $supplier->setName($supplierPrice['supplierName']);

            $entityManager->persist($supplier);
            $entityManager->flush();
        }
        $suppliersPrice = new SuppliersPrice($supplier, $offerData);
        $suppliersPrice->setPrice($supplierPrice['supplierPrice']);

        $entityManager->persist($suppliersPrice);
        $entityManager->flush();


        return $suppliersPrice;
    }

    /**
     * Возвращает очищенную ссылку
     *
     * @param string $rawHref необработанная ссылка
     *
     * @return string
     */
    private function _parseAkassaHref(string $rawHref): string
    {
        return preg_replace(
            '/^([^=]+)=/',
            '',
            $rawHref
        );
    }

    /**
     * Ищет среди всех цен цену предлагаемую Авиакассой
     *
     * @param array $suppliersPrices список цен всех поставщиков
     *
     * @return float
     *
     * @throws Exception
     */
    private function _findAkassaPrice(array $suppliersPrices): float
    {
        foreach ($suppliersPrices as $suppliersPrice) {
            if ($suppliersPrice['supplierName'] == 'Aviakassa') {
                return $suppliersPrice['supplierPrice'];
            }
        }
        throw new Exception('Не удалось найти цену от Авиакассы!');
    }

    /**
     * Получение цены с кнопки
     *
     * @param array $splitResult массив с строками сырых данных в качестве элементов
     * @param int   $buyForIndex индекс строки 'Купить'
     * 
     * @return float
     */
    private function _parseButtonPrice(array $splitResult, int $buyForIndex): float
    {
        // тут в разделителе нормальный пробел код &#32;
        $buttonPrice = explode(' ', $splitResult[++$buyForIndex])[1];
        // тут в разделителе пробел, но не совсем (Thin space) код &#8201;,
        $buttonPrice = explode(' ', $buttonPrice);
        $buttonPrice = $buttonPrice[0].$buttonPrice[1];
        $buttonPrice = floatval($buttonPrice);
        return $buttonPrice;
    }

    /**
     * Функция возвращает индекс элемента массива в котором хранится строка "Купить"
     * от размещения этого элемента стартует парсинг цен и поставщиков
     *
     * @param array $splitResult массив с строками сырых данных в качестве элементов
     *
     * @return int
     *
     * @throws Exception
     */
    private function _buyFor(array $splitResult): int
    {
        for ($i = 1; $i < count($splitResult); $i++) {
            if ($splitResult[$i] == 'Купить') {
                return $i;
            }
        }
        throw new Exception('В массиве отсустствует строка \'Купить\'');
    }

    /**
     * Парсинг цен поставщиков
     *
     * @param array $splitResult массив с строками сырых данных в качестве элементов
     * @param int   $buyForIndex индекс строки 'Купить'
     * @param float $buttonPrice цена отображаемая на кнопке
     *
     * @return array
     *
     * @throws Exception
     */
    private function _parseSuppliersPrices(
        array $splitResult,
        int $buyForIndex,
        float $buttonPrice
    ): array {
        $suppliersPrices = [];

        $buttonSupplierName = str_replace(
            'на ',
            '',
            $splitResult[($buyForIndex + 2)]
        );
        $suppliersPrices[] = [
            'supplierName' => $buttonSupplierName,
            'supplierPrice' => $buttonPrice
        ];
        for ($i = ($buyForIndex + 3); $i < count($splitResult); $i++) {
            if (!preg_match('([0-9][0-9]:[0-9][0-9])', $splitResult[$i])
                && $splitResult[$i] != 'ЧАРТЕР'
                && $splitResult[$i] != 'ЛОУКОСТ'
                && !preg_match('(ТУДА.*)', $splitResult[$i])
            ) {
                $supplierName = $splitResult[$i];
                $supplierPrice = explode(' ', $splitResult[++$i]);
                $supplierPrice = floatval($supplierPrice[0].$supplierPrice[1]);
                $suppliersPrices[] = [
                    'supplierName' => $supplierName,
                    'supplierPrice' => $supplierPrice
                ];
            } else {
                return $suppliersPrices;
            }
        }
        throw new Exception('Не удалось получить цены по данному предложению!');
    }

    /**
     * Преобразование информации о багаже в необходимый формат
     * 
     * @param string $rawBaggage строка с необработанной информацией о багаже
     * 
     * @return string
     * 
     * @throws Exception
     */
    private function _parseBaggage(string $rawBaggage): string
    {
        switch ($rawBaggage) {
            case 'Нет багажа':
                return '0PC';
            case 'С багажом':
                return '1PC';
            case 'Багаж неизвестен':
                return 'N/A';
        }
        throw new Exception('Невозможно определить наличие багажа!');
    }

    /**
     * Перевод времени в пути к количеству минут
     *
     * @param string $rawTransferTime строка с необработанным временем перелёта
     *
     * @return string
     */
    private function _parseTransferTime(string $rawTransferTime): string
    {
        $rawTransferTimeArray = preg_split('/ /', $rawTransferTime);
        $transferHours = intval(str_replace('ч', '', $rawTransferTimeArray[2]));
        if (count($rawTransferTimeArray) == 4) {
            $transferMinutes = intval(
                str_replace(
                    'м',
                    '',
                    $rawTransferTimeArray[3]
                )
            );
            return strval(($transferHours*60 + $transferMinutes));
        }
        return strval(($transferHours*60));
    }

    /**
     * Преобразует дату и время из 2 строк в единый DateTime
     *
     * @param string $rawDate строка с необработанной датой
     * @param string $rawTime строка с необработтаным временем
     *
     * @return DateTime
     *
     * @throws Exception
     */
    private function _parseOfferDate(string $rawDate, string $rawTime): DateTime
    {
        try {
            $rawDateArray = preg_split('/ /', $rawDate);
            $day = $rawDateArray[0];
            $month = $this->_checkMonth($rawDateArray[1]);
            $year = str_replace(',',  '', $rawDateArray[2]);

            return DateTime::createFromFormat(
                $this->_datetimeFormat,
                $year.'-'.$month.'-'.$day.' '.$rawTime.':00'
            );
        }
        catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Переопределение символьного названия месяца в числовое
     *
     * @param string $rawMonth строка с необработанным названием месяца
     *
     * @return string
     *
     * @throws Exception
     */
    private function _checkMonth(string $rawMonth): string
    {
        switch ($rawMonth) {
            case 'янв':
                return '01';
            case 'фев':
                return '02';
            case 'мар':
                return '03';
            case 'апр':
                return '04';
            case 'май':
                return '05';
            case 'июн':
                return '06';
            case 'июл':
                return '07';
            case 'авг':
                return '08';
            case 'сен':
                return '09';
            case 'окт':
                return '10';
            case 'ноя':
                return '11';
            case 'дек':
                return '12';
        }
        throw new Exception('Не найдена информация о месяце');
    }

    /**
     * Метод распарсивает информацию по входящим в предложениям перелётам
     *
     * @param array $splitResult
     * @return array
     */
//    private function parseFlightsInfo(array $splitResult): array
//    {
//        for ($i = 0; $i < count($splitResult); $i++) {
//            if (!preg_match('([0-9][0-9]:[0-9][0-9])', $splitResult[$i])) {
//                $fromTime = $splitResult[$i];
//
//            }
//        }
//    }
}

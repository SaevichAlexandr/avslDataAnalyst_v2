<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\SearchParams;
use App\Entity\RawData;
use App\Entity\OfferData;
use DateTime;
use Exception;
use App\CustomException\StringNotFoundException;

class DataParserController extends AbstractController
{
    private $datetimeFormat = 'Y-m-d H:i:s';

    /**
     * Старт парсинга
     *
     * @return Response
     */
    public function parsingQueueOrganizer()
    {
        $response = new Response();
        $response->headers->set('Content-type', 'application/json');

        $repository = $this->getDoctrine()->getRepository(RawData::class);
        $rawDataList = $repository->findBy(['isParsed' => false]);

        if($rawDataList) {
            $completeList = [];
            foreach ($rawDataList as $rawData) {
                // TODO: сюда вставить ызов функции парсинга
                $errorCode = $this->parseData($rawData);
//                $rawData->setIsParsed(true);
            }
            return $response->setContent(json_encode($completeList));
        }
        else {
            return $response->setContent('There are no unchecked searchParams');
        }
    }

    /**
     * Разбиение строки на части
     *
     * @param RawData $rawData
     * @return int
     * @throws Exception
     */
    private function parseData(RawData $rawData): int
    {
        // внесение данных в таблицу OfferData
        $splitResult = preg_split('/\r\n|\r|\n/', $rawData->getOfferText());
        echo 'ID = '.$rawData->getId();
        $offerData = new OfferData();
        $offerData->setRawData($rawData);

        $this->_parseOfferData($splitResult, $offerData);


        // TODO: весь сбор инфы по OfferData отсюда нужно перенести в функцию parseOfferInfo


        return 200;
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
    private function _parseOfferData(
        array $splitResult,
        OfferData $offerData
    ): OfferData {
        try {
            $baggage = $this->_parseBaggage($splitResult[0]);
            echo 'Baggage = ' . $baggage . '<br>';
            $buyForIndex = $this->_buyFor($splitResult);
            echo 'BuyForIndex = ' . $buyForIndex . '<br>';
            $buttonPrice = $this->_parseButtonPrice($splitResult, $buyForIndex);
            echo 'ButtonPrice = ' . $buttonPrice . '<br>';
            $suppliersPrices = $this->_parseSuppliersPrices(
                $splitResult,
                $buyForIndex,
                $buttonPrice
            );
            var_dump($suppliersPrices);
            $akassaPrice = $this->_findAkassaPrice($suppliersPrices);
            echo 'AkassaPrice = '.$akassaPrice.'<br>';
            $akassaHref = $splitResult[(count($splitResult) - 1)];
            echo 'AkassaHref = ' . $akassaHref . '<br>';

            for ($i = 0; $i < count($splitResult); $i++) {
                if (preg_match('([0-9][0-9]:[0-9][0-9])', $splitResult[$i])) {
                    $currentElement = $i;
                    $departurePoint = $splitResult[($currentElement + 4)];
                    echo 'DeparturePoint = '.$departurePoint.'<br>';
                    $arrivalPoint = $splitResult[($currentElement + 5)];
                    echo 'ArrivalPoint = '.$arrivalPoint.'<br>';
                    // пересадки может и не быть, йобана
                    $transferTime = $this->_parseTransferTime(
                        $splitResult[($currentElement + 3)]
                    );
                    echo 'TransferTime = '.$transferTime.'<br>';

                    $departureTime = $splitResult[$currentElement];
                    $departureDate = $splitResult[($currentElement + 2)];
                    $arrivalTime = $splitResult[($currentElement + 6)];
                    $arrivalDate = $splitResult[($currentElement + 8)];

                    $departureDatetime = $this->_parseOfferDate(
                        $departureDate,
                        $departureTime
                    );
                    var_dump($departureDatetime);
                    $arrivalDatetime = $this->_parseOfferDate(
                        $arrivalDate,
                        $arrivalTime
                    );
                    var_dump($arrivalDatetime);
                    $createdAt = DateTime::createFromFormat(
                        $this->datetimeFormat, date('Y-m-d H:i:s')
                    );
                    echo '=====================================================<br>';
                    break;
                }
            }
        }
        catch (Exception $ex) {
            throw $ex;
        }
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
        // TODO: стоит подумать, может всю работу с поставщиками можно в одном цикле сделать
        // тут в разделителе нормальный пробел код &#32;
        $buttonPrice = explode(' ', $splitResult[++$buyForIndex])[1];
        // тут в разделителе пробел, но не совсем (Thin space) код &#8201;, крч лучше не трогать пока работает
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
     * @return bool
     * 
     * @throws Exception
     */
    private function _parseBaggage(string $rawBaggage): bool
    {
        switch ($rawBaggage) {
            case 'Нет багажа':
                return '0PC';
            case 'С багажом':
                return '1PC';
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
                $this->datetimeFormat,
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

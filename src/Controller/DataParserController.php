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
        // ебатория, но работает
//        $firstResult = explode('
//', $rawData->getOfferText());
        // чуть получше, но тоже работает
        $splitResult = preg_split('/\r\n|\r|\n/', $rawData->getOfferText());

//        $i = 0;
//        foreach ($splitResult as $item) {
//            echo ++$i.'||'.$item.'<br>';
//        }
        // TODO: весь сбор инфы по OfferData отсюда нужно перенести в функцию parseOfferInfo
        $offerData = new OfferData();
        $offerData->setRawData($rawData);
        switch ($splitResult[0]) {
            case 'Нет багажа':
//                $offerData->setBaggage('0PC');
                echo '0PC<br>';
                break;
            case 'С багажом':
//                $offerData->setBaggage('1PC');
                echo '1PC<br>';
                break;
        }
        try {
            $buyForIndex = $this->buyFor($splitResult);
        }
        catch (Exception $ex) {
            throw $ex;
        }

        $buttonPrice = $this->parseButtonPrice($splitResult, $buyForIndex);
        echo $buttonPrice.'<br>';

        $suppliersPrices = $this->parseSuppliersPrices($splitResult, $buyForIndex, $buttonPrice);
        // здесь нужн проверка, так как метод может вернуть пустой массив если всё пошло не так
        if($suppliersPrices) {
            var_dump($suppliersPrices);
        } else {
            echo 'При парсинге цен произошла ошибка и он был завершен некорректно';
        }

        return 200;
    }

    /**
     * Получение цены с кнопки
     *
     * @param array $splitResult
     * @param int $buyForIndex
     * @return float
     */
    private function parseButtonPrice(array $splitResult, int $buyForIndex): float
    {
        // TODO: стоит подумать, может всю работу с поставщиками можно в одном цикле сделать
        // тут в разделителе нормальный пробел код &#32;
        $buttonPrice = explode( ' ', $splitResult[++$buyForIndex])[1];
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
     * @param array $splitResult
     * @return int
     * @throws Exception
     */
    private function buyFor(array $splitResult): int
    {
        for ($i = 1; $i < count($splitResult); $i++) {
            if($splitResult[$i] == 'Купить') {
                return $i;
            }
        }
        throw new Exception('В массиве отсустствует строка \'Купить\'');
    }

    /**
     * Парсинг цен поставщиков
     *
     * @param array $splitResult
     * @param int $buyForIndex
     * @param float $buttonPrice
     * @return array
     */
    private function parseSuppliersPrices(array $splitResult, int $buyForIndex, float $buttonPrice): array
    {
        $suppliersPrices = [];
        for ($i = ($buyForIndex + 3); $i < count($splitResult); $i++) {
            if (!preg_match('([0-9][0-9]:[0-9][0-9])', $splitResult[$i])) {
                $supplierName = $splitResult[$i];
                $supplierPrice = explode(' ', $splitResult[++$i]);
                $supplierPrice = floatval($supplierPrice[0].$supplierPrice[1]);
                $suppliersPrices[] = ['supplierName' => $supplierName, 'supplierPrice' => $supplierPrice];
            }
            else {
                return $suppliersPrices;
            }
        }
        return $suppliersPrices;
    }

    private function parseOfferInfo(array $splitResult)
    {
        for ($i = 0; $i < count($splitResult); $i++) {
            if (!preg_match('([0-9][0-9]:[0-9][0-9])', $splitResult[$i])) {
                $currentElement = $i;
                $departureTime = $splitResult[$currentElement];
                $departureDate = $splitResult[($currentElement + 2)];
                $arrivalTime = $splitResult[($currentElement + 6)];
                $arrivalDate = $splitResult[($currentElement + 8)];
                try {
                    $departureDatetime = $this->parseOfferDate($departureDate, $departureTime);
                    $arrivalDatetime = $this->parseOfferDate($arrivalDate, $arrivalTime);
                }
                catch (Exception $ex) {
                    throw $ex;
                }

            }
        }
    }

    /**
     * Преобразует дату и время из 2 строк в единый DateTime
     *
     * @param string $rawDate
     * @param string $rawTime
     * @return DateTime
     * @throws Exception
     */
    private function parseOfferDate(string $rawDate, string $rawTime): DateTime
    {
        try {
            $rawDateArray = preg_split('/ /', $rawDate);
            $day = $rawDateArray[0];
            $month = $this->checkMonth($rawDateArray[1]);
            $year = str_replace(',',  '', $rawDateArray[2]);

            return DateTime::createFromFormat($this->datetimeFormat, $year.'-'.$month.'-'.$day.' '.$rawTime.':00');
        }
        catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Переопределение символьного названия месяца в числовое
     *
     * @param string $rawMonth
     * @return string
     * @throws Exception
     */
    private function checkMonth(string $rawMonth): string
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

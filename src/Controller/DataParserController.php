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

class DataParserController extends AbstractController
{
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
        $buyForIndex = $this->buyFor($splitResult);

        if($buyForIndex == -1) {
            return 501;
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
     */
    private function buyFor(array $splitResult): int
    {
        for ($i = 1; $i < count($splitResult); $i++) {
            if($splitResult[$i] == 'Купить') {
                return $i;
            }
        }
        return -1;
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

    private function parseFlightsInfo()
    {

    }
}

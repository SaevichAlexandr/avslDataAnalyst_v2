<?php

namespace App\Controller;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use App\Entity\SearchParams;
use App\Entity\RawData;
use Exception;

class DataCollectorController extends AbstractController
{
    // starting selenium server
    // java -Dwebdriver.gecko.driver="geckodriver.exe" -jar selenium-server-standalone-3.141.59.jar
    // This is where Selenium server 2/3 listens by default. For Selenium 4, Chromedriver or Geckodriver, use http://localhost:4444/
    // for geckodriver on selenium server
    //$host = 'http://localhost:4444/wd/hub';
    // for clear geckodriver
    //$host = 'http://localhost:4444';
    // for clear chromedriver
    //$host = 'http://localhost:9515';

    public function gatherQueueOrganizer()
    {
        $response = new Response();
        $response->headers->set('Content-type', 'application/json');

        $repository = $this->getDoctrine()->getRepository(SearchParams::class);
        $searchParamsList = $repository->findBy(['isChecked' => false]);

        if($searchParamsList) {
            $completeList = [];
            foreach ($searchParamsList as $searchParams) {
                set_time_limit(60 * $searchParams->getShowMoreClicks());
                $timeToComplete = $this->gatherData($searchParams);
                $completeList[] = ['searchParamsId' => $searchParams->getId(), 'timeToComplete' => $timeToComplete];
            }
            return $response->setContent(json_encode($completeList));
        }
        else {
            return $response->setContent('There are no unchecked searchParams');
        }
    }


    public function gatherData(SearchParams $searchParams)
    {
        $time_pre = microtime(true);

        $host = 'http://localhost:4444/wd/hub';
        $capabilities = DesiredCapabilities::firefox();
        // включение "безголового" режима
        $capabilities->setCapability('moz:firefoxOptions', ['args' => ['-headless']]);
        $driver = RemoteWebDriver::create($host, $capabilities);

        // TODO: хорошая идея сохранять сырые данные, а потом уже их обрабатывать. Таким образом не будет задержки у парсера страницы.

        $continueButtonClicks = $searchParams->getShowMoreClicks();

        $driver->get('https://www.aviasales.ru/search/'.
            $searchParams->getDeparturePoint().
            $searchParams->getToDepartureDay().
            $searchParams->getToDepartureMonth().
            $searchParams->getArrivalPoint().
            $searchParams->getFromDepartureDay().
            $searchParams->getFromDepartureMonth().
            $searchParams->getReservationClass().
            $searchParams->getAdults().
            $searchParams->getChildren().
            $searchParams->getInfants().
            '?back=true'
        );

        $driver->findElement(WebDriverBy::className('theme-switcher'))->click();

        $driver->wait()->until(WebDriverExpectedCondition::not(WebDriverExpectedCondition::titleIs(
            $searchParams->getToDepartureDay().
            '.'.
            $searchParams->getToDepartureMonth().
            ', '.
            $searchParams->getDeparturePoint().
            ' → '.
            $searchParams->getArrivalPoint()
        )));

        $showMoreButton = $driver->findElement(WebDriverBy::cssSelector('div.show-more-products > button'));

        $continueButtonClicks = $this->showMoreResults(
            $continueButtonClicks,
            $showMoreButton
        );

        // проверка на совпадение количества элементов возвращаемых при поиске с количеством элементов загруженных в браузере
        $driver->wait()->until(
            function () use ($driver, $continueButtonClicks)
            {
                $listElements = $driver->findElements(WebDriverBy::cssSelector('div.fade-enter-done'));

                return count($listElements) >= ($continueButtonClicks + 1) * 10;
            },
            'Count of listElements must be another'
        );

        //sleep(5);

        // почему-то через этот класс у div всё работает
        $listElements = $driver->findElements(WebDriverBy::cssSelector('div.fade-enter-done'));

        foreach ($listElements as $element)
        {
            $element->click();
        }

        $dataArray = $driver->findElements(WebDriverBy::className('ticket-desktop'));

        $entityManager = $this->getDoctrine()->getManager();

        for ($i = 1; $i < count($dataArray); $i++)
        {
            $rawData = new RawData();
            try {
                $akassaLink = $dataArray[$i]->findElement(WebDriverBy::partialLinkText('Aviakassa'))->getAttribute('href');
            }
            catch (Exception $ex) {
                continue;
            }
//            echo '<br>'.$akassaLink;
            $rawData->setOfferText($dataArray[$i]->getText());
            $rawData->setSearchParams($searchParams);

            $entityManager->persist($rawData);
            $entityManager->flush();
        }

//        $driver->quit();

        $time_post = microtime(true);

        // указываем что данная запись уже проверенна
        $searchParams->setIsChecked(true);
        $entityManager->persist($searchParams);
        $entityManager->flush();

        return ($time_post - $time_pre);
    }

    /**
     * Функция возвращает количество реально совершенных нажатий на кнопку "Показать еще 10 результатов"
     * (необходимо в случае если в запросе указано больше кликов чем можно выполнить из-за недостаточного количества рейсов)
     *
     * @param int $continueButtonClicks
     * @param RemoteWebElement $driverItem
     * @return int
     */
    private function showMoreResults(int $continueButtonClicks, RemoteWebElement $driverItem): int
    {
        for ($i = 0; $i < $continueButtonClicks; $i++)
        {
            try {
                $driverItem->click();
            }
            catch (Exception $ex) {
                return --$i;
            }
        }
        return $continueButtonClicks;
    }
}

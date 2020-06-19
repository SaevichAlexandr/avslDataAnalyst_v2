<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\SearchParams;
use DateTime;

class SearchParamsController extends AbstractController
{
    private $maxPassengersNumber = 9;
    private $datetimeFormat = 'Y-m-d H:i:s';

    public function getSearchParams($id)
    {
        $response = new Response();
        $response->headers->set('Content-type', 'application/json');
        $repository = $this->getDoctrine()->getRepository(SearchParams::class);
        $searchParams = $repository->find($id);
        if($searchParams) {
            $response->setContent(json_encode(
                [
                    'id' => $searchParams->getId(),
                    'departurePoint' => $searchParams->getDeparturePoint(),
                    'arrivalPoint' => $searchParams->getArrivalPoint(),
                    'toDepartureDay' => $searchParams->getToDepartureDay(),
                    'toDepartureMonth' => $searchParams->getToDepartureMonth(),
                    'fromDepartureDay' => $searchParams->getFromDepartureDay(),
                    'fromDepartureMonth' => $searchParams->getFromDepartureMonth(),
                    'reservationClass' => $searchParams->getReservationClass(),
                    'adults' => $searchParams->getAdults(),
                    'children' => $searchParams->getChildren(),
                    'infants' => $searchParams->getInfants(),
                    'showMoreClicks' => $searchParams->getShowMoreClicks(),
                    'createdAt' => $searchParams->getCreatedAt(),
                    'isChecked' => $searchParams->getIsChecked(),
                ]
            ));

            return $response;
        }
        else
        {
            $response->setContent(json_encode('Element not found'));
            return $response;
        }
    }

    public function getAll()
    {
        $repository = $this->getDoctrine()->getRepository(SearchParams::class);
        $searchParamsList = $repository->findAll();
        $response = new Response();
        $response->headers->set('Content-type', 'application/json');

        if($searchParamsList)
        {
            $items = [];
            foreach ($searchParamsList as $searchParams)
            {
                $item = [
                    'id' => $searchParams->getId(),
                    'departurePoint' => $searchParams->getDeparturePoint(),
                    'arrivalPoint' => $searchParams->getArrivalPoint(),
                    'toDepartureDay' => $searchParams->getToDepartureDay(),
                    'toDepartureMonth' => $searchParams->getToDepartureMonth(),
                    'fromDepartureDay' => $searchParams->getFromDepartureDay(),
                    'fromDepartureMonth' => $searchParams->getFromDepartureMonth(),
                    'reservationClass' => $searchParams->getReservationClass(),
                    'adults' => $searchParams->getAdults(),
                    'children' => $searchParams->getChildren(),
                    'infants' => $searchParams->getInfants(),
                    'showMoreClicks' => $searchParams->getShowMoreClicks(),
                    'createdAt' => $searchParams->getCreatedAt(),
                    'isChecked' => $searchParams->getIsChecked(),
                ];
                $items[] = $item;
            }

            $response->setContent(json_encode($items));

            return $response;
        }
        else
        {
            $response->setContent(json_encode('Elements not found'));
            return $response;
        }
    }

    /**
     * Запись параметров поискового запроса для нахождения данных по рейсам
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $reqBody = json_decode($request->getContent(), true);
        $response = new Response();
        $response->headers->set('Content-type', 'application/json');

        $validationResponse = $this->isValidReqBody($reqBody);

        if($validationResponse == 200) {
            $searchParams = new SearchParams();
            $response->setContent(json_encode(['id' => $this->setData($searchParams, $reqBody, true)->getId()]));
            return $response;
        }
        // TODO: пожалуй нужно добавить класс с описаниями ошибок потом
        else {
            $response->setContent(json_encode('Invalid data was sent. Validation error №'.$validationResponse));
            return $response;
        }
    }

    /**
     * Изменение записи в таблице searchParams
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function update(int $id, Request $request): Response
    {
        $reqBody = json_decode($request->getContent(), true);
        $response = new Response();
        $response->headers->set('Content-type', 'application/json');

        $repository = $this->getDoctrine()->getRepository(SearchParams::class);
        $searchParams = $repository->find($id);

        // проверка существования записи и её не использованности
        if($searchParams && !$searchParams->getIsChecked()) {
            $validationResponse = $this->isValidReqBody($reqBody);

            if($validationResponse == 200) {
                $searchParams = $this->setData($searchParams, $reqBody, false);
                $response->setContent(json_encode([
                    'id' => $searchParams->getId(),
                    'departurePoint' => $searchParams->getDeparturePoint(),
                    'arrivalPoint' => $searchParams->getArrivalPoint(),
                    'toDepartureDay' => $searchParams->getToDepartureDay(),
                    'toDepartureMonth' => $searchParams->getToDepartureMonth(),
                    'fromDepartureDay' => $searchParams->getFromDepartureDay(),
                    'fromDepartureMonth' => $searchParams->getFromDepartureMonth(),
                    'reservationClass' => $searchParams->getReservationClass(),
                    'adults' => $searchParams->getAdults(),
                    'children' => $searchParams->getChildren(),
                    'infants' => $searchParams->getInfants(),
                    'showMoreClicks' => $searchParams->getShowMoreClicks(),
                    'createdAt' => $searchParams->getCreatedAt(),
                    'isChecked' => $searchParams->getIsChecked(),
                ]));
                return $response;
            }
            else {
                $response->setContent(json_encode('Invalid data was sent. Validation error №'.$validationResponse));
                return $response;
            }
        }
        else {
            $response->setContent(json_encode('There is no element with such id or it`s already checked'));
            return $response;
        }

    }

    /**
     * Удаление записи из таблицы
     *
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $response = new Response();
        $response->headers->set('Content-type', 'application/json');

        $repository = $this->getDoctrine()->getRepository(SearchParams::class);
        $searchParams = $repository->find($id);
        // проверка наличия записи и того что по ней не было работы
        if($searchParams && !$searchParams->getIsChecked())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($searchParams);
            $entityManager->flush();

            $response->setContent(json_encode(['isDeleted' => true]));
            return $response;
        }
        else
        {
            $response->setContent(json_encode('There is no element with such id or it`s already checked'));
            return $response;
        }
    }

    // TODO: нужно реализовать проверку наличия рейса, и в случае наличия брать его id из бд

    /**
     * Функция записи данных через ORM
     *
     * @param SearchParams $searchParams
     * @param array $reqBody
     * @param bool $createAction
     * @return SearchParams
     */
    private function setData(SearchParams $searchParams, array $reqBody, bool $createAction): SearchParams
    {
        $searchParams->setDeparturePoint($reqBody['departurePoint']);
        $searchParams->setArrivalPoint($reqBody['arrivalPoint']);
        $searchParams->setToDepartureDay($reqBody['toDepartureDay']);
        $searchParams->setToDepartureMonth($reqBody['toDepartureMonth']);
        $searchParams->setFromDepartureDay($reqBody['fromDepartureDay']);
        $searchParams->setFromDepartureMonth($reqBody['fromDepartureMonth']);
        $searchParams->setReservationClass($reqBody['reservationClass']);
        $searchParams->setAdults($reqBody['adults']);
        $searchParams->setChildren($reqBody['children']);
        $searchParams->setInfants($reqBody['infants']);
        $searchParams->setShowMoreClicks($reqBody['showMoreClicks']);
        if($createAction) {
            $searchParams->setCreatedAt(DateTime::createFromFormat($this->datetimeFormat, date('Y-m-d H:i:s')));
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($searchParams);
        $entityManager->flush();

        return $searchParams;
    }

    /**
     * Проверка корректности вводимых данных
     *
     * @param array $reqBody
     * @return int
     */
    private function isValidReqBody(array $reqBody): int
    {
        if(
            isset($reqBody['departurePoint']) &&
            isset($reqBody['arrivalPoint']) &&
            isset($reqBody['toDepartureDay']) &&
            isset($reqBody['toDepartureMonth']) &&
            isset($reqBody['adults']) &&
            isset($reqBody['showMoreClicks'])
        ) {
            if(
                is_string($reqBody['departurePoint']) &&
                is_string($reqBody['arrivalPoint']) &&
                is_string($reqBody['toDepartureDay']) &&
                is_string($reqBody['toDepartureMonth']) &&
                is_integer($reqBody['adults']) &&
                is_integer($reqBody['showMoreClicks'])
            ) {
                if(
                    /**
                     * Возможные значения для $reservationClass:
                     * '' - эконом;
                     * 'w' - комфорт;
                     * 'c' - бизнес;
                     * 'f' - первый класс.
                     */
                    $reqBody['reservationClass'] == null ||
                    $reqBody['reservationClass'] == "w" ||
                    $reqBody['reservationClass'] == "c" ||
                    $reqBody['reservationClass'] == "f"
                ) {
                    if(
                        ($reqBody['adults'] >= $reqBody['infants']) &&
                        (($reqBody['adults'] + $reqBody['children'] + $reqBody['infants']) <= $this->maxPassengersNumber)
                    ) {
                        return 200;
                    }
                    return 504;
                }
                return 503;
            }
            return 502;
        }
        return 501;
    }

    public function isTrue(bool $ex): bool
    {
        return $ex;
    }
}

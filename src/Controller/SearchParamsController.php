<?php

namespace App\Controller;

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
            $response->setContent(json_encode(['id' => $this->setData($searchParams, $reqBody)->getId()]));
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
                $searchParams = $this->setData($searchParams, $reqBody);
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

    public function delete(int $id)
    {
        $response = new Response();
        $response->headers->set('Content-type', 'application/json');

        $repository = $this->getDoctrine()->getRepository(SearchParams::class);
        $searchParams = $repository->find($id);
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

    /**
     * Функция записи данных через ORM
     *
     * @param array $reqBody
     * @param string $addType
     * @param Response $response
     * @return Response
     */
    private function writeData(array $reqBody, string $addType, Response $response): Response
    {
        $searchParams = new SearchParams();
//        $searchParams->setDeparturePoint($reqBody['departurePoint']);
//        $searchParams->setArrivalPoint($reqBody['arrivalPoint']);
//        $searchParams->setToDepartureDay($reqBody['toDepartureDay']);
//        $searchParams->setToDepartureMonth($reqBody['toDepartureMonth']);
//        $searchParams->setFromDepartureDay($reqBody['fromDepartureDay']);
//        $searchParams->setFromDepartureMonth($reqBody['fromDepartureMonth']);
//        $searchParams->setReservationClass($reqBody['reservationClass']);
//        $searchParams->setAdults($reqBody['adults']);
//        $searchParams->setChildren($reqBody['children']);
//        $searchParams->setInfants($reqBody['infants']);
//        $searchParams->setShowMoreClicks($reqBody['showMoreClicks']);
//        $searchParams->setCreatedAt(DateTime::createFromFormat($this->datetimeFormat, date('Y-m-d H:i:s')));
//
//        $entityManager = $this->getDoctrine()->getManager();
//        $entityManager->persist($searchParams);
//        $entityManager->flush();
//
//        switch ($addType) {
//            case 'create':
//                $response->setContent(json_encode(['id' => $searchParams->getId()]));
//                break;
//            case 'update':
//                $response->setContent(json_encode([
//                    'id' => $searchParams->getId(),
//                    'departurePoint' => $searchParams->getDeparturePoint(),
//                    'arrivalPoint' => $searchParams->getArrivalPoint(),
//                    'toDepartureDay' => $searchParams->getToDepartureDay(),
//                    'toDepartureMonth' => $searchParams->getToDepartureMonth(),
//                    'fromDepartureDay' => $searchParams->getFromDepartureDay(),
//                    'fromDepartureMonth' => $searchParams->getFromDepartureMonth(),
//                    'reservationClass' => $searchParams->getReservationClass(),
//                    'adults' => $searchParams->getAdults(),
//                    'children' => $searchParams->getChildren(),
//                    'infants' => $searchParams->getInfants(),
//                    'showMoreClicks' => $searchParams->getShowMoreClicks(),
//                    'createdAt' => $searchParams->getCreatedAt(),
//                    'isChecked' => $searchParams->getIsChecked(),
//                ]));
//                break;
//        }

        return $response;
    }

    private function setData(SearchParams $searchParams, array $reqBody): SearchParams
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
        $searchParams->setCreatedAt(DateTime::createFromFormat($this->datetimeFormat, date('Y-m-d H:i:s')));

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
        // TODO: нужно сделать проверку на корректность ввода числа детей и инфантов
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
                    return 404;
                }
                return 403;
            }
            return 402;
        }
        return 401;
    }
}

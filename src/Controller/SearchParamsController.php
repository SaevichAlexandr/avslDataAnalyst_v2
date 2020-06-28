<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\SearchParams;
use DateTime;
use Exception;
use App\CustomException\ParameterTypeException;
use App\CustomException\ParameterFormatException;

class SearchParamsController extends AbstractController
{
    /**
     * Возможные значения для $reservationClass:
     * '' - эконом;
     * 'w' - комфорт;
     * 'c' - бизнес;
     * 'f' - первый класс.
     */
    private $_maxPassengersNumber;
    private $_datetimeFormat;
    private $_response;
    private $_repository;

    /**
     * SearchParamsController constructor.
     */
    public function __construct()
    {
         $this->_maxPassengersNumber = 9;
         $this->_datetimeFormat = 'Y-m-d H:i:s';
         $this->_response = new Response();

         $this->_response->headers->set('Content-type', 'application/json');
    }


    /**
     * Поиск записи search_params по её id
     *
     * @param int $id id записи
     *
     * @return Response информация о записи
     *
     * @throws Exception
     */
    public function getSearchParams(int $id): Response
    {
        $this->_repository = $this
            ->getDoctrine()
            ->getRepository(SearchParams::class);
        $searchParams = $this->_repository->find($id);
        if ($searchParams) {
            return $this->_setResponseContent($this->_response, $searchParams);
        } else {
            throw new Exception("search_params element with id:".$id." is missing");
        }
    }

    /**
     * Метод возвращает все записи из таблицы search_params
     *
     * @return Response
     *
     * @throws Exception
     */
    public function getAll(): Response
    {
        $this->_repository = $this
            ->getDoctrine()
            ->getRepository(SearchParams::class);
        $searchParamsList = $this->_repository->findAll();
        if ($searchParamsList) {
            $items = [];
            foreach ($searchParamsList as $searchParams) {
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
            return $this->_response->setContent(json_encode($items));
        } else {
            throw new Exception("There are no elements in table search_params");
        }
    }

    /**
     * Запись параметров поискового запроса для нахождения данных по рейсам
     *
     * @param Request $request объект Request
     *
     * @return Response
     *
     * @throws Exception
     */
    public function create(Request $request): Response
    {
        $reqBody = json_decode($request->getContent(), true);
        if ($this->isValidReqBody($reqBody)) {
            $searchParams = new SearchParams();
            return $this->_response->setContent(
                json_encode(
                    ['id' => $this->setData($searchParams, $reqBody, true)->getId()]
                )
            );
        } else {
            return $this->_response;
        }
    }

    /**
     * Изменение записи в таблице searchParams
     *
     * @param int     $id      id записи
     * @param Request $request объект Request
     *
     * @return Response
     *
     * @throws Exception
     */
    public function update(int $id, Request $request): Response
    {
        $reqBody = json_decode($request->getContent(), true);
        $repository = $this->getDoctrine()->getRepository(SearchParams::class);
        $searchParams = $repository->find($id);

        // проверка существования записи и её не использованности
        if ($searchParams && !$searchParams->getIsChecked()) {
            $isValidationCorrect = $this->isValidReqBody($reqBody);

            if ($isValidationCorrect) {
                $searchParams = $this->setData($searchParams, $reqBody, false);
                return $this->_setResponseContent($this->_response, $searchParams);
            }
            else {
//                $response->setContent(json_encode('Invalid data was sent. Validation error №'.$validationResponse));
//                return $response;
            }
        }
        else {
//            $response->setContent(json_encode('There is no element with such id or it`s already checked'));
//            return $response;
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
            $searchParams->setCreatedAt(DateTime::createFromFormat($this->_datetimeFormat, date('Y-m-d H:i:s')));
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($searchParams);
        $entityManager->flush();

        return $searchParams;
    }

    /**
     * Упаковка данных из записи в объект Response
     *
     * @param Response     $response     объект Response
     * @param SearchParams $searchParams объект SearchParams
     *
     * @return Response
     */
    private function _setResponseContent(
        Response $response,
        SearchParams $searchParams
    ): Response {
        $response->setContent(
            json_encode(
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
            )
        );
        return $response;
    }

    /**
     * Проверка корректности данных в передаваемом запросе
     *
     * @param array $reqBody параметры из запроса

     * @return bool
     *
     * @throws Exception
     */
    public function isValidReqBody(array $reqBody): bool
    {
        if (isset($reqBody['departurePoint'])) {
            if (is_string($reqBody['departurePoint'])) {
                if (!$this->_isValidWayPointFormat($reqBody['departurePoint'])) {
                    throw new ParameterFormatException(
                        'MOW',
                        $reqBody['departurePoint'],
                        'departurePoint'
                    );
                }
            } else {
                throw new ParameterTypeException(
                    $reqBody['departurePoint'],
                    'string',
                    'departurePoint'
                );
            }
        } else {
            throw new Exception('Parameter \'departurePoint\' is missing');
        }

        if (isset($reqBody['arrivalPoint'])) {
            if (is_string($reqBody['arrivalPoint'])) {
                if (!$this->_isValidWayPointFormat($reqBody['arrivalPoint'])) {
                    throw new ParameterFormatException(
                        'MOW',
                        $reqBody['arrivalPoint'],
                        'arrivalPoint'
                    );
                }
            } else {
                throw new ParameterTypeException(
                    $reqBody['arrivalPoint'],
                    'string',
                    'arrivalPoint'
                );
            }
        } else {
            throw new Exception('Parameter \'arrivalPoint\' is missing');
        }

        if (isset($reqBody['toDepartureDay'])) {
            if (is_string($reqBody['toDepartureDay'])) {
                if (!$this->_isValidDayFormat($reqBody['toDepartureDay'])) {
                    throw new ParameterFormatException(
                        '01 or 19',
                        $reqBody['toDepartureDay'],
                        'toDepartureDay'
                    );
                }
            } else {
                throw new ParameterTypeException(
                    $reqBody['toDepartureDay'],
                    'string',
                    'toDepartureDay'
                );
            }
        } else {
            throw new Exception('Parameter \'toDepartureDay\' is missing');
        }

        if (isset($reqBody['toDepartureMonth'])) {
            if (is_string($reqBody['toDepartureMonth'])) {
                if (!$this->_isValidMonthFormat($reqBody['toDepartureMonth'])) {
                    throw new ParameterFormatException(
                        '01 or 12',
                        $reqBody['toDepartureMonth'],
                        'toDepartureMonth'
                    );
                }
            } else {
                throw new ParameterTypeException(
                    $reqBody['toDepartureMonth'],
                    'string',
                    'toDepartureMonth'
                );
            }
        } else {
            throw new Exception('Parameter \'toDepartureMonth\' is missing');
        }

        if ((!isset($reqBody['fromDepartureDay'])
            && isset($reqBody['fromDepartureMonth']))
            || (isset($reqBody['fromDepartureDay'])
            && !isset($reqBody['fromDepartureMonth']))
        ) {
            throw new Exception(
                'fromDepartureDay or fromDepartureMonth value is missing'
            );
        } elseif (isset($reqBody['fromDepartureDay'])
            && isset($reqBody['fromDepartureMonth'])
        ) {
            //TODO: нужно добавить проверку того, чтобы дата обратного перелёта была позже
            // чем дата перелёта туда
        }

        if (isset($reqBody['fromDepartureDay'])
            && $reqBody['fromDepartureDay'] != null
        ) {
            if (is_string($reqBody['fromDepartureDay'])) {
                if (!$this->_isValidDayFormat($reqBody['fromDepartureDay'])) {
                    throw new ParameterFormatException(
                        '01 or 19',
                        $reqBody['fromDepartureDay'],
                        'fromDepartureDay'
                    );
                }
            } else {
                throw new ParameterTypeException(
                    $reqBody['fromDepartureDay'],
                    'string',
                    'fromDepartureDay'
                );
            }
        }

        if (isset($reqBody['fromDepartureMonth'])
            && $reqBody['fromDepartureMonth'] != null
        ) {
            if (is_string($reqBody['fromDepartureMonth'])) {
                if (!$this->_isValidMonthFormat($reqBody['fromDepartureMonth'])) {
                    throw new ParameterFormatException(
                        '01 or 12',
                        $reqBody['fromDepartureMonth'],
                        'fromDepartureMonth'
                    );
                }
            } else {
                throw new ParameterTypeException(
                    $reqBody['fromDepartureMonth'],
                    'string',
                    'fromDepartureMonth'
                );
            }
        }

        if (isset($reqBody['adults'])
        ) {
            if (!is_integer($reqBody['adults'])) {
                throw new ParameterTypeException(
                    $reqBody['adults'],
                    'int',
                    'adults'
                );
            }
        } else {
            throw new Exception('Parameter \'adults\' is missing');
        }

        if (isset($reqBody['children'])
            && $reqBody['children'] !== null
        ) {
            if (!is_integer($reqBody['children'])) {
                throw new ParameterTypeException(
                    $reqBody['children'],
                    'int',
                    'children'
                );
            }
        }

        if (isset($reqBody['infants'])
            && $reqBody['infants'] !== null
        ) {
            if (!is_integer($reqBody['infants'])) {
                throw new ParameterTypeException(
                    $reqBody['infants'],
                    'int',
                    'infants'
                );
            }
        }

        if (!isset($reqBody['showMoreClicks'])
            || !is_integer($reqBody['showMoreClicks'])
        ) {
            throw new Exception(
                'Wrong type of parameter \'showMoreClicks\'
                (must be int) or parameter does not exist'
            );
        }
        if ($reqBody['reservationClass'] != null
            && $reqBody['reservationClass'] != "w"
            && $reqBody['reservationClass'] != "c"
            && $reqBody['reservationClass'] != "f"
        ) {
            throw new Exception(
                'Wrong reservation class. It can be \'null\' - economy,'.
                ' \'w\' - comfort, \'c\' - business, \'f\' - first class.'
            );
        }

        if (($reqBody['adults'] <= $reqBody['infants'])
            || (($reqBody['adults'] + $reqBody['children'] + $reqBody['infants'])
                >= $this->_maxPassengersNumber)
        ) {
            throw new Exception('Wrong number of passengers');
        }
        return true;
    }

    /**
     * Проверка на корректность формата параметра departurePoint
     *
     * @param string $departurePoint строка с IATA-кодом пункта отправления
     *
     * @return bool
     */
    private function _isValidWayPointFormat(string $departurePoint): bool
    {
        if (preg_match('/\A[A-Z][A-Z][A-Z]\z/', $departurePoint)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Проверка корректности формата указанного числа месяца
     *
     * @param string $day строка с номером дня
     *
     * @return bool
     */
    private function _isValidDayFormat(string $day): bool
    {
        if (preg_match('/\A[0][1-9]\z/', $day)) {
            return true;
        } elseif (preg_match('/\A[1-2][0-9]\z/', $day)) {
            return true;
        } elseif (preg_match('/\A[3][0-1]\z/', $day)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Провера корректности формата месяца
     *
     * @param string $month строка с номером месяца
     *
     * @return bool
     */
    private function _isValidMonthFormat(string $month): bool
    {
        if (preg_match('/\A[0][1-9]\z/', $month)) {
            return true;
        } elseif (preg_match('/\A[1][0-2]\z/', $month)) {
            return true;
        } else {
            return false;
        }
    }
}

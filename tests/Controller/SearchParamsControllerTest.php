<?php

namespace App\Tests\Controller;

use App\Controller\SearchParamsController;
use App\CustomException\ParameterFormatException;
use PHPUnit\Framework\TestCase;
use App\Entity\SearchParams;
use Exception;

class SearchParamsControllerTest extends TestCase
{

    private $_searchParamsController;
    private $_reqBody;

    /**
     * Преднастройка теста
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->_searchParamsController = new SearchParamsController();
        $this->_reqBody = [
            "departurePoint" => "MOW",
            "arrivalPoint" => "LED",
            "toDepartureDay" => "01",
            "toDepartureMonth" => "06",
            "fromDepartureDay" => null,
            "fromDepartureMonth" => null,
            "reservationClass" => null,
            "adults" => 1,
            "children" => null,
            "infants" => null,
            "showMoreClicks" => 4
        ];
    }

    /**
     * @throws Exception
     */
    public function testIsValidReqBodyCorrect()
    {
        $reqBody = $this->_reqBody;
        // проверка корректного запроса
        $this->assertEquals(
            true,
            $this->_searchParamsController->isValidReqBody($reqBody)
        );
    }

    public function testIsValidReqBodyNotIssetDeparturePoint()
    {
        $reqBody = $this->_reqBody;
        unset($reqBody['departurePoint']);

        $this->expectExceptionMessage('Parameter \'departurePoint\' is missing');

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyNotStringDeparturePoint()
    {
        $reqBody = $this->_reqBody;
        $reqBody['departurePoint'] = 1;

        $this->expectExceptionMessage(
            'Parameter \'departurePoint\' with value \'1\' should be a \'string\' '.
            'type, \'integer\' is given.'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyWrongFormatDeparturePoint()
    {
        $reqBody = $this->_reqBody;
        $reqBody['departurePoint'] = 'sdasd';

        $this->expectExceptionMessage(
            'Parameter \'departurePoint\' should have format'.
            ' like \'MOW\', \'sdasd\' is given.'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyNotIssetArrivalPoint()
    {
        $reqBody = $this->_reqBody;
        unset($reqBody['arrivalPoint']);

        $this->expectExceptionMessage('Parameter \'arrivalPoint\' is missing');

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyNotStringArrivalPoint()
    {
        $reqBody = $this->_reqBody;
        $reqBody['arrivalPoint'] = 1;

        $this->expectExceptionMessage(
            'Parameter \'arrivalPoint\' with value \'1\' should be a \'string\' '.
            'type, \'integer\' is given.'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyWrongFormatArrivalPoint()
    {
        $reqBody = $this->_reqBody;
        $reqBody['arrivalPoint'] = 'sdasd';

        $this->expectExceptionMessage(
            'Parameter \'arrivalPoint\' should have format'.
            ' like \'MOW\', \'sdasd\' is given.'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyNotIssetToDepartureDay()
    {
        $reqBody = $this->_reqBody;
        unset($reqBody['toDepartureDay']);

        $this->expectExceptionMessage('Parameter \'toDepartureDay\' is missing');

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyNotStringToDepartureDay()
    {
        $reqBody = $this->_reqBody;
        $reqBody['toDepartureDay'] = 1;

        $this->expectExceptionMessage(
            'Parameter \'toDepartureDay\' with value \'1\' should be a \'string\' '.
            'type, \'integer\' is given.'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyWrongFormatToDepartureDay()
    {
        $reqBody = $this->_reqBody;
        $reqBody['toDepartureDay'] = '00';

        $this->expectExceptionMessage(
            'Parameter \'toDepartureDay\' should have format'.
            ' like \'01 or 19\', \'00\' is given.'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyNotIssetToDepartureMonth()
    {
        $reqBody = $this->_reqBody;
        unset($reqBody['toDepartureMonth']);

        $this->expectExceptionMessage('Parameter \'toDepartureMonth\' is missing');

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyNotStringToDepartureMonth()
    {
        $reqBody = $this->_reqBody;
        $reqBody['toDepartureMonth'] = 1;

        $this->expectExceptionMessage(
            'Parameter \'toDepartureMonth\' with value \'1\' should be a '.
            '\'string\' type, \'integer\' is given.'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyWrongFormatToDepartureMonth()
    {
        $reqBody = $this->_reqBody;
        $reqBody['toDepartureMonth'] = '00';

        $this->expectExceptionMessage(
            'Parameter \'toDepartureMonth\' should have format'.
            ' like \'01 or 12\', \'00\' is given.'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyNotIssetFromDepartureDay()
    {
        $reqBody = $this->_reqBody;
        unset($reqBody['fromDepartureDay']);
        $reqBody['fromDepartureMonth'] = '01';
        $this->expectExceptionMessage(
            'fromDepartureDay or fromDepartureMonth value is missing'
        );

        var_dump($this->_searchParamsController->isValidReqBody($reqBody));
    }

    public function testIsValidReqBodyNotIssetFromDepartureMonth()
    {
        $reqBody = $this->_reqBody;
        unset($reqBody['fromDepartureMonth']);
        $reqBody['fromDepartureDay'] = '01';

        $this->expectExceptionMessage(
            'fromDepartureDay or fromDepartureMonth value is missing'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyNotStringFromDepartureDay()
    {
        $reqBody = $this->_reqBody;
        $reqBody['fromDepartureMonth'] = '02';
        $reqBody['fromDepartureDay'] = 1;

        $this->expectExceptionMessage(
            'Parameter \'fromDepartureDay\' with value \'1\' '.
            'should be a \'string\' type, \'integer\' is given.'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyWrongFormatFromDepartureDay()
    {
        $reqBody = $this->_reqBody;
        $reqBody['fromDepartureMonth'] = '02';
        $reqBody['fromDepartureDay'] = '00';

        $this->expectExceptionMessage(
            'Parameter \'fromDepartureDay\' should have format'.
            ' like \'01 or 19\', \'00\' is given.'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyNotStringFromDepartureMonth()
    {
        $reqBody = $this->_reqBody;
        $reqBody['fromDepartureDay'] = '25';
        $reqBody['fromDepartureMonth'] = 1;

        $this->expectExceptionMessage(
            'Parameter \'fromDepartureMonth\' with value \'1\' should be a '.
            '\'string\' type, \'integer\' is given.'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyWrongFormatFromDepartureMonth()
    {
        $reqBody = $this->_reqBody;
        $reqBody['fromDepartureDay'] = '25';
        $reqBody['fromDepartureMonth'] = '00';

        $this->expectExceptionMessage(
            'Parameter \'fromDepartureMonth\' should have format'.
            ' like \'01 or 12\', \'00\' is given.'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyNotIssetAdults()
    {
        $reqBody = $this->_reqBody;
        unset($reqBody['adults']);

        $this->expectExceptionMessage(
            'Parameter \'adults\' is missing'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyNotIntAdults()
    {
        $reqBody = $this->_reqBody;
        $reqBody['adults'] = 's';

        $this->expectExceptionMessage(
            'Parameter \'adults\' with value \'s\' should be a'.
            ' \'int\' type, \'string\' is given.'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyNotIntChildren()
    {
        $reqBody = $this->_reqBody;
        $reqBody['children'] = 's';

        $this->expectExceptionMessage(
            'Parameter \'children\' with value \'s\' should be a '.
            '\'int\' type, \'string\' is given.'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }
    public function testIsValidReqBodyNotIntInfants()
    {
        $reqBody = $this->_reqBody;
        $reqBody['infants'] = 's';

        $this->expectExceptionMessage(
            'Parameter \'infants\' with value \'s\' should be a '.
            '\'int\' type, \'string\' is given.'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyWrongReservationClass()
    {
        $reqBody = $this->_reqBody;
        $reqBody['reservationClass'] = 'd';

        $this->expectExceptionMessage(
            'Wrong reservation class. It can be \'null\' - economy,'.
            ' \'w\' - comfort, \'c\' - business, \'f\' - first class.'
        );

        $this->_searchParamsController->isValidReqBody($reqBody);
    }

    public function testIsValidReqBodyWrongNumberOfPassengersMoreInfants()
    {
        $reqBody = $this->_reqBody;
        $reqBody['adults'] = 1;
        $reqBody['children'] = 1;
        $reqBody['infants'] = 2;

        $this->expectExceptionMessage(
            'Wrong number of passengers'
        );

        var_dump($this->_searchParamsController->isValidReqBody($reqBody));
    }

    public function testIsValidReqBodyWrongNumberOfPassengersOverLimit()
    {
        $reqBody = $this->_reqBody;
        $reqBody['adults'] = 5;
        $reqBody['children'] = 3;
        $reqBody['infants'] = 2;

        $this->expectExceptionMessage(
            'Wrong number of passengers'
        );

        var_dump($this->_searchParamsController->isValidReqBody($reqBody));
    }
}

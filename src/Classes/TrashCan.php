<?php


namespace App\Classes;


class TrashCan
{
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
        if (!isset($reqBody['departurePoint'])
            || !is_string($reqBody['departurePoint'])
        ) {
            //TODO: в идеале тут можно использовать свой класс ошибки
            // в который бы передавались тип и имя поля в качестве параметров
            throw new Exception(
                'Wrong type of parameter \'departurePoint\'
                (must be string) or parameter does not exist'
            );
        }
        if (!isset($reqBody['arrivalPoint'])
            || !is_string($reqBody['arrivalPoint'])
        ) {
            throw new Exception(
                'Wrong type of parameter \'arrivalPoint\'
                (must be string) or parameter does not exist'
            );
        }
        if (!isset($reqBody['toDepartureDay'])
            || !is_string($reqBody['toDepartureDay'])
        ) {
            throw new Exception(
                'Wrong type of parameter \'toDepartureDay\'
                (must be string) or parameter does not exist'
            );
        }
        if (!isset($reqBody['toDepartureMonth'])
            || !is_string($reqBody['toDepartureMonth'])
        ) {
            throw new Exception(
                'Wrong type of parameter \'toDepartureMonth\'
                (must be string) or parameter does not exist'
            );
        }
        if (!isset($reqBody['fromDepartureDay'])
            && !isset($reqBody['fromDepartureMonth'])
        ) {
            throw new Exception(
                'fromDepartureDay or fromDepartureMonth value is missing'
            );
        }
        if (!is_string($reqBody['fromDepartureDay'])) {
            throw new Exception(
                'Wrong type of parameter \'fromDepartureDay\'
                (must be string)'
            );
        }
        if (!is_string($reqBody['fromDepartureMonth'])) {
            throw new Exception(
                'Wrong type of parameter \'fromDepartureMonth\'
                (must be string)'
            );
        }
        if (!isset($reqBody['adults'])
            || !is_integer($reqBody['adults'])
        ) {
            throw new Exception(
                'Wrong type of parameter \'adults\'
                (must be int) or parameter does not exist'
            );
        }
        if (!is_integer($reqBody['children'])

        ) {
            throw new Exception(
                'Wrong type of parameter \'children\'
                (must be int)'
            );
        }
        if (!is_integer($reqBody['infants'])
        ) {
            throw new Exception(
                'Wrong type of parameter \'infants\'
                (must be int)'
            );
        }
        if (!isset($reqBody['showMoreClicks'])
            || !is_integer($reqBody['showMoreClicks'])
        ) {
            throw new Exception(
                'Wrong type of parameter \'showMoreClicks\'
                (must be int) or parameter does not exist'
            );
        }
        if ($reqBody['reservationClass'] == null
            || $reqBody['reservationClass'] == "w"
            || $reqBody['reservationClass'] == "c"
            || $reqBody['reservationClass'] == "f"
        ) {
            throw new Exception('Wrong reservation class');
        }
        if (($reqBody['adults'] <= $reqBody['infants'])
            && (($reqBody['adults']
                    + $reqBody['children']
                    + $reqBody['infants'])
                >= $this->_maxPassengersNumber)
        ) {
            throw new Exception('Wrong number of passengers');
        }
        return true;
    }
}
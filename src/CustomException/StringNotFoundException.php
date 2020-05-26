<?php


namespace App\CustomException;

use Exception;
use Throwable;

class StringNotFoundException extends Exception
{
    private $string;

    /**
     * StringNotFoundException constructor.
     * @param string $string
     */
    public function __construct(string $string)
    {
        parent::__construct('Не удалось найти строку: '.$string);
        $this->string = $string;
    }

    public function getString()
    {
        return $this->string;
    }
}
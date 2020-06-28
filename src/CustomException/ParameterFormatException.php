<?php


namespace App\CustomException;

use Exception;
use phpDocumentor\Reflection\Types\Mixed_;
use Throwable;

class ParameterFormatException extends Exception
{
    private $_correctParamValueExample;
    private $_variableName;
    private $_wrongVariable;

    /**
     * ParameterFormatException constructor.
     *
     * @param mixed  $correctParamValueExample пример корректного значения переменной
     * @param mixed  $wrongVariable            некорректное значение переменной
     * @param string $variableName             название переменной
     */
    public function __construct(
        $correctParamValueExample,
        $wrongVariable,
        string $variableName
    ) {
        $this->_correctParamValueExample = $correctParamValueExample;
        $this->_variableName = $variableName;

        parent::__construct(
            'Parameter \''.$this->_variableName.'\' should have'.
            ' format like \''.$this->_correctParamValueExample.'\', \''.
            $wrongVariable.'\' is given.'
        );
    }
}
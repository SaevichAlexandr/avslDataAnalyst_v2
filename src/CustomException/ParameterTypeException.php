<?php


namespace App\CustomException;

use Exception;
use phpDocumentor\Reflection\Types\Mixed_;
use Throwable;

class ParameterTypeException extends Exception
{
    private $_wrongVariable;
    private $_correctVariableType;
    private $_variableName;

    /**
     * ParameterTypeException constructor.
     *
     * @param mixed  $wrongVariable       переменная с некоррекнтым типом
     * @param string $correctVariableType тип необходимый для данной переменной
     * @param string $variableName        имя переменной
     */
    public function __construct(
        $wrongVariable,
        string $correctVariableType,
        string $variableName
    ) {
        $this->_wrongVariable = $wrongVariable;
        $this->_correctVariableType = $correctVariableType;
        $this->_variableName = $variableName;

        $wrongVariableType = gettype($this->_wrongVariable);
        parent::__construct(
            'Parameter \''.$this->_variableName.'\' with value \''.
            $this->_wrongVariable.'\' should be a \''.$this->_correctVariableType.
            '\' type, \''.$wrongVariableType.'\' is given.'
        );

    }
}
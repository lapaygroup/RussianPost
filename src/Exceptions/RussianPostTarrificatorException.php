<?php
namespace LapayGroup\RussianPost\Exceptions;

class RussianPostTarrificatorException extends \Exception
{
    /** @var array  */
    private $errors = [];

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    public function __construct($message = "", $code = 0, $errors = [])
    {
        $this->setErrors($errors);
        parent::__construct($message, $code);
    }
}
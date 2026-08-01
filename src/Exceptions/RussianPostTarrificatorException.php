<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Exceptions;

/**
 * @deprecated Используйте RussianPostTarifficatorException.
 */
class RussianPostTarrificatorException extends \Exception
{
    private array $errors = [];

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    public function __construct(string $message = '', int $code = 0, array $errors = [], ?\Throwable $previous = null)
    {
        $this->setErrors($errors);
        parent::__construct($message, $code, $previous);
    }
}

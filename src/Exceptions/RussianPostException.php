<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Exceptions;

class RussianPostException extends \Exception
{
    private ?string $rawResponse;
    private array $response = [];
    private ?string $rawRequest;

    public function __construct(
        string $message = '',
        int $code = 0,
        ?string $rawResponse = null,
        ?string $rawRequest = null,
        ?\Throwable $previous = null
    )
    {
        $this->rawRequest = $rawRequest;
        $this->rawResponse = $rawResponse;

        if ($rawResponse !== null && $rawResponse !== '') {
            try {
                $response = json_decode($rawResponse, true, 512, JSON_THROW_ON_ERROR);
                if (is_array($response)) {
                    $this->response = $response;
                }
            } catch (\JsonException) {
                // Сырой ответ доступен через getRawResponse().
            }
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getRawResponse(): ?string
    {
        return $this->rawResponse;
    }

    /**
     * @param string|null $rawResponse
     */
    public function setRawResponse(?string $rawResponse): void
    {
        $this->rawResponse = $rawResponse;
    }

    /**
     * @return string
     */
    public function getRawRequest(): ?string
    {
        return $this->rawRequest;
    }

    /**
     * @param string|null $rawRequest
     */
    public function setRawRequest(?string $rawRequest): void
    {
        $this->rawRequest = $rawRequest;
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return isset($this->response['code']) ? (string) $this->response['code'] : '';
    }

    /**
     * @return string
     */
    public function getErrorDescription(): string
    {
        return isset($this->response['desc']) ? (string) $this->response['desc'] : '';
    }

    /**
     * @return string
     */
    public function getErrorSubCode(): string
    {
        return isset($this->response['sub-code']) ? (string) $this->response['sub-code'] : '';
    }
}

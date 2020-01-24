<?php
namespace LapayGroup\RussianPost\Exceptions;

class RussianPostException extends \Exception
{
    /**
     * @var string
     */
    private $raw_response = null;

    /**
     * @var array
     */
    private $response = [];

    /**
     * @var string
     */
    private $raw_request = null;

    public function __construct($message = "", $code = 0, $raw_response = null, $raw_request = null, $previous = null)
    {
        $this->raw_request = $raw_request;
        $this->raw_response = $raw_response;

        $response = json_decode($this->getRawResponse(), true);
        if ($response !== null && json_last_error() === JSON_ERROR_NONE)
        {
            $this->response = $response;
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getRawResponse()
    {
        return $this->raw_response;
    }

    /**
     * @param string $raw_response
     */
    public function setRawResponse($raw_response)
    {
        $this->raw_response = $raw_response;
    }

    /**
     * @return string
     */
    public function getRawRequest()
    {
        return $this->raw_request;
    }

    /**
     * @param string $raw_request
     */
    public function setRawRequest($raw_request)
    {
        $this->raw_request = $raw_request;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        if (array_key_exists('code', $this->response))
            return $this->response['code'];

        return '';
    }

    /**
     * @return string
     */
    public function getErrorDescription()
    {
        if (array_key_exists('desc', $this->response))
            return $this->response['desc'];

        return '';
    }

    /**
     * @return string
     */
    public function getErrorSubCode()
    {
        if (array_key_exists('sub-code', $this->response))
            return $this->response['sub-code'];

        return '';
    }
}

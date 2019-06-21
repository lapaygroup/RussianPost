<?php
namespace LapayGroup\RussianPost\Exceptions;

class RussianPostException extends \Exception
{
    /**
     * @var string
     */
    private $raw_response = null;

    /**
     * @var string
     */
    private $raw_request  = null;

    public function __construct($message = "", $code = 0, $raw_response = null, $raw_request = null, $previous = null)
    {
        $this->raw_request = $raw_request;
        $this->raw_response = $raw_response;
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
}
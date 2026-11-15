<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Http\Exception;

use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;

final class RequestException extends ClientException implements RequestExceptionInterface
{
    private RequestInterface $request;

    public function __construct(RequestInterface $request)
    {
        parent::__construct('HTTP request could not be sent.');
        $this->request = $request;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}

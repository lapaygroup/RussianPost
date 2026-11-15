<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Http\Exception;

use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;

final class NetworkException extends ClientException implements NetworkExceptionInterface
{
    private RequestInterface $request;

    public function __construct(RequestInterface $request)
    {
        parent::__construct('HTTP network request failed.');
        $this->request = $request;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}

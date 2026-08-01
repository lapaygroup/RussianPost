<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Tests\Support;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RecordingClient implements ClientInterface
{
    /** @var ResponseInterface[] */
    private $responses;

    /** @var RequestInterface[] */
    private $requests = [];

    public function __construct(array $responses)
    {
        $this->responses = $responses;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->requests[] = $request;

        if (empty($this->responses)) {
            throw new \RuntimeException('Для тестового PSR-18 клиента не задан следующий ответ');
        }

        return array_shift($this->responses);
    }

    /**
     * @return RequestInterface[]
     */
    public function getRequests()
    {
        return $this->requests;
    }
}

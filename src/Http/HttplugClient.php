<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Http;

use Http\Client\Exception as HttplugException;
use Http\Client\Exception\HttpException;
use Http\Client\Exception\NetworkException as HttplugNetworkException;
use Http\Client\Exception\RequestException as HttplugRequestException;
use Http\Client\HttpClient;
use LapayGroup\RussianPost\Http\Exception\ClientException;
use LapayGroup\RussianPost\Http\Exception\NetworkException;
use LapayGroup\RussianPost\Http\Exception\RequestException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/** Bridges an existing HTTPlug 1/2 client without upgrading its consumers. */
final class HttplugClient implements ClientInterface
{
    private HttpClient $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->client->sendRequest($request);
        } catch (HttpException $error) {
            // PSR-18 returns every well-formed HTTP response, including 4xx/5xx.
            return $error->getResponse();
        } catch (HttplugNetworkException $error) {
            throw new NetworkException($error->getRequest());
        } catch (HttplugRequestException $error) {
            throw new RequestException($error->getRequest());
        } catch (HttplugException $error) {
            // Vendor messages/previous exceptions may contain credentials or payloads.
            throw new ClientException('HTTP client failed.');
        }
    }
}

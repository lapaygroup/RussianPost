<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Client\Exception\RequestException;
use Http\Client\Exception\TransferException;
use Http\Client\HttpClient;
use Http\Factory\Guzzle\RequestFactory;
use Http\Factory\Guzzle\StreamFactory;
use Http\Factory\Guzzle\UploadedFileFactory;
use LapayGroup\RussianPost\Exceptions\TrackingApiException;
use LapayGroup\RussianPost\Http\HttplugClient;
use LapayGroup\RussianPost\Http\Psr18Transport;
use LapayGroup\RussianPost\Providers\TrackingApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;

final class HttplugClientTest extends TestCase
{
    public function testHttpErrorsAndRedirectAreResponsesWithoutRetriesOrRedirects(): void
    {
        $history = [];
        $statuses = [301, 401, 403, 429, 500, 503];
        $stack = HandlerStack::create(new MockHandler(array_map(static function ($status) {
            return new Response($status, ['Location' => 'https://unrelated.invalid/'], 'synthetic');
        }, $statuses)));
        $stack->push(Middleware::history($history));
        // Deliberately leave http_errors enabled: HTTPlug 1 throws HttpException.
        $client = new HttplugClient(new GuzzleAdapter(new Client(['handler' => $stack, 'allow_redirects' => false])));
        $request = new Request('POST', 'https://postapi.pochta.ru/fc/', ['Authorization' => 'ApiKey synthetic'], '<synthetic/>');
        foreach ($statuses as $status) {
            self::assertSame($status, $client->sendRequest($request)->getStatusCode());
        }
        self::assertCount(count($statuses), $history);
        foreach ($history as $exchange) {
            self::assertSame('postapi.pochta.ru', $exchange['request']->getUri()->getHost());
            self::assertSame('ApiKey synthetic', $exchange['request']->getHeaderLine('Authorization'));
        }
    }

    public function testNetworkExceptionBecomesPsr18AndSdkSanitizesIt(): void
    {
        $request = new Request('GET', 'https://postapi.pochta.ru/');
        $http = new Client(['handler' => HandlerStack::create(new MockHandler([
            new ConnectException('secret-token transport details', $request),
            new ConnectException('secret-token transport details', $request),
        ]))]);
        $client = new HttplugClient(new GuzzleAdapter($http));
        try {
            $client->sendRequest($request);
            self::fail('Expected a PSR-18 network exception.');
        } catch (NetworkExceptionInterface $error) {
            self::assertSame($request, $error->getRequest());
            self::assertNull($error->getPrevious());
            self::assertStringNotContainsString('secret-token', $error->getMessage());
        }
        $api = new TrackingApi(['auth' => ['tracking' => ['api_key' => 'synthetic']]], new Psr18Transport(
            $client, new RequestFactory(), new StreamFactory(), new UploadedFileFactory()
        ));
        try {
            $api->getTrackingInfo('00000000000001');
            self::fail('Expected a sanitized SDK error.');
        } catch (TrackingApiException $error) {
            self::assertSame('transport', $error->getCategory());
            self::assertNull($error->getPrevious());
            self::assertStringNotContainsString('secret-token', $error->getMessage());
        }
    }

    public function testRequestAndGenericHttplugErrorsArePsr18WithoutVendorMessages(): void
    {
        $request = new Request('GET', 'https://postapi.pochta.ru/');
        foreach ([new RequestException('secret-token', $request), new TransferException('secret-token')] as $failure) {
            $inner = $this->createMock(HttpClient::class);
            $inner->expects(self::once())->method('sendRequest')->with($request)->willThrowException($failure);
            try {
                (new HttplugClient($inner))->sendRequest($request);
                self::fail('Expected a PSR-18 exception.');
            } catch (ClientExceptionInterface $error) {
                self::assertNull($error->getPrevious());
                self::assertStringNotContainsString('secret-token', $error->getMessage());
                if ($failure instanceof RequestException) {
                    self::assertInstanceOf(RequestExceptionInterface::class, $error);
                    self::assertSame($request, $error->getRequest());
                }
            }
        }
    }

    public function testBatchSoapFaultSurvivesGuzzle6Http500Exception(): void
    {
        $fault = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><soap:Fault><faultcode>soap:Client</faultcode><faultstring>secret-token</faultstring></soap:Fault></soap:Body></soap:Envelope>';
        $client = new HttplugClient(new GuzzleAdapter(new Client(['handler' => HandlerStack::create(new MockHandler([
            new Response(500, ['Content-Type' => 'text/xml'], $fault),
        ])), 'allow_redirects' => false])));
        $api = new TrackingApi(['auth' => ['tracking' => ['api_key' => 'synthetic']], 'tracking' => ['batch_enabled' => true]], new Psr18Transport(
            $client, new RequestFactory(), new StreamFactory(), new UploadedFileFactory()
        ));
        try {
            $api->createTicket(['00000000000001']);
            self::fail('Expected a SOAP Fault.');
        } catch (TrackingApiException $error) {
            self::assertSame('soap_fault', $error->getCategory());
            self::assertSame(500, $error->getHttpStatus());
            self::assertFalse($error->isTransient());
            self::assertNull($error->getPrevious());
            self::assertStringNotContainsString('secret-token', $error->getMessage());
        }
    }
}

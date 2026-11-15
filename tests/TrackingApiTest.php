<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Tests;

use LapayGroup\RussianPost\Exceptions\TicketSubmissionException;
use LapayGroup\RussianPost\Exceptions\TrackingApiException;
use LapayGroup\RussianPost\Http\Psr18Transport;
use LapayGroup\RussianPost\Providers\TrackingApi;
use LapayGroup\RussianPost\Tests\Support\RecordingClient;
use LapayGroup\RussianPost\Tests\Support\RecordingLogger;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class TrackingApiTest extends TestCase
{
    private const TOKEN = 'synthetic-tracking-token';

    private function client(array $responses, array $config = []): array
    {
        $client = new RecordingClient($responses);
        return [$this->api($client, $config), $client];
    }
    private function api(ClientInterface $client, array $config = []): TrackingApi
    {
        $factory = new Psr17Factory();
        $config = array_replace_recursive([
            'auth' => ['tracking' => ['api_key' => self::TOKEN, 'login' => 'unused-login', 'password' => 'unused-password']],
            'tracking' => ['batch_enabled' => true],
        ], $config);
        return new TrackingApi($config, new Psr18Transport($client, $factory, $factory, $factory), static function (): int { return 1000; });
    }
    private function fixture(string $name): string
    {
        return file_get_contents(__DIR__ . '/fixtures/tracking/' . $name);
    }
    private function xmlResponse(string $root, string $body): Response
    {
        return new Response(200, [], '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" xmlns:p="http://fclient.russianpost.org/postserver"><s:Body><p:' . $root . '>' . $body . '</p:' . $root . '></s:Body></s:Envelope>');
    }

    public function testSinglePreservesActualEventsNullsOffsetsUnknownCodesAndReturnDirection(): void
    {
        [$api, $client] = $this->client([new Response(200, [], $this->fixture('single.json'))]);
        $logger = new RecordingLogger();
        $api->setLogger($logger);
        $result = $api->getTrackingInfo('00000000000001', 'EN');
        $operations = $result->getOperations();
        self::assertCount(2, $operations);
        self::assertSame('2026-11-01T10:15:00+03:00', $operations[0]['operation']['date']);
        self::assertNull($operations[0]['operation']['humanOperationStatus']);
        self::assertSame(999, $operations[1]['operation']['operationType']);
        self::assertSame(888, $operations[1]['operation']['operationAttr']);
        self::assertSame('TO_SENDER', $operations[1]['group']['direction']);
        self::assertArrayNotHasKey('isFinal', $operations[0]['operation']);
        self::assertArrayNotHasKey('paid', $result->getData());
        self::assertSame(1000, $result->getData()['cashOnDelivery']);
        self::assertCount(4, $result->getData()['trackingItemOperationGroupStatuses']);
        $request = $client->getRequests()[0];
        self::assertSame('GET', $request->getMethod());
        self::assertSame('https://postapi.pochta.ru/tracking/api/external/v1/info?language=EN&barcodeList=00000000000001', (string) $request->getUri());
        self::assertSame('ApiKey ' . self::TOKEN, $request->getHeaderLine('Authorization'));
        self::assertFalse($request->hasHeader('Language'));
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertSame('', (string) $request->getBody());
        $logs = json_encode($logger->records);
        foreach ([self::TOKEN, '00000000000001', 'unused-login', 'unused-password', 'cashOnDelivery'] as $secret) self::assertStringNotContainsString($secret, $logs);
    }

    public function testEmptyHistoryIsNotAnError(): void
    {
        [$api] = $this->client([new Response(200, [], '{"trackingItemOperationGroupStatuses":[]}')]);
        self::assertSame([], $api->getTrackingInfo('AA000000000RU')->getOperations());
    }

    public function testExplicitNullHistoryIsEmptyAndRawPayloadRemainsUnchanged(): void
    {
        [$api] = $this->client([new Response(200, [], $this->fixture('single-empty.json'))]);
        $result = $api->getTrackingInfo('00000000000000');
        self::assertSame([], $result->getOperations());
        self::assertArrayHasKey('trackingItemOperationGroupStatuses', $result->getData());
        self::assertNull($result->getData()['trackingItemOperationGroupStatuses']);
        self::assertSame('00000000000000', $result->getData()['barcode']);
    }

    public function testMalformedOrUnexpectedSinglePayloadsAreRejected(): void
    {
        foreach (['{broken', 'null', '[]', '{}', '{"barcode":"00000000000001"}', '{"barcode":"wrong","trackingItemOperationGroupStatuses":[]}', '{"barcode":"wrong","trackingItemOperationGroupStatuses":null}', '{"trackingItemOperationGroupStatuses":false}', '{"trackingItemOperationGroupStatuses":0}', '{"trackingItemOperationGroupStatuses":""}', '{"trackingItemOperationGroupStatuses":{"unexpected":true}}', '{"trackingItemOperationGroupStatuses":[{"future":null}]}', '{"trackingItemOperationGroupStatuses":[{"future":false,"originTrackingHistoryItems":[{}]}]}'] as $body) {
            [$api] = $this->client([new Response(200, [], $body)]);
            try { $api->getTrackingInfo('00000000000001'); self::fail('Expected invalid response'); }
            catch (TrackingApiException $error) { self::assertSame(TrackingApiException::INVALID_RESPONSE, $error->getCategory()); }
        }
    }

    public function testHttpFailuresAreClassifiedWithoutLeakingResponseBodiesOrRetrying(): void
    {
        foreach ([401 => 'auth', 403 => 'auth', 429 => 'rate_limit', 500 => 'server', 503 => 'server', 400 => 'http', 302 => 'http'] as $status => $category) {
            [$api, $client] = $this->client([new Response($status, ['Retry-After' => '900'], self::TOKEN)]);
            try { $api->getTrackingInfo('00000000000001'); self::fail('Expected HTTP error'); }
            catch (TrackingApiException $error) {
                self::assertSame($category, $error->getCategory());
                self::assertSame($status, $error->getHttpStatus());
                self::assertSame('900', $error->getRetryAfter());
                self::assertStringNotContainsString(self::TOKEN, (string) $error);
                self::assertNull($error->getPrevious());
            }
            self::assertCount(1, $client->getRequests());
        }
    }

    public function testArbitraryRetryAfterTextIsNotExposed(): void
    {
        [$api] = $this->client([new Response(429, ['Retry-After' => self::TOKEN])]);
        try { $api->getTrackingInfo('00000000000001'); self::fail('Expected limit error'); }
        catch (TrackingApiException $error) { self::assertNull($error->getRetryAfter()); }
    }

    public function testTransportErrorIsSafeForSingleAndBatch(): void
    {
        $client = new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                throw new class($request) extends \RuntimeException implements NetworkExceptionInterface {
                    private RequestInterface $request;
                    public function __construct(RequestInterface $request) { parent::__construct($request->getHeaderLine('Authorization')); $this->request = $request; }
                    public function getRequest(): RequestInterface { return $this->request; }
                };
            }
        };
        $api = $this->api($client);
        foreach (['single', 'batch'] as $mode) {
            try {
                if ($mode === 'single') $api->getTrackingInfo('00000000000001');
                else $api->getResponseByTicket('synthetic-ticket');
                self::fail('Expected transport error');
            } catch (TrackingApiException $error) {
                self::assertSame('transport', $error->getCategory());
                self::assertTrue($error->isTransient());
                self::assertNull($error->getPrevious());
                self::assertStringNotContainsString(self::TOKEN, (string) $error);
            }
        }
    }

    public function testBatchRequiresExplicitOptIn(): void
    {
        [$api] = $this->client([], ['tracking' => ['batch_enabled' => false]]);
        $this->expectException(\LogicException::class);
        $api->createTicket(['00000000000001']);
    }

    public function testEmptyTicketsPerformNoRequests(): void
    {
        [$api, $client] = $this->client([]);
        self::assertSame([], $api->createTickets([]));
        self::assertSame([], $client->getRequests());
    }

    public function testBatchesAreIndependentAndTicketScheduleIsExplicit(): void
    {
        [$api, $client] = $this->client([new Response(200, [], $this->fixture('ticket.xml')), new Response(200, [], $this->fixture('ticket.xml'))]);
        $barcodes = [];
        for ($i = 1; $i <= 3001; ++$i) $barcodes[] = str_pad((string) $i, 14, '0', STR_PAD_LEFT);
        $tickets = $api->createTickets($barcodes, 'ENG');
        self::assertCount(2, $tickets);
        self::assertCount(3000, $tickets[0]->getBarcodes());
        self::assertSame([$barcodes[3000]], $tickets[1]->getBarcodes());
        self::assertSame(1900, $tickets[0]->getFirstPollAt());
        self::assertSame(116200, $tickets[0]->getRetentionDeadlineEstimate());
        foreach ($client->getRequests() as $index => $request) {
            self::assertSame('POST', $request->getMethod());
            self::assertSame('https://postapi.pochta.ru/fc/', (string) $request->getUri());
            self::assertSame('ApiKey ' . self::TOKEN, $request->getHeaderLine('Authorization'));
            self::assertSame('text/xml; charset=utf-8', $request->getHeaderLine('Content-Type'));
            $xml = new \DOMDocument(); $xml->loadXML((string) $request->getBody());
            self::assertSame('http://schemas.xmlsoap.org/soap/envelope/', $xml->documentElement->namespaceURI);
            self::assertSame(1, $xml->getElementsByTagNameNS('http://fclient.russianpost.org/postserver', 'ticketRequest')->length);
            self::assertSame($index === 0 ? 3000 : 1, $xml->getElementsByTagNameNS('http://fclient.russianpost.org', 'Item')->length);
            self::assertSame('ENG', $xml->getElementsByTagName('language')->item(0)->textContent);
            foreach (['login', 'password', self::TOKEN] as $forbidden) self::assertStringNotContainsString($forbidden, (string) $request->getBody());
        }
    }

    public function testInvalidBarcodesAndLanguagesFailBeforeNetwork(): void
    {
        foreach (['', '123', 'aa000000000ru', '00000000000001\"', "00000000000001\n", 12345678901234] as $barcode) {
            [$api, $client] = $this->client([]);
            try { $api->createTickets(array_merge(array_fill(0, 3000, '00000000000001'), [$barcode])); self::fail('Expected validation'); }
            catch (\InvalidArgumentException $error) { self::assertSame([], $client->getRequests()); }
        }
        [$api] = $this->client([]);
        try { $api->getTrackingInfo('00000000000001', 'RUS'); self::fail('Expected single language validation'); }
        catch (\InvalidArgumentException $error) { self::assertTrue(true); }
        try { $api->createTicket(['00000000000001'], 'RU'); self::fail('Expected batch language validation'); }
        catch (\InvalidArgumentException $error) { self::assertTrue(true); }
        foreach ([[], array_fill(0, 3001, '00000000000001')] as $barcodes) {
            try { $api->createTicket($barcodes); self::fail('Expected size validation'); }
            catch (\InvalidArgumentException $error) { self::assertTrue(true); }
        }
    }

    public function testSuccessfulTicketsSurviveLaterChunkFailure(): void
    {
        [$api] = $this->client([new Response(200, [], $this->fixture('ticket.xml')), new Response(503)]);
        try { $api->createTickets(array_fill(0, 3001, '00000000000001')); self::fail('Expected second chunk failure'); }
        catch (TicketSubmissionException $error) {
            self::assertCount(1, $error->getCompletedTickets());
            self::assertSame(['00000000000001'], $error->getRemainingBarcodes());
            self::assertSame('server', $error->getCategory());
        }
    }

    public function testBatchPreservesPartialErrorsEmptyHistoryUnknownCodesAndLocalDates(): void
    {
        [$api, $client] = $this->client([new Response(200, [], $this->fixture('batch.xml'))]);
        $result = $api->getResponseByTicket('synthetic<&>ticket');
        self::assertSame('synthetic<&>ticket', $result->getTicketId());
        $items = $result->getItems();
        self::assertCount(3, $items);
        self::assertCount(2, $items[0]['operations']);
        self::assertSame(999, $items[0]['operations'][1]['OperTypeID']);
        self::assertSame('14.11.2026 12:30:00', $items[0]['operations'][0]['DateOper']);
        self::assertSame('000001', $items[0]['operations'][0]['IndexOper']);
        self::assertSame([['code' => 12, 'category' => 'not_found']], $items[1]['errors']);
        self::assertSame([], $items[2]['operations']);
        self::assertSame('15.11.2026 12:00:00', $result->getMetadata()['DatePreparation']);
        $xml = new \DOMDocument(); $xml->loadXML((string) $client->getRequests()[0]->getBody());
        self::assertSame('synthetic<&>ticket', $xml->getElementsByTagName('ticket')->item(0)->textContent);
        self::assertSame(1, $xml->getElementsByTagNameNS('http://fclient.russianpost.org/postserver', 'answerByTicketRequest')->length);
    }

    public function testCarrierErrorsAreNeverAllTreatedAsPending(): void
    {
        foreach ([2 => 'validation', 3 => 'auth', 6 => 'pending', 12 => 'not_found', 16 => 'server', 17 => 'expired', 18 => 'validation', 20 => 'validation', 21 => 'validation', 22 => 'validation', 999 => 'carrier'] as $code => $category) {
            [$api] = $this->client([$this->xmlResponse('answerByTicketResponse', '<error ErrorTypeID="' . $code . '" ErrorName="' . self::TOKEN . '"/>')]);
            try { $api->getResponseByTicket('synthetic-ticket'); self::fail('Expected carrier error'); }
            catch (TrackingApiException $error) {
                self::assertSame($category, $error->getCategory());
                self::assertSame($code, $error->getCarrierCode());
                self::assertSame($code === 6 ? '900' : null, $error->getRetryAfter());
                self::assertStringNotContainsString(self::TOKEN, (string) $error);
            }
        }
    }

    public function testMalformedXmlEntitiesWrongNamespacesAndSoapFaults(): void
    {
        $valid = $this->fixture('batch.xml');
        foreach (['', '<broken', '<!DOCTYPE s [<!ENTITY test SYSTEM "file:///etc/passwd">]>' . $valid,
            str_replace('http://schemas.xmlsoap.org/soap/envelope/', 'https://wrong.test', $valid),
            str_replace('http://fclient.russianpost.org"', 'https://wrong.test"', $valid),
            str_replace('answerByTicketResponse', 'unexpectedResponse', $valid)] as $body) {
            [$api] = $this->client([new Response(200, [], $body)]);
            try { $api->getResponseByTicket('synthetic-ticket'); self::fail('Expected invalid XML response'); }
            catch (TrackingApiException $error) { self::assertSame('invalid_response', $error->getCategory()); }
        }
        [$api] = $this->client([new Response(200, [], '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/"><s:Body><s:Fault><faultstring>' . self::TOKEN . '</faultstring></s:Fault></s:Body></s:Envelope>')]);
        try { $api->getResponseByTicket('synthetic-ticket'); self::fail('Expected SOAP fault'); }
        catch (TrackingApiException $error) { self::assertSame('soap_fault', $error->getCategory()); self::assertStringNotContainsString(self::TOKEN, (string) $error); }
    }

    public function testConfigurationUsesSeparateKeyAndSafeCustomEndpoints(): void
    {
        foreach (['', "token\r\nheader", 'ApiKey already-prefixed'] as $key) {
            try { $this->client([], ['auth' => ['tracking' => ['api_key' => $key]]]); self::fail('Expected key validation'); }
            catch (\InvalidArgumentException $error) { self::assertStringNotContainsString('unused-password', $error->getMessage()); }
        }
        foreach (['http://example.test/path', 'https://user:pass@example.test/path', 'https://example.test/path?token=secret'] as $url) {
            try { $this->client([], ['tracking' => ['single_url' => $url]]); self::fail('Expected endpoint validation'); }
            catch (\InvalidArgumentException $error) { self::assertStringNotContainsString('secret', $error->getMessage()); }
        }
        [$api, $client] = $this->client([new Response(200, [], '{"trackingItemOperationGroupStatuses":[]}')], ['tracking' => ['single_url' => 'http://127.0.0.1:1234/info']]);
        $api->getTrackingInfo('AA000000000RU');
        self::assertSame('http://127.0.0.1:1234/info?language=RU&barcodeList=AA000000000RU', (string) $client->getRequests()[0]->getUri());
    }

    public function testSoapFaultOnHttp500IsNotRetriedAsAServerError(): void
    {
        $fault = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/"><s:Body><s:Fault><faultcode>s:Client</faultcode><faultstring>' . self::TOKEN . '</faultstring></s:Fault></s:Body></s:Envelope>';
        [$api] = $this->client([new Response(500, [], $fault)]);
        try { $api->getResponseByTicket('synthetic-ticket'); self::fail('Expected SOAP fault'); }
        catch (TrackingApiException $error) {
            self::assertSame('soap_fault', $error->getCategory());
            self::assertSame(500, $error->getHttpStatus());
            self::assertFalse($error->isTransient());
            self::assertNull($error->getPrevious());
            self::assertStringNotContainsString(self::TOKEN, (string) $error);
        }
        foreach (['', '<html>Internal server error</html>', $this->fixture('batch.xml')] as $body) {
            [$api] = $this->client([new Response(500, [], $body)]);
            try { $api->getResponseByTicket('synthetic-ticket'); self::fail('Expected server error'); }
            catch (TrackingApiException $error) { self::assertSame('server', $error->getCategory()); self::assertSame(500, $error->getHttpStatus()); }
        }
        foreach ([401 => 'auth', 403 => 'auth', 429 => 'rate_limit'] as $status => $category) {
            [$api] = $this->client([new Response($status, [], $fault)]);
            try { $api->getResponseByTicket('synthetic-ticket'); self::fail('Expected HTTP error'); }
            catch (TrackingApiException $error) { self::assertSame($category, $error->getCategory()); self::assertSame($status, $error->getHttpStatus()); }
        }
    }
}

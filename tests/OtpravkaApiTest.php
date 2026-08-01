<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Tests;

use LapayGroup\RussianPost\Http\Psr18Transport;
use LapayGroup\RussianPost\Exceptions\RussianPostException;
use LapayGroup\RussianPost\Providers\OtpravkaApi;
use LapayGroup\RussianPost\Tests\Support\RecordingClient;
use LapayGroup\RussianPost\Tests\Support\RecordingLogger;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

final class OtpravkaApiTest extends TestCase
{
    private function createApi(array $responses, ?RecordingClient &$client): OtpravkaApi
    {
        $client = new RecordingClient($responses);
        $factory = new Psr17Factory();
        $transport = new Psr18Transport($client, $factory, $factory, $factory);

        return new OtpravkaApi([
            'auth' => [
                'otpravka' => [
                    'token' => 'test-token',
                    'key' => 'test-key'
                ]
            ]
        ], $transport);
    }

    public function testUsesInjectedClientForOtpravkaEndpoint()
    {
        $api = $this->createApi([
            new Response(200, ['Content-Type' => 'application/json'], '{"balance":42}')
        ], $client);

        $this->assertSame(['balance' => 42], $api->getBalance());

        $request = $client->getRequests()[0];
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('https://otpravka-api.pochta.ru/1.0/counterpart/balance', (string)$request->getUri());
        $this->assertSame('AccessToken test-token', $request->getHeaderLine('Authorization'));
        $this->assertSame('Basic test-key', $request->getHeaderLine('X-User-Authorization'));
    }

    public function testEncodesJsonBodyForWriteRequest()
    {
        $api = $this->createApi([
            new Response(200, ['Content-Type' => 'application/json'], '{"success":true}')
        ], $client);

        $this->assertSame(['success' => true], $api->archivingBatch(['batch-1']));

        $request = $client->getRequests()[0];
        $this->assertSame('PUT', $request->getMethod());
        $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertSame('["batch-1"]', (string)$request->getBody());
    }

    public function testBuildsPostOfficeEndpointAndQuery()
    {
        $api = $this->createApi([
            new Response(200, ['Content-Type' => 'application/json'], '[]')
        ], $client);

        $api->searchPostOfficeByAddress('Москва', 2);

        $request = $client->getRequests()[0];
        $this->assertSame(
            'https://otpravka-api.pochta.ru/postoffice/1.0/by-address?address=%D0%9C%D0%BE%D1%81%D0%BA%D0%B2%D0%B0&top=2',
            (string)$request->getUri()
        );
        $this->assertSame('application/json;charset=UTF-8', $request->getHeaderLine('Accept'));
    }

    public function testReturnsPsrUploadedFileForBinaryResponse()
    {
        $api = $this->createApi([
            new Response(200, [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename=passport'
            ], 'zip-content')
        ], $client);

        $file = $api->getPostOfficeFromPassport();

        $this->assertInstanceOf(UploadedFileInterface::class, $file);
        $this->assertSame('passport.zip', $file->getClientFilename());
        $this->assertSame('zip-content', (string)$file->getStream());
    }

    public function testThrowsForEveryHttpErrorEvenWithUnknownPayloadShape(): void
    {
        $api = $this->createApi([
            new Response(500, ['Content-Type' => 'application/json'], '{"unexpected":"failure"}')
        ], $client);

        try {
            $api->getBalance();
            self::fail('Ожидалось исключение для HTTP 500');
        } catch (RussianPostException $exception) {
            self::assertSame(500, $exception->getCode());
            self::assertSame('{"unexpected":"failure"}', $exception->getRawResponse());
        }
    }

    public function testThrowsForInvalidJsonResponse(): void
    {
        $api = $this->createApi([
            new Response(200, ['Content-Type' => 'application/json'], 'not-json')
        ], $client);

        $this->expectException(RussianPostException::class);
        $this->expectExceptionMessage('некорректный JSON');

        $api->getBalance();
    }

    public function testDoesNotWriteRequestPayloadToLog(): void
    {
        $api = $this->createApi([
            new Response(200, ['Content-Type' => 'application/json'], '{"success":true}')
        ], $client);
        $logger = new RecordingLogger();
        $api->setLogger($logger);

        $api->archivingBatch(['personal-order-number']);

        $serializedLog = json_encode($logger->records, JSON_THROW_ON_ERROR);
        self::assertStringNotContainsString('personal-order-number', $serializedLog);
        self::assertStringNotContainsString('test-token', $serializedLog);
        self::assertStringNotContainsString('test-key', $serializedLog);
    }

    public function testDownloadActionReturnsFileWithoutTerminatingProcess(): void
    {
        $api = $this->createApi([
            new Response(200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename=batch'
            ], 'pdf-content')
        ], $client);

        $file = $api->generateDocPackage('batch-1', OtpravkaApi::DOWNLOAD_FILE);

        self::assertInstanceOf(UploadedFileInterface::class, $file);
        self::assertSame('pdf-content', (string) $file->getStream());
    }
}

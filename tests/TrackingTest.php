<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Tests;

use LapayGroup\RussianPost\Providers\Tracking;
use LapayGroup\RussianPost\Tests\Support\FakeSoapClient;
use LapayGroup\RussianPost\Tests\Support\RecordingLogger;
use PHPUnit\Framework\TestCase;

final class TrackingTest extends TestCase
{
    private const CONFIG = [
        'auth' => [
            'tracking' => [
                'login' => 'synthetic-login',
                'password' => 'synthetic-password',
            ],
        ],
    ];

    public function testPassesConfiguredTimeoutToSoapClientFactory(): void
    {
        $calls = [];
        $factory = static function (string $wsdl, array $options) use (&$calls): \SoapClient {
            $calls[] = [$wsdl, $options];

            return new FakeSoapClient();
        };

        new Tracking('single', self::CONFIG, 37, $factory);

        self::assertSame('https://tracking.pochta.ru/tracking-web-static/rtm34_wsdl.xml', $calls[0][0]);
        self::assertSame(37, $calls[0][1]['connection_timeout']);
        self::assertFalse($calls[0][1]['trace']);
    }

    public function testRejectsUnknownServiceBeforeNetworkCall(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Tracking('unknown', self::CONFIG, 60, static fn (): \SoapClient => new FakeSoapClient());
    }

    public function testLogsOnlySafeOperationMetadata(): void
    {
        $client = new FakeSoapClient();
        $client->operationHistoryResponse = (object) [
            'OperationHistoryData' => (object) ['historyRecord' => []],
        ];
        $tracking = new Tracking(
            'single',
            self::CONFIG,
            60,
            static fn (): \SoapClient => $client
        );
        $logger = new RecordingLogger();
        $tracking->setLogger($logger);

        self::assertSame([], $tracking->getOperationsByRpo('00000000000000'));

        $serializedLog = json_encode($logger->records, JSON_THROW_ON_ERROR);
        self::assertStringNotContainsString('synthetic-login', $serializedLog);
        self::assertStringNotContainsString('synthetic-password', $serializedLog);
        self::assertStringNotContainsString('00000000000000', $serializedLog);
        self::assertStringContainsString('getOperationHistory', $serializedLog);
    }
}

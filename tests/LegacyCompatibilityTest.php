<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use LapayGroup\RussianPost\Exceptions\TrackingApiException;
use LapayGroup\RussianPost\Http\Psr18Transport;
use LapayGroup\RussianPost\Providers\OtpravkaApi;
use LapayGroup\RussianPost\Providers\TrackingApi;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;

final class LegacyCompatibilityTest extends TestCase
{
    public function testOldOtpravkaConstructorAndNewPsr18TrackingCanCoexist(): void
    {
        $legacy = new OtpravkaApi(['auth' => ['otpravka' => ['token' => 'synthetic', 'key' => 'synthetic']]], 12);
        self::assertInstanceOf(OtpravkaApi::class, $legacy);
        $factory = new Psr17Factory();
        $http = new Client(['handler' => HandlerStack::create(new MockHandler([
            new Response(200, [], '{"trackingItemOperationGroupStatuses":[]}'),
            new Response(403, [], 'synthetic-token'),
        ])), 'timeout' => 12, 'allow_redirects' => false]);
        $api = new TrackingApi(['auth' => ['tracking' => ['api_key' => 'synthetic-token']]], new Psr18Transport($http, $factory, $factory, $factory));
        self::assertSame([], $api->getTrackingInfo('00000000000001')->getOperations());
        try {
            $api->getTrackingInfo('00000000000001');
            self::fail('PSR-18 must return HTTP errors for SDK classification.');
        } catch (TrackingApiException $error) {
            self::assertSame('auth', $error->getCategory());
        }
    }
}

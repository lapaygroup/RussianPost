<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use LapayGroup\RussianPost\AddressList;
use LapayGroup\RussianPost\CalculateInfo;
use LapayGroup\RussianPost\FioList;
use LapayGroup\RussianPost\PhoneList;
use LapayGroup\RussianPost\Providers\Calculation;
use LapayGroup\RussianPost\Providers\OtpravkaApi;
use LapayGroup\RussianPost\Tariff;
use LapayGroup\RussianPost\TariffCalculation;
use PHPUnit\Framework\TestCase;

/** Public non-tracking contracts used by LC with SDK 1.0.1. */
final class Lc101CompatibilityTest extends TestCase
{
    public function testLcNormalizationKeepsIdsAndIterableCollections(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], '[{"id":42,"index":"000001"}]'),
            new Response(200, [], '[{"id":42,"surname":"Synthetic"}]'),
            new Response(200, [], '[{"id":42,"phone":"0000000000"}]'),
        ]));
        $stack->push(Middleware::history($history));
        $api = new OtpravkaApi(['auth' => ['otpravka' => ['token' => 'synthetic', 'key' => 'synthetic']]], 12);
        $property = new \ReflectionProperty($api, 'otpravkaClient');
        $property->setAccessible(true);
        $property->setValue($api, new Client(['base_uri' => 'https://otpravka-api.pochta.ru/', 'handler' => $stack]));
        $address = new AddressList();
        $address->add('Synthetic address', 42);
        $fio = new FioList();
        $fio->add('Synthetic name', 42);
        $phone = new PhoneList();
        $phone->add('0000000000', 42);
        self::assertSame('000001', $api->clearAddress($address)[0]['index']);
        self::assertSame('Synthetic', $api->clearFio($fio)[0]['surname']);
        self::assertSame('0000000000', $api->clearPhone($phone)[0]['phone']);
        foreach ([$address, $fio, $phone] as $i => $collection) {
            self::assertInstanceOf(\IteratorAggregate::class, $collection);
            self::assertSame(iterator_to_array($collection), json_decode((string) $history[$i]['request']->getBody(), true));
            self::assertSame(42, json_decode((string) $history[$i]['request']->getBody(), true)[0]['id']);
        }
        self::assertSame(['/1.0/clean/address', '/1.0/clean/physical', '/1.0/clean/phone'], array_map(static function ($item) {
            return $item['request']->getUri()->getPath();
        }, $history));
    }

    public function testTariffUsesV2EndpointsAndPreservesLcServiceParameter(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([new Response(200, [], '{"items":[]}'), new Response(200, [], '{"items":[]}')]));
        $stack->push(Middleware::history($history));
        $calculation = new Calculation();
        $property = new \ReflectionProperty($calculation, 'httpClient');
        $property->setAccessible(true);
        $original = $property->getValue($calculation);
        self::assertSame('https://delivery.pochta.ru/v2/', (string) $original->getConfig('base_uri'));
        $property->setValue($calculation, new Client(['base_uri' => $original->getConfig('base_uri'), 'handler' => $stack]));
        self::assertSame(['items' => []], $calculation->getTariff(2000, ['weight' => 20], [1, 2]));
        self::assertSame(['items' => []], $calculation->getTariffAndDeliveryPeriod(2000, ['weight' => 20], [1, 2]));
        self::assertSame('/v2/calculate/tariff', $history[0]['request']->getUri()->getPath());
        self::assertSame('/v2/calculate/tariff/delivery', $history[1]['request']->getUri()->getPath());
        parse_str($history[0]['request']->getUri()->getQuery(), $params);
        self::assertSame('1,2', $params['service']);
        self::assertSame('2000', $params['object']);
        self::assertSame('1', $params['json']);
        $method = new \ReflectionMethod(TariffCalculation::class, 'calculate');
        self::assertSame(['object_id', 'params', 'delivery_period', 'services', 'date'], array_map(static function ($parameter) {
            return $parameter->getName();
        }, $method->getParameters()));
        self::assertFalse($method->getParameters()[2]->getDefaultValue());
    }

    public function testCalculationDtoKeepsDeliveryAndMoneyContracts(): void
    {
        $info = new CalculateInfo();
        $info->setDeliveryPeriodMin(2);
        $info->setDeliveryPeriodMax(5);
        $info->setDeliveryDeadLine('2026-11-20');
        $info->setPayNds(12345);
        $tariff = new Tariff(1, 'Synthetic', 10000, 12000, 0);
        $info->addTariff($tariff);
        self::assertSame(2, $info->getDeliveryPeriodMin());
        self::assertSame(5, $info->getDeliveryPeriodMax());
        self::assertSame('2026-11-20', $info->getDeliveryDeadLine()->format('Y-m-d'));
        self::assertSame('123.45', $info->getPayNds());
        self::assertSame('120.00', $info->getTariffList()[0]->getValueNds());
    }
}

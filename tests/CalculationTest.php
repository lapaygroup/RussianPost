<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Tests;

use LapayGroup\RussianPost\CategoryList;
use LapayGroup\RussianPost\Exceptions\RussianPostException;
use LapayGroup\RussianPost\Http\Psr18Transport;
use LapayGroup\RussianPost\Providers\Calculation;
use LapayGroup\RussianPost\TariffCalculation;
use LapayGroup\RussianPost\Tests\Support\RecordingClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class CalculationTest extends TestCase
{
    private function createTransport(array $responses, ?RecordingClient &$client): Psr18Transport
    {
        $client = new RecordingClient($responses);
        $factory = new Psr17Factory();

        return new Psr18Transport($client, $factory, $factory, $factory);
    }

    public function testCalculationUsesInjectedPsrClient()
    {
        $transport = $this->createTransport([
            new Response(200, ['Content-Type' => 'application/json'], '{"category":[]}')
        ], $client);
        $calculation = new Calculation($transport);

        $this->assertSame(['category' => []], $calculation->getCategoryList());

        $request = $client->getRequests()[0];
        $this->assertSame(
            'https://delivery.pochta.ru/v2/dictionary/tariff/delivery?category=all&json=1',
            (string)$request->getUri()
        );
    }

    public function testCategoryListPropagatesInjectedTransport()
    {
        $transport = $this->createTransport([
            new Response(200, ['Content-Type' => 'application/json'], '{"category":[]}')
        ], $client);
        $categoryList = new CategoryList($transport);

        $this->assertSame([], $categoryList->parseToArray());
        $this->assertCount(1, $client->getRequests());
    }

    public function testTariffCalculationPropagatesInjectedTransport()
    {
        $response = [
            'version' => 'test',
            'id' => 23030,
            'name' => 'Посылка',
            'weight' => 600,
            'transid' => 1,
            'transname' => 'Наземно',
            'pay' => 100,
            'paynds' => 120,
            'items' => []
        ];
        $transport = $this->createTransport([
            new Response(200, ['Content-Type' => 'application/json'], json_encode($response))
        ], $client);
        $tariffCalculation = new TariffCalculation($transport);

        $result = $tariffCalculation->calculate(23030, ['weight' => 600], false, [], '20260801');

        $this->assertSame(23030, $result->getCategoryItemId());
        $this->assertSame(
            'https://delivery.pochta.ru/v2/calculate/tariff?weight=600&date=20260801&object=23030&json=1',
            (string)$client->getRequests()[0]->getUri()
        );
    }

    public function testCalculationThrowsForGenericHttpError(): void
    {
        $transport = $this->createTransport([
            new Response(503, ['Content-Type' => 'application/json'], '{"message":"maintenance"}')
        ], $client);
        $calculation = new Calculation($transport);

        $this->expectException(RussianPostException::class);
        $this->expectExceptionCode(503);

        $calculation->getCategoryList();
    }
}

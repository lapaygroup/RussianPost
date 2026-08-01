<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Tests;

use LapayGroup\RussianPost\AddressList;
use LapayGroup\RussianPost\CalculateInfo;
use LapayGroup\RussianPost\Entity\AddressReturn;
use LapayGroup\RussianPost\Entity\Item;
use LapayGroup\RussianPost\Entity\Order;
use LapayGroup\RussianPost\Exceptions\RussianPostException;
use LapayGroup\RussianPost\Tariff;
use PHPUnit\Framework\TestCase;

final class EntitySerializationTest extends TestCase
{
    public function testAddressReturnSerializesKnownFields(): void
    {
        $address = new AddressReturn();
        $address->setIndex('101000');
        $address->setPlace('Москва');
        $address->setRegion('Москва');
        $address->setHouse('1');

        self::assertSame([
            'address-type' => 'DEFAULT',
            'index' => '101000',
            'house' => '1',
            'place' => 'Москва',
            'region' => 'Москва',
        ], $address->asArr());
    }

    public function testOrderUsesLatinItemAccessorsAndKeepsDeprecatedAliases(): void
    {
        $item = new Item();
        $item->setDescription('Товар');
        $item->setCountryCode(643);
        $item->setCustomsDeclarationNumber('DECL-1');

        self::assertSame(643, $item->getСountryCode());
        self::assertSame('DECL-1', $item->getСustomsDeclarationNumber());

        $order = new Order();
        $order->setIndexTo(101000);
        $order->setItems([$item]);
        $serialized = $order->asArr();

        self::assertSame(643, $serialized['goods']['items'][0]['country-code']);
        self::assertSame('DECL-1', $serialized['goods']['items'][0]['customs-declaration-number']);
    }

    public function testEmptyNormalizationCollectionIsIterable(): void
    {
        $addresses = new AddressList();

        self::assertCount(0, $addresses);
        self::assertSame([], iterator_to_array($addresses));
    }

    public function testCalculateInfoHasExplicitNullableAndMoneyContracts(): void
    {
        $info = new CalculateInfo();
        $info->setTransportationName(null);
        $info->setPay(12345);
        $info->setDeliveryDeadLine('2026-08-02');

        self::assertNull($info->getTransportationName());
        self::assertSame(123.45, $info->getPay());
        self::assertSame(12345, $info->getPayKopecks());
        self::assertInstanceOf(\DateTimeImmutable::class, $info->getDeliveryDeadLine());
    }

    public function testTariffExposesRublesAndExactKopecks(): void
    {
        $tariff = new Tariff(1, 'Пересылка', 12345, 14814, 10000);

        self::assertSame(123.45, $tariff->getValue());
        self::assertSame(12345, $tariff->getValueKopecks());
        self::assertSame(148.14, $tariff->getValueNds());
        self::assertSame(14814, $tariff->getValueNdsKopecks());
        self::assertSame(100.0, $tariff->getValueMark());
        self::assertSame(10000, $tariff->getValueMarkKopecks());
    }

    public function testRussianPostExceptionHandlesNullAndStructuredResponses(): void
    {
        $empty = new RussianPostException('error');
        self::assertNull($empty->getRawResponse());

        $exception = new RussianPostException(
            'error',
            400,
            '{"code":123,"desc":"description","sub-code":"sub"}'
        );
        self::assertSame('123', $exception->getErrorCode());
        self::assertSame('description', $exception->getErrorDescription());
        self::assertSame('sub', $exception->getErrorSubCode());
    }
}

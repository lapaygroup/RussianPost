<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost;

class CalculateInfo
{
    private string $version = '';
    private int $categoryItemId = 0;
    private ?string $categoryItemName = null;
    private ?int $weight = null;
    private ?int $transportationID = null;
    private ?string $transportationName = null;
    private int $pay = 0;
    private int $payNds = 0;
    private int $ground = 0;
    private int $groundNds = 0;
    private int $cover = 0;
    private int $coverNds = 0;
    private int $service = 0;
    private int $serviceNds = 0;
    private ?int $deliveryPeriodMin = null;
    private ?int $deliveryPeriodMax = null;
    private ?\DateTimeImmutable $deliveryDeadLine = null;

    /** @var list<Tariff> */
    private array $tariffList = [];

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getCategoryItemId(): int
    {
        return $this->categoryItemId;
    }

    /**
     * @param int $categoryItemId
     */
    public function setCategoryItemId(int $categoryItemId): void
    {
        $this->categoryItemId = $categoryItemId;
    }

    /**
     * @return string|null
     */
    public function getCategoryItemName(): ?string
    {
        return $this->categoryItemName;
    }

    /**
     * @param string|null $categoryItemName
     */
    public function setCategoryItemName(?string $categoryItemName): void
    {
        $this->categoryItemName = $categoryItemName;
    }

    /**
     * @return int|null
     */
    public function getWeight(): ?int
    {
        return $this->weight;
    }

    /**
     * @param int|null $weight
     */
    public function setWeight(?int $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getTransportationID(): ?int
    {
        return $this->transportationID;
    }

    /**
     * @param int $transportationID
     */
    public function setTransportationID(?int $transportationID): void
    {
        $this->transportationID = $transportationID;
    }

    /**
     * @return string
     */
    public function getTransportationName(): ?string
    {
        return $this->transportationName;
    }

    /**
     * @param string $transportationName
     */
    public function setTransportationName(?string $transportationName): void
    {
        $this->transportationName = self::mb_ucfirst($transportationName);
    }

    /**
     * Денежная сумма в рублях.
     */
    public function getPay(): float
    {
        return $this->pay / 100;
    }

    public function getPayKopecks(): int
    {
        return $this->pay;
    }

    /**
     * @param int $pay
     */
    public function setPay(int|float|string|null $pay): void
    {
        $this->pay = self::normalizeKopecks($pay);
    }

    /**
     * Денежная сумма в рублях.
     */
    public function getPayNds(): float
    {
        return $this->payNds / 100;
    }

    public function getPayNdsKopecks(): int
    {
        return $this->payNds;
    }

    /**
     * @param int $payNds
     */
    public function setPayNds(int|float|string|null $payNds): void
    {
        $this->payNds = self::normalizeKopecks($payNds);
    }

    /**
     * @return array
     */
    public function getTariffList(): array
    {
        return $this->tariffList;
    }

    /**
     * @param Tariff $tariff
     */
    public function addTariff(Tariff $tariff): void
    {
        $this->tariffList[] = $tariff;
    }

    /**
     * Денежная сумма в рублях.
     */
    public function getGround(): float
    {
        return $this->ground / 100;
    }

    public function getGroundKopecks(): int
    {
        return $this->ground;
    }

    /**
     * @param float $ground
     */
    public function setGround(int|float|string|null $ground): void
    {
        $this->ground = self::normalizeKopecks($ground);
    }

    /**
     * Денежная сумма в рублях.
     */
    public function getGroundNds(): float
    {
        return $this->groundNds / 100;
    }

    public function getGroundNdsKopecks(): int
    {
        return $this->groundNds;
    }

    /**
     * @param float $groundNds
     */
    public function setGroundNds(int|float|string|null $groundNds): void
    {
        $this->groundNds = self::normalizeKopecks($groundNds);
    }

    /**
     * Денежная сумма в рублях.
     */
    public function getCover(): float
    {
        return $this->cover / 100;
    }

    public function getCoverKopecks(): int
    {
        return $this->cover;
    }

    /**
     * @param float $cover
     */
    public function setCover(int|float|string|null $cover): void
    {
        $this->cover = self::normalizeKopecks($cover);
    }

    /**
     * Денежная сумма в рублях.
     */
    public function getCoverNds(): float
    {
        return $this->coverNds / 100;
    }

    public function getCoverNdsKopecks(): int
    {
        return $this->coverNds;
    }

    /**
     * @param float $coverNds
     */
    public function setCoverNds(int|float|string|null $coverNds): void
    {
        $this->coverNds = self::normalizeKopecks($coverNds);
    }

    /**
     * Денежная сумма в рублях.
     */
    public function getService(): float
    {
        return $this->service / 100;
    }

    public function getServiceKopecks(): int
    {
        return $this->service;
    }

    /**
     * @param float $service
     */
    public function setService(int|float|string|null $service): void
    {
        $this->service = self::normalizeKopecks($service);
    }

    /**
     * Денежная сумма в рублях.
     */
    public function getServiceNds(): float
    {
        return $this->serviceNds / 100;
    }

    public function getServiceNdsKopecks(): int
    {
        return $this->serviceNds;
    }

    /**
     * @param float $serviceNds
     */
    public function setServiceNds(int|float|string|null $serviceNds): void
    {
        $this->serviceNds = self::normalizeKopecks($serviceNds);
    }

    /**
     * @return int
     */
    public function getDeliveryPeriodMin(): ?int
    {
        return $this->deliveryPeriodMin;
    }

    /**
     * @param int $deliveryPeriodMin
     */
    public function setDeliveryPeriodMin(?int $deliveryPeriodMin): void
    {
        $this->deliveryPeriodMin = $deliveryPeriodMin;
    }

    /**
     * @return int
     */
    public function getDeliveryPeriodMax(): ?int
    {
        return $this->deliveryPeriodMax;
    }

    /**
     * @param int $deliveryPeriodMax
     */
    public function setDeliveryPeriodMax(?int $deliveryPeriodMax): void
    {
        $this->deliveryPeriodMax = $deliveryPeriodMax;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDeliveryDeadLine(): ?\DateTimeImmutable
    {
        return $this->deliveryDeadLine;
    }

    /**
     * @param string $deliveryDeadLine
     */
    public function setDeliveryDeadLine(?string $deliveryDeadLine): void
    {
        $this->deliveryDeadLine = $deliveryDeadLine === null ? null : new \DateTimeImmutable($deliveryDeadLine);
    }

    public static function mb_ucfirst(?string $string): ?string
    {
        if ($string === null || $string === '') {
            return $string;
        }

        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_strtolower(mb_substr($string, 1));
    }

    private static function normalizeKopecks(int|float|string|null $value): int
    {
        if ($value === null || !is_numeric($value)) {
            return 0;
        }

        return (int) round((float) $value);
    }
}

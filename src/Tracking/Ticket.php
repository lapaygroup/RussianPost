<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Tracking;

final class Ticket
{
    public const POLL_INTERVAL_SECONDS = 900;
    public const RETENTION_SECONDS = 115200;
    private string $id;
    private int $receivedAt;
    /** @var string[] */
    private array $barcodes;

    public function __construct(string $id, int $receivedAt, array $barcodes)
    {
        $this->id = $id;
        $this->receivedAt = $receivedAt;
        $this->barcodes = $barcodes;
    }
    public function getId(): string { return $this->id; }
    public function getReceivedAt(): int { return $this->receivedAt; }
    public function getFirstPollAt(): int { return $this->receivedAt + self::POLL_INTERVAL_SECONDS; }
    /** Local retention estimate, not a server-confirmed expiry timestamp. */
    public function getRetentionDeadlineEstimate(): int { return $this->receivedAt + self::RETENTION_SECONDS; }
    /** @return string[] */
    public function getBarcodes(): array { return $this->barcodes; }
}

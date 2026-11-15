<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Tracking;

final class BatchResult
{
    private string $ticketId;
    private array $items;
    private array $metadata;
    public function __construct(string $ticketId, array $items, array $metadata)
    {
        $this->ticketId = $ticketId;
        $this->items = $items;
        $this->metadata = $metadata;
    }
    public function getTicketId(): string { return $this->ticketId; }
    /** List of {barcode, operations, errors}; errors on one item never hide other items. */
    public function getItems(): array { return $this->items; }
    public function getMetadata(): array { return $this->metadata; }
}

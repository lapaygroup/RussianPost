<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Exceptions;

use LapayGroup\RussianPost\Tracking\Ticket;

/** Preserves progress when a later chunk fails; do not resubmit completed chunks. */
final class TicketSubmissionException extends TrackingApiException
{
    /** @var Ticket[] */
    private array $completedTickets;
    /** @var string[] */
    private array $remainingBarcodes;

    public function __construct(TrackingApiException $error, array $completedTickets, array $remainingBarcodes)
    {
        parent::__construct($error->getCategory(), $error->getHttpStatus(), $error->getCarrierCode(), $error->getRetryAfter());
        $this->completedTickets = $completedTickets;
        $this->remainingBarcodes = $remainingBarcodes;
    }

    /** @return Ticket[] */
    public function getCompletedTickets(): array { return $this->completedTickets; }
    /** @return string[] Includes the failed chunk, whose acceptance is unknown after a transport error. */
    public function getRemainingBarcodes(): array { return $this->remainingBarcodes; }
}

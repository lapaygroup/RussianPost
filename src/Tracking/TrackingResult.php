<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Tracking;

use LapayGroup\RussianPost\Exceptions\TrackingApiException;

/** Carrier payload and actual events; no inferred financial or terminal state. */
final class TrackingResult
{
    private array $data;
    private array $operations = [];

    public function __construct(array $data)
    {
        if (!array_key_exists('trackingItemOperationGroupStatuses', $data)) {
            throw new TrackingApiException(TrackingApiException::INVALID_RESPONSE);
        }
        // An explicit null is the carrier's empty-history representation.
        // Preserve it in getData(), while exposing an empty operations list.
        $groups = $data['trackingItemOperationGroupStatuses'] ?? [];
        if (!is_array($groups) || array_values($groups) !== $groups) {
            throw new TrackingApiException(TrackingApiException::INVALID_RESPONSE);
        }
        foreach ($groups as $group) {
            if (!is_array($group) || !isset($group['future']) || !is_bool($group['future'])) {
                throw new TrackingApiException(TrackingApiException::INVALID_RESPONSE);
            }
            if ($group['future']) {
                continue;
            }
            $events = $group['originTrackingHistoryItems'] ?? [];
            if (!is_array($events) || array_values($events) !== $events) {
                throw new TrackingApiException(TrackingApiException::INVALID_RESPONSE);
            }
            foreach ($events as $event) {
                if (!is_array($event) || !isset($event['date'], $event['operationType'], $event['operationAttr'])
                    || !is_string($event['date']) || !is_int($event['operationType']) || !is_int($event['operationAttr'])) {
                    throw new TrackingApiException(TrackingApiException::INVALID_RESPONSE);
                }
                // Keep the original offset, nulls and unknown carrier fields/codes verbatim.
                $this->operations[] = [
                    'operation' => $event,
                    'group' => [
                        'code' => $group['code'] ?? null,
                        'title' => $group['title'] ?? null,
                        'direction' => $group['direction'] ?? null,
                    ],
                ];
            }
        }
        $this->data = $data;
    }
    public function getData(): array { return $this->data; }
    public function getOperations(): array { return $this->operations; }
}

<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Tests\Support;

final class FakeSoapClient extends \SoapClient
{
    public ?object $operationHistoryResponse = null;

    public function __construct()
    {
        parent::__construct(null, [
            'location' => 'https://example.test/soap',
            'uri' => 'https://example.test/soap',
        ]);
    }

    public function getOperationHistory(mixed $request): object
    {
        return $this->operationHistoryResponse ?? (object) [
            'OperationHistoryData' => (object) [],
        ];
    }
}

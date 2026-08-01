<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Tests\Support;

use Psr\Log\AbstractLogger;

final class RecordingLogger extends AbstractLogger
{
    public array $records = [];

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->records[] = [
            'level' => $level,
            'message' => (string) $message,
            'context' => $context,
        ];
    }
}

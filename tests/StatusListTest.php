<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Tests;

use LapayGroup\RussianPost\StatusList;
use PHPUnit\Framework\TestCase;

final class StatusListTest extends TestCase
{
    public function testIsFinal()
    {
        $statusList = new StatusList();

        $this->assertTrue($statusList->isFinal(5, 2));
    }
}

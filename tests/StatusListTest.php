<?php

require_once __DIR__."/../src/StatusList.php";


class StatusListTest extends \PHPUnit\Framework\TestCase
{


	public function testIsFinal()
	{
		$statusList = new \LapayGroup\RussianPost\StatusList();
		
		$this->assertEquals(
			true,
			$statusList->isFinal(5,2)
		);
	}
}


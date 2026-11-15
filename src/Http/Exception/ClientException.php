<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Http\Exception;

use Psr\Http\Client\ClientExceptionInterface;

class ClientException extends \RuntimeException implements ClientExceptionInterface
{
}

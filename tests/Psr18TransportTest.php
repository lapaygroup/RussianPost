<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Tests;

use LapayGroup\RussianPost\Http\Psr18Transport;
use LapayGroup\RussianPost\Tests\Support\RecordingClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

final class Psr18TransportTest extends TestCase
{
    public function testSendsPsrRequestWithQueryHeadersAndBody()
    {
        $client = new RecordingClient([new Response(204)]);
        $factory = new Psr17Factory();
        $transport = new Psr18Transport($client, $factory, $factory, $factory);

        $transport->send(
            'post',
            'https://example.test/path?fixed=yes',
            ['X-Test' => 'value'],
            ['space' => 'a b'],
            '{"ok":true}'
        );

        $requests = $client->getRequests();
        $this->assertCount(1, $requests);
        $this->assertSame('POST', $requests[0]->getMethod());
        $this->assertSame('https://example.test/path?fixed=yes&space=a%20b', (string)$requests[0]->getUri());
        $this->assertSame('value', $requests[0]->getHeaderLine('X-Test'));
        $this->assertSame('{"ok":true}', (string)$requests[0]->getBody());
    }

    public function testCreatesUploadedFileThroughPsr17Factory()
    {
        $client = new RecordingClient([]);
        $factory = new Psr17Factory();
        $transport = new Psr18Transport($client, $factory, $factory, $factory);
        $stream = $factory->createStream('file-content');

        $file = $transport->createUploadedFile($stream, 12, 'document.pdf', 'application/pdf');

        $this->assertInstanceOf(UploadedFileInterface::class, $file);
        $this->assertSame('document.pdf', $file->getClientFilename());
        $this->assertSame('application/pdf', $file->getClientMediaType());
        $this->assertSame('file-content', (string)$file->getStream());
    }
}

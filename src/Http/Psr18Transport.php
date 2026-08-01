<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Http;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;

final class Psr18Transport
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly UploadedFileFactoryInterface $uploadedFileFactory
    ) {}

    /**
     * @param string $method
     * @param string $uri
     * @param array $headers
     * @param array $query
     * @param string|null $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function send(
        string $method,
        string $uri,
        array $headers = [],
        array $query = [],
        ?string $body = null
    ): ResponseInterface
    {
        if (!empty($query)) {
            $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);
            if ($queryString !== '') {
                $uri .= strpos($uri, '?') === false ? '?' : '&';
                $uri .= $queryString;
            }
        }

        $request = $this->requestFactory->createRequest(strtoupper($method), $uri);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if ($body !== null) {
            $request = $request->withBody($this->streamFactory->createStream($body));
        }

        return $this->client->sendRequest($request);
    }

    /**
     * @param \Psr\Http\Message\StreamInterface $stream
     * @param int|null $size
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     * @return \Psr\Http\Message\UploadedFileInterface
     */
    public function createUploadedFile(
        StreamInterface $stream,
        ?int $size = null,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ): UploadedFileInterface
    {
        return $this->uploadedFileFactory->createUploadedFile(
            $stream,
            $size,
            UPLOAD_ERR_OK,
            $clientFilename,
            $clientMediaType
        );
    }
}

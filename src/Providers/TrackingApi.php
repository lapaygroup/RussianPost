<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Providers;

use LapayGroup\RussianPost\Exceptions\TicketSubmissionException;
use LapayGroup\RussianPost\Exceptions\TrackingApiException;
use LapayGroup\RussianPost\Http\Psr18Transport;
use LapayGroup\RussianPost\Tracking\BatchResult;
use LapayGroup\RussianPost\Tracking\Ticket;
use LapayGroup\RussianPost\Tracking\TrackingResult;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/** New ApiKey API. The legacy Tracking provider is deliberately unchanged. */
final class TrackingApi implements LoggerAwareInterface
{
    public const MAX_BATCH_SIZE = 3000;
    private const SOAP_NS = 'http://schemas.xmlsoap.org/soap/envelope/';
    private const POST_NS = 'http://fclient.russianpost.org/postserver';
    private const DATA_NS = 'http://fclient.russianpost.org';
    private Psr18Transport $transport;
    private string $apiKey;
    private string $singleUrl;
    private string $batchUrl;
    private bool $batchEnabled;
    private \Closure $clock;
    private ?LoggerInterface $logger = null;

    public function __construct(array $config, Psr18Transport $transport, ?callable $clock = null)
    {
        $key = $config['auth']['tracking']['api_key'] ?? null;
        if (!is_string($key) || trim($key) === '' || preg_match('/[\x00-\x20\x7f]/', $key)) {
            throw new \InvalidArgumentException('Tracking API requires a non-empty api_key without whitespace.');
        }
        $this->apiKey = $key;
        $this->transport = $transport;
        $this->singleUrl = $this->endpoint($config['tracking']['single_url'] ?? 'https://postapi.pochta.ru/tracking/api/external/v1/info');
        $this->batchUrl = $this->endpoint($config['tracking']['batch_url'] ?? 'https://postapi.pochta.ru/fc/');
        // Public batch docs still conflict with their own examples; release requires WSDL confirmation.
        $this->batchEnabled = ($config['tracking']['batch_enabled'] ?? false) === true;
        $this->clock = $clock === null ? static function (): int { return time(); } : \Closure::fromCallable($clock);
    }

    public function setLogger(LoggerInterface $logger): void { $this->logger = $logger; }

    public function getTrackingInfo(string $barcode, string $language = 'RU'): TrackingResult
    {
        $this->validateBarcodes([$barcode]);
        $this->validateLanguage($language, ['RU', 'EN']);
        $response = $this->request('GET', $this->singleUrl, 'single', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ], ['language' => $language, 'barcodeList' => $barcode]);
        try {
            $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $error) {
            throw new TrackingApiException(TrackingApiException::INVALID_RESPONSE);
        }
        // The documentation specifies a TrackedItemDetail object, not the legacy SOAP record list.
        if (!is_array($data) || ($data['barcode'] ?? $barcode) !== $barcode) {
            throw new TrackingApiException(TrackingApiException::INVALID_RESPONSE);
        }
        return new TrackingResult($data);
    }

    public function createTicket(array $barcodes, string $language = 'RUS'): Ticket
    {
        $this->requireBatch();
        $this->validateBarcodes($barcodes);
        $this->validateLanguage($language, ['RUS', 'ENG']);
        if (count($barcodes) === 0 || count($barcodes) > self::MAX_BATCH_SIZE) {
            throw new \InvalidArgumentException('A ticket must contain 1 to 3000 barcodes.');
        }
        $document = $this->envelope('ticketRequest');
        $operation = $document->getElementsByTagNameNS(self::POST_NS, 'ticketRequest')->item(0);
        $request = $document->createElement('request');
        $operation->appendChild($request);
        foreach ($barcodes as $barcode) {
            $item = $document->createElementNS(self::DATA_NS, 'fcl:Item');
            $item->setAttribute('Barcode', $barcode);
            $request->appendChild($item);
        }
        $operation->appendChild($document->createElement('language', $language));
        $root = $this->soap($document, 'ticketResponse', 'getTicket');
        $this->throwCarrierError($root);
        $value = $this->child($root, 'value');
        if ($value === null || trim($value->textContent) === '') {
            throw new TrackingApiException(TrackingApiException::INVALID_RESPONSE);
        }
        return new Ticket(trim($value->textContent), ($this->clock)(), array_values($barcodes));
    }

    /**
     * @return Ticket[] Empty input performs no request. All barcodes are validated before the first request.
     * @throws TicketSubmissionException Preserves successfully created tickets if a later chunk fails.
     */
    public function createTickets(array $barcodes, string $language = 'RUS'): array
    {
        $this->requireBatch();
        $this->validateBarcodes($barcodes);
        $this->validateLanguage($language, ['RUS', 'ENG']);
        $barcodes = array_values($barcodes);
        $tickets = [];
        foreach (array_chunk($barcodes, self::MAX_BATCH_SIZE) as $index => $chunk) {
            try {
                $tickets[] = $this->createTicket($chunk, $language);
            } catch (TrackingApiException $error) {
                throw new TicketSubmissionException($error, $tickets, array_slice($barcodes, $index * self::MAX_BATCH_SIZE));
            }
        }
        return $tickets;
    }

    public function getResponseByTicket(string $ticketId): BatchResult
    {
        $this->requireBatch();
        if ($ticketId === '' || preg_match('/[\x00-\x1f\x7f]/', $ticketId)) {
            throw new \InvalidArgumentException('A non-empty ticket id without control characters is required.');
        }
        $document = $this->envelope('answerByTicketRequest');
        $ticket = $document->createElement('ticket');
        $ticket->appendChild($document->createTextNode($ticketId));
        $document->getElementsByTagNameNS(self::POST_NS, 'answerByTicketRequest')->item(0)->appendChild($ticket);
        $root = $this->soap($document, 'answerByTicketResponse', 'getResponseByTicket');
        $this->throwCarrierError($root);
        $value = $this->child($root, 'value');
        if ($value === null) {
            throw new TrackingApiException(TrackingApiException::INVALID_RESPONSE);
        }
        $this->validateChildren($value, ['Item'], self::DATA_NS);
        $items = [];
        foreach ($this->children($value, 'Item', self::DATA_NS) as $item) {
            $this->validateChildren($item, ['Operation', 'Error'], self::DATA_NS);
            $barcode = $item->getAttribute('Barcode');
            if ($barcode === '') {
                throw new TrackingApiException(TrackingApiException::INVALID_RESPONSE);
            }
            $operations = [];
            foreach ($this->children($item, 'Operation', self::DATA_NS) as $operation) {
                $attributes = $this->attributes($operation);
                foreach (['OperTypeID', 'OperCtgID'] as $field) {
                    if (!isset($attributes[$field]) || !ctype_digit($attributes[$field])) {
                        throw new TrackingApiException(TrackingApiException::INVALID_RESPONSE);
                    }
                    $attributes[$field] = (int) $attributes[$field];
                }
                if (!isset($attributes['DateOper']) || $attributes['DateOper'] === '') {
                    throw new TrackingApiException(TrackingApiException::INVALID_RESPONSE);
                }
                $operations[] = $attributes;
            }
            $errors = [];
            foreach ($this->children($item, 'Error', self::DATA_NS) as $error) {
                $code = $this->errorCode($error);
                $errors[] = ['code' => $code, 'category' => $this->carrierCategory($code)];
            }
            $items[] = ['barcode' => $barcode, 'operations' => $operations, 'errors' => $errors];
        }
        return new BatchResult($ticketId, $items, $this->attributes($value));
    }

    private function request(string $method, string $url, string $operation, array $headers, array $query = [], ?string $body = null, bool $inspectSoapFault = false): ResponseInterface
    {
        $headers['Authorization'] = 'ApiKey ' . $this->apiKey;
        try {
            $response = $this->transport->send($method, $url, $headers, $query, $body);
        } catch (ClientExceptionInterface $error) {
            // PSR clients often include credentials or payloads in exception messages and attached requests.
            throw new TrackingApiException(TrackingApiException::TRANSPORT);
        }
        $status = $response->getStatusCode();
        if ($this->logger !== null) {
            $this->logger->info('Russian Post Tracking API response', ['operation' => $operation, 'http_status' => $status]);
        }
        // SOAP 1.1 normally transports Fault in HTTP 500. Only batch may inspect that status.
        if ($inspectSoapFault && $status === 500) {
            return $response;
        }
        if ($status < 200 || $status >= 300) {
            $category = TrackingApiException::HTTP;
            if ($status === 401 || $status === 403) $category = TrackingApiException::AUTH;
            elseif ($status === 429) $category = TrackingApiException::RATE_LIMIT;
            elseif ($status >= 500) $category = TrackingApiException::SERVER;
            $retryAfter = $response->getHeaderLine('Retry-After');
            // Preserve only the two HTTP Retry-After forms, never arbitrary server text.
            if (!preg_match('/^(?:[0-9]+|(?:Mon|Tue|Wed|Thu|Fri|Sat|Sun), [0-9]{2} [A-Z][a-z]{2} [0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2} GMT)$/D', $retryAfter)) {
                $retryAfter = null;
            }
            throw new TrackingApiException($category, $status, null, $retryAfter);
        }
        return $response;
    }

    private function envelope(string $operation): \DOMDocument
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $envelope = $document->createElementNS(self::SOAP_NS, 'soapenv:Envelope');
        $document->appendChild($envelope);
        $envelope->appendChild($document->createElementNS(self::SOAP_NS, 'soapenv:Header'));
        $body = $document->createElementNS(self::SOAP_NS, 'soapenv:Body');
        $envelope->appendChild($body);
        $body->appendChild($document->createElementNS(self::POST_NS, 'pos:' . $operation));
        return $document;
    }

    private function soap(\DOMDocument $document, string $expectedRoot, string $operation): \DOMElement
    {
        $response = $this->request('POST', $this->batchUrl, $operation, ['Content-Type' => 'text/xml; charset=utf-8'], [], $document->saveXML(), true);
        $status = $response->getStatusCode();
        $invalidCategory = $status === 500 ? TrackingApiException::SERVER : TrackingApiException::INVALID_RESPONSE;
        $xml = (string) $response->getBody();
        // DTD/entity declarations are not part of this protocol. Never resolve network resources/entities.
        if (stripos($xml, '<!DOCTYPE') !== false || stripos($xml, '<!ENTITY') !== false) {
            throw new TrackingApiException($invalidCategory, $status);
        }
        $result = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        try {
            $loaded = $xml !== '' && $result->loadXML($xml, LIBXML_NONET);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        if (!$loaded || $result->doctype !== null) {
            throw new TrackingApiException($invalidCategory, $status);
        }
        $envelope = $result->documentElement;
        if ($envelope === null || $envelope->localName !== 'Envelope' || $envelope->namespaceURI !== self::SOAP_NS) {
            throw new TrackingApiException($invalidCategory, $status);
        }
        $body = $this->child($envelope, 'Body', self::SOAP_NS);
        if ($body === null) throw new TrackingApiException($invalidCategory, $status);
        if ($this->child($body, 'Fault', self::SOAP_NS) !== null) {
            // Do not expose faultstring/detail or retry a Client fault as a transient HTTP failure.
            throw new TrackingApiException(TrackingApiException::SOAP_FAULT, $status);
        }
        if ($status === 500) {
            throw new TrackingApiException(TrackingApiException::SERVER, $status);
        }
        $root = $this->child($body, $expectedRoot, self::POST_NS);
        if ($root === null) throw new TrackingApiException(TrackingApiException::INVALID_RESPONSE);
        return $root;
    }

    private function throwCarrierError(\DOMElement $root): void
    {
        $error = $this->child($root, 'error');
        if ($error !== null) {
            $code = $this->errorCode($error);
            throw new TrackingApiException($this->carrierCategory($code), null, $code, $code === 6 ? '900' : null);
        }
    }
    private function errorCode(\DOMElement $error): int
    {
        $code = $error->getAttribute('ErrorTypeID');
        if (!ctype_digit($code)) throw new TrackingApiException(TrackingApiException::INVALID_RESPONSE);
        return (int) $code;
    }
    private function carrierCategory(int $code): string
    {
        $categories = [2 => TrackingApiException::VALIDATION, 3 => TrackingApiException::AUTH,
            6 => TrackingApiException::PENDING, 12 => TrackingApiException::NOT_FOUND,
            16 => TrackingApiException::SERVER, 17 => TrackingApiException::EXPIRED,
            18 => TrackingApiException::VALIDATION, 20 => TrackingApiException::VALIDATION,
            21 => TrackingApiException::VALIDATION, 22 => TrackingApiException::VALIDATION];
        return $categories[$code] ?? TrackingApiException::CARRIER;
    }
    /** @return \DOMElement[] */
    private function children(\DOMElement $parent, string $name, string $namespace = ''): array
    {
        $elements = [];
        foreach ($parent->childNodes as $node) {
            if ($node instanceof \DOMElement && $node->localName === $name && ($node->namespaceURI ?? '') === $namespace) {
                $elements[] = $node;
            }
        }
        return $elements;
    }
    private function child(\DOMElement $parent, string $name, string $namespace = ''): ?\DOMElement
    {
        $children = $this->children($parent, $name, $namespace);
        if (count($children) > 1) throw new TrackingApiException(TrackingApiException::INVALID_RESPONSE);
        return $children[0] ?? null;
    }
    private function attributes(\DOMElement $element): array
    {
        $result = [];
        foreach ($element->attributes as $attribute) $result[$attribute->name] = $attribute->value;
        return $result;
    }
    /** Do not silently turn a changed namespace/schema into a successful empty result. */
    private function validateChildren(\DOMElement $parent, array $names, string $namespace): void
    {
        foreach ($parent->childNodes as $node) {
            if ($node instanceof \DOMElement && (!in_array($node->localName, $names, true) || $node->namespaceURI !== $namespace)) {
                throw new TrackingApiException(TrackingApiException::INVALID_RESPONSE);
            }
        }
    }
    private function validateBarcodes(array $barcodes): void
    {
        foreach ($barcodes as $barcode) {
            if (!is_string($barcode) || !preg_match('/^(?:[0-9]{14}|[A-Z]{2}[0-9]{9}[A-Z]{2})$/D', $barcode)) {
                throw new \InvalidArgumentException('Expected a 14-digit barcode or a 13-character S10 barcode.');
            }
        }
    }
    private function validateLanguage(string $language, array $allowed): void
    {
        if (!in_array($language, $allowed, true)) throw new \InvalidArgumentException('Unsupported tracking language.');
    }
    private function endpoint($url): string
    {
        $parts = is_string($url) ? parse_url($url) : false;
        if ($parts === false || !isset($parts['scheme'], $parts['host'])
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])
            || ($parts['scheme'] !== 'https' && !($parts['scheme'] === 'http' && in_array($parts['host'], ['localhost', '127.0.0.1', '[::1]'], true)))) {
            throw new \InvalidArgumentException('Tracking endpoint must use HTTPS (HTTP is allowed only for loopback tests), without userinfo, query or fragment.');
        }
        return $url;
    }
    private function requireBatch(): void
    {
        if (!$this->batchEnabled) throw new \LogicException('Batch requires explicit tracking.batch_enabled=true; see docs/tracking-api.md for unresolved carrier contract requirements.');
    }
}

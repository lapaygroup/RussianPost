<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Exceptions;

/** Safe metadata only: no request, response, credentials or chained transport exception. */
class TrackingApiException extends TrackingException
{
    public const AUTH = 'auth';
    public const RATE_LIMIT = 'rate_limit';
    public const TRANSPORT = 'transport';
    public const SERVER = 'server';
    public const HTTP = 'http';
    public const INVALID_RESPONSE = 'invalid_response';
    public const VALIDATION = 'validation';
    public const PENDING = 'pending';
    public const EXPIRED = 'expired';
    public const NOT_FOUND = 'not_found';
    public const CARRIER = 'carrier';
    public const SOAP_FAULT = 'soap_fault';

    private string $category;
    private ?int $httpStatus;
    private ?int $carrierCode;
    private ?string $retryAfter;

    public function __construct(string $category, ?int $httpStatus = null, ?int $carrierCode = null, ?string $retryAfter = null)
    {
        parent::__construct('Russian Post Tracking API: ' . $category);
        $this->category = $category;
        $this->httpStatus = $httpStatus;
        $this->carrierCode = $carrierCode;
        $this->retryAfter = $retryAfter;
    }

    public function getCategory(): string { return $this->category; }
    public function getHttpStatus(): ?int { return $this->httpStatus; }
    public function getCarrierCode(): ?int { return $this->carrierCode; }
    public function getRetryAfter(): ?string { return $this->retryAfter; }
    public function isTransient(): bool
    {
        return in_array($this->category, [self::RATE_LIMIT, self::TRANSPORT, self::SERVER, self::PENDING], true);
    }
}

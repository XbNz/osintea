<?php

declare(strict_types=1);

namespace XbNz\Shared;

use XbNz\Shared\Exception\InvalidIpAddressException;
use XbNz\Shared\ValueObjects\IpType;

final class IpValidator
{
    public function __construct(
        private readonly string $ip
    ) {}

    public static function make(string $ip): self
    {
        return new self($ip);
    }

    public function assertValid(): self
    {
        if (filter_var($this->ip, FILTER_VALIDATE_IP) === false) {
            throw InvalidIpAddressException::invalid($this->ip);
        }

        return $this;
    }

    public function assertV4(): self
    {
        if (filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
            throw InvalidIpAddressException::invalidV4($this->ip);
        }

        return $this;
    }

    public function assertV6(): self
    {
        if (filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
            throw InvalidIpAddressException::invalidV6($this->ip);
        }

        return $this;
    }

    public function assertNotPublic(): self
    {
        if (filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
            throw InvalidIpAddressException::invalidNonPublic($this->ip);
        }

        return $this;
    }

    public function assertPublic(): self
    {
        if (filter_var(
            $this->ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false) {
            throw InvalidIpAddressException::invalidPublic($this->ip);
        }

        return $this;
    }

    public function determineType(): IpType
    {
        if (filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            return IpType::IPv4;
        }

        if (filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
            return IpType::IPv6;
        }

        throw InvalidIpAddressException::invalid($this->ip);
    }
}

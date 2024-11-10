<?php

declare(strict_types=1);

namespace XbNz\Shared\Exception;

use Exception;

final class InvalidIpAddressException extends Exception
{
    public static function invalid(string $ip): self
    {
        return new self("Invalid IP address: {$ip}");
    }

    public static function invalidV4(string $ip): self
    {
        return new self("Invalid IPv4 address: {$ip}");
    }

    public static function invalidV6(string $ip): self
    {
        return new self("Invalid IPv6 address: {$ip}");
    }

    public static function invalidNonPublic(string $ip): self
    {
        return new self("Invalid private IP address: {$ip}");
    }

    public static function invalidPublic(string $ip): self
    {
        return new self("Invalid public IP address: {$ip}");
    }
}

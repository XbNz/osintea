<?php

declare(strict_types=1);

namespace XbNz\RouteviewsIntegration;

use MaxMind\Db\Reader;
use Psl\Type;
use XbNz\Asn\Contracts\IpToAsnInterface;
use XbNz\Asn\ValueObject\Asn;
use XbNz\Shared\IpValidator;
use XbNz\Shared\ValueObjects\IpType;

final class RouteViewsIpToAsn implements IpToAsnInterface
{
    public function __construct(
        private readonly Reader $ipv4Reader,
        private readonly Reader $ipv6Reader
    ) {}

    public function execute(string $ip): Asn
    {
        IpValidator::make($ip)->assertPublic();

        $type = IpValidator::make($ip)->determineType();

        $reader = match ($type) {
            IpType::IPv4 => $this->ipv4Reader,
            IpType::IPv6 => $this->ipv6Reader,
        };

        $sanitized = Type\shape([
            'autonomous_system_number' => Type\positive_int(),
            'autonomous_system_organization' => Type\non_empty_string(),
        ])->coerce($reader->get($ip));

        return new Asn(
            $sanitized['autonomous_system_organization'],
            $sanitized['autonomous_system_number']
        );
    }
}
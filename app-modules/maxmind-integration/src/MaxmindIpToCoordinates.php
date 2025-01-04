<?php

declare(strict_types=1);

namespace XbNz\MaxmindIntegration;

use MaxMind\Db\Reader;
use Psl\Type;
use XbNz\Location\Contracts\IpToCoordinatesInterface;
use XbNz\Location\Enums\Provider;
use XbNz\Shared\IpValidator;
use XbNz\Shared\ValueObjects\Coordinates;
use XbNz\Shared\ValueObjects\IpType;

final class MaxmindIpToCoordinates implements IpToCoordinatesInterface
{
    public function __construct(
        private readonly Reader $ipv4Reader,
        private readonly Reader $ipv6Reader
    ) {}

    public function execute(string $ip): ?Coordinates
    {
        $ipType = IpValidator::make($ip)
            ->assertValid()
            ->assertPublic()
            ->determineType();

        $reader = match ($ipType) {
            IpType::IPv4 => $this->ipv4Reader,
            IpType::IPv6 => $this->ipv6Reader,
        };

        $locationInfo = $reader->get($ip);

        if ($locationInfo === null) {
            return null;
        }

        if (array_key_exists('latitude', $locationInfo) === false) {
            return null;
        }

        if (array_key_exists('longitude', $locationInfo) === false) {
            return null;
        }

        $sanitized = Type\shape([
            'latitude' => Type\float(),
            'longitude' => Type\float(),
        ])->coerce($locationInfo);

        return new Coordinates(
            $sanitized['latitude'],
            $sanitized['longitude']
        );
    }

    public function supports(Provider $provider): bool
    {
        return $provider === Provider::Maxmind;
    }
}

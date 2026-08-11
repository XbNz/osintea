<?php

declare(strict_types=1);

namespace XbNz\Preferences\Support;

use Illuminate\Support\Collection;

final class NetworkAdapters
{
    /**
     * @param  array<string, array<string, mixed>>|null  $interfaces
     */
    public function __construct(private readonly ?array $interfaces = null) {}

    /**
     * @return Collection<string, string>
     */
    public function options(): Collection
    {
        $interfaces = $this->interfaces ?? net_get_interfaces();

        if ($interfaces === false) {
            return collect();
        }

        return collect($interfaces)
            ->mapWithKeys(function (array $interface, string $name): array {
                $addresses = [];
                $unicastAddresses = $interface['unicast'] ?? [];

                if ( ! is_array($unicastAddresses)) {
                    $unicastAddresses = [];
                }

                foreach ($unicastAddresses as $unicast) {
                    if (is_array($unicast) && isset($unicast['address']) && is_string($unicast['address'])) {
                        $addresses[$unicast['address']] = $unicast['address'];
                    }
                }

                $label = $addresses === []
                    ? $name
                    : sprintf('%s (%s)', $name, implode(', ', $addresses));

                return [$name => $label];
            })
            ->sortKeys();
    }
}

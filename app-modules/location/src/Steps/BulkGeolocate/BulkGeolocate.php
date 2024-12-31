<?php

declare(strict_types=1);

namespace XbNz\Location\Steps\BulkGeolocate;

use Illuminate\Container\Attributes\Tag;
use Illuminate\Container\RewindableGenerator;
use XbNz\Location\Contracts\IpToCoordinatesInterface;

final class BulkGeolocate
{
    /**
     * @param  RewindableGenerator<int, IpToCoordinatesInterface>  $providers
     */
    public function __construct(
        #[Tag('ip-to-coordinates')]
        private readonly RewindableGenerator $providers,
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        $this->bulkPingAction->handle($transporter->ipAddressDtos);

        return $transporter;
    }
}

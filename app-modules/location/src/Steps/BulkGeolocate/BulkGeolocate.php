<?php

declare(strict_types=1);

namespace XbNz\Location\Steps\BulkGeolocate;

use Illuminate\Container\Attributes\Tag;
use Illuminate\Container\RewindableGenerator;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Location\Actions\CreateCoordinatesAction;
use XbNz\Location\Contracts\IpToCoordinatesInterface;
use XbNz\Location\DTOs\CreateCoordinatesDto;
use XbNz\Location\Models\Coordinates as CoordinatesModel;

final class BulkGeolocate
{
    /**
     * @param  RewindableGenerator<int, IpToCoordinatesInterface>  $providers
     */
    public function __construct(
        #[Tag('ip-to-coordinates')]
        private readonly RewindableGenerator $providers,
        private readonly CreateCoordinatesAction $createCoordinatesAction,
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        $ipToCoordinates = collect(iterator_to_array($this->providers))
            ->filter(fn (IpToCoordinatesInterface $ipToCoordinates) => $ipToCoordinates->supports($transporter->provider))
            ->sole();

        $transporter->ipAddressDtos
            ->each(function (IpAddressDto $ipAddressDto) use ($ipToCoordinates): void {
                $coordinates = $ipToCoordinates->execute($ipAddressDto->ip);

                if ($coordinates === null) {
                    return;
                }

                if (CoordinatesModel::query()->where('ip_address_id', $ipAddressDto->id)->exists()) {
                    return;
                }

                $this->createCoordinatesAction->handle(new CreateCoordinatesDto(
                    $ipAddressDto,
                    $coordinates
                ));
            });

        return $transporter;
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Asn\Steps\BulkAsnLookup;

use Illuminate\Container\Attributes\Tag;
use Illuminate\Container\RewindableGenerator;
use Illuminate\Support\Collection;
use XbNz\Asn\Actions\CreateAsnAction;
use XbNz\Asn\Contracts\IpToAsnInterface;
use XbNz\Asn\DTOs\CreateAsnDto;
use XbNz\Asn\Model\Asn as AsnModel;
use XbNz\Ip\DTOs\IpAddressDto;

final class BulkAsnLookup
{
    /**
     * @param  RewindableGenerator<int, IpToAsnInterface>  $providers
     */
    public function __construct(
        #[Tag('ip-to-asn')]
        private readonly RewindableGenerator $providers,
        private readonly CreateAsnAction $createAsnAction,
    ) {}

    public function handle(Transporter $transporter): Transporter
    {
        $ipToAsn = Collection::make(iterator_to_array($this->providers))
            ->filter(fn (IpToAsnInterface $ipToAsn) => $ipToAsn->supports($transporter->provider))
            ->sole();

        $transporter->ipAddressDtos
            ->each(function (IpAddressDto $ipAddressDto) use ($ipToAsn): void {
                $asInfo = $ipToAsn->execute($ipAddressDto->ip);

                if ($asInfo === null) {
                    return;
                }

                if (AsnModel::query()->where('ip_address_id', $ipAddressDto->id)->exists()) {
                    return;
                }

                $this->createAsnAction->handle(new CreateAsnDto(
                    $ipAddressDto,
                    $asInfo
                ));
            });

        return $transporter;
    }
}

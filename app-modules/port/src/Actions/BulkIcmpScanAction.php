<?php

declare(strict_types=1);

namespace XbNz\Port\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Ip\Models\IpAddress;
use XbNz\Masscan\Contracts\MasscanIcmpScannerInterface;
use XbNz\Port\DTOs\CreatePortDto;
use XbNz\Port\DTOs\PortScanResultDto;
use XbNz\Preferences\Models\MasscanPreferences;
use XbNz\Shared\Enums\ProtocolType;
use XbNz\Shared\ValueObjects\Port;

final class BulkIcmpScanAction
{
    private string $inputFile;

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly MasscanIcmpScannerInterface $masscanIcmpScanner,
        private readonly CreatePortAction $createPortAction,
    ) {
        $this->inputFile = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('input_'.Str::random(5).'.txt');

        touch($this->inputFile);
    }

    /**
     * @param  Collection<int, IpAddressDto>  $ipAddressDtos
     */
    public function handle(Collection $ipAddressDtos): void
    {
        $activeMasscanPreferences = MasscanPreferences::query()
            ->where('enabled', true)
            ->sole()
            ->getData();

        $this->filesystem
            ->put(
                $this->inputFile,
                $ipAddressDtos->pluck('ip')->implode(PHP_EOL)
            );

        $masscanIcmp = $this->masscanIcmpScanner->inputFilePath($this->inputFile)
            ->rate($activeMasscanPreferences->rate)
            ->retries($activeMasscanPreferences->retries)
            ->timeToLive($activeMasscanPreferences->ttl);

        if ($activeMasscanPreferences->adapter !== null) {
            $masscanIcmp->adapter($activeMasscanPreferences->adapter);
        }

        Collection::make($masscanIcmp->execute())
            ->each(function (PortScanResultDto $portScanResultDto): void {
                $ipAddress = IpAddress::query()->where('ip', $portScanResultDto->ip)->first()?->getData();

                if ($ipAddress === null) {
                    return;
                }

                foreach ($portScanResultDto->ports as $port => $state) {
                    $this->createPortAction->handle(
                        new CreatePortDto(
                            $ipAddress,
                            new Port($port, ProtocolType::ICMP),
                            $state,
                            CarbonImmutable::now()
                        )
                    );
                }
            });
    }

    public function __destruct()
    {
        $this->filesystem->delete($this->inputFile);
    }
}

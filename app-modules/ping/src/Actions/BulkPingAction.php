<?php

declare(strict_types=1);

namespace XbNz\Ping\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use XbNz\Fping\Contracts\FpingInterface;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ping\DTOs\CreatePingSequenceDto;
use XbNz\Ping\DTOs\PingResultDto;
use XbNz\Ping\ValueObjects\Sequence;
use XbNz\Preferences\Models\FpingPreferences;

final class BulkPingAction
{
    private string $inputFile;

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly FpingInterface $fping,
        private readonly CreatePingSequenceAction $createPingSequenceAction,
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
        $activeFpingPreferences = FpingPreferences::query()
            ->where('enabled', true)
            ->sole()
            ->getData();

        $this->filesystem
            ->put(
                $this->inputFile,
                $ipAddressDtos->pluck('ip')->implode(PHP_EOL)
            );

        Collection::make($this->fping->inputFilePath($this->inputFile)
            ->size($activeFpingPreferences->size)
            ->backoffFactor($activeFpingPreferences->backoff)
            ->count($activeFpingPreferences->count)
            ->timeToLive($activeFpingPreferences->ttl)
            ->interval($activeFpingPreferences->interval)
            ->intervalPerHost($activeFpingPreferences->interval_per_target)
            ->typeOfService($activeFpingPreferences->type_of_service)
            ->retries($activeFpingPreferences->retries)
            ->timeout($activeFpingPreferences->timeout)
            ->sendRandomData($activeFpingPreferences->send_random_data)
            ->execute())
            ->each(function (PingResultDto $pingResultDto): void {
                Collection::make($pingResultDto->sequences)
                    ->each(function (Sequence $sequence) use ($pingResultDto): void {
                        $ipAddress = IpAddress::query()->where('ip', $pingResultDto->ip)->first()?->getData();

                        if ($ipAddress === null) {
                            return;
                        }

                        $this->createPingSequenceAction->handle(
                            new CreatePingSequenceDto(
                                $ipAddress,
                                $sequence,
                                CarbonImmutable::now()
                            )
                        );
                    });
            });
    }

    public function __destruct()
    {
        $this->filesystem->delete($this->inputFile);
    }
}

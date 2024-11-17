<?php

declare(strict_types=1);

namespace XbNz\Ping\Jobs;

use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use React\EventLoop\Factory;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use XbNz\Fping\Contracts\FpingInterface;
use XbNz\Ip\Actions\ImportIpAddressesAction;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ping\Actions\CreatePingSequenceAction;
use XbNz\Ping\DTOs\CreatePingSequenceDto;

final class PingJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly string $target,
        private readonly int $timeBetweenRequests,
    ) {}

    public function handle(
        CreatePingSequenceAction $createPingSequenceAction,
        ImportIpAddressesAction $importIpAddressesAction,
    ): void {
        $ip = $this->target;
        $loop = Factory::create();

        // Run the ping command every 1 second
        $loop->addPeriodicTimer(1, function () use ($ip, $createPingSequenceAction, $importIpAddressesAction): void {
            $temporaryFilePath = TemporaryDirectory::make()
                ->force()
                ->create()
                ->path('ping.txt');

            file_put_contents($temporaryFilePath, $ip);

            $pingResultDto = App::make(FpingInterface::class)
                ->inputFilePath($temporaryFilePath)
                ->count(1)
                ->intervalPerHost(1)
                ->execute()[0];

            $importIpAddressesAction->handle($temporaryFilePath);

            $createPingSequenceAction->handle(
                new CreatePingSequenceDto(
                    IpAddress::query()->where('ip', $ip)->sole()->getData(),
                    $pingResultDto->sequences[0],
                    CarbonImmutable::now(),
                )
            );
        });

        $loop->addTimer(30, function () use ($loop): void {
            $loop->stop();
        });

        $loop->run();
    }
}

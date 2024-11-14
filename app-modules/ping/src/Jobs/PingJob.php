<?php

declare(strict_types=1);

namespace XbNz\Ping\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use React\EventLoop\Factory;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use XbNz\Fping\Contracts\FpingInterface;
use XbNz\Ping\Events\PingUpdateEvent;

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

    public function handle(Dispatcher $dispatcher): void
    {
        $ip = $this->target;
        $loop = Factory::create();

        // Run the ping command every 1 second
        $loop->addPeriodicTimer(1, function () use ($ip, $dispatcher): void {
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

            $dispatcher->dispatch(new PingUpdateEvent($pingResultDto->toArray()));
        });

        // Stop the loop after 30 seconds (optional)
        $loop->addTimer(30, function () use ($loop): void {
            $loop->stop();
        });

        $loop->run();
    }
}

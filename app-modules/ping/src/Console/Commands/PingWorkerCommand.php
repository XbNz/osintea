<?php

declare(strict_types=1);

namespace XbNz\Ping\Console\Commands;

use Carbon\CarbonImmutable;
use Chefhasteeth\Pipeline\Pipeline;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use React\EventLoop\StreamSelectLoop;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ping\Actions\FulfillSequenceAction;
use XbNz\Ping\DTOs\AddTargetRequestDto;
use XbNz\Ping\Models\PingSequence;
use XbNz\Ping\Steps\LoopingStandardInPingWorker\GatherTargetToAdd;
use XbNz\Ping\Steps\LoopingStandardInPingWorker\GatherTargetToRemove;
use XbNz\Ping\Steps\LoopingStandardInPingWorker\Transporter;

final class PingWorkerCommand extends Command
{
    public function __construct(
        private readonly FulfillSequenceAction $fulfillSequenceAction,
        private readonly StreamSelectLoop $loop,
    ) {
        parent::__construct();
    }

    protected $signature = 'ping:work';

    protected $description = 'Command description';

    public function handle(): void
    {
        $input = new ReadableResourceStream(STDIN, $this->loop);
        $output = new WritableResourceStream(STDOUT, $this->loop);

        $requests = [];

        $input->on('data', function (string $data) use (&$requests): void {
            $pipes = [
                GatherTargetToAdd::class,
                GatherTargetToRemove::class,
            ];

            $transporter = new Transporter(trim($data));

            /** @var Transporter $transporterReturned */
            $transporterReturned = Pipeline::make()
                ->send($transporter)
                ->through($pipes)
                ->thenReturn();

            if ($transporterReturned->addTargetRequestDto !== null) {
                $requests[] = $transporterReturned->addTargetRequestDto;
            }

            if ($transporterReturned->removeTargetRequestDto !== null) {
                $requests = array_filter(
                    $requests,
                    fn (AddTargetRequestDto $request) => $request->target !== $transporterReturned->removeTargetRequestDto->target
                );
            }

            $requests = Collection::make($requests)->unique('target')->toArray();
        });

        $this->loop->addPeriodicTimer(0.1, function () use (&$requests): void {
            Collection::make($requests)
                ->filter(function (AddTargetRequestDto $addTargetRequestDto) {
                    $pingSequenceDto = PingSequence::query()
                        ->whereHas('ipAddress', fn (Builder $query) => $query->where('ip', $addTargetRequestDto->target))
                        ->latest()
                        ->first()
                        ?->getData();

                    if ($pingSequenceDto === null) {
                        return true;
                    }

                    return $pingSequenceDto->created_at->diffInMilliseconds(CarbonImmutable::now()) >= $addTargetRequestDto->interval;
                })
                ->map(fn (AddTargetRequestDto $addTargetRequestDto) => IpAddress::query()
                    ->where('ip', $addTargetRequestDto->target)
                    ->sole()
                    ->getData()
                )
                ->each(fn (IpAddressDto $ipAddressDto) => $this->fulfillSequenceAction->handle($ipAddressDto));
        });
    }
}

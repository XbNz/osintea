<?php

declare(strict_types=1);

namespace XbNz\Ping\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use XbNz\Ping\DTOs\CreatePingSequenceDto;
use XbNz\Ping\DTOs\PingSequenceDto;
use XbNz\Ping\Events\PingSequenceInsertedEvent;
use XbNz\Ping\Models\PingSequence;

final class CreatePingSequenceAction
{
    public function __construct(
        private readonly Dispatcher $dispatcher
    ) {}

    public function handle(CreatePingSequenceDto $dto): PingSequenceDto
    {
        $pingSequence = PingSequence::query()
            ->create([
                'ip_address_id' => $dto->ip_address_dto->ip,
                'round_trip_time' => $dto->sequence->roundTripTime,
                'loss' => $dto->sequence->lost,
                'created_at' => $dto->created_at->format('U.u'),
            ])
            ->getData();

        $this->dispatcher->dispatch(new PingSequenceInsertedEvent($pingSequence));

        return $pingSequence;
    }
}

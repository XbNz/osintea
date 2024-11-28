<?php

declare(strict_types=1);

namespace XbNz\Ping\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use XbNz\Ping\DTOs\PingSequenceDto;
use XbNz\Ping\Events\PingSequenceDeletedEvent;
use XbNz\Ping\Models\PingSequence;

final class DeletePingSequenceAction
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function handle(PingSequenceDto $pingSequenceDto): PingSequenceDto
    {
        PingSequence::query()->findOrFail($pingSequenceDto->id)->deleteOrFail();

        $this->dispatcher->dispatch(new PingSequenceDeletedEvent($pingSequenceDto));

        return $pingSequenceDto;
    }
}

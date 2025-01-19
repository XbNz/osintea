<?php

declare(strict_types=1);

namespace XbNz\Port\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use XbNz\Port\DTOs\PortDto;
use XbNz\Port\Events\PortDeletedEvent;
use XbNz\Port\Models\Port;

final class DeletePortAction
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function handle(PortDto $port): PortDto
    {
        Port::query()->findOrFail($port->id)->deleteOrFail();

        $this->dispatcher->dispatch(new PortDeletedEvent($port));

        return $port;
    }
}

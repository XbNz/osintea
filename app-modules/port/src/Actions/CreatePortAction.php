<?php

declare(strict_types=1);

namespace XbNz\Port\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use XbNz\Port\DTOs\CreatePortDto;
use XbNz\Port\DTOs\PortDto;
use XbNz\Port\Events\PortInsertedEvent;
use XbNz\Port\Models\Port;

final class CreatePortAction
{
    public function __construct(
        private readonly Dispatcher $dispatcher
    ) {}

    public function handle(CreatePortDto $dto): PortDto
    {
        $port = Port::query()
            ->create([
                'ip_address_id' => $dto->ip_address_dto->id,
                'port' => $dto->port->port,
                'protocol' => $dto->port->protocol->value,
                'state' => $dto->state->value,
                'created_at' => $dto->created_at->format('U.u'),
            ])
            ->getData();

        $this->dispatcher->dispatch(new PortInsertedEvent($port));

        return $port;
    }
}

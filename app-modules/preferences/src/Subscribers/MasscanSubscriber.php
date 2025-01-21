<?php

declare(strict_types=1);

namespace XbNz\Preferences\Subscribers;

use Illuminate\Contracts\Bus\Dispatcher;
use XbNz\Preferences\Events\Intentions\CreateMasscanPreferencesIntention;
use XbNz\Preferences\Events\Intentions\DeleteMasscanPreferencesIntention;
use XbNz\Preferences\Events\Intentions\EnableMasscanPreferencesIntention;
use XbNz\Preferences\Events\Intentions\UpdateMasscanPreferencesIntention;
use XbNz\Preferences\Jobs\CreateMasscanPreferencesJob;
use XbNz\Preferences\Jobs\DeleteMasscanPreferencesJob;
use XbNz\Preferences\Jobs\EnableMasscanPreferencesJob;
use XbNz\Preferences\Jobs\UpdateMasscanPreferencesJob;
use XbNz\Shared\Attributes\ListensTo;

final class MasscanSubscriber
{
    public function __construct(
        private readonly Dispatcher $dispatcher
    ) {}

    #[ListensTo(CreateMasscanPreferencesIntention::class)]
    public function onCreateMasscanPreferencesIntention(CreateMasscanPreferencesIntention $intention): void
    {
        $this->dispatcher->dispatchSync(new CreateMasscanPreferencesJob($intention->dto));
    }

    #[ListensTo(UpdateMasscanPreferencesIntention::class)]
    public function onUpdateMasscanPreferencesIntention(UpdateMasscanPreferencesIntention $intention): void
    {
        $this->dispatcher->dispatchSync(new UpdateMasscanPreferencesJob($intention->dto));
    }

    #[ListensTo(EnableMasscanPreferencesIntention::class)]
    public function onEnableMasscanPreferencesIntention(EnableMasscanPreferencesIntention $intention): void
    {
        $this->dispatcher->dispatchSync(new EnableMasscanPreferencesJob($intention->record));
    }

    #[ListensTo(DeleteMasscanPreferencesIntention::class)]
    public function onDeleteMasscanPreferencesIntention(DeleteMasscanPreferencesIntention $intention): void
    {
        $this->dispatcher->dispatchSync(new DeleteMasscanPreferencesJob($intention->record));
    }
}

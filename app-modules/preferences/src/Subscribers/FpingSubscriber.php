<?php

declare(strict_types=1);

namespace XbNz\Preferences\Subscribers;

use Illuminate\Contracts\Bus\Dispatcher;
use XbNz\Preferences\Events\Intentions\CreateFpingPreferencesIntention;
use XbNz\Preferences\Events\Intentions\DeleteFpingPreferencesIntention;
use XbNz\Preferences\Events\Intentions\EnableFpingPreferencesIntention;
use XbNz\Preferences\Events\Intentions\UpdateFpingPreferencesIntention;
use XbNz\Preferences\Jobs\CreateFpingPreferencesJob;
use XbNz\Preferences\Jobs\DeleteFpingPreferencesJob;
use XbNz\Preferences\Jobs\EnableFpingPreferencesJob;
use XbNz\Preferences\Jobs\UpdateFpingPreferencesJob;
use XbNz\Shared\Attributes\ListensTo;

final class FpingSubscriber
{
    public function __construct(
        private readonly Dispatcher $dispatcher
    ) {}

    #[ListensTo(CreateFpingPreferencesIntention::class)]
    public function onCreateFpingPreferencesIntention(CreateFpingPreferencesIntention $intention): void
    {
        $this->dispatcher->dispatchSync(new CreateFpingPreferencesJob($intention->dto));
    }

    #[ListensTo(UpdateFpingPreferencesIntention::class)]
    public function onUpdateFpingPreferencesIntention(UpdateFpingPreferencesIntention $intention): void
    {
        $this->dispatcher->dispatchSync(new UpdateFpingPreferencesJob($intention->dto));
    }

    #[ListensTo(EnableFpingPreferencesIntention::class)]
    public function onEnableFpingPreferencesIntention(EnableFpingPreferencesIntention $intention): void
    {
        $this->dispatcher->dispatchSync(new EnableFpingPreferencesJob($intention->record));
    }

    #[ListensTo(DeleteFpingPreferencesIntention::class)]
    public function onDeleteFpingPreferencesIntention(DeleteFpingPreferencesIntention $intention): void
    {
        $this->dispatcher->dispatchSync(new DeleteFpingPreferencesJob($intention->record));
    }
}

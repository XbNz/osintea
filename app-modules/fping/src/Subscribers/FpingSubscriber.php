<?php

declare(strict_types=1);

namespace XbNz\Fping\Subscribers;

use Illuminate\Contracts\Bus\Dispatcher;
use XbNz\Fping\Events\Intentions\CreateFpingPreferencesIntention;
use XbNz\Fping\Events\Intentions\DeleteFpingPreferencesIntention;
use XbNz\Fping\Events\Intentions\EnableFpingPreferencesIntention;
use XbNz\Fping\Events\Intentions\UpdateFpingPreferencesIntention;
use XbNz\Fping\Jobs\CreateFpingPreferencesJob;
use XbNz\Fping\Jobs\DeleteFpingPreferencesJob;
use XbNz\Fping\Jobs\EnableFpingPreferencesJob;
use XbNz\Fping\Jobs\UpdateFpingPreferencesJob;
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

<?php

declare(strict_types=1);

namespace XbNz\Fping\Subscribers;

use Illuminate\Contracts\Bus\Dispatcher;
use XbNz\Fping\Events\Intentions\CreateFpingPreferencesIntention;
use XbNz\Fping\Events\Intentions\UpdateFpingPreferencesIntention;
use XbNz\Fping\Jobs\CreateFpingPreferencesJob;
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
        $this->dispatcher->dispatch(new CreateFpingPreferencesJob($intention->dto));
    }

    #[ListensTo(UpdateFpingPreferencesIntention::class)]
    public function onUpdateFpingPreferencesIntention(UpdateFpingPreferencesIntention $intention): void
    {
        $this->dispatcher->dispatch(new UpdateFpingPreferencesJob($intention->dto));
    }
}

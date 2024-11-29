<?php

declare(strict_types=1);

namespace Tests\Feature\Subscribers\NativePhpSubscriber;

use Illuminate\Contracts\Events\Dispatcher;
use Native\Laravel\Events\Windows\WindowClosed;
use Native\Laravel\Facades\GlobalShortcut;
use Tests\TestCase;

final class OnWindowClosedTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_unregisters_the_hotkey_for_the_preferences_page(): void
    {
        // Arrange
        GlobalShortcut::fake();

        // Act
        $this->app->make(Dispatcher::class)->dispatch(new WindowClosed('doesnt-matter'));

        // Assert
        GlobalShortcut::assertKey('CmdOrCtrl+,');
        GlobalShortcut::assertUnregisteredCount(1);
    }
}

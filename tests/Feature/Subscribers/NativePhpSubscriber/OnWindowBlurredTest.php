<?php

declare(strict_types=1);

namespace Tests\Feature\Subscribers\NativePhpSubscriber;

use Illuminate\Contracts\Events\Dispatcher;
use Native\Desktop\Events\Windows\WindowBlurred;
use Native\Desktop\Facades\GlobalShortcut;
use Tests\TestCase;

final class OnWindowBlurredTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_unregisters_the_hotkey_for_the_preferences_page(): void
    {
        // Arrange
        GlobalShortcut::fake();

        // Act
        $this->app->make(Dispatcher::class)->dispatch(new WindowBlurred('doesnt-matter'));

        // Assert
        GlobalShortcut::assertKey('CmdOrCtrl+,');
        GlobalShortcut::assertUnregisteredCount(1);
    }
}

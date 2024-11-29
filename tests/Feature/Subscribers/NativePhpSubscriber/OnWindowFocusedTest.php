<?php

declare(strict_types=1);

namespace Tests\Feature\Subscribers\NativePhpSubscriber;

use App\Events\OpenPreferencesEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Native\Laravel\Events\Windows\WindowFocused;
use Native\Laravel\Facades\GlobalShortcut;
use Tests\TestCase;

final class OnWindowFocusedTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_registers_a_hotkey_for_the_preferences_page(): void
    {
        // Arrange
        GlobalShortcut::fake();

        // Act
        $this->app->make(Dispatcher::class)->dispatch(new WindowFocused('doesnt-matter'));

        // Assert
        GlobalShortcut::assertKey('CmdOrCtrl+,');
        GlobalShortcut::assertRegisteredCount(1);
        GlobalShortcut::assertEvent(OpenPreferencesEvent::class);
    }
}

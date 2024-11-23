<?php

declare(strict_types=1);

namespace Tests\Feature\Subscribers\NativePhpSubscriber;

use App\Events\OpenCommandPaletteEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Http;
use Mockery;
use Native\Laravel\Facades\Window;
use Native\Laravel\Windows\Window as WindowImplementation;
use Tests\TestCase;
use XbNz\Shared\Enums\NativePhpWindow;

final class OnOpenCommandPaletteTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_opens_the_search_command_palette(): void
    {
        // Arrange
        Http::fake();
        Window::fake();
        Window::alwaysReturnWindows([
            $mockWindow = Mockery::mock(WindowImplementation::class)->makePartial(),
        ]);

        // Act
        $this->app->make(Dispatcher::class)->dispatch(new OpenCommandPaletteEvent());

        // Assert
        Window::assertOpened(NativePhpWindow::CommandPalette->value);
        $mockWindow->shouldHaveReceived('route')->once()->with('search');
    }
}

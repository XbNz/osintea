<?php

namespace Tests\Feature\Subscribers\NativePhpSubscriber;

use App\Events\OpenCommandPaletteEvent;
use App\Subscribers\NativePhpSubscriber;
use Illuminate\Contracts\Events\Dispatcher;
use Native\Laravel\Facades\Window;
use Native\Laravel\Windows\Window as WindowClass;
use Tests\TestCase;
use XbNz\Shared\Enums\NativePhpWindow;

class OnOpenCommandPaletteTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_opens_the_search_command_palette(): void
    {
        // Arrange
        $fakeWindow = new WindowClass(NativePhpWindow::CommandPalette->value);
        Window::shouldReceive('open')
            ->once()
            ->andReturn($fakeWindow);

        // Act
        $this->app->make(Dispatcher::class)->dispatch(new OpenCommandPaletteEvent);

        // Assert
        $this->assertSame(route('search'), invade($fakeWindow)->url);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Search;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Mockery;
use Native\Laravel\Facades\Window;
use Native\Laravel\Windows\Window as WindowImplementation;
use Tests\TestCase;

final class SearchTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders(): void
    {
        Livewire::test(Search::class)
            ->assertOk();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_opens_a_new_nativephp_ping_window(): void
    {
        // Arrange
        Http::fake();
        Window::fake();
        Window::alwaysReturnWindows([
            $mockWindow = Mockery::mock(WindowImplementation::class)->makePartial(),
        ]);

        $mockWindow->shouldReceive('route')->once()->with('ping')->andReturnSelf();
        $mockWindow->shouldReceive('showDevTools')->once()->with(false)->andReturnSelf();
        $mockWindow->shouldReceive('titleBarHiddenInset')->once()->andReturnSelf();
        $mockWindow->shouldReceive('transparent')->once()->andReturnSelf();
        $mockWindow->shouldReceive('height')->once()->with(500)->andReturnSelf();
        $mockWindow->shouldReceive('width')->once()->with(775)->andReturnSelf();
        $mockWindow->shouldReceive('minHeight')->once()->with(500)->andReturnSelf();
        $mockWindow->shouldReceive('minWidth')->once()->with(775)->andReturnSelf();

        // Act
        Livewire::test(Search::class)->call('openPing');

        // Assert
        Window::assertOpened(fn (string $windowId) => Str::startsWith($windowId, ['ping']));
    }
}
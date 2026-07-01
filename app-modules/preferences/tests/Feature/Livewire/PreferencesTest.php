<?php

declare(strict_types=1);

namespace XbNz\Preferences\Tests\Feature\Livewire;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use XbNz\Preferences\Livewire\Preferences;
use XbNz\Preferences\Models\FpingPreferences;
use XbNz\Preferences\Models\MasscanPreferences;

final class PreferencesTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_nested_preferences_components(): void
    {
        // Arrange
        FpingPreferences::factory()->create(['enabled' => true]);
        MasscanPreferences::factory()->create(['enabled' => true]);

        // Act
        $response = Livewire::test(Preferences::class);

        // Assert
        $response
            ->assertSee('Fping')
            ->assertSee('Databases')
            ->assertSee('Masscan');
    }
}

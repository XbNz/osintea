<?php

declare(strict_types=1);

namespace Feature\Jobs;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Preferences\Jobs\EnableMasscanPreferencesJob;
use XbNz\Preferences\Models\MasscanPreferences;

final class EnableMasscanPreferencesJobTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_disables_previously_enabled_preferences(): void
    {
        // Arrange
        $previousPreferencesRecord = MasscanPreferences::factory()->create(['enabled' => true])->getData();
        $newPreferencesRecord = MasscanPreferences::factory()->create(['enabled' => false])->getData();

        // Act
        $this->app->make(Dispatcher::class)->dispatch(
            new EnableMasscanPreferencesJob(
                $newPreferencesRecord
            )
        );

        // Assert
        $this->assertDatabaseHas(MasscanPreferences::class, [
            'id' => $previousPreferencesRecord->id,
            'enabled' => false,
        ]);

        $this->assertDatabaseHas(MasscanPreferences::class, [
            'id' => $newPreferencesRecord->id,
            'enabled' => true,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Fping\Tests\Feature\Jobs;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Fping\Jobs\EnableFpingPreferencesJob;
use XbNz\Fping\Models\FpingPreferences;

final class EnableFpingPreferencesJobTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_disables_previously_enabled_preferences(): void
    {
        // Arrange
        $previousPreferencesRecord = FpingPreferences::factory()->create(['enabled' => true])->getData();
        $newPreferencesRecord = FpingPreferences::factory()->create(['enabled' => false])->getData();

        // Act
        $this->app->make(Dispatcher::class)->dispatch(
            new EnableFpingPreferencesJob(
                $newPreferencesRecord
            )
        );

        // Assert
        $this->assertDatabaseHas(FpingPreferences::class, [
            'id' => $previousPreferencesRecord->id,
            'enabled' => false,
        ]);

        $this->assertDatabaseHas(FpingPreferences::class, [
            'id' => $newPreferencesRecord->id,
            'enabled' => true,
        ]);
    }
}

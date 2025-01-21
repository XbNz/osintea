<?php

declare(strict_types=1);

namespace Feature\Jobs;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Preferences\Jobs\DeleteMasscanPreferencesJob;
use XbNz\Preferences\Models\MasscanPreferences;

final class DeleteMasscanPreferencesJobTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_masscan_preferences(): void
    {
        // Arrange
        $masscanPreferences = MasscanPreferences::factory()->create()->getData();

        // Act
        $this->app->make(Dispatcher::class)->dispatch(
            new DeleteMasscanPreferencesJob(
                $masscanPreferences
            )
        );

        // Assert
        $this->assertDatabaseMissing(MasscanPreferences::class, [
            'id' => $masscanPreferences->id,
        ]);
    }
}

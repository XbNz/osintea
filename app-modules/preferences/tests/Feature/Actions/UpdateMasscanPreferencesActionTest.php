<?php

declare(strict_types=1);

namespace Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Preferences\Actions\UpdateMasscanPreferencesAction;
use XbNz\Preferences\DTOs\UpdateMasscanPreferencesDto;
use XbNz\Preferences\Models\MasscanPreferences;

final class UpdateMasscanPreferencesActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_an_masscan_preferences_record_from_a_dto(): void
    {
        // Arrange
        $action = $this->app->make(UpdateMasscanPreferencesAction::class);
        $masscanPreferences = MasscanPreferences::factory()->create()->getData();
        $dto = new UpdateMasscanPreferencesDto(
            $masscanPreferences->id,
            'new name',
            44,
            100,
            'eth0',
            3,
            true,
        );

        // Act
        $result = $action->handle($dto);

        // Assert
        $this->assertDatabaseHas(MasscanPreferences::class, [
            'id' => $masscanPreferences->id,
            'name' => 'new name',
            'ttl' => 44,
            'rate' => 100,
            'adapter' => 'eth0',
            'retries' => 3,
            'enabled' => true,
        ]);
    }
}

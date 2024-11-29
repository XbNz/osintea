<?php

declare(strict_types=1);

namespace XbNz\Fping\Tests\Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Fping\Actions\UpdateFpingPreferencesAction;
use XbNz\Fping\DTOs\UpdateFpingPreferencesDto;
use XbNz\Fping\Models\FpingPreferences;

final class UpdateFpingPreferencesActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_an_fping_preferences_record_from_a_dto(): void
    {
        // Arrange
        $action = $this->app->make(UpdateFpingPreferencesAction::class);
        $fpingPreferences = FpingPreferences::factory()->create()->getData();
        $dto = new UpdateFpingPreferencesDto(
            $fpingPreferences->id,
            'new name',
            44,
            2,
            66,
            62,
            15,
            1200,
            '0x10',
            12,
            5000,
            true,
            true,
            true
        );

        // Act
        $result = $action->handle($dto);

        // Assert
        $this->assertDatabaseHas(FpingPreferences::class, [
            'id' => $fpingPreferences->id,
            'name' => 'new name',
            'size' => 44,
            'backoff' => 2,
            'count' => 66,
            'ttl' => 62,
            'interval' => 15,
            'interval_per_target' => 1200,
            'type_of_service' => '0x10',
            'retries' => 12,
            'timeout' => 5000,
            'dont_fragment' => true,
            'send_random_data' => true,
            'enabled' => true,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Fping\Tests\Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Fping\Actions\DeleteFpingPreferencesAction;
use XbNz\Fping\Models\FpingPreferences;

final class DeleteFpingPreferencesActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_an_fping_preferences_record_from_a_dto(): void
    {
        // Arrange
        $action = $this->app->make(DeleteFpingPreferencesAction::class);
        $fpingPreferences = FpingPreferences::factory()->create()->getData();

        // Act
        $action->handle($fpingPreferences);

        // Assert
        $this->assertDatabaseMissing(FpingPreferences::class, [
            'id' => $fpingPreferences->id,
        ]);
    }
}

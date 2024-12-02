<?php

declare(strict_types=1);

namespace XbNz\Fping\Tests\Feature\Jobs;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Fping\Jobs\DeleteFpingPreferencesJob;
use XbNz\Fping\Models\FpingPreferences;

final class DeleteFpingPreferencesJobTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_fping_preferences(): void
    {
        // Arrange
        $fpingPreferences = FpingPreferences::factory()->create()->getData();

        // Act
        $this->app->make(Dispatcher::class)->dispatch(
            new DeleteFpingPreferencesJob(
                $fpingPreferences
            )
        );

        // Assert
        $this->assertDatabaseMissing(FpingPreferences::class, [
            'id' => $fpingPreferences->id,
        ]);
    }
}

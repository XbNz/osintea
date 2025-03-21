<?php

declare(strict_types=1);

namespace XbNz\Preferences\Tests\Feature\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use XbNz\Preferences\Actions\CreateFpingPreferencesAction;
use XbNz\Preferences\DTOs\CreateFpingPreferencesDto;
use XbNz\Preferences\Events\FpingPreferencesInsertedEvent;
use XbNz\Preferences\Models\FpingPreferences;

final class CreateFpingPreferencesActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_create_an_fping_preferences_record(): void
    {
        // Arrange
        $action = $this->app->make(CreateFpingPreferencesAction::class);
        $dto = new CreateFpingPreferencesDto(
            'test',
            56,
            1,
            5,
            64,
            10,
            1000,
            '0x00',
            5,
            500,
            false,
            false
        );

        // Act
        $result = $action->handle($dto);

        // Assert
        $this->assertDatabaseHas(FpingPreferences::class, [
            'name' => 'test',
            'size' => 56,
            'backoff' => 1,
            'count' => 5,
            'ttl' => 64,
            'interval' => 10,
            'interval_per_target' => 1000,
            'type_of_service' => '0x00',
            'retries' => 5,
            'timeout' => 500,
            'dont_fragment' => false,
            'send_random_data' => false,
        ]);

        $result->created_at->isSameSecond(CarbonImmutable::now());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function event_is_dispatched(): void
    {
        // Arrange
        Event::fake([FpingPreferencesInsertedEvent::class]);

        $action = $this->app->make(CreateFpingPreferencesAction::class);

        // Act
        $action->handle($dto = CreateFpingPreferencesDto::sampleData());

        // Assert
        Event::assertDispatched(FpingPreferencesInsertedEvent::class, fn (FpingPreferencesInsertedEvent $event) => $event->record->name === $dto->name);
    }
}

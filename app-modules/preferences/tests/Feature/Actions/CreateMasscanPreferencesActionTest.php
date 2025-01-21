<?php

declare(strict_types=1);

namespace Feature\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use XbNz\Preferences\Actions\CreateMasscanPreferencesAction;
use XbNz\Preferences\DTOs\CreateMasscanPreferencesDto;
use XbNz\Preferences\Events\MasscanPreferencesInsertedEvent;
use XbNz\Preferences\Models\MasscanPreferences;

final class CreateMasscanPreferencesActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_create_an_masscan_preferences_record(): void
    {
        // Arrange
        $action = $this->app->make(CreateMasscanPreferencesAction::class);
        $dto = new CreateMasscanPreferencesDto(
            'test',
            55,
            1000,
            null,
            1,
        );

        // Act
        $result = $action->handle($dto);

        // Assert
        $this->assertDatabaseHas(MasscanPreferences::class, [
            'name' => 'test',
            'ttl' => 55,
            'rate' => 1000,
            'adapter' => null,
            'retries' => 1,
        ]);

        $result->created_at->isSameSecond(CarbonImmutable::now());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function event_is_dispatched(): void
    {
        // Arrange
        Event::fake([MasscanPreferencesInsertedEvent::class]);

        $action = $this->app->make(CreateMasscanPreferencesAction::class);

        // Act
        $action->handle($dto = CreateMasscanPreferencesDto::sampleData());

        // Assert
        Event::assertDispatched(MasscanPreferencesInsertedEvent::class, fn (MasscanPreferencesInsertedEvent $event) => $event->record->name === $dto->name);
    }
}

<?php

declare(strict_types=1);

namespace Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use XbNz\Preferences\Actions\DeleteMasscanPreferencesAction;
use XbNz\Preferences\Events\MasscanPreferencesDeletedEvent;
use XbNz\Preferences\Models\MasscanPreferences;

final class DeleteMasscanPreferencesActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_an_masscan_preferences_record_from_a_dto(): void
    {
        // Arrange
        $action = $this->app->make(DeleteMasscanPreferencesAction::class);
        $masscanPreferences = MasscanPreferences::factory()->create()->getData();

        // Act
        $action->handle($masscanPreferences);

        // Assert
        $this->assertDatabaseMissing(MasscanPreferences::class, [
            'id' => $masscanPreferences->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function event_is_dispatched(): void
    {
        // Arrange
        Event::fake([MasscanPreferencesDeletedEvent::class]);

        $masscanPreferences = MasscanPreferences::factory()->create()->getData();

        $action = $this->app->make(DeleteMasscanPreferencesAction::class);

        // Act
        $action->handle($masscanPreferences);

        // Assert
        Event::assertDispatched(MasscanPreferencesDeletedEvent::class, fn (MasscanPreferencesDeletedEvent $event) => $event->record === $masscanPreferences);
    }
}

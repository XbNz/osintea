<?php

declare(strict_types=1);

namespace XbNz\Preferences\Tests\Feature\Livewire;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Tests\TestCase;
use XbNz\Preferences\Jobs\DatabaseUpdaterJob;
use XbNz\Preferences\Livewire\DatabasePreferences;
use XbNz\Shared\Enums\UpdatableDatabase;

final class DatabasePreferencesTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_the_correct_database(): void
    {
        // Arrange
        Bus::fake([DatabaseUpdaterJob::class]);
        $updatableDatabases = Collection::make(UpdatableDatabase::cases())
            ->filter(fn (UpdatableDatabase $database) => $database->canBeUsedInProduction())
            ->mapWithKeys(fn (UpdatableDatabase $database) => [$database->value => $database->friendlyName()]);

        // Act
        $response = Livewire::test(DatabasePreferences::class)
            ->assertViewHas('updatableDatabases', $updatableDatabases)
            ->call('updateDatabase', UpdatableDatabase::Fake->value);

        // Assert
        Bus::assertDispatched(DatabaseUpdaterJob::class, fn (DatabaseUpdaterJob $job) => $job->database === UpdatableDatabase::Fake);
    }
}

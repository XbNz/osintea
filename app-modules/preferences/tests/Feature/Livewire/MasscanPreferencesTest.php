<?php

declare(strict_types=1);

namespace Feature\Livewire;

use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;
use XbNz\Preferences\DTOs\MasscanPreferencesDto;
use XbNz\Preferences\Events\Intentions\EnableMasscanPreferencesIntention;
use XbNz\Preferences\Livewire\MasscanPreferences;
use XbNz\Preferences\Models\MasscanPreferences as MasscanPreferencesModel;

final class MasscanPreferencesTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_with_the_first_preference_as_the_active_tab(): void
    {
        // Arrange
        $preferences = MasscanPreferencesDto::collect(MasscanPreferencesModel::factory()->count(2)->create());

        // Act
        $response = Livewire::test(MasscanPreferences::class);

        // Assert
        $response->assertSet('form.id', $preferences->first()->id);
        $response->assertSet('form.name', $preferences->first()->name);
        $response->assertSet('form.ttl', $preferences->first()->ttl);
        $response->assertSet('form.rate', $preferences->first()->rate);
        $response->assertSet('form.adapter', $preferences->first()->adapter);
        $response->assertSet('form.retries', $preferences->first()->retries);
        $response->assertSet('form.enabled', $preferences->first()->enabled);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function disabled_preferences_can_be_enabled(): void
    {
        // Arrange
        MasscanPreferencesModel::factory()->create(['enabled' => true]);
        $disabledPreferencesRecord = MasscanPreferencesModel::factory()->create(['enabled' => false]);

        // Act
        $response = Livewire::test(MasscanPreferences::class)
            ->call('selectTab', $disabledPreferencesRecord->id)
            ->call('enable');

        // Assert
        $this->assertDatabaseHas(MasscanPreferencesModel::class, [
            'id' => $disabledPreferencesRecord->id,
            'enabled' => true,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_test(): void
    {
        // Arrange
        MasscanPreferencesModel::factory()->create(['enabled' => true]);

        // Act
        $response = Livewire::test(MasscanPreferences::class)
            ->set('form.name', 'new name')
            ->set('form.ttl', 63)
            ->set('form.rate', 100)
            ->set('form.adapter', 'eth0')
            ->set('form.retries', 6);

        // Assert
        $this->assertDatabaseHas(MasscanPreferencesModel::class, [
            'name' => 'new name',
            'ttl' => 63,
            'rate' => 100,
            'adapter' => 'eth0',
            'retries' => 6,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_is_not_possible_to_delete_a_currently_active_preferences_record(): void
    {
        // Arrange
        MasscanPreferencesModel::factory()->create(['enabled' => true]);
        $enabledPreferencesRecord = MasscanPreferencesModel::factory()->create(['enabled' => true]);
        $disabledPreferencesRecord = MasscanPreferencesModel::factory()->create(['enabled' => false]);

        // Act
        $response = Livewire::test(MasscanPreferences::class)
            ->call('selectTab', $enabledPreferencesRecord->id)
            ->call('delete')
            ->call('selectTab', $disabledPreferencesRecord->id)
            ->call('delete');

        // Assert
        $this->assertDatabaseHas(MasscanPreferencesModel::class, [
            'id' => $enabledPreferencesRecord->id,
        ]);

        $this->assertDatabaseMissing(MasscanPreferencesModel::class, [
            'id' => $disabledPreferencesRecord->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_is_not_possible_to_enable_a_currently_enabled_preferences_record(): void
    {
        // Arrange
        Event::fake([EnableMasscanPreferencesIntention::class]);

        MasscanPreferencesModel::factory()->create(['enabled' => true]);
        $enabledPreferencesRecord = MasscanPreferencesModel::factory()->create(['enabled' => true]);

        // Act
        $response = Livewire::test(MasscanPreferences::class)
            ->call('selectTab', $enabledPreferencesRecord->id)
            ->call('enable');

        // Assert
        $this->assertDatabaseHas(MasscanPreferencesModel::class, [
            'id' => $enabledPreferencesRecord->id,
            'enabled' => true,
        ]);

        Event::assertNotDispatched(EnableMasscanPreferencesIntention::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_a_new_preferences_record_with_sensible_defaults(): void
    {
        // Arrange
        MasscanPreferencesModel::factory()->create(['enabled' => true]);

        // Act
        $response = Livewire::test(MasscanPreferences::class)
            ->call('createNewPreferencesRecord');

        // Assert
        $this->assertDatabaseHas(MasscanPreferencesModel::class, [
            'enabled' => false,
            'ttl' => 55,
            'rate' => 10000,
            'adapter' => null,
            'retries' => 0,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('validationProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function validation_tests(array $payload, array $errors): void
    {
        // Arrange
        MasscanPreferencesModel::factory()->create(['enabled' => true]);
        $livewire = Livewire::test(MasscanPreferences::class);

        // Act
        foreach ($payload as $key => $value) {
            $livewire->set($key, $value);
        }

        // Assert
        $livewire->assertHasErrors($errors);
    }

    public static function validationProvider(): Generator
    {
        $default = self::sampleData();

        yield from [
            'form.name is required' => [
                'payload' => array_merge($default, ['form.name' => null]),
                'errors' => ['form.name'],
            ],
            'form.ttl is required' => [
                'payload' => array_merge($default, ['form.ttl' => null]),
                'errors' => ['form.ttl'],
            ],
            'form.retries is required' => [
                'payload' => array_merge($default, ['form.retries' => null]),
                'errors' => ['form.retries'],
            ],
            'form.rate is required' => [
                'payload' => array_merge($default, ['form.rate' => null]),
                'errors' => ['form.rate'],
            ],
            'form.enabled is required' => [
                'payload' => array_merge($default, ['form.enabled' => null]),
                'errors' => ['form.enabled'],
            ],
        ];
    }

    public static function sampleData(): array
    {
        return [
            'form.name' => 'new name',
            'form.ttl' => 63,
            'form.rate' => 100,
            'form.adapter' => 'eth0',
            'form.retries' => 6,
            'form.enabled' => true,
        ];
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Fping\Tests\Feature\Livewire;

use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;
use XbNz\Fping\DTOs\FpingPreferencesDto;
use XbNz\Fping\Events\Intentions\EnableFpingPreferencesIntention;
use XbNz\Fping\Livewire\FpingPreferences;
use XbNz\Fping\Models\FpingPreferences as FpingPreferencesModel;

final class FpingPreferencesTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_with_the_first_preference_as_the_active_tab(): void
    {
        // Arrange
        $preferences = FpingPreferencesDto::collect(FpingPreferencesModel::factory()->count(2)->create());

        // Act
        $response = Livewire::test(FpingPreferences::class);

        // Assert
        $response->assertSet('form.id', $preferences->first()->id);
        $response->assertSet('form.name', $preferences->first()->name);
        $response->assertSet('form.size', $preferences->first()->size);
        $response->assertSet('form.backoff', $preferences->first()->backoff);
        $response->assertSet('form.count', $preferences->first()->count);
        $response->assertSet('form.ttl', $preferences->first()->ttl);
        $response->assertSet('form.interval', $preferences->first()->interval);
        $response->assertSet('form.interval_per_target', $preferences->first()->interval_per_target);
        $response->assertSet('form.type_of_service', $preferences->first()->type_of_service);
        $response->assertSet('form.retries', $preferences->first()->retries);
        $response->assertSet('form.timeout', $preferences->first()->timeout);
        $response->assertSet('form.dont_fragment', $preferences->first()->dont_fragment);
        $response->assertSet('form.send_random_data', $preferences->first()->send_random_data);
        $response->assertSet('form.enabled', $preferences->first()->enabled);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function disabled_preferences_can_be_enabled(): void
    {
        // Arrange
        FpingPreferencesModel::factory()->create(['enabled' => true]);
        $disabledPreferencesRecord = FpingPreferencesModel::factory()->create(['enabled' => false]);

        // Act
        $response = Livewire::test(FpingPreferences::class)
            ->call('selectTab', $disabledPreferencesRecord->id)
            ->call('enable');

        // Assert
        $this->assertDatabaseHas(FpingPreferencesModel::class, [
            'id' => $disabledPreferencesRecord->id,
            'enabled' => true,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_test(): void
    {
        // Arrange
        FpingPreferencesModel::factory()->create(['enabled' => true]);

        // Act
        $response = Livewire::test(FpingPreferences::class)
            ->set('form.name', 'new name')
            ->set('form.size', 11)
            ->set('form.backoff', 3)
            ->set('form.count', 77)
            ->set('form.ttl', 63)
            ->set('form.interval', 11)
            ->set('form.interval_per_target', 1300)
            ->set('form.type_of_service', '0x01')
            ->set('form.retries', 6)
            ->set('form.timeout', 6000)
            ->set('form.dont_fragment', true)
            ->set('form.send_random_data', true);

        // Assert
        $this->assertDatabaseHas(FpingPreferencesModel::class, [
            'name' => 'new name',
            'size' => 11,
            'backoff' => 3,
            'count' => 77,
            'ttl' => 63,
            'interval' => 11,
            'interval_per_target' => 1300,
            'type_of_service' => '0x01',
            'retries' => 6,
            'timeout' => 6000,
            'dont_fragment' => true,
            'send_random_data' => true,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_is_not_possible_to_delete_a_currently_active_preferences_record(): void
    {
        // Arrange
        FpingPreferencesModel::factory()->create(['enabled' => true]);
        $enabledPreferencesRecord = FpingPreferencesModel::factory()->create(['enabled' => true]);
        $disabledPreferencesRecord = FpingPreferencesModel::factory()->create(['enabled' => false]);

        // Act
        $response = Livewire::test(FpingPreferences::class)
            ->call('selectTab', $enabledPreferencesRecord->id)
            ->call('delete')
            ->call('selectTab', $disabledPreferencesRecord->id)
            ->call('delete');

        // Assert
        $this->assertDatabaseHas(FpingPreferencesModel::class, [
            'id' => $enabledPreferencesRecord->id,
        ]);

        $this->assertDatabaseMissing(FpingPreferencesModel::class, [
            'id' => $disabledPreferencesRecord->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_is_not_possible_to_enable_a_currently_enabled_preferences_record(): void
    {
        // Arrange
        Event::fake([EnableFpingPreferencesIntention::class]);

        FpingPreferencesModel::factory()->create(['enabled' => true]);
        $enabledPreferencesRecord = FpingPreferencesModel::factory()->create(['enabled' => true]);

        // Act
        $response = Livewire::test(FpingPreferences::class)
            ->call('selectTab', $enabledPreferencesRecord->id)
            ->call('enable');

        // Assert
        $this->assertDatabaseHas(FpingPreferencesModel::class, [
            'id' => $enabledPreferencesRecord->id,
            'enabled' => true,
        ]);

        Event::assertNotDispatched(EnableFpingPreferencesIntention::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_a_new_preferences_record_with_sensible_defaults(): void
    {
        // Arrange
        FpingPreferencesModel::factory()->create(['enabled' => true]);

        // Act
        $response = Livewire::test(FpingPreferences::class)
            ->call('createNewPreferencesRecord');

        // Assert
        $this->assertDatabaseHas(FpingPreferencesModel::class, [
            'size' => 56,
            'backoff' => 1.5,
            'count' => 1,
            'ttl' => 64,
            'interval' => 10,
            'interval_per_target' => 1000,
            'type_of_service' => '0x00',
            'retries' => 1,
            'timeout' => 500,
            'dont_fragment' => false,
            'send_random_data' => false,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('validationProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function validation_tests(array $payload, array $errors): void
    {
        // Arrange
        FpingPreferencesModel::factory()->create(['enabled' => true]);
        $livewire = Livewire::test(FpingPreferences::class);

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
            'form.size is required' => [
                'payload' => array_merge($default, ['form.size' => null]),
                'errors' => ['form.size'],
            ],
            'form.backoff is required' => [
                'payload' => array_merge($default, ['form.backoff' => null]),
                'errors' => ['form.backoff'],
            ],
            'form.count is required' => [
                'payload' => array_merge($default, ['form.count' => null]),
                'errors' => ['form.count'],
            ],
            'form.ttl is required' => [
                'payload' => array_merge($default, ['form.ttl' => null]),
                'errors' => ['form.ttl'],
            ],
            'form.interval is required' => [
                'payload' => array_merge($default, ['form.interval' => null]),
                'errors' => ['form.interval'],
            ],
            'form.interval_per_target is required' => [
                'payload' => array_merge($default, ['form.interval_per_target' => null]),
                'errors' => ['form.interval_per_target'],
            ],
            'form.type_of_service is required' => [
                'payload' => array_merge($default, ['form.type_of_service' => null]),
                'errors' => ['form.type_of_service'],
            ],
            'form.retries is required' => [
                'payload' => array_merge($default, ['form.retries' => null]),
                'errors' => ['form.retries'],
            ],
            'form.timeout is required' => [
                'payload' => array_merge($default, ['form.timeout' => null]),
                'errors' => ['form.timeout'],
            ],
            'form.dont_fragment is required' => [
                'payload' => array_merge($default, ['form.dont_fragment' => null]),
                'errors' => ['form.dont_fragment'],
            ],
            'form.send_random_data is required' => [
                'payload' => array_merge($default, ['form.send_random_data' => null]),
                'errors' => ['form.send_random_data'],
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
            'form.size' => 44,
            'form.backoff' => 2,
            'form.count' => 66,
            'form.ttl' => 62,
            'form.interval' => 15,
            'form.interval_per_target' => 1200,
            'form.type_of_service' => '0x10',
            'form.retries' => 12,
            'form.timeout' => 5000,
            'form.dont_fragment' => true,
            'form.send_random_data' => true,
            'form.enabled' => true,
        ];
    }
}

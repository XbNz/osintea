<?php

declare(strict_types=1);

namespace XbNz\Ping\Tests\Feature\Livewire;

use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Native\Laravel\Facades\ChildProcess;
use Tests\TestCase;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ping\Events\PingSequenceInsertedEvent;
use XbNz\Ping\Livewire\Ping;
use XbNz\Ping\Models\PingSequence;
use XbNz\Shared\Enums\NativePhpChildProcess;

final class PingTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function rendered_with_defaults(): void
    {
        Livewire::test(Ping::class)
            ->assertViewHasAll([
                'interval' => 1000,
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function submitting_form_results_in_icmp_ping_action_being_hit_with_requested_ip_address(): void
    {
        // Act
        ChildProcess::fake();

        $response = Livewire::test(Ping::class)
            ->set('target', '1.1.1.1')
            ->set('interval', 1000)
            ->call('ping');

        // Assert
        $this->assertDatabaseHas(IpAddress::class, [
            'ip' => '1.1.1.1',
        ]);

        ChildProcess::assertGet(NativePhpChildProcess::PingWorker->value);
        ChildProcess::assertMessage(fn (string $message, ?string $alias) => $message === 'target-add:1.1.1.1::1000' && $alias === null);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function statistics_are_calculated_correctly(): void
    {
        // Arrange
        $ipAddress = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(5)
                    ->sequence(
                        ['round_trip_time' => 1, 'loss' => false],
                        ['round_trip_time' => 2, 'loss' => false],
                        ['round_trip_time' => 3, 'loss' => false],
                        ['round_trip_time' => 4, 'loss' => false],
                        ['round_trip_time' => null, 'loss' => true],
                    )
            )
            ->create()
            ->fresh()
            ->getData();

        // Act
        $response = Livewire::withQueryParams(['target' => $ipAddress->ip])
            ->test(Ping::class);

        // Assert
        $response->assertViewHas('averageRoundTripTime', 2.5);
        $response->assertViewHas('minimumRoundTripTime', 1);
        $response->assertViewHas('maximumRoundTripTime', 4);
        $response->assertViewHas('packetLossPercentage', 20);
        $response->assertViewHas('standardDeviation', '1.12');
        $response->assertViewHas('lossCount', 1);
        $response->assertViewHas('totalCount', 5);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_dispatches_an_event_to_update_the_chart_when_a_new_ping_sequence_event_is_received(): void
    {
        // Arrange
        $pingSequence = PingSequence::factory()->create([
            'round_trip_time' => 1,
        ])->fresh()->getData();

        // Act
        $livewire = Livewire::test(Ping::class)
            ->set('target', $pingSequence->ip->ip)
            ->set('interval', 1000)
            ->dispatch('native:'.PingSequenceInsertedEvent::class, $pingSequence->toArray());

        // Assert
        $livewire->assertDispatched('newDataPoint', [
            'label' => $pingSequence->created_at->format('H:i:s'),
            'newData' => 1,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_ignores_events_dispatched_for_other_ip_addresses(): void
    {
        // Arrange
        $ipAddressA = IpAddress::factory()->create()->fresh()->getData();
        $ipAddressB = IpAddress::factory()->create()->fresh()->getData();

        $pingSequenceA = PingSequence::factory()->create([
            'ip_address_id' => $ipAddressA->id,
        ])->fresh()->getData();
        $pingSequenceB = PingSequence::factory()->create([
            'ip_address_id' => $ipAddressB->id,
        ])->fresh()->getData();

        // Act
        $livewire = Livewire::test(Ping::class)
            ->set('target', $ipAddressB->ip)
            ->set('interval', 1000)
            ->dispatch('native:'.PingSequenceInsertedEvent::class, $pingSequenceA->toArray());

        // Assert
        $livewire->assertNotDispatched('newDataPoint');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function can_delete_ip_results(): void
    {
        // Arrange
        $ipAddress = IpAddress::factory()
            ->has(PingSequence::factory()->count(5))
            ->create()
            ->fresh()
            ->getData();

        // Act
        $this->assertDatabaseCount(PingSequence::class, 5);
        $response = Livewire::test(Ping::class)
            ->set('target', $ipAddress->ip)
            ->call('deleteSequences');

        // Assert
        $this->assertDatabaseCount(PingSequence::class, 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function can_stop_ongoing_ping(): void
    {
        // Arrange
        ChildProcess::fake();

        // Act
        $response = Livewire::test(Ping::class)
            ->set('target', '1.1.1.1')
            ->set('interval', 1000)
            ->call('ping')
            ->call('stop');

        // Assert
        ChildProcess::assertGet(NativePhpChildProcess::PingWorker->value);
        ChildProcess::assertMessage(fn (string $message, ?string $alias) => $message === 'target-remove:1.1.1.1' && $alias === null);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('validationProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function validation_tests(array $payload, array $errors): void
    {
        // Arrange
        $livewire = Livewire::test(Ping::class);

        foreach ($payload as $key => $value) {
            $livewire->set($key, $value);
        }

        // Act
        $response = $livewire->call('ping');

        // Assert
        $response->assertHasErrors($errors);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unresolvable_host_throws_exception(): void
    {
        // Act
        $response = Livewire::test(Ping::class)
            ->set('target', 'unresolvable-host')
            ->call('ping');

        // Assert
        $response->assertHasErrors(['target']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unresolvable_host_throws_exception_when_set_from_query_param(): void
    {
        // Act
        $response = Livewire::withQueryParams(['target' => 'unresolvable-host'])
            ->test(Ping::class);

        // Assert
        $response->assertHasErrors(['target']);
    }

    public static function validationProvider(): Generator
    {
        $default = self::sampleData();

        yield from [
            'interval must be at least 100' => [
                'payload' => array_merge($default, [
                    'interval' => 99,
                ]),
                'errors' => ['interval'],
            ],
        ];
    }

    private static function sampleData(): array
    {
        return [
            'target' => '1.1.1.1',
            'interval' => 1000,
        ];
    }
}

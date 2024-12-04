<?php

declare(strict_types=1);

namespace XbNz\Ip\Tests\Feature\Livewire;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Mockery;
use Native\Laravel\Facades\Window;
use Native\Laravel\Windows\Window as WindowImplementation;
use Tests\TestCase;
use XbNz\Ip\Livewire\ListIpAddresses;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ping\Jobs\BulkPingJob;
use XbNz\Ping\Models\PingSequence;

final class ListIpAddressesTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_renders_with_ip_addresses(): void
    {
        // Arrange
        IpAddress::factory()
            ->count(2)
            ->sequence(
                ['ip' => '1.1.1.1'],
                ['ip' => '8.8.8.8'],
            )
            ->create();

        // Act
        $response = Livewire::test(ListIpAddresses::class);

        // Assert
        $response->assertSee('1.1.1.1');
        $response->assertSee('8.8.8.8');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_limits_to_ipv4_and_overwrites_previous_limits(): void
    {
        // Arrange
        IpAddress::factory()
            ->count(2)
            ->sequence(
                ['ip' => '1.1.1.1'],
                ['ip' => '2606:4700:4700::1111'],
            )
            ->create();

        // Act
        $response = Livewire::test(ListIpAddresses::class)
            ->call('limitV6')
            ->call('limitV4');

        // Assert
        $response->assertSee('1.1.1.1');
        $response->assertDontSee('2606:4700:4700::1111');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_limits_to_ipv6_and_overwrites_previous_limits(): void
    {
        // Arrange
        IpAddress::factory()
            ->count(2)
            ->sequence(
                ['ip' => '1.1.1.1'],
                ['ip' => '2606:4700:4700::1111'],
            )
            ->create();

        // Act
        $response = Livewire::test(ListIpAddresses::class)
            ->call('limitV4')
            ->call('limitV6');

        // Assert
        $response->assertSee('2606:4700:4700::1111');
        $response->assertDontSee('1.1.1.1');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_resets_the_ip_limit(): void
    {
        // Arrange
        IpAddress::factory()
            ->count(2)
            ->sequence(
                ['ip' => '1.1.1.1'],
                ['ip' => '2606:4700:4700::1111'],
            )
            ->create();

        // Act
        $response = Livewire::test(ListIpAddresses::class)
            ->call('limitV4')
            ->call('clearIpTypeLimits');

        // Assert
        $response->assertSee('2606:4700:4700::1111');
        $response->assertSee('1.1.1.1');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function sorting_by_ip(): void
    {
        // Arrange
        IpAddress::factory()
            ->count(2)
            ->sequence(
                ['ip' => '1.1.1.1'],
                ['ip' => '8.8.8.8'],
            )
            ->create();

        // Act & Assert
        $livewire = Livewire::test(ListIpAddresses::class);

        $livewire->call('sort', 'ip');
        $livewire->assertSeeInOrder(['1.1.1.1', '8.8.8.8']);

        $livewire->call('sort', 'ip');
        $livewire->assertSeeInOrder(['8.8.8.8', '1.1.1.1']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function sorting_by_created_at(): void
    {
        // Arrange
        $ipAddressA = IpAddress::factory()->create()->refresh()->getData();
        $ipAddressB = IpAddress::factory()->create()->refresh()->getData();

        // Act & Assert
        $livewire = Livewire::test(ListIpAddresses::class);

        $livewire->call('sort', 'created_at');
        $livewire->assertSeeInOrder([$ipAddressA->ip, $ipAddressB->ip]);

        $livewire->call('sort', 'created_at');
        $livewire->assertSeeInOrder([$ipAddressB->ip, $ipAddressA->ip]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function sorting_by_average_rtt(): void
    {
        // Arrange
        $ipAddressA = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['round_trip_time' => 1],
                        ['round_trip_time' => 2],
                    )
            )
            ->create();

        $ipAddressB = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['round_trip_time' => 5],
                        ['round_trip_time' => 10],
                    )
            )
            ->create();

        // Act & Assert
        $livewire = Livewire::test(ListIpAddresses::class);

        $livewire->call('sort', 'average_rtt');
        $livewire->assertSeeInOrder([1.50, 7.50]);

        $livewire->call('sort', 'average_rtt');
        $livewire->assertSeeInOrder([7.50, 1.50]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function filters_by_minimum_round_trip_range(): void
    {
        // Arrange
        $ipAddressA = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['round_trip_time' => 1],
                        ['round_trip_time' => 2],
                    )
            )
            ->create();

        $ipAddressB = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['round_trip_time' => 3],
                        ['round_trip_time' => 4],
                    )
            )
            ->create();

        $ipAddressC = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['round_trip_time' => 4],
                        ['round_trip_time' => 5],
                    )
            )
            ->create();

        // Act
        $livewire = Livewire::test(ListIpAddresses::class);

        $livewire->set('roundTripTimeFilter.minFloor', 3)
            ->set('roundTripTimeFilter.maxFloor', 4)
            ->call('applyFilters');

        // Assert
        $livewire->assertSee($ipAddressB->ip);
        $livewire->assertSee($ipAddressC->ip);
        $livewire->assertDontSee($ipAddressA->ip);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function filters_by_average_round_trip_range(): void
    {
        // Arrange
        $ipAddressA = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['round_trip_time' => 50],
                        ['round_trip_time' => 100],
                    )
            )
            ->create();

        $ipAddressB = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['round_trip_time' => 10],
                        ['round_trip_time' => 20],
                    )
            )
            ->create();

        $ipAddressC = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['round_trip_time' => 5],
                        ['round_trip_time' => 10],
                    )
            )
            ->create();

        // Act
        $livewire = Livewire::test(ListIpAddresses::class);

        $livewire->set('roundTripTimeFilter.minAverage', 8)
            ->set('roundTripTimeFilter.maxAverage', 16)
            ->call('applyFilters');

        // Assert
        $livewire->assertSee($ipAddressB->ip);
        $livewire->assertDontSee($ipAddressC->ip);
        $livewire->assertDontSee($ipAddressA->ip);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function filters_by_maximum_round_trip_time(): void
    {
        // Arrange
        $ipAddressA = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['round_trip_time' => 50],
                        ['round_trip_time' => 100],
                    )
            )
            ->create();

        $ipAddressB = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['round_trip_time' => 10],
                        ['round_trip_time' => 20],
                    )
            )
            ->create();

        $ipAddressC = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['round_trip_time' => 5],
                        ['round_trip_time' => 10],
                    )
            )
            ->create();

        // Act
        $livewire = Livewire::test(ListIpAddresses::class);

        $livewire->set('roundTripTimeFilter.minCeiling', 11)
            ->set('roundTripTimeFilter.maxCeiling', 20)
            ->call('applyFilters');

        // Assert
        $livewire->assertSee($ipAddressB->ip);
        $livewire->assertDontSee($ipAddressC->ip);
        $livewire->assertDontSee($ipAddressA->ip);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function filters_by_packet_loss_range(): void
    {
        // Arrange
        $ipAddressA = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['loss' => false],
                        ['loss' => true, 'round_trip_time' => null],
                    )
            )
            ->create();

        $ipAddressB = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['loss' => false],
                        ['loss' => false],
                    )
            )
            ->create();

        $ipAddressC = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['loss' => true, 'round_trip_time' => null],
                        ['loss' => true, 'round_trip_time' => null],
                    )
            )
            ->create();

        // Act
        $livewire = Livewire::test(ListIpAddresses::class);

        $livewire->set('packetLossFilter.minPercent', 0)
            ->set('packetLossFilter.maxPercent', 99)
            ->call('applyFilters');

        // Assert
        $livewire->assertSee($ipAddressA->ip);
        $livewire->assertSee($ipAddressB->ip);
        $livewire->assertDontSee($ipAddressC->ip);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function clearing_filters_works(): void
    {
        // Arrange
        $ipAddress = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['loss' => false, 'round_trip_time' => 2],
                        ['loss' => true, 'round_trip_time' => null],
                    )
            )
            ->create();

        // Act
        $livewire = Livewire::test(ListIpAddresses::class)
            ->set('packetLossFilter.minPercent', 51)
            ->set('roundTripTimeFilter.minAverage', 3)
            ->call('applyFilters');

        $livewire->assertDontSee($ipAddress->ip);

        $livewire->call('clearFilters');

        // Assert
        $livewire->assertSee($ipAddress->ip);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_shows_the_ip_address_count_with_friendly_formatting(): void
    {
        // Arrange
        IpAddress::factory()->count(2)->create();

        // Act
        $response = Livewire::test(ListIpAddresses::class);

        // Assert
        $response->assertSee('2 IP Addresses');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_pings_active_ip_addresses(): void
    {
        // Arrange
        Bus::fake();
        $ipAddressA = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['round_trip_time' => 1],
                        ['round_trip_time' => 2],
                    )
            )
            ->create(['ip' => '1.1.1.1'])
            ->refresh()
            ->getData();

        $ipAddressB = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['round_trip_time' => 3],
                        ['round_trip_time' => 4],
                    )
            )
            ->create(['ip' => '8.8.8.8'])
            ->refresh()
            ->getData();

        // Act
        $livewire = Livewire::test(ListIpAddresses::class)
            ->set('roundTripTimeFilter.minFloor', 3)
            ->call('applyFilters');

        $livewire->assertDontSee($ipAddressA->ip);
        $livewire->assertSee($ipAddressB->ip);

        $livewire->call('pingActive');

        // Assert
        Bus::assertDispatchedTimes(BulkPingJob::class, 1);
        Bus::assertDispatched(
            BulkPingJob::class,
            function (BulkPingJob $job) use ($ipAddressB) {
                $this->assertSame($ipAddressB->ip, $job->ipAddressDtos->sole()->ip);

                return true;
            }
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_active_ip_addresses_along_with_all_its_sequences(): void
    {
        // Arrange
        $ipAddressA = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['round_trip_time' => 1],
                        ['round_trip_time' => 2],
                    )
            )
            ->create(['ip' => '1.1.1.1'])
            ->refresh()
            ->getData();

        $ipAddressB = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['round_trip_time' => 3],
                        ['round_trip_time' => 4],
                    )
            )
            ->create(['ip' => '8.8.8.8'])
            ->refresh()
            ->getData();

        // Act
        $livewire = Livewire::test(ListIpAddresses::class)
            ->set('roundTripTimeFilter.minFloor', 3)
            ->call('applyFilters');

        $livewire->assertDontSee($ipAddressA->ip);
        $livewire->assertSee($ipAddressB->ip);

        $livewire->call('deleteActive');

        // Assert
        $this->assertDatabaseMissing(IpAddress::class, ['id' => $ipAddressB->id]);
        $this->assertDatabaseMissing(PingSequence::class, ['ip_address_id' => $ipAddressB->id]);
        $this->assertDatabaseHas(IpAddress::class, ['id' => $ipAddressA->id]);
        $this->assertDatabaseHas(PingSequence::class, ['ip_address_id' => $ipAddressA->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function example(): void
    {
        // Arrange
        $ipAddress = IpAddress::factory()
            ->has(
                PingSequence::factory()
                    ->count(2)
                    ->sequence(
                        ['round_trip_time' => 1],
                        ['round_trip_time' => 2],
                    )
            )
            ->create(['ip' => '1.1.1.1'])
            ->refresh()
            ->getData();

        // Act
        $livewire = Livewire::test(ListIpAddresses::class);
        $livewire->call('deleteActive');

        // Assert
        $this->assertDatabaseMissing(IpAddress::class, ['id' => $ipAddress->id]);
        $this->assertDatabaseMissing(PingSequence::class, ['ip_address_id' => $ipAddress->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_opens_the_corresponding_ping_window_for_the_ip(): void
    {
        // Arrange
        Window::fake();
        Window::alwaysReturnWindows([
            $mockWindow = Mockery::mock(WindowImplementation::class)->makePartial(),
        ]);

        $ipAddress = IpAddress::factory()->create()->refresh()->getData();
        $mockWindow->shouldReceive('route')->once()->with('ping', ['target' => $ipAddress->ip])->andReturnSelf();

        // Act
        $response = Livewire::test(ListIpAddresses::class)
            ->call('goToPingWindow', $ipAddress->ip);

        // Assert
        Window::assertOpened(fn (string $windowId) => Str::startsWith($windowId, ['ping']));
    }
}

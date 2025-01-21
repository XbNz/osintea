<?php

declare(strict_types=1);

namespace XbNz\Port\Tests\Features\Jobs;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use XbNz\Ip\Models\IpAddress;
use XbNz\Masscan\Contracts\MasscanIcmpScannerInterface;
use XbNz\Masscan\FakeMasscanIcmpScanner;
use XbNz\Port\DTOs\PortScanResultDto;
use XbNz\Port\Events\BulkIcmpScanCompleted;
use XbNz\Port\Jobs\BulkIcmpScanJob;
use XbNz\Port\Models\Port;
use XbNz\Preferences\Models\MasscanPreferences;
use XbNz\Shared\Enums\IpType;
use XbNz\Shared\Enums\PortState;
use XbNz\Shared\Enums\ProtocolType;

final class BulkIcmpScanJobTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_accepts_a_collection_of_ips_and_icmp_scans_them(): void
    {
        // Arrange
        Event::fake([BulkIcmpScanCompleted::class]);

        $this->swap(MasscanIcmpScannerInterface::class, $fake = $this->app->make(FakeMasscanIcmpScanner::class));
        $fake->forceReturn([
            new PortScanResultDto(
                '1.1.1.1',
                IpType::IPv4,
                ProtocolType::ICMP,
                [
                    0 => PortState::Open,
                ]
            ),
        ]);

        MasscanPreferences::query()->create([
            'name' => 'default',
            'enabled' => true,
        ]);

        $ipAddress = IpAddress::factory()->create(['ip' => '1.1.1.1'])->refresh()->getData();

        // Act
        $this->assertDatabaseCount(Port::class, 0);
        $this->app->make(Dispatcher::class)->dispatch(new BulkIcmpScanJob([$ipAddress->id]));

        // Assert
        $this->assertDatabaseCount(Port::class, 1);
        Event::assertDispatched(BulkIcmpScanCompleted::class, fn (BulkIcmpScanCompleted $event) => $event->completedCount === 1);
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Port\Tests\Features\Actions;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Ip\Models\IpAddress;
use XbNz\Masscan\Contracts\MasscanIcmpScannerInterface;
use XbNz\Masscan\FakeMasscanIcmpScanner;
use XbNz\Port\Actions\BulkIcmpScanAction;
use XbNz\Preferences\Models\MasscanPreferences;

final class BulkIcmpScanActionTest extends TestCase
{
    use RefreshDatabase;

    private readonly FakeMasscanIcmpScanner $fake;

    protected function setUp(): void
    {
        parent::setUp();

        $this->swap(MasscanIcmpScannerInterface::class, $fake = $this->app->make(FakeMasscanIcmpScanner::class));

        $this->fake = $fake;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function if_the_ip_is_deleted_while_busy_pinging_it_discards_the_result_without_throwing_any_error(): void
    {
        // Arrange
        MasscanPreferences::query()->create([
            'name' => 'default',
            'enabled' => true,
        ]);

        $ipAddressA = IpAddress::query()->create(['ip' => '1.1.1.1'])->refresh()->getData();
        $ipAddressB = IpAddress::query()->create(['ip' => '8.8.8.8'])->refresh()->getData();

        $action = $this->app->make(BulkIcmpScanAction::class);

        // Act
        IpAddress::query()->where('ip', '8.8.8.8')->delete();
        $action->handle(IpAddressDto::collect(IpAddress::query()->get()));

        // Assert
        $this->fake->assertExecuted(1);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_destroys_the_temporary_input_file_after_destruction(): void
    {
        // Arrange
        $filesystemMock = $this->partialMock(Filesystem::class);
        $filesystemMock->shouldReceive('delete')->once();

        MasscanPreferences::query()->create([
            'name' => 'default',
            'enabled' => true,
        ]);

        $action = $this->app->make(BulkIcmpScanAction::class);

        // Act
        $action->handle(Collection::make([
            IpAddress::query()->create(['ip' => '1.1.1.1'])->refresh()->getData(),
        ]));

        unset($action);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function preferences_are_relayed_to_masscan_correctly(): void
    {
        // Arrange
        MasscanPreferences::query()->create([
            'name' => 'default',
            'adapter' => 'eth0',
            'rate' => 1000,
            'ttl' => 64,
            'retries' => 0,
            'enabled' => true,
        ]);

        MasscanPreferences::query()->create([
            'name' => 'should_not_be_used',
            'enabled' => false,
        ]);

        IpAddress::query()->create(['ip' => '1.1.1.1'])->refresh()->getData();

        $action = $this->app->make(BulkIcmpScanAction::class);

        // Act
        $action->handle(IpAddressDto::collect(IpAddress::query()->get()));

        // Assert
        $this->fake->assertExecuted(1);
        $this->fake->assertRetries(0);
        $this->fake->assertRate(1000);
        $this->fake->assertTimeToLive(64);
        $this->fake->assertAdapter('eth0');
        $this->fake->assertInputFileIncludesTarget('1.1.1.1');
    }
}

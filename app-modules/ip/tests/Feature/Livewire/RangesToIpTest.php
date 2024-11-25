<?php

declare(strict_types=1);

namespace XbNz\Ip\Tests\Feature\Livewire;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use XbNz\Ip\Livewire\RangeToIp;
use XbNz\Ip\Models\IpAddress;

final class RangesToIpTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_accepts_a_line_of_line_separated_ranges_and_returns_a_list_of_line_separated_ip_addresses(): void
    {
        // Act
        $response = Livewire::test(RangeToIp::class)
            ->set('rangeList', <<<RANGES
            1.1.1.1/30
            8.8.8.8-8.8.8.10
            2002::1234:abcd:ffff:c0a8:101/128
            9.9.9.9
            RANGES
            )
            ->call('convert')
            ->call('addToMyIpAddresses');

        // Assert
        $this->assertDatabaseHas(IpAddress::class, ['ip' => '1.1.1.1']);
        $this->assertDatabaseHas(IpAddress::class, ['ip' => '8.8.8.8']);
        $this->assertDatabaseHas(IpAddress::class, ['ip' => '9.9.9.9']);
        $this->assertDatabaseHas(IpAddress::class, ['ip' => '2002::1234:abcd:ffff:c0a8:101']);
    }
}

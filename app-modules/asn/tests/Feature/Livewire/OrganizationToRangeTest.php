<?php

declare(strict_types=1);

namespace XbNz\Asn\Tests\Feature\Livewire;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use XbNz\Asn\Livewire\OrganizationToRange;
use XbNz\Ip\Models\IpAddress;

final class OrganizationToRangeTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_accepts_an_array_of_as_numbers_and_converts_them_to_ip_ranges(): void
    {
        // Act
        $this->assertDatabaseCount(IpAddress::class, 0);
        $response = Livewire::test(OrganizationToRange::class)
            ->set('selectedAsNumbers', [9978])
            ->call('convert')
            ->call('addToMyIpAddresses');

        // Assert
        $this->assertStringContainsString('210.125.176.0 - 210.125.183.255', $response->get('ranges'));
        $this->assertDatabaseHas(IpAddress::class, [
            'ip' => '210.125.183.1',
        ]);
    }
}

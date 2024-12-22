<?php

declare(strict_types=1);

namespace XbNz\Asn\Tests\Feature\Livewire;

use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use XbNz\Asn\Contracts\AsnToRangeInterface;
use XbNz\Asn\Livewire\OrganizationToRange;
use XbNz\Ip\Models\IpAddress;

final class OrganizationToRangeTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_accepts_an_array_of_as_numbers_and_converts_them_to_ip_ranges(): void
    {
        // Arrange
        $this->app->make(DatabaseManager::class)
            ->table('route_views_v4_asns')
            ->insert([
                [
                    'start_ip' => '1.1.1.0',
                    'end_ip' => '1.1.1.255',
                    'asn' => 13335,
                    'organization' => 'Cloudflare, Inc.',
                ],
            ]);

        // Act
        $this->assertDatabaseCount(IpAddress::class, 0);
        $response = Livewire::test(OrganizationToRange::class)
            ->set('selectedAsNumbers', [13335])
            ->call('convert')
            ->call('addToMyIpAddresses');

        // Assert
        $this->assertStringContainsString('1.1.1.0 - 1.1.1.255', $response->get('ranges'));
        $this->assertDatabaseHas(IpAddress::class, [
            'ip' => '1.1.1.1',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_retrieves_ipv4_or_ipv6_or_both_based_on_user_request(): void
    {
        // Arrange
        $this->app->make(DatabaseManager::class)
            ->table('route_views_v4_asns')
            ->insert([
                [
                    'start_ip' => '1.1.1.0',
                    'end_ip' => '1.1.1.255',
                    'asn' => 13335,
                    'organization' => 'Cloudflare, Inc.',
                ],
            ]);

        $this->app->make(DatabaseManager::class)
            ->table('route_views_v6_asns')
            ->insert([
                [
                    'start_ip' => '2001:4860:4860::8888',
                    'end_ip' => '2001:4860:4860::8888',
                    'asn' => 6939,
                    'organization' => 'Hurricane Electric LLC',
                ],
            ]);

        // Act
        $this->assertDatabaseCount(IpAddress::class, 0);
        $response = Livewire::test(OrganizationToRange::class)
            ->set('selectedAsNumbers', [13335, 6939])
            ->set('ipTypeMask', AsnToRangeInterface::FILTER_IPV4 | AsnToRangeInterface::FILTER_IPV6)
            ->call('convert');

        // Assert
        $this->assertStringContainsString('2001:4860:4860::8888 - 2001:4860:4860::8888', $response->get('ranges'));
        $this->assertStringContainsString('1.1.1.0 - 1.1.1.255', $response->get('ranges'));
    }
}

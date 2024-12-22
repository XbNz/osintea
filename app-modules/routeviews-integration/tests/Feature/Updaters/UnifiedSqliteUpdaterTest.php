<?php

declare(strict_types=1);

namespace XbNz\Tests\Feature\Updaters;

use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use XbNz\RouteviewsIntegration\Updaters\UnifiedSqliteUpdater;

final class UnifiedSqliteUpdaterTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fills_the_ipv4_and_ipv6_tables_with_data_from_the_downloaded_csvs(): void
    {
        // Arrange
        Http::fake([
            '*ipv4.csv' => Http::response(file_get_contents(__DIR__.'/../../Fixtures/route_views_ipv4_asn_sample.csv')),
            '*ipv6.csv' => Http::response(file_get_contents(__DIR__.'/../../Fixtures/route_views_ipv6_asn_sample.csv')),
        ]);

        $updater = $this->app->make(UnifiedSqliteUpdater::class);

        // Act
        $updater->update();

        // Assert
        $this->assertDatabaseHas('route_views_v4_asns', ['asn' => 13335]);
        $this->assertDatabaseHas('route_views_v6_asns', ['asn' => 6939]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_all_previous_data_in_both_tables(): void
    {
        // Arrange
        Http::fake([
            '*ipv4.csv' => Http::response(file_get_contents(__DIR__.'/../../Fixtures/route_views_ipv4_asn_sample.csv')),
            '*ipv6.csv' => Http::response(file_get_contents(__DIR__.'/../../Fixtures/route_views_ipv6_asn_sample.csv')),
        ]);

        $updater = $this->app->make(UnifiedSqliteUpdater::class);

        $this->app->make(DatabaseManager::class)
            ->table('route_views_v4_asns')
            ->insert([
                'start_ip' => '70.25.136.0',
                'end_ip' => '70.25.136.255',
                'asn' => 577,
                'organization' => 'Bell Canada',
            ]);

        $this->app->make(DatabaseManager::class)
            ->table('route_views_v6_asns')
            ->insert([
                'start_ip' => '2001:4860:4860::8888',
                'end_ip' => '2001:4860:4860::8888',
                'asn' => 15169,
                'organization' => 'Google LLC',
            ]);

        // Act
        $updater->update();

        // Assert
        $this->assertDatabaseHas('route_views_v4_asns', ['asn' => 13335]);
        $this->assertDatabaseHas('route_views_v6_asns', ['asn' => 6939]);
        $this->assertDatabaseMissing('route_views_v4_asns', ['asn' => 577]);
        $this->assertDatabaseMissing('route_views_v6_asns', ['asn' => 15169]);
    }
}

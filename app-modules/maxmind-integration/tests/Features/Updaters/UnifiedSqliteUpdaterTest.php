<?php

declare(strict_types=1);

namespace XbNz\MaxmindIntegration\Tests\Features\Updaters;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use XbNz\MaxmindIntegration\Updaters\UnifiedSqliteUpdater;

final class UnifiedSqliteUpdaterTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fills_the_ipv4_and_ipv6_tables_with_data_from_the_downloaded_csvs(): void
    {
        // Arrange
        Http::fake([
            '*ipv4.csv.gz' => Http::response(file_get_contents(__DIR__.'/../../Fixtures/geolite2-city-ipv4.csv.gz')),
            '*ipv6.csv.gz' => Http::response(file_get_contents(__DIR__.'/../../Fixtures/geolite2-city-ipv6.csv.gz')),
        ]);

        $updater = $this->app->make(UnifiedSqliteUpdater::class);

        // Act
        $updater->update();

        // Assert
        $this->assertDatabaseCount('maxmind_v4_geolocations', 1);
        $this->assertDatabaseHas('maxmind_v4_geolocations', [
            'start_ip' => '1.0.1.0',
            'end_ip' => '1.0.3.255',
        ]);

        $this->assertDatabaseCount('maxmind_v6_geolocations', 1);
        $this->assertDatabaseHas('maxmind_v6_geolocations', [
            'start_ip' => '2001:200::',
            'end_ip' => '2001:200:ffff:ffff:ffff:ffff:ffff:ffff',
        ]);

        $pointIpv4 = $this->app->make(DatabaseManager::class)
            ->table('maxmind_v4_geolocations')
            ->selectRaw('ST_AsText(coordinates) as coordinates')
            ->sole();

        $pointIpv6 = $this->app->make(DatabaseManager::class)
            ->table('maxmind_v6_geolocations')
            ->selectRaw('ST_AsText(coordinates) as coordinates')
            ->sole();

        $this->assertSame('POINT(34.7732 113.722)', $pointIpv4->coordinates);
        $this->assertSame('POINT(35.6897 139.6895)', $pointIpv6->coordinates);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_all_previous_data_in_both_tables(): void
    {
        // Arrange
        Http::fake([
            '*ipv4.csv.gz' => Http::response(file_get_contents(__DIR__.'/../../Fixtures/geolite2-city-ipv4.csv.gz')),
            '*ipv6.csv.gz' => Http::response(file_get_contents(__DIR__.'/../../Fixtures/geolite2-city-ipv6.csv.gz')),
        ]);

        $updater = $this->app->make(UnifiedSqliteUpdater::class);

        $this->app->make(DatabaseManager::class)
            ->table('maxmind_v4_geolocations')
            ->insert([
                'start_ip' => '1.1.1.0',
                'end_ip' => '1.1.1.255',
                'coordinates' => new Expression('ST_GeomFromText("POINT(10.739314523850254 59.91158561885328)")'),
            ]);

        $this->app->make(DatabaseManager::class)
            ->table('maxmind_v6_geolocations')
            ->insert([
                'start_ip' => '2001:4860:4860:0000:0000:0000:0000:8888',
                'end_ip' => '2001:4860:4860:0000:0000:0000:0000:8889',
                'coordinates' => new Expression('ST_GeomFromText("POINT(10.739314523850254 59.91158561885328)")'),
            ]);

        // Act
        $updater->update();

        // Assert
        $this->assertDatabaseCount('maxmind_v4_geolocations', 1);
        $this->assertDatabaseCount('maxmind_v6_geolocations', 1);

        $this->assertDatabaseMissing('maxmind_v4_geolocations', ['start_ip' => '1.1.1.0']);
        $this->assertDatabaseMissing('maxmind_v6_geolocations', ['start_ip' => '2001:4860:4860:0000:0000:0000:0000:8888']);
    }
}

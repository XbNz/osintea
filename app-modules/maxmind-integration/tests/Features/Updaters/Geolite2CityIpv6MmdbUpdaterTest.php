<?php

declare(strict_types=1);

namespace XbNz\MaxmindIntegration\Tests\Feature\Updaters;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use XbNz\MaxmindIntegration\MaxmindIpToCoordinates;
use XbNz\MaxmindIntegration\Updaters\Geolite2CityIpv6MmdbUpdater;
use XbNz\Shared\ValueObjects\Coordinates;

final class Geolite2CityIpv6MmdbUpdaterTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_downloads_a_fresh_mmbd_and_uses_it_to_execute_a_successful_ip_lookup(): void
    {
        // Arrange
        $mmdb = $this->app->make(Repository::class)->get('maxmind-integration.geolite2_city_mmdb.ipv6');

        @rename($mmdb, "{$mmdb}.old");

        Http::fake([
            '*ipv6.mmdb' => Http::response(file_get_contents("{$mmdb}.old")),
        ])->preventStrayRequests();

        try {
            $this->app->make(MaxmindIpToCoordinates::class)->execute('2001:4860:4860::8888');
            $this->fail('Expected exception to be thrown');
        } catch (Exception) {
        }

        // Act
        $this->app->make(Geolite2CityIpv6MmdbUpdater::class)->update();

        // Assert
        $result = $this->app->make(MaxmindIpToCoordinates::class)->execute('2001:4860:4860::8888');
        $this->assertInstanceOf(Coordinates::class, $result);

        @unlink("{$mmdb}.old");
    }
}

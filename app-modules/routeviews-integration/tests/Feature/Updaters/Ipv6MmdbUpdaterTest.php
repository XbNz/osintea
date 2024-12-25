<?php

declare(strict_types=1);

namespace XbNz\RouteviewsIntegration\Tests\Feature\Updaters;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use XbNz\RouteviewsIntegration\RouteViewsIpToAsn;
use XbNz\RouteviewsIntegration\Updaters\Ipv6MmdbUpdater;

final class Ipv6MmdbUpdaterTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_downloads_a_fresh_mmbd_and_uses_it_to_execute_a_successful_ip_lookup(): void
    {
        // Arrange
        $mmdb = $this->app->make(Repository::class)->get('routeviews-integration.asn_mmdb.ipv6');

        @rename($mmdb, "{$mmdb}.old");

        Http::fake([
            '*ipv6.mmdb' => Http::response(file_get_contents("{$mmdb}.old")),
        ])->preventStrayRequests();

        try {
            $this->app->make(RouteViewsIpToAsn::class)->execute('2606:4700:4700::1111');
            $this->fail('Expected exception to be thrown');
        } catch (Exception) {
        }

        // Act
        $this->app->make(Ipv6MmdbUpdater::class)->update();

        // Assert
        $shouldBeCloudflare = $this->app->make(RouteViewsIpToAsn::class)->execute('2606:4700:4700::1111');
        $this->assertSame('Cloudflare, Inc.', $shouldBeCloudflare->organization);

        @unlink("{$mmdb}.old");
    }
}

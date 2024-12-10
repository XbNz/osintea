<?php

declare(strict_types=1);

namespace XbNz\RouteviewsIntegration\Tests\Feature;

use InvalidArgumentException;
use Tests\TestCase;
use XbNz\Asn\Contracts\AsnToRangeInterface;
use XbNz\Asn\ValueObject\IpRange;
use XbNz\RouteviewsIntegration\RouteViewsAsnToRange;
use XbNz\RouteviewsIntegration\RouteViewsIpToAsn;
use XbNz\Shared\ValueObjects\IpType;

final class RouteViewsAsnToRangeTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function returns_a_collection_of_ipv4_ranges_when_provided_an_as_number(): void
    {
        // Arrange
        $asnToRange = $this->app->make(RouteViewsAsnToRange::class);

        // Act
        $result = $asnToRange
            ->asNumber(13335)
            ->execute();

        // Assert
        $shouldBeCloudflare = $result
            ->unique(fn (IpRange $ipRange) => $ipRange->asn->asNumber)
            ->sole();

        $this->assertStringContainsStringIgnoringCase('cloudflare', $shouldBeCloudflare->asn->organization);

        $this->assertStringContainsStringIgnoringCase(
            'cloudflare',
            $this->app->make(RouteViewsIpToAsn::class)->execute($shouldBeCloudflare->startIp)->organization
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function returns_a_collection_of_ipv6_ranges_when_provided_an_as_number(): void
    {
        // Arrange
        $asnToRange = $this->app->make(RouteViewsAsnToRange::class);

        // Act
        $result = $asnToRange
            ->asNumber(13335)
            ->filterIpType(AsnToRangeInterface::FILTER_IPV6)
            ->execute();

        // Assert
        $shouldBeCloudflare = $result
            ->unique(fn (IpRange $ipRange) => $ipRange->asn->asNumber)
            ->sole();

        $this->assertStringContainsStringIgnoringCase('cloudflare', $shouldBeCloudflare->asn->organization);

        $this->assertStringContainsStringIgnoringCase(
            'cloudflare',
            $this->app->make(RouteViewsIpToAsn::class)->execute($shouldBeCloudflare->startIp)->organization
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function returns_a_collection_of_fuzzy_searched_ipv4_ranges_when_provided_an_organization(): void
    {
        // Arrange
        $asnToRange = $this->app->make(RouteViewsAsnToRange::class);

        // Act
        $result = $asnToRange
            ->organization('loudflar')
            ->execute();

        // Assert
        $shouldBeCloudflare = $result
            ->unique(fn (IpRange $ipRange) => $ipRange->asn->asNumber)
            ->filter(fn (IpRange $ipRange) => $ipRange->asn->asNumber === 13335)
            ->sole();

        $this->assertStringContainsStringIgnoringCase('cloudflare', $shouldBeCloudflare->asn->organization);

        $this->assertStringContainsStringIgnoringCase(
            'cloudflare',
            $this->app->make(RouteViewsIpToAsn::class)->execute($shouldBeCloudflare->startIp)->organization
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function returns_a_collection_of_fuzzy_searched_ipv6_ranges_when_provided_an_organization(): void
    {
        // Arrange
        $asnToRange = $this->app->make(RouteViewsAsnToRange::class);

        // Act
        $result = $asnToRange
            ->organization('loudflar')
            ->filterIpType(AsnToRangeInterface::FILTER_IPV6)
            ->execute();

        // Assert
        $shouldBeCloudflare = $result
            ->unique(fn (IpRange $ipRange) => $ipRange->asn->asNumber)
            ->filter(fn (IpRange $ipRange) => $ipRange->asn->asNumber === 13335)
            ->sole();

        $this->assertStringContainsStringIgnoringCase('cloudflare', $shouldBeCloudflare->asn->organization);

        $this->assertStringContainsStringIgnoringCase(
            'cloudflare',
            $this->app->make(RouteViewsIpToAsn::class)->execute($shouldBeCloudflare->startIp)->organization
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function returns_a_collection_of_ipv4_and_ipv6_ranges(): void
    {
        // Arrange
        $asnToRange = $this->app->make(RouteViewsAsnToRange::class);

        // Act
        $result = $asnToRange
            ->asNumber(13335)
            ->filterIpType(AsnToRangeInterface::FILTER_IPV4 | AsnToRangeInterface::FILTER_IPV6)
            ->execute();

        // Assert
        $hasV4 = $result->filter(fn (IpRange $ipRange) => $ipRange->ipType === IpType::IPv4)->isNotEmpty();
        $hasV6 = $result->filter(fn (IpRange $ipRange) => $ipRange->ipType === IpType::IPv6)->isNotEmpty();

        $this->assertTrue($hasV4);
        $this->assertTrue($hasV6);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_an_exception_when_given_no_identifiers(): void
    {
        // Arrange
        $asnToRange = $this->app->make(RouteViewsAsnToRange::class);

        // Act & Assert
        try {
            $asnToRange->execute();
        } catch (InvalidArgumentException) {
            $this->assertTrue(true);

            return;
        }

        $this->fail('Expected exception was not thrown');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_a_builder_instance_with_all_unique_as_numbers(): void
    {
        // Arrange
        $asnToRange = $this->app->make(RouteViewsAsnToRange::class);

        // Act
        $result = $asnToRange
            ->asNumber(13335)
            ->all();

        // Assert
        $this->assertSame(1, $result->count());
    }
}

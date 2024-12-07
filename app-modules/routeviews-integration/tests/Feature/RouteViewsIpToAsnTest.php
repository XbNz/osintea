<?php

declare(strict_types=1);

namespace XbNz\RouteviewsIntegration\Tests\Feature;

use Tests\TestCase;
use XbNz\RouteviewsIntegration\RouteViewsIpToAsn;
use XbNz\Shared\Exceptions\InvalidIpAddressException;

final class RouteViewsIpToAsnTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_converts_an_ipv4_to_its_asn(): void
    {
        // Arrange
        $ipToAsn = $this->app->make(RouteViewsIpToAsn::class);

        // Act
        $result = $ipToAsn->execute('1.1.1.1');

        // Assert
        $this->assertSame('Cloudflare, Inc.', $result->organization);
        $this->assertSame(13335, $result->asNumber);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_an_exception_if_supplied_a_private_ip(): void
    {
        // Arrange
        $ipToAsn = $this->app->make(RouteViewsIpToAsn::class);

        // Act & Assert
        try {
            $ipToAsn->execute('127.0.0.1');
        } catch (InvalidIpAddressException) {
            $this->assertTrue(true);

            return;
        }

        $this->fail('Expected exception was not thrown');
    }
}

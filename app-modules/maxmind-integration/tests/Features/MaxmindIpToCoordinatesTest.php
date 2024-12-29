<?php

declare(strict_types=1);

namespace XbNz\MaxmindIntegration\Tests\Features;

use Tests\TestCase;
use XbNz\MaxmindIntegration\MaxmindIpToCoordinates;
use XbNz\Shared\Exceptions\InvalidIpAddressException;
use XbNz\Shared\ValueObjects\Coordinates;

final class MaxmindIpToCoordinatesTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_takes_an_ip_and_returns_coordinates(): void
    {
        // Arrange
        $ipToCoordinates = $this->app->make(MaxmindIpToCoordinates::class);

        // Act
        $resultV4 = $ipToCoordinates->execute('55.55.55.55');
        $resultV6 = $ipToCoordinates->execute('2001:4860:4860::8888');

        // Assert
        $this->assertInstanceOf(Coordinates::class, $resultV4);
        $this->assertInstanceOf(Coordinates::class, $resultV6);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_an_exception_if_supplied_a_private_ip(): void
    {
        // Arrange
        $ipToCoordinates = $this->app->make(MaxmindIpToCoordinates::class);

        // Act & Assert
        try {
            $ipToCoordinates->execute('127.0.0.1');
        } catch (InvalidIpAddressException) {
            $this->assertTrue(true);

            return;
        }

        $this->fail('Expected exception was not thrown');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function if_the_ip_given_does_not_belong_to_any_location_it_returns_null(): void
    {
        // Arrange
        $ipToCoordinates = $this->app->make(MaxmindIpToCoordinates::class);

        // Act
        $result = $ipToCoordinates->execute('224.0.0.0');

        // Assert
        $this->assertNull($result);
    }
}

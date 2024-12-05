<?php

declare(strict_types=1);

namespace XbNz\Shared\Tests\Unit;

use PHPUnit\Framework\TestCase;
use XbNz\Shared\Exceptions\InvalidIpAddressException;
use XbNz\Shared\IpValidator;
use XbNz\Shared\ValueObjects\IpType;

final class IpValidatorTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function public_ipv4_test(): void
    {
        IpValidator::make('1.1.1.1')
            ->assertV4()
            ->assertPublic();

        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function private_ipv4_test(): void
    {
        IpValidator::make('10.0.0.1')
            ->assertV4()
            ->assertNotPublic();

        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function public_ipv6_test(): void
    {
        IpValidator::make('2001:4860:4860::8888')
            ->assertV6()
            ->assertPublic();

        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function private_ipv6_test(): void
    {
        IpValidator::make('fd00::1')
            ->assertV6()
            ->assertNotPublic();

        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function invalid_ip_test(): void
    {
        $this->expectException(InvalidIpAddressException::class);

        IpValidator::make('invalid')
            ->assertValid();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function invalid_ipv4_test(): void
    {
        $this->expectException(InvalidIpAddressException::class);

        IpValidator::make('2001:4860:4860::8888')
            ->assertV4();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function invalid_ipv6_test(): void
    {
        $this->expectException(InvalidIpAddressException::class);

        IpValidator::make('1.1.1.1')
            ->assertV6();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_determines_ip_type(): void
    {
        $ipValidator = IpValidator::make('1.1.1.1');
        $ipValidatorV6 = IpValidator::make('2001:4860:4860::8888');

        $this->assertEquals(IpType::IPv4, $ipValidator->determineType());
        $this->assertEquals(IpType::IPv6, $ipValidatorV6->determineType());
    }
}

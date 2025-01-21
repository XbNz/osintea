<?php

declare(strict_types=1);

namespace XbNz\Masscan\Tests\Feature;

use Spatie\TemporaryDirectory\TemporaryDirectory;
use Tests\TestCase;
use XbNz\Masscan\MasscanIcmpScanner;
use XbNz\Shared\Enums\IpType;
use XbNz\Shared\Enums\PortState;
use XbNz\Shared\Enums\ProtocolType;

final class MasscanIcmpScannerTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_probes_ip_addresses_in_an_input_file_for_available_icmp_protocol(): void
    {
        // Arrange
        $masscanIcmp = $this->app->make(MasscanIcmpScanner::class);
        $path = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('input.txt');

        touch($path);

        $this->assertFileExists($path);

        file_put_contents(
            $path,
            implode(PHP_EOL, [
                '1.1.1.1',
            ]),
        );

        $masscanIcmp
            ->rate(10)
            ->inputFilePath($path);

        // Act
        $result = $masscanIcmp->execute();

        // Assert
        $this->assertSame('1.1.1.1', $result[0]->ip);
        $this->assertSame(IpType::IPv4, $result[0]->ipType);
        $this->assertSame(ProtocolType::ICMP, $result[0]->protocol);
        $this->assertArrayHasKey(0, $result[0]->ports);
        $this->assertSame(PortState::Open, $result[0]->ports[0]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function the_output_file_is_destroyed_when_the_object_is_garbage_collected(): void
    {
        // Arrange
        $masscanIcmp = $this->app->make(MasscanIcmpScanner::class);
        $path = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('output.txt');

        touch($path);

        $this->assertFileExists($path);

        $masscanIcmp->outputFilePath($path);

        // Act
        unset($masscanIcmp);

        // Assert
        $this->assertFileDoesNotExist($path);
    }
}

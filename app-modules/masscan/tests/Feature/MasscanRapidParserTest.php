<?php

declare(strict_types=1);

namespace XbNz\Masscan\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Tests\TestCase;
use XbNz\Ip\Exceptions\IpParserException;
use XbNz\Masscan\MasscanRapidParser;

final class MasscanRapidParserTest extends TestCase
{
    private string $inputFilePath;

    private string $outputFilePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inputFilePath = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path(Str::random(10).'.txt');

        $this->outputFilePath = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path(Str::random(10).'.txt');

        touch($this->inputFilePath);
        touch($this->outputFilePath);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_receives_an_input_file_with_ips_and_cidrs_and_returns_a_list_of_ips(): void
    {
        // Arrange
        $masscanRapidParser = $this->app->make(MasscanRapidParser::class);

        file_put_contents(
            $this->inputFilePath,
            implode(PHP_EOL, [
                '8.8.8.8',
                '1.1.1.0/30',
                '2002::1234:abcd:ffff:c0a8:101/128',
            ]),
        );

        // Act
        $masscanRapidParser
            ->inputFilePath($this->inputFilePath)
            ->outputFilePath($this->outputFilePath)
            ->parse();

        // Assert
        $this->assertFileExists($this->outputFilePath);
        $ips = $this->app->make(Filesystem::class)
            ->lines($this->outputFilePath)
            ->reject(fn (string $line) => empty($line))
            ->toArray();

        $this->assertContains('2002::1234:abcd:ffff:c0a8:101', $ips);
        $this->assertContains('1.1.1.0', $ips);
        $this->assertContains('1.1.1.1', $ips);
        $this->assertContains('1.1.1.2', $ips);
        $this->assertContains('1.1.1.3', $ips);
        $this->assertContains('8.8.8.8', $ips);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_an_exception_if_the_opration_fails(): void
    {
        // Arrange
        $masscanRapidParser = $this->app->make(MasscanRapidParser::class);

        file_put_contents(
            $this->inputFilePath,
            implode(PHP_EOL, [
                '1.1.1.22222',
            ]),
        );

        // Act & Assert
        try {
            $masscanRapidParser
                ->inputFilePath($this->inputFilePath)
                ->outputFilePath($this->outputFilePath)
                ->timeout(1)
                ->parse();
        } catch (IpParserException) {
            $this->assertTrue(true);

            return;
        }

        $this->fail('An exception should have been thrown');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unlink($this->inputFilePath);
        unlink($this->outputFilePath);
    }
}

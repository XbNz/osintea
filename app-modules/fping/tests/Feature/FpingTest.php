<?php

declare(strict_types=1);

namespace XbNz\Fping\Tests\Feature;

use Generator;
use Illuminate\Support\Facades\Process;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Tests\TestCase;
use XbNz\Fping\Fping;

final class FpingTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function making_live_icmp_request_works(): void
    {
        // Arrange
        $fping = $this->app->make(Fping::class);
        $path = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('input.txt');

        touch($path);

        $this->assertFileExists($path);

        file_put_contents(
            $path,
            implode(PHP_EOL, [
                '8.8.8.8',
                'one.one.one.one',
            ]),
        );

        // Act
        $results = $fping
            ->inputFilePath($path)
            ->intervalPerHost(0.1)
            ->count(3)
            ->execute();

        // Assert
        $this->assertCount(2, $results);
        $this->assertCount(3, $results[0]->sequences);
        $this->assertCount(3, $results[1]->sequences);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('optionProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function options_are_applied(string $methodName, mixed $value, $commandLineOptionExpected): void
    {
        // Arrange
        Process::fake([
            "*fping --{$commandLineOptionExpected}" => Process::result(exitCode: 0),
        ])->preventingStrayProcesses();
        $fping = $this->app->make(Fping::class);

        $path = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('input.txt');

        touch($path);

        $this->assertFileExists($path);

        file_put_contents(
            $path,
            implode(PHP_EOL, [
                '8.8.8.8',
            ]),
        );

        // Act
        $fping
            ->inputFilePath($path)
            ->{$methodName}($value)
            ->execute();
    }

    public static function optionProvider(): Generator
    {
        yield from [
            'size' => ['size', 100, 'size'],
            'backoffFactor' => ['backoffFactor', 2.5, 'backoff'],
            'count' => ['count', 5, 'vcount'],
            'timeToLive' => ['timeToLive', 128, 'ttl'],
            'interval' => ['interval', 0.5, 'interval'],
            'dontFragment' => ['dontFragment', true, 'dontfrag'],
            'typeOfService' => ['typeOfService', '0x00', 'tos'],
            'intervalPerHost' => ['intervalPerHost', 2000, 'period'],
            'retries' => ['retries', 2, 'retry'],
            'sendRandomData' => ['sendRandomData', true, 'random'],
            'sourceAddress' => ['sourceAddress', '1.1.1.1', 'src'],
            'timeout' => ['timeout', 1000, 'timeout'],
            'showByIp' => ['showByIp', false, 'addr'],
            'quiet' => ['quiet', false, 'quiet'],
        ];
    }
}

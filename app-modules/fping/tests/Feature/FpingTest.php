<?php

declare(strict_types=1);

namespace XbNz\Fping\Tests\Feature;

use Generator;
use Illuminate\Process\PendingProcess;
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function the_output_file_is_destroyed_when_the_object_is_garbage_collected(): void
    {
        // Arrange
        $fping = $this->app->make(Fping::class);
        $path = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('output.txt');

        touch($path);

        $this->assertFileExists($path);

        $fping->outputFilePath($path);

        // Act
        unset($fping);

        // Assert
        $this->assertFileDoesNotExist($path);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('optionalOptionProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function options_are_applied(string $methodName, mixed $value, $commandLineOptionExpected): void
    {
        // Arrange
        $fake = Process::fake([
            "*--{$commandLineOptionExpected}*" => Process::result(exitCode: 0),
        ]);

        $this->swap(PendingProcess::class, $fake->newPendingProcess());

        $fping = $this->app->make(Fping::class);

        $inputPath = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('input.txt');

        $outputPath = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('output.txt');

        touch($inputPath);
        touch($outputPath);

        $this->assertFileExists($inputPath);

        // Act
        $results = $fping
            ->inputFilePath($inputPath)
            ->outputFilePath($outputPath)
            ->{$methodName}($value)
            ->execute();

        // Assert
        $fake->assertRan(function (PendingProcess $process) use ($commandLineOptionExpected) {
            $this->assertStringContainsString("--{$commandLineOptionExpected}", $process->command);

            return true;
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_accept_an_ip_address_string(): void
    {
        // Arrange
        $fping = $this->app->make(Fping::class);

        // Act
        $results = $fping
            ->target('1.1.1.1')
            ->execute();

        // Assert
        $this->assertCount(1, $results);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('staticOptionProviders')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function static_options_are_applied($commandLineOptionExpected): void
    {
        // Arrange
        $fake = Process::fake([
            "*--{$commandLineOptionExpected}*" => Process::result(exitCode: 0),
        ]);

        $this->swap(PendingProcess::class, $fake->newPendingProcess());

        $fping = $this->app->make(Fping::class);

        $inputPath = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('input.txt');

        $outputPath = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('output.txt');

        touch($inputPath);
        touch($outputPath);

        $this->assertFileExists($inputPath);

        // Act
        $results = $fping
            ->inputFilePath($inputPath)
            ->outputFilePath($outputPath)
            ->execute();

        // Assert
        $fake->assertRan(function (PendingProcess $process) use ($commandLineOptionExpected) {
            $this->assertStringContainsString("--{$commandLineOptionExpected}", $process->command);

            return true;
        });
    }

    public static function optionalOptionProvider(): Generator
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
        ];
    }

    public static function staticOptionProviders(): Generator
    {
        yield from [
            'showByIp' => ['addr'],
            'quiet' => ['quiet'],
        ];
    }
}

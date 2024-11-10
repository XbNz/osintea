<?php

declare(strict_types=1);

namespace XbNz\Shared\Tests\Feature;

use Tests\TestCase;
use XbNz\Shared\BinFinder;
use XbNz\Shared\Exception\BinaryNotExecutableException;
use XbNz\Shared\Exception\BinaryNotFoundException;

final class BinFinderTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_detects_the_architecture_and_finds_a_matching_bin_in_the_supplied_folder(): void
    {
        // Arrange
        $binFinder = $this->app->make(BinFinder::class);

        // Act
        $result = $binFinder->prefix('example_executable')
            ->inDirectory(__DIR__.'/../Fixtures')
            ->find();

        // Assert
        $this->assertStringContainsString(
            'example',
            $result
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_an_exception_is_no_bin_is_found(): void
    {
        // Arrange
        $binFinder = $this->app->make(BinFinder::class);

        // Act & Assert
        try {
            $binFinder->prefix('definitely_non_existent')
                ->inDirectory(__DIR__.'/../Fixtures')
                ->find();
        } catch (BinaryNotFoundException) {
            $this->assertTrue(true);

            return;
        }

        $this->fail('BinaryNotFoundException was not thrown');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_an_exception_if_the_file_is_not_executable(): void
    {
        // Arrange
        $binFinder = $this->app->make(BinFinder::class);

        // Act & Assert
        try {
            $binFinder->prefix('example')
                ->inDirectory(__DIR__.'/../Fixtures')
                ->find();
        } catch (BinaryNotExecutableException) {
            $this->assertTrue(true);

            return;
        }

        $this->fail('BinaryNotExecutableException was not thrown');
    }
}

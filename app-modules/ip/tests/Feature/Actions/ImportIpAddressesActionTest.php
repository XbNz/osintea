<?php

declare(strict_types=1);

namespace XbNz\Ip\Tests\Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Tests\TestCase;
use Webmozart\Assert\InvalidArgumentException;
use XbNz\Ip\Actions\ImportIpAddressesAction;
use XbNz\Ip\Models\IpAddress;

final class ImportIpAddressesActionTest extends TestCase
{
    use RefreshDatabase;

    private string $temporaryFilePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->temporaryFilePath = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path(Str::random(10).'.txt');

        touch($this->temporaryFilePath);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_imports_ip_addresses_from_a_file(): void
    {
        // Arrange
        $this->assertFileExists($this->temporaryFilePath);

        file_put_contents($this->temporaryFilePath, <<<'TEXT'
        1.1.1.1
        255.255.255.255
        2002::1234:abcd:ffff:c0a7:a4eb
        TEXT);

        $action = $this->app->make(ImportIpAddressesAction::class);

        // Act
        $action->handle($this->temporaryFilePath);

        // Assert
        $this->assertDatabaseCount(IpAddress::class, 3);
        $this->assertDatabaseHas(IpAddress::class,
            ['ip' => '1.1.1.1', 'type' => 4],
        );

        $this->assertDatabaseHas(IpAddress::class,
            ['ip' => '255.255.255.255', 'type' => 4],
        );

        $this->assertDatabaseHas(IpAddress::class,
            ['ip' => '2002::1234:abcd:ffff:c0a7:a4eb', 'type' => 6],
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function exception_thrown_if_invalid_ip_detected(): void
    {
        // Arrange
        $this->assertFileExists($this->temporaryFilePath);

        file_put_contents($this->temporaryFilePath, <<<'TEXT'
        totally invalid ip
        TEXT);

        $action = $this->app->make(ImportIpAddressesAction::class);

        // Act & Assert
        try {
            $action->handle($this->temporaryFilePath);
            $this->fail('An exception should have been thrown');
        } catch (InvalidArgumentException) {
            $this->assertDatabaseCount(IpAddress::class, 0);

            return;
        }

        $this->fail('An exception should have been thrown');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unlink($this->temporaryFilePath);
    }
}

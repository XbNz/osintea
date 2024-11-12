<?php

declare(strict_types=1);

namespace XbNz\Ping\Tests\Feature\Livewire;

use Generator;
use Illuminate\Contracts\Session\Session;
use Livewire\Livewire;
use Native\Laravel\Facades\Window;
use Tests\TestCase;
use XbNz\Fping\Contracts\FpingInterface;
use XbNz\Fping\DTOs\PingResultDTO;
use XbNz\Fping\FakeFping;
use XbNz\Ping\Livewire\Ping;
use Native\Laravel\Windows\Window as WindowClass;
use XbNz\Shared\ValueObjects\IpType;

final class PingTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function rendered_with_defaults(): void
    {
        Livewire::test(Ping::class)
            ->assertViewHasAll([
                'target' => '1.1.1.1',
                'count' => 5,
                'timeBetweenRequests' => 500,
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function submitting_form_results_in_fping_being_hit_with_requested_parameters(): void
    {
        // Arrange
        $fakeFping = new FakeFping();
        $fakeFping->forceReturn([
            $expectedResult = new PingResultDTO(
                ip: '1.1.1.1',
                ipType: IpType::IPv4,
                sequences: []
            ),
        ]);

        Window::shouldReceive('open')
            ->once()
            ->andReturn($fakeWindow = new WindowClass('ping-results'));

        $this->swap(FpingInterface::class, $fakeFping);

        // Act
        $response = Livewire::test(Ping::class)
            ->set('target', '1.1.1.1')
            ->set('count', 2)
            ->set('timeBetweenRequests', 500)
            ->call('ping');

        // Assert
        $fakeFping->assertExecuted();
        $fakeFping->assertCount(2);
        $fakeFping->assertIntervalPerHost(500);
        $fakeFping->assertInputFileIncludesTarget('1.1.1.1');

        $this->assertEquals(
            $this->app->make(Session::class)->get('ping-result'),
            $expectedResult
        );

        $this->assertSame(route('ping-results'), invade($fakeWindow)->url);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('validationProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function validation_tests(array $payload, array $errors): void
    {
        // Arrange
        $livewire = Livewire::test(Ping::class);

        foreach ($payload as $key => $value) {
            $livewire->set($key, $value);
        }

        // Act
        $response = $livewire->call('ping');

        // Assert
        $response->assertHasErrors($errors);
    }

    public static function validationProvider(): Generator
    {
        $default = self::sampleData();

        yield from [
            'count must be at least 1' => [
                'payload' => array_merge($default, [
                    'count' => 0,
                ]),
                'errors' => ['count'],
            ],
            'count must be at most 100' => [
                'payload' => array_merge($default, [
                    'count' => 101,
                ]),
                'errors' => ['count'],
            ],
            'timeBetweenRequests must be at least 100' => [
                'payload' => array_merge($default, [
                    'timeBetweenRequests' => 99,
                ]),
                'errors' => ['timeBetweenRequests'],
            ],
        ];
    }

    private static function sampleData(): array
    {
        return [
            'target' => '1.1.1.1',
            'count' => 5,
            'timeBetweenRequests' => 500,
        ];
    }
}

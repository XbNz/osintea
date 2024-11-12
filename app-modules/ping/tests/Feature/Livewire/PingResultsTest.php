<?php

declare(strict_types=1);

namespace XbNz\Ping\Tests\Feature\Livewire;

use Illuminate\Contracts\Session\Session;
use Livewire\Livewire;
use Tests\TestCase;
use XbNz\Fping\DTOs\PingResultDTO;
use XbNz\Fping\ValueObjects\Sequence;
use XbNz\Ping\Livewire\PingResults;
use XbNz\Shared\ValueObjects\IpType;

final class PingResultsTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_grabs_ping_result_dto_from_session_and_builds_statistics(): void
    {
        // Arrange
        $pingResultDto = new PingResultDTO(
            '1.1.1.1',
            IpType::IPv4,
            [
                new Sequence(1, false, 1),
                new Sequence(1, false, 2),
                new Sequence(2, true, null),
            ]
        );

        $this->app->make(Session::class)->put('ping-result', $pingResultDto);

        // Act
        $response = Livewire::test(PingResults::class);

        // Assert
        $response->assertSet('pingResult', $pingResultDto);
        $response->assertSet('averageRoundTripTime', '1.50');
        $response->assertSet('maximumRoundTripTime', '2.00');
        $response->assertSet('minimumRoundTripTime', '1.00');
        $response->assertSet('packetLossPercentage', '33.33');
        $response->assertSet('lossCount', 1);
        $response->assertSet('totalCount', 3);
        $response->assertSet('standardDeviation', '0.50');
    }
}

<?php

declare(strict_types=1);

namespace XbNz\Ping\Tests\Feature\Jobs;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use XbNz\Ping\Jobs\DeletePingSequenceJob;
use XbNz\Ping\Models\PingSequence;

final class DeletePingSequenceJobTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_a_ping_sequence(): void
    {
        // Arrange
        $pingSequence = PingSequence::factory()->create();

        // Act
        $this->assertDatabaseCount(PingSequence::class, 1);
        $this->app->make(Dispatcher::class)->dispatch(new DeletePingSequenceJob($pingSequence->getData()));

        // Assert
        $this->assertDatabaseMissing(PingSequence::class, ['id' => $pingSequence->id]);
    }
}

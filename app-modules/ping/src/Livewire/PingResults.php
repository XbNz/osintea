<?php

declare(strict_types=1);

namespace XbNz\Ping\Livewire;

use Asantibanez\LivewireCharts\Models\LineChartModel;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Webmozart\Assert\Assert;
use XbNz\Ping\DTOs\PingResultDTO;
use XbNz\Ping\Events\PingSequenceInsertedEvent;
use XbNz\Ping\ValueObjects\Sequence;

#[Layout('components.layouts.secondary-window')]
final class PingResults extends Component
{
    /**
     * @var array<int, PingResultDTO>
     */
    private array $pingResults;

    #[On('native:'.PingSequenceInsertedEvent::class)]
    public function updatePingResult(array $pingResult): void
    {
        if (isset($this->pingResults) && count($this->pingResults) > 100) {
            unset($this->pingResults[max(array_keys($this->pingResults))]);
        }

        $this->pingResults[] = PingResultDTO::fromArray($pingResult);
    }

    #[Computed]
    public function lineChartModel(): LineChartModel
    {
        $lineChart = (new LineChartModel())
            ->sparklined()
            ->setSmoothCurve();

        if (isset($this->pingResults) === false) {
            return $lineChart;
        }

        $sequences = Collection::make($this->pingResults)->pluck('sequences')->flatten();

        foreach ($sequences as $sequence) {
            $lineChart->addPoint(
                null,
                $sequence->lost ? null : $sequence->roundTripTime
            );
        }

        return $lineChart;
    }

    #[Computed]
    public function ip(): string
    {
        if (isset($this->pingResults) === false) {
            return '-';
        }

        return $this->pingResults[0]->ip;
    }

    #[Computed]
    public function averageRoundTripTime(): string
    {
        if (isset($this->pingResults) === false) {
            return '-';
        }

        $roundTripTimes = Collection::make($this->pingResults)
            ->pluck('sequences')
            ->flatten()
            ->reject(fn (Sequence $sequence) => $sequence->lost === true)
            ->map(fn (Sequence $sequence) => $sequence->roundTripTime);

        if ($roundTripTimes->count() === 0) {
            return '-';
        }

        Assert::float($avg = $roundTripTimes->avg());

        return number_format($avg, 2);
    }

    #[Computed]
    public function minimumRoundTripTime(): string
    {
        if (isset($this->pingResults) === false) {
            return '-';
        }

        $roundTripTimes = Collection::make($this->pingResults)
            ->pluck('sequences')
            ->flatten()
            ->reject(fn (Sequence $sequence) => $sequence->lost === true)
            ->map(fn (Sequence $sequence) => $sequence->roundTripTime);

        if ($roundTripTimes->count() === 0) {
            return '-';
        }

        Assert::float($min = $roundTripTimes->min());

        return number_format($min, 2);
    }

    #[Computed]
    public function maximumRoundTripTime(): string
    {
        if (isset($this->pingResults) === false) {
            return '-';
        }

        $roundTripTimes = Collection::make($this->pingResults)
            ->pluck('sequences')
            ->flatten()
            ->reject(fn (Sequence $sequence) => $sequence->lost === true)
            ->map(fn (Sequence $sequence) => $sequence->roundTripTime);

        if ($roundTripTimes->count() === 0) {
            return '-';
        }

        Assert::float($max = $roundTripTimes->max());

        return number_format($max, 2);
    }

    #[Computed]
    public function packetLossPercentage(): string
    {
        if (isset($this->pingResults) === false) {
            return '-';
        }

        $lostSequences = Collection::make($this->pingResults)
            ->pluck('sequences')
            ->flatten()
            ->filter(fn (Sequence $sequence) => $sequence->lost);

        return number_format((count($lostSequences) / $this->totalCount()) * 100, 2);
    }

    #[Computed]
    public function lossCount(): int
    {
        if (isset($this->pingResults) === false) {
            return 0;
        }

        $lostSequences = Collection::make($this->pingResults)
            ->pluck('sequences')
            ->flatten()
            ->filter(fn (Sequence $sequence) => $sequence->lost);

        return count($lostSequences);
    }

    #[Computed]
    public function totalCount(): int
    {
        if (isset($this->pingResults) === false) {
            return 0;
        }

        return Collection::make($this->pingResults)
            ->pluck('sequences')
            ->flatten()
            ->count();
    }

    #[Computed]
    public function standardDeviation(): string
    {
        if (isset($this->pingResults) === false) {
            return '-';
        }

        $roundTripTimes = Collection::make($this->pingResults)
            ->pluck('sequences')
            ->flatten()
            ->reject(fn (Sequence $sequence) => $sequence->lost === true)
            ->map(fn (Sequence $sequence) => $sequence->roundTripTime);

        $mean = $roundTripTimes->avg();
        $variance = $roundTripTimes->map(fn (float $roundTripTime) => ($roundTripTime - $mean) ** 2)->avg();

        Assert::float($variance);

        $standardDeviation = sqrt($variance);

        return number_format($standardDeviation, 2);
    }

    public function render(): View
    {
        return view('ping::livewire.ping-results');
    }
}

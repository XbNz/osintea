<?php

declare(strict_types=1);

namespace XbNz\Ping\Livewire;

use Asantibanez\LivewireCharts\Models\LineChartModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Webmozart\Assert\Assert;
use XbNz\Fping\DTOs\PingResultDTO;
use XbNz\Fping\ValueObjects\Sequence;

final class PingResults extends Component
{
    #[Computed]
    public function lineChartModel(): LineChartModel
    {
        $lineChart = (new LineChartModel())
            ->sparklined()
            ->setSmoothCurve();

        foreach ($this->pingResult()->sequences as $sequence) {
            $lineChart->addPoint(
                $sequence->sequence,
                $sequence->lost ? null : $sequence->roundTripTime
            );
        }

        return $lineChart;
    }

    #[Computed]
    public function pingResult(): PingResultDTO
    {
        $dto = Session::get('ping-result');
        Assert::isInstanceOf($dto, PingResultDTO::class);

        return $dto;
    }

    #[Computed]
    public function averageRoundTripTime(): string
    {
        $roundTripTimes = Collection::make($this->pingResult()->sequences)
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
        $roundTripTimes = Collection::make($this->pingResult()->sequences)
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
        $roundTripTimes = Collection::make($this->pingResult()->sequences)
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
        $lostSequences = Collection::make($this->pingResult()->sequences)
            ->filter(fn (Sequence $sequence) => $sequence->lost);

        return number_format((count($lostSequences) / count($this->pingResult()->sequences)) * 100, 2);
    }

    #[Computed]
    public function lossCount(): int
    {
        $lostSequences = Collection::make($this->pingResult()->sequences)
            ->filter(fn (Sequence $sequence) => $sequence->lost);

        return count($lostSequences);
    }

    #[Computed]
    public function totalCount(): int
    {
        return count($this->pingResult()->sequences);
    }

        #[Computed]
        public function standardDeviation(): string
        {
            $roundTripTimes = Collection::make($this->pingResult()->sequences)
                ->reject(fn ($sequence) => $sequence->lost === true)
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

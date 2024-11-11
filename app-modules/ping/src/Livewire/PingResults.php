<?php

declare(strict_types=1);

namespace XbNz\Ping\Livewire;

use Asantibanez\LivewireCharts\Models\LineChartModel;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use XbNz\Fping\DTOs\PingResultDTO;

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
                $sequence->lost ? 0 : $sequence->roundTripTime
            );
        }

        return $lineChart;
    }

    #[Computed]
    public function pingResult(): PingResultDTO
    {
        return Session::get('ping-result');
    }

    #[Computed]
    public function averageRoundTripTime(): string
    {
        $roundTripTimes = collect($this->pingResult()->sequences)
            ->filter(fn($sequence) => ! $sequence->lost)
            ->map(fn($sequence) => $sequence->roundTripTime);

        return number_format($roundTripTimes->avg(), 2);
    }

    #[Computed]
    public function minimumRoundTripTime(): string
    {
        $roundTripTimes = collect($this->pingResult()->sequences)
            ->filter(fn($sequence) => ! $sequence->lost)
            ->map(fn($sequence) => $sequence->roundTripTime);

        return number_format($roundTripTimes->min(), 2);
    }

    #[Computed]
    public function maximumRoundTripTime(): string
    {
        $roundTripTimes = collect($this->pingResult()->sequences)
            ->filter(fn($sequence) => ! $sequence->lost)
            ->map(fn($sequence) => $sequence->roundTripTime);

        return number_format($roundTripTimes->max(), 2);
    }

    #[Computed]
    public function packetLossPercentage(): string
    {
        $lostSequences = collect($this->pingResult()->sequences)
            ->filter(fn($sequence) => $sequence->lost);

        return number_format((count($lostSequences) / count($this->pingResult()->sequences)) * 100, 2);
    }

    #[Computed]
    public function lossCount(): int
    {
        $lostSequences = collect($this->pingResult()->sequences)
            ->filter(fn($sequence) => $sequence->lost);

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
        $roundTripTimes = collect($this->pingResult()->sequences)
            ->filter(fn($sequence) => ! $sequence->lost)
            ->map(fn($sequence) => $sequence->roundTripTime);

        $mean = $roundTripTimes->avg();
        $variance = $roundTripTimes->map(fn($roundTripTime) => ($roundTripTime - $mean) ** 2)->avg();
        $standardDeviation = sqrt($variance);

        return number_format($standardDeviation, 2);
    }

    public function render(): View
    {
        return view('ping::livewire.ping-results');
    }
}

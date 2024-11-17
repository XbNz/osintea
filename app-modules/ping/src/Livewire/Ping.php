<?php

declare(strict_types=1);

namespace XbNz\Ping\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Webmozart\Assert\Assert;
use XbNz\Ip\Rules\StringResolvesToIpAddressRule;
use XbNz\Ping\DTOs\PingSequenceDto;
use XbNz\Ping\Events\PingSequenceInsertedEvent;
use XbNz\Ping\Jobs\PingJob;
use XbNz\Ping\Models\PingSequence;

#[Layout('components.layouts.secondary-window')]
final class Ping extends Component
{
    public string $target = '';

    protected $listeners = ['refreshComponent' => '$refresh'];

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'target' => ['required', 'string', new StringResolvesToIpAddressRule()],
        ];
    }

    #[On('native:'.PingSequenceInsertedEvent::class)]
    #[Renderless]
    public function updatePingResult(array $record): void
    {
        $sequence = PingSequence::query()
            ->with(['ipAddress'])
            ->findOrFail($record['id'])
            ->getData();

        $ip = gethostbyname($this->target);

        if ($sequence->ip->ip !== $ip) {
            return;
        }

        $this->dispatch('refreshComponent');

        $this->dispatch('newDataPoint', [
            'label' => $sequence->created_at->format('H:i:s'),
            'newData' => $sequence->round_trip_time,
        ]);
    }

    #[Computed]
    public function options(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
                'zoom' => [
                    'enabled' => true,
                    'mode' => 'x',
                    'zoom' => [
                        'wheel' => [
                            'enabled' => true,
                        ],
                        'mode' => 'x',
                    ],
                    'pan' => [
                        'enabled' => true,
                        'mode' => 'x',
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
            'elements' => [
                'line' => [
                    'borderWidth' => 2,
                ],
                'point' => [
                    'radius' => 5,
                    'hoverRadius' => 7,
                ],
            ],
        ];
    }

    #[Computed]
    public function dataset(): array
    {
        $sequences = $this->sequences(20);

        return [
            'labels' => $sequences
                ->map(fn (PingSequenceDto $pingSequence) => $pingSequence->created_at->format('H:i:s'))
                ->values()
                ->toArray(),
            'datasets' => [
                [
                    'label' => 'Round trip',
                    'data' => $sequences
                        ->pluck('round_trip_time')
                        ->values()
                        ->toArray(),
                    'borderWidth' => 2,
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'fill' => false,
                    'tension' => 0.4,
                    'pointBackgroundColor' => 'rgba(75, 192, 192, 1)',
                    'pointRadius' => 5,
                ],
            ],
        ];
    }

    private function sequences(?int $limit = null): Collection
    {
        return PingSequence::query()
            ->with(['ipAddress'])
            ->whereHas('ipAddress', fn (Builder $query) => $query->where('ip', $this->target))
            ->orderBy('created_at', 'desc')
            ->when($limit !== null, fn (Builder $query) => $query->limit($limit))
            ->get()
            ->reverse()
            ->map(fn (PingSequence $pingSequence) => $pingSequence->getData());
    }

    public function deleteSequences(): void
    {
        $this->dispatch('resetChart');

        PingSequence::query()
            ->whereHas('ipAddress', fn (Builder $query) => $query->where('ip', $this->target))
            ->delete();

        $this->reset();
    }

    public function ping(): void
    {
        $this->validate();

        $ip = gethostbyname($this->target);

        $this->target = $ip;

        $this->dispatch('resetChart');

        PingJob::dispatch($ip, 1);
    }

    #[Computed]
    public function averageRoundTripTime(): string
    {
        $roundTripTimes = $this->sequences()
            ->reject(fn (PingSequenceDto $sequence) => $sequence->loss)
            ->map(fn (PingSequenceDto $sequence) => $sequence->round_trip_time);

        if ($roundTripTimes->count() === 0) {
            return '-';
        }

        Assert::float($avg = $roundTripTimes->avg());

        return number_format($avg, 2);
    }

    #[Computed]
    public function minimumRoundTripTime(): string
    {
        $roundTripTimes = $this->sequences()
            ->reject(fn (PingSequenceDto $sequence) => $sequence->loss)
            ->map(fn (PingSequenceDto $sequence) => $sequence->round_trip_time);

        if ($roundTripTimes->count() === 0) {
            return '-';
        }

        Assert::float($min = $roundTripTimes->min());

        return number_format($min, 2);
    }

    #[Computed]
    public function maximumRoundTripTime(): string
    {
        $roundTripTimes = $this->sequences()
            ->reject(fn (PingSequenceDto $sequence) => $sequence->loss)
            ->map(fn (PingSequenceDto $sequence) => $sequence->round_trip_time);

        if ($roundTripTimes->count() === 0) {
            return '-';
        }

        Assert::float($max = $roundTripTimes->max());

        return number_format($max, 2);
    }

    #[Computed]
    public function packetLossPercentage(): string
    {
        if ($this->totalCount() === 0) {
            return '0.00';
        }

        $lostSequences = $this->sequences()
            ->filter(fn (PingSequenceDto $sequence) => $sequence->loss);

        return number_format((count($lostSequences) / $this->totalCount()) * 100, 2);
    }

    #[Computed]
    public function lossCount(): int
    {
        $lostSequences = $this->sequences()
            ->filter(fn (PingSequenceDto $sequence) => $sequence->loss);

        return count($lostSequences);
    }

    #[Computed]
    public function totalCount(): int
    {
        return $this->sequences()->count();
    }

    #[Computed]
    public function standardDeviation(): string
    {
        if ($this->totalCount() === 0) {
            return '-';
        }

        $roundTripTimes = $this->sequences()
            ->reject(fn (PingSequenceDto $sequence) => $sequence->loss)
            ->map(fn (PingSequenceDto $sequence) => $sequence->round_trip_time);

        if ($roundTripTimes->count() === 0) {
            return '-';
        }

        $mean = $roundTripTimes->avg();
        $variance = $roundTripTimes->map(fn (float $roundTripTime) => ($roundTripTime - $mean) ** 2)->avg();

        Assert::float($variance);

        $standardDeviation = sqrt($variance);

        return number_format($standardDeviation, 2);
    }

    public function mount(Request $request): void
    {
        if ($request->has('target')) {
            $this->target = $request->get('target');

            return;
        }

        $this->target = PingSequence::query()
            ->with(['ipAddress'])
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->first()
            ?->getData()
            ->ip
            ->ip
            ?? '1.1.1.1';
    }

    public function render(): View
    {
        return view('ping::livewire.ping');
    }
}

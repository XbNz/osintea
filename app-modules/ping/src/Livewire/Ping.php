<?php

declare(strict_types=1);

namespace XbNz\Ping\Livewire;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Native\Laravel\Facades\ChildProcess;
use Native\Laravel\Facades\Notification;
use Psl\Type;
use Webmozart\Assert\Assert;
use XbNz\Ip\Actions\CreateIpAddressAction;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ip\Rules\StringResolvesToIpAddressRule;
use XbNz\Ping\DTOs\PingSequenceDto;
use XbNz\Ping\Events\PingSequenceInsertedEvent;
use XbNz\Ping\Models\PingSequence;
use XbNz\Shared\Actions\StandardDeviationAction;
use XbNz\Shared\Enums\NativePhpChildProcess;

#[Layout('components.layouts.secondary-window')]
final class Ping extends Component
{
    /**
     * @var array<string, string>
     */
    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];

    public string $target;

    public int $interval = 1000;

    public string $averageRoundTripTime = '-';

    public string $minimumRoundTripTime = '-';

    public string $maximumRoundTripTime = '-';

    public string $packetLossPercentage = '-';

    public string $standardDeviation = '-';

    public int $lossCount = 0;

    public int $totalCount = 0;

    /**
     * @var array<string, mixed>
     */
    private array $defaultChartData = [
        'labels' => [],
        'datasets' => [
            [
                'label' => 'Round trip',
                'data' => [],
                'borderWidth' => 2,
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'fill' => false,
                'tension' => 0.4,
                'pointBackgroundColor' => 'rgba(75, 192, 192, 1)',
                'pointRadius' => 5,
            ],
        ],
    ];

    /**
     * @var array<string, mixed>
     */
    public array $chartOptions = [
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

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'target' => ['required', 'string', new StringResolvesToIpAddressRule()],
            'interval' => ['required', 'integer', 'min:100'],
        ];
    }

    /**
     * @param  array<string, mixed>  $record
     */
    #[On('native:'.PingSequenceInsertedEvent::class)]
    #[Renderless]
    public function updatePingResult(array $record): void
    {
        $sanitized = Type\shape([
            'id' => Type\int(),
        ])->coerce($record);

        if (isset($this->target) === false) {
            return;
        }

        $sequence = PingSequence::query()
            ->with(['ipAddress'])
            ->findOrFail($sanitized['id'])
            ->getData();

        if ($sequence->ip->ip !== $this->ipAddress()->ip) {
            return;
        }

        $this->dispatch('newDataPoint', [
            'label' => $sequence->created_at->format('H:i:s'),
            'newData' => $sequence->round_trip_time,
        ]);

        $this->recalculateStatistics();
        $this->dispatch('refreshComponent');
    }

    public function recalculateStatistics(): void
    {
        $this->averageRoundTripTime = $this->averageRoundTripTime();
        $this->minimumRoundTripTime = $this->minimumRoundTripTime();
        $this->maximumRoundTripTime = $this->maximumRoundTripTime();
        $this->packetLossPercentage = $this->packetLossPercentage();
        $this->standardDeviation = $this->standardDeviation();
        $this->lossCount = $this->lossCount();
        $this->totalCount = $this->totalCount();
    }

    /**
     * @return array<string, mixed>
     */
    private function data(): array
    {
        if (isset($this->target) === false) {
            Notification::new()
                ->title('Cannot load chart')
                ->message('No target has been entered')
                ->show();

            return $this->defaultChartData;
        }

        return [
            'labels' => $this->ipAddress()
                ->ping_sequences
                ->map(fn (PingSequenceDto $pingSequence) => $pingSequence->created_at->format('H:i:s'))
                ->values()
                ->toArray(),
            'datasets' => [
                [
                    'label' => 'Round trip',
                    'data' => $this->ipAddress()
                        ->ping_sequences
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

    public function deleteSequences(): void
    {
        if (isset($this->target) === false) {
            Notification::new()
                ->title('No target to stop')
                ->message('Please enter a target to stop the ping')
                ->show();

            return;
        }

        $this->ipAddress()
            ->ping_sequences
            ->each(fn (PingSequenceDto $sequence) => PingSequence::query()
                ->findOrFail($sequence->id)
                ->delete()
            );

        $this->reset();
    }

    public function ping(
        Request $request,
        CreateIpAddressAction $createIpAddress,
    ): void {
        $this->validate();

        $result = $createIpAddress->handle($this->target);

        ChildProcess::get(NativePhpChildProcess::PingWorker->value)->message('target-add:'.$this->ipAddress()->ip.'::'.$this->interval);
    }

    private function ipAddress(): IpAddressDto
    {
        return IpAddress::query()
            ->with(['pingSequences'])
            ->where('ip', $this->target)
            ->firstOrFail()
            ->getData();
    }

    private function averageRoundTripTime(): string
    {
        $roundTripTimes = $this->ipAddress()
            ->ping_sequences
            ->reject(fn (PingSequenceDto $sequence) => $sequence->loss)
            ->map(fn (PingSequenceDto $sequence) => $sequence->round_trip_time);

        if ($roundTripTimes->count() === 0) {
            return '-';
        }

        Assert::float($avg = $roundTripTimes->avg());

        return number_format($avg, 2);
    }

    private function minimumRoundTripTime(): string
    {
        $roundTripTimes = $this->ipAddress()
            ->ping_sequences
            ->reject(fn (PingSequenceDto $sequence) => $sequence->loss)
            ->map(fn (PingSequenceDto $sequence) => $sequence->round_trip_time);

        if ($roundTripTimes->count() === 0) {
            return '-';
        }

        Assert::float($min = $roundTripTimes->min());

        return number_format($min, 2);
    }

    private function maximumRoundTripTime(): string
    {
        $roundTripTimes = $this->ipAddress()
            ->ping_sequences
            ->reject(fn (PingSequenceDto $sequence) => $sequence->loss)
            ->map(fn (PingSequenceDto $sequence) => $sequence->round_trip_time);

        if ($roundTripTimes->count() === 0) {
            return '-';
        }

        Assert::float($max = $roundTripTimes->max());

        return number_format($max, 2);
    }

    private function packetLossPercentage(): string
    {
        if ($this->ipAddress()->ping_sequences->count() === 0) {
            return '0.00';
        }

        $lostSequences = $this->ipAddress()
            ->ping_sequences
            ->filter(fn (PingSequenceDto $sequence) => $sequence->loss);

        return number_format((count($lostSequences) / $this->totalCount()) * 100, 2);
    }

    private function lossCount(): int
    {
        $lostSequences = $this->ipAddress()
            ->ping_sequences
            ->filter(fn (PingSequenceDto $sequence) => $sequence->loss);

        return count($lostSequences);
    }

    public function totalCount(): int
    {
        return $this->ipAddress()->ping_sequences->count();
    }

    private function standardDeviation(): string
    {
        if ($this->totalCount() === 0) {
            return '-';
        }

        $standardDeviationAction = app(StandardDeviationAction::class);

        $roundTripTimes = $this->ipAddress()
            ->ping_sequences
            ->reject(fn (PingSequenceDto $sequence) => $sequence->loss)
            ->map(fn (PingSequenceDto $sequence) => $sequence->round_trip_time);

        if ($roundTripTimes->count() === 0) {
            return '-';
        }

        $standardDeviation = $standardDeviationAction->handle($roundTripTimes->toArray());

        return number_format($standardDeviation, 2);
    }

    public function stop(): void
    {
        if (isset($this->target) === false) {
            Notification::new()
                ->title('No target to stop')
                ->message('Please enter a target to stop the ping')
                ->show();

            return;
        }

        ChildProcess::get(NativePhpChildProcess::PingWorker->value)->message('target-remove:'.$this->ipAddress()->ip);
    }

    public function mount(Request $request): void
    {
        $this->dispatch('populateChart', [
            'data' => $this->defaultChartData,
            'options' => $this->chartOptions,
        ]);

        if ($request->has('target')) {
            $this->target = $request->string('target')->toString();

            $this->validate();

            $this->target = gethostbyname($this->target);

            $this->recalculateStatistics();

            return;
        }
    }

    public function populateChart(): void
    {
        $this->dispatch('populateChart', [
            'data' => $this->data(),
            'options' => $this->chartOptions,
        ]);
    }

    public function updated(string $property, mixed $value): void
    {
        if ($property !== 'target') {
            return;
        }

        $this->target = gethostbyname($value);
    }

    public function render(): View
    {
        return view('ping::livewire.ping');
    }
}

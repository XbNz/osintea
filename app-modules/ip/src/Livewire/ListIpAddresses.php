<?php

declare(strict_types=1);

namespace XbNz\Ip\Livewire;

use Carbon\CarbonImmutable;
use Chefhasteeth\Pipeline\Pipeline;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Native\Laravel\Facades\Window;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use XbNz\Fping\Contracts\FpingInterface;
use XbNz\Ip\Filters\PacketLossFilter;
use XbNz\Ip\Filters\RoundTripTimeFilter;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\FilterPacketLoss;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\FilterRoundTripTime;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\LimitIpv4;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\LimitIpv6;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\SortByAverageRtt;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\Transporter;
use XbNz\Ip\ViewModels\ListIpAddressesTableViewModel;
use XbNz\Ping\Actions\CreatePingSequenceAction;
use XbNz\Ping\DTOs\CreatePingSequenceDto;
use XbNz\Shared\Enums\NativePhpWindow;

#[Layout('components.layouts.secondary-window')]
final class ListIpAddresses extends Component
{
    /**
     * @var array<string, class-string>
     */
    const array SORT_MAP = [
        'average_rtt' => SortByAverageRtt::class,
    ];

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public int $rowAmount = 100;

    public array $manipulations = [];

    public RoundTripTimeFilter $roundTripTimeFilter;

    public PacketLossFilter $packetLossFilter;

    private function query(): Builder
    {
        $query = IpAddress::query()->with(['pingSequences']);

        $pipes = $this->manipulations;

        if (array_key_exists($this->sortBy, self::SORT_MAP)) {
            $pipes[] = self::SORT_MAP[$this->sortBy];
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        if (empty($pipes) === true) {
            return $query;
        }

        return Pipeline::make()
            ->send(new Transporter(
                $this->sortDirection,
                $query,
                $this->roundTripTimeFilter,
                $this->packetLossFilter,
            ))
            ->through($pipes)
            ->thenReturn()
            ->query;
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function ipAddressCount(): string
    {
        $ipAddress = Str::plural('IP Address', $this->query()->count());
        return number_format($this->query()->count(), thousands_separator: ',') . ' ' . $ipAddress;
    }

    #[Computed]
    public function ipAddresses(): CursorPaginator
    {
        return ListIpAddressesTableViewModel::collect($this->query()->cursorPaginate($this->rowAmount));
    }

    public function pingActive(FpingInterface $fping, CreatePingSequenceAction $createPingSequenceAction): void
    {
        $inputFile = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('input_'.Str::random(5).'.txt');

        touch($inputFile);

        $this->query()
            ->clone()
            ->select('ip')
            ->lazyById(50_000)
            ->pluck('ip')
            ->chunk(50_000)
            ->each(fn (LazyCollection $chunk) => file_put_contents($inputFile, $chunk->implode(PHP_EOL).PHP_EOL, FILE_APPEND));

        foreach ($fping->inputFilePath($inputFile)->execute() as $pingResult) {
            $createPingSequenceAction->handle(
                new CreatePingSequenceDto(
                    IpAddress::query()->where('ip', $pingResult->ip)->sole()->getData(),
                    $pingResult->sequences[0],
                    CarbonImmutable::now(),
                )
            );
        }
    }

    public function deleteActive(): void
    {
        $this->query()->delete();
    }

    public function goToPingWindow(string $ipAddress): void
    {
        Window::open(NativePhpWindow::Ping->value.':'.Str::random(8))
            ->route('ping', [
                'target' => $ipAddress,
            ])
            ->showDevTools(false)
            ->titleBarHiddenInset()
            ->transparent()
            ->height(500)
            ->width(775)
            ->minHeight(500)
            ->minWidth(775);
    }

    public function limitV4(): void
    {
        $this->clearIpTypeLimits();

        $this->manipulations[] = LimitIpv4::class;
    }

    public function limitV6(): void
    {
        $this->clearIpTypeLimits();

        $this->manipulations[] = LimitIpv6::class;
    }

    public function applyFilters(): void
    {
        $this->clearFilters();

        $toApply = [
            FilterRoundTripTime::class => $this->roundTripTimeFilter->canBeApplied(),
            FilterPacketLoss::class => $this->packetLossFilter->canBeApplied(),
        ];

        Collection::make($toApply)
            ->filter(fn (bool $shouldApply, string $manipulation) => $shouldApply === true)
            ->each(fn (bool $shouldApply, string $manipulation) => $this->manipulations[] = $manipulation);
    }

    public function clearIpTypeLimits(): void
    {
        $toRemove = [
            LimitIpv4::class,
            LimitIpv6::class,
        ];

        $this->manipulations = array_filter($this->manipulations, fn (string $manipulation) => in_array($manipulation, $toRemove) === false);
    }

    public function clearFilters(): void
    {
        $toRemove = [
            FilterRoundTripTime::class,
            FilterPacketLoss::class,
        ];

        $this->manipulations = array_filter($this->manipulations, fn (string $manipulation) => in_array($manipulation, $toRemove) === false);
    }

    public function loadMore(): void
    {
        $this->rowAmount += 100;
    }

    public function mount(): void
    {
        $this->roundTripTimeFilter = new RoundTripTimeFilter();
        $this->packetLossFilter = new PacketLossFilter();
    }

    public function render()
    {
        return view('ip::livewire.list-ip-addresses');
    }
}

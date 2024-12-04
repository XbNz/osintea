<?php

declare(strict_types=1);

namespace XbNz\Ip\Livewire;

use Chefhasteeth\Pipeline\Pipeline;
use Flux\Flux;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Native\Laravel\Dialog;
use Native\Laravel\Facades\Window;
use Throwable;
use Webmozart\Assert\Assert;
use XbNz\Ip\Actions\ImportIpAddressesAction;
use XbNz\Ip\Contracts\RapidParserInterface;
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
use XbNz\Ping\Events\BulkPingCompleted;
use XbNz\Ping\Jobs\BulkPingJob;
use XbNz\Ping\Models\PingSequence;
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

    /**
     * @var array<int, class-string>
     */
    public array $manipulations = [];

    public RoundTripTimeFilter $roundTripTimeFilter;

    public PacketLossFilter $packetLossFilter;

    #[On('native:'.BulkPingCompleted::class)]
    public function notifyPingResultsReady(int $completedCount): void
    {
        Flux::toast("{$completedCount} ping results are ready for viewing", 'Ping completed', 3000, 'success');
    }

    /**
     * @return Builder<IpAddress>
     */
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

        return number_format($this->query()->count(), thousands_separator: ',').' '.$ipAddress;
    }

    /**
     * @return CursorPaginator<IpAddress>
     */
    #[Computed]
    public function ipAddresses(): CursorPaginator
    {
        return ListIpAddressesTableViewModel::collect($this->query()->cursorPaginate($this->rowAmount));
    }

    public function pingActive(Dispatcher $bus): void
    {
        $this->query()
            ->clone()
            ->lazyById(2_000)
            ->map(fn (IpAddress $ipAddress) => $ipAddress->getData())
            ->chunk(2_000)
            ->each(fn (LazyCollection $chunk) => $bus->dispatch(
                new BulkPingJob($chunk->collect())
            ));

        Flux::toast('Pinging has commenced in the background. You may continue using the app.', 'Ping started', 10000, 'success');
    }

    public function fileImport(
        RapidParserInterface $rapidParser,
        ImportIpAddressesAction $importIpAddressesAction
    ): void {
        $file = Dialog::new()
            ->title('Import IP Addresses')
            ->button('Import')
            ->filter('Text files', ['txt'])
            ->files()
            ->open();

        $importIpAddressesAction->handle($rapidParser->inputFilePath($file)->parse());
    }

    public function deleteActive(DatabaseManager $database): void
    {
        $database->beginTransaction();

        try {
            $this->query()
                ->lazyById(1000)
                ->chunk(1000)
                ->each(fn (LazyCollection $chunk) => PingSequence::query()->whereIn('ip_address_id', $chunk->pluck('id'))->delete());

            $this->query()->delete();

            $database->commit();
        } catch (Throwable $e) {
            $database->rollBack();

            throw $e;
        }
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

    public function render(): View
    {
        return view('ip::livewire.list-ip-addresses');
    }
}

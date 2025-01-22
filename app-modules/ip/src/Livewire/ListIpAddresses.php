<?php

declare(strict_types=1);

namespace XbNz\Ip\Livewire;

use Chefhasteeth\Pipeline\Pipeline;
use Flux\Flux;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Native\Laravel\Dialog;
use Native\Laravel\Facades\Window;
use XbNz\Asn\Enums\Provider as AsnProvider;
use XbNz\Asn\Events\BulkAsnLookupCompleted;
use XbNz\Asn\Jobs\BulkAsnLookupJob;
use XbNz\Asn\Model\Asn;
use XbNz\Ip\Actions\ImportIpAddressesAction;
use XbNz\Ip\Contracts\RapidParserInterface;
use XbNz\Ip\Filters\IcmpFilter;
use XbNz\Ip\Filters\OrganizationFilter;
use XbNz\Ip\Filters\PacketLossFilter;
use XbNz\Ip\Filters\PolygonFilter;
use XbNz\Ip\Filters\RoundTripTimeFilter;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\FilterIcmp;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\FilterOrganization;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\FilterPacketLoss;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\FilterPolygon;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\FilterRoundTripTime;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\LimitIpv4;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\LimitIpv6;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\SortByAsn;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\SortByAverageRtt;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\SortByGeolocated;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\SortByLossPercent;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\SortByOrganization;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\Transporter;
use XbNz\Ip\ViewModels\ListIpAddressesTableViewModel;
use XbNz\Location\Enums\Provider as GeolocationProvider;
use XbNz\Location\Events\BulkGeolocationCompleted;
use XbNz\Location\Jobs\BulkGeolocateJob;
use XbNz\Ping\Events\BulkPingCompleted;
use XbNz\Ping\Jobs\BulkPingJob;
use XbNz\Port\Events\BulkIcmpScanCompleted;
use XbNz\Port\Jobs\BulkIcmpScanJob;
use XbNz\Shared\Enums\NativePhpWindow;

#[Layout('components.layouts.secondary-window')]
final class ListIpAddresses extends Component
{
    /**
     * @var array<string, class-string>
     */
    const array SORT_MAP = [
        'average_rtt' => SortByAverageRtt::class,
        'loss_percent' => SortByLossPercent::class,
        'organization' => SortByOrganization::class,
        'as_number' => SortByAsn::class,
        'geolocated' => SortByGeolocated::class,
    ];

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public int $rowAmount = 100;

    public int $pingSampleSizePercent = 100;

    /**
     * @var array<int, class-string>
     */
    public array $manipulations = [];

    public RoundTripTimeFilter $roundTripTimeFilter;

    public PacketLossFilter $packetLossFilter;

    public OrganizationFilter $organizationFilter;

    public PolygonFilter $polygonFilter;

    public IcmpFilter $icmpFilter;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'pingSampleSizePercent' => ['sometimes', 'numeric', 'min:1', 'max:100'],
        ];
    }

    #[On('native:'.BulkPingCompleted::class)]
    public function notifyPingResultsReady(int $completedCount): void
    {
        Flux::toast("{$completedCount} ping results are ready for viewing", 'Ping completed', 3000, 'success');
    }

    #[On('native:'.BulkAsnLookupCompleted::class)]
    public function notifyAsnLookupResultsReady(int $completedCount): void
    {
        Flux::toast("{$completedCount} ASN lookup results are ready for viewing", 'ASN lookup completed', 1000, 'success');
    }

    #[On('native:'.BulkGeolocationCompleted::class)]
    public function notifyGeolocationResultsReady(int $completedCount): void
    {
        Flux::toast("{$completedCount} geolocation results are ready for viewing", 'Geolocation completed', 1000, 'success');
    }

    #[On('native:'.BulkIcmpScanCompleted::class)]
    public function notifyIcmpScanResultsReady(int $completedCount): void
    {
        Flux::toast("{$completedCount} ICMP scan results are ready for viewing", 'ICMP scan completed', 1000, 'success');
    }

    /**
     * @return Builder<IpAddress>
     */
    private function query(): Builder
    {
        $query = IpAddress::query()->with(['pingSequences', 'asn', 'coordinates']);

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
                $this->organizationFilter,
                $this->polygonFilter,
                $this->icmpFilter
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
     * @return array<int, string>
     */
    #[Computed]
    #[Renderless]
    public function organizationNames(): array
    {
        return $this->query()
            ->clone()
            ->addSelect([
                'organization' => Asn::query()
                    ->select('organization')
                    ->whereColumn('asns.ip_address_id', 'ip_addresses.id')
                    ->limit(1),
            ])
            ->whereNotNull('organization')
            ->groupBy('organization')
            ->pluck('organization')
            ->toArray();
    }

    /**
     * @return CursorPaginator<IpAddress>
     */
    #[Computed]
    public function ipAddresses(): CursorPaginator
    {
        return ListIpAddressesTableViewModel::collect($this->query()->cursorPaginate($this->rowAmount));
    }

    public function geolocateActive(string $provider): void
    {
        $provider = GeolocationProvider::from($provider);

        $this->validate();

        $bus = app(Dispatcher::class);

        $query = $this->query()->clone();

        $query->getQuery()->orders = null;

        $query
            ->lazyById(500)
            ->map(fn (IpAddress $ipAddress) => $ipAddress->getData())
            ->chunk(500)
            ->each(fn (LazyCollection $chunk) => $bus->dispatch(
                new BulkGeolocateJob($chunk->collect(), $provider)->onQueue('bulk_geolocate')
            ));

        Flux::toast('Geolocation has commenced in the background. You may continue using the app.', 'Geoloation started', 10000, 'success');
    }

    public function pingActive(): void
    {
        $this->validate();

        $bus = app(Dispatcher::class);

        $query = $this->query()->clone();

        $query->getQuery()->orders = null;

        $sampleSizeCount = (int) ceil($query->count() * ($this->pingSampleSizePercent / 100));

        $query
            ->lazyById(100)
            ->take($sampleSizeCount)
            ->map(fn (IpAddress $ipAddress) => $ipAddress->getData())
            ->chunk(100)
            ->each(fn (LazyCollection $chunk) => $bus->dispatch(
                new BulkPingJob($chunk->collect())->onQueue('bulk_ping')
            ));

        Flux::toast('Pinging has commenced in the background. You may continue using the app.', 'Ping started', 10000, 'success');
    }

    public function icmpScanActive(): void
    {
        $bus = app(Dispatcher::class);

        $query = $this->query()->clone();

        $query->getQuery()->orders = null;

        $query
            ->lazyById(20_000)
            ->map(fn (IpAddress $ipAddress) => $ipAddress->getData())
            ->chunk(20_000)
            ->each(fn (LazyCollection $chunk) => $bus->dispatch(
                new BulkIcmpScanJob($chunk->pluck('id')->toArray())->onQueue('bulk_icmp_scan')
            ));

        Flux::toast('ICMP scanning has commenced in the background. You may continue using the app.', 'ICMP scan started', 10000, 'success');
    }

    public function lookupActiveAsn(string $provider): void
    {
        $provider = AsnProvider::from($provider);
        $dispatcher = app(Dispatcher::class);

        $query = $this->query()->clone();

        $query->getQuery()->orders = null;

        $query
            ->lazyById(200)
            ->map(fn (IpAddress $ipAddress) => $ipAddress->getData())
            ->chunk(200)
            ->each(function (LazyCollection $chunk) use ($provider, $dispatcher): void {
                $dispatcher->dispatch(new BulkAsnLookupJob(
                    $chunk->collect(),
                    $provider
                )->onQueue('bulk_asn'));
            });

        Flux::toast('ASN lookup has commenced in the background. You may continue using the app.', 'ASN lookup started', 10000, 'success');
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
            FilterOrganization::class => $this->organizationFilter->canBeApplied(),
            FilterPolygon::class => $this->polygonFilter->canBeApplied(),
            FilterIcmp::class => $this->icmpFilter->canBeApplied(),
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
            FilterOrganization::class,
            FilterPolygon::class,
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
        $this->organizationFilter = new OrganizationFilter();
        $this->polygonFilter = new PolygonFilter();
        $this->icmpFilter = new IcmpFilter();
    }

    public function render(): View
    {
        return view('ip::livewire.list-ip-addresses', [
            'asnProviders' => array_column(array_filter(AsnProvider::cases(), fn (AsnProvider $provider) => $provider->canBeUsedInProduction()), 'value'),
            'geolocationProviders' => array_column(array_filter(GeolocationProvider::cases(), fn (GeolocationProvider $provider) => $provider->canBeUsedInProduction()), 'value'),
        ]);
    }
}

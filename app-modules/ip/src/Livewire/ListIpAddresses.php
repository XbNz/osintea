<?php

declare(strict_types=1);

namespace XbNz\Ip\Livewire;

use Chefhasteeth\Pipeline\Pipeline;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Native\Laravel\Facades\Window;
use XbNz\Ip\DTOs\IpAddressDto;
use XbNz\Ip\Models\IpAddress;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\FilterIpv4;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\FilterIpv6;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\SortByAverageRtt;
use XbNz\Ip\Steps\ManipulateIpAddressQuery\Transporter;
use XbNz\Ip\ViewModels\ListIpAddressesViewModel;
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

    private array $filters = [];

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
    public function ipAddresses(): CursorPaginator
    {
        $query = IpAddress::query()->with(['pingSequences']);

        $pipes = $this->filters;

        if (array_key_exists($this->sortBy, self::SORT_MAP)) {
            $pipes[] = self::SORT_MAP[$this->sortBy];
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        if (empty($pipes) === true) {
            return ListIpAddressesViewModel::collect($query->cursorPaginate($this->rowAmount));
        }

        $query = Pipeline::make()
            ->send(new Transporter($this->sortDirection, $query))
            ->through($pipes)
            ->thenReturn()
            ->query;

        return ListIpAddressesViewModel::collect($query->cursorPaginate($this->rowAmount));
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

    public function filterV4(): void
    {
        $this->clearFilters();

        $this->filters[] = FilterIpv4::class;
    }

    public function filterV6(): void
    {
        $this->clearFilters();

        $this->filters[] = FilterIpv6::class;
    }

    public function clearFilters(): void
    {
        $this->filters = [];
        }

    public function loadMore(): void
    {
        $this->rowAmount += 100;
    }

    public function render()
    {
        return view('ip::livewire.list-ip-addresses');
    }
}

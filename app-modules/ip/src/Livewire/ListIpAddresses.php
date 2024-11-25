<?php

declare(strict_types=1);

namespace XbNz\Ip\Livewire;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use XbNz\Ip\Models\IpAddress;

#[Layout('components.layouts.secondary-window')]
final class ListIpAddresses extends Component
{
    use WithPagination;

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public int $rowAmount = 100;

    public int $seedCount = 0;

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
        return IpAddress::query()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->cursorPaginate($this->rowAmount);
    }

    public function loadMore(): void
    {
        $this->rowAmount += 100;
    }

    public function seed(): void
    {
        IpAddress::factory()->count($this->seedCount)->create();
    }

    public function render()
    {
        return view('ip::livewire.list-ip-addresses');
    }
}

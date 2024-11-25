<div>
    <div class="w-full">
        <flux:tabs variant="pills">
            <flux:tab wire:click="filterV4" name="ipv4">IPv4</flux:tab>
            <flux:tab wire:click="filterV6" name="ipv6">IPv6</flux:tab>
            <flux:tab selected wire:click="clearFilters" name="all">Both</flux:tab>
        </flux:tabs>

        <flux:table class="mt-3">
            <flux:columns>
                <flux:column sortable :sorted="$sortBy === 'ip'" :direction="$sortDirection" wire:click="sort('ip')">IP Address</flux:column>
                <flux:column sortable :sorted="$sortBy === 'type'" :direction="$sortDirection" wire:click="sort('type')">Type</flux:column>
                <flux:column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Created At</flux:column>
                <flux:column sortable :sorted="$sortBy === 'average_rtt'" :direction="$sortDirection" wire:click="sort('average_rtt')">Average RTT</flux:column>
            </flux:columns>

            <flux:rows>
                @foreach ($this->ipAddresses as $ipAddress)
                    <flux:row :key="$ipAddress->id">
                        <flux:cell class="flex items-center gap-3">
                            {{ $ipAddress->ip }}
                        </flux:cell>
                        <flux:cell variant="strong">{{ $ipAddress->type }}</flux:cell>
                        <flux:cell class="whitespace-nowrap">{{ $ipAddress->created_at }}</flux:cell>
                        <flux:cell class="whitespace-nowrap">
                            <flux:button variant="ghost" wire:click="goToPingWindow('{{ $ipAddress->ip }}')" size="sm" wire:target="goToPingWindow('{{ $ipAddress->ip }}')">
                                <span class="flex gap-2">
                                    @svg('fad-wifi', 'h-5 w-5')
                                    @if($ipAddress->average_rtt !== null)
                                        {{ $ipAddress->average_rtt }} ms
                                    @endif
                                </span>
                            </flux:button>
                        </flux:cell>
                    </flux:row>
                @endforeach
            </flux:rows>
        </flux:table>
    </div>

    @if($this->ipAddresses->hasMorePages())
        <div x-intersect="$wire.loadMore">
            <div class="flex justify-center mt-4">
                <flux:icon.loading />
            </div>
        </div>
    @endif
</div>

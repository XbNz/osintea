<div>
    <div class="w-1/2 mb-5">
        <form wire:submit="seed" class="flex gap-3">
            <flux:input wire:model="seedCount" type="number" />
            <flux:button type="submit">Seed</flux:button>
        </form>
    </div>

    <div class="w-full">
        <flux:table>
            <flux:columns>
                <flux:column sortable :sorted="$sortBy === 'ip'" :direction="$sortDirection" wire:click="sort('ip')">IP Address</flux:column>
                <flux:column sortable :sorted="$sortBy === 'type'" :direction="$sortDirection" wire:click="sort('type')">Type</flux:column>
                <flux:column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Created At</flux:column>
            </flux:columns>

            <flux:rows>
                @foreach ($this->ipAddresses as $ipAddress)
                    <flux:row :key="$ipAddress->id">
                        <flux:cell class="flex items-center gap-3">
                            {{ $ipAddress->ip }}
                        </flux:cell>
                        <flux:cell variant="strong">{{ $ipAddress->type->value }}</flux:cell>
                        <flux:cell class="whitespace-nowrap">{{ $ipAddress->created_at->format('Y-m-d H:i:s') }}</flux:cell>
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

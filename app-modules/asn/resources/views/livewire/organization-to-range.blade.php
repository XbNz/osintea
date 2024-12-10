<div>
    <div class="w-full">
        <form wire:submit="convert">
            <flux:select label="Organization" variant="listbox" multiple placeholder="Organizations..." :filter="false" wire:model="selectedAsNumbers">
                <flux:select.input wire:model.live.debouce="searchTerm" class="mb-2" />

                @foreach ($this->organizations as $organization)
                    <flux:option value="{{ $organization->asNumber }}">{{ $organization->organization }} (AS{{ $organization->asNumber }})</flux:option>
                @endforeach
            </flux:select>

            <div class="flex items-center gap-3 mt-3">
                <flux:tabs variant="segmented">
                    <flux:tab wire:click="limitV4" name="ipv4">IPv4</flux:tab>
                    <flux:tab wire:click="limitV6" name="ipv6">IPv6</flux:tab>
                    <flux:tab selected wire:click="clearIpTypeLimits" name="all">Both</flux:tab>
                </flux:tabs>
                <flux:select variant="listbox" placeholder="Providers..." wire:model.live="selectedProvider">
                    @foreach ($providers as $provider)
                        <flux:option value="{{ $provider }}">{{ $provider }}</flux:option>
                    @endforeach
                </flux:select>
            </div>
            <flux:button type="submit" class="w-full mt-3">
                    <span class="flex gap-3">
                        Convert
                        @svg('fad-arrow-right-arrow-left', 'h-5 w-5')
                    </span>
            </flux:button>
        </form>
        <flux:textarea
            class="mt-3"
            placeholder="1.1.1.0 - 1.1.1.255"
            wire:model="ranges"
            rows="7"
        />
        <div class="flex flex-row gap-3">
            <flux:button wire:click="addToMyIpAddresses" class="w-full mt-3">
            <span class="flex gap-3">
                Add to database
                @svg('fad-database', 'h-5 w-5')
            </span>
            </flux:button>
        </div>
    </div>
</div>

<div>
    <div class="w-full">
        <div class="flex justify-between items-center gap-2">
            <div class="flex gap-2">
                <flux:button variant="ghost" wire:click="$refresh">
                    <span class="flex gap-2">
                        @svg('fad-arrows-rotate', 'h-5 w-5')
                    </span>
                </flux:button>
                <flux:tabs variant="segmented">
                    <flux:tab wire:click="limitV4" name="ipv4">IPv4</flux:tab>
                    <flux:tab wire:click="limitV6" name="ipv6">IPv6</flux:tab>
                    <flux:tab selected wire:click="clearIpTypeLimits" name="all">Both</flux:tab>
                </flux:tabs>
            </div>

            <flux:heading>
                {{ $this->ipAddressCount }}
            </flux:heading>

            <flux:dropdown>
                <flux:button>
                    <span class="flex gap-2">
                        @svg('fad-circle-nodes', 'h-5 w-5')
                        Actions
                    </span>
                </flux:button>

                <flux:menu>
                    <flux:menu.group heading="Filters">
                        <form wire:submit="applyFilters">
                            <flux:menu.submenu heading="Round trip">
                                <div class="flex flex-col gap-2">
                                    <flux:spacer />
                                    <flux:input.group label="Minimum">
                                        <flux:input placeholder="50" type="number" wire:model="roundTripTimeFilter.minFloor" />
                                        <flux:input placeholder="50" type="number" wire:model="roundTripTimeFilter.maxFloor" />
                                        <flux:input.group.suffix>ms</flux:input.group.suffix>
                                    </flux:input.group>
                                    <flux:spacer />
                                    <flux:menu.separator />
                                    <flux:input.group label="Average">
                                        <flux:input placeholder="50" type="number" wire:model="roundTripTimeFilter.minAverage" />
                                        <flux:input placeholder="50" type="number" wire:model="roundTripTimeFilter.maxAverage" />
                                        <flux:input.group.suffix>ms</flux:input.group.suffix>
                                    </flux:input.group>
                                    <flux:spacer />
                                    <flux:menu.separator />
                                    <flux:input.group label="Maximum">
                                        <flux:input placeholder="50" type="number" wire:model="roundTripTimeFilter.minCeiling" />
                                        <flux:input placeholder="50" type="number" wire:model="roundTripTimeFilter.maxCeiling" />
                                        <flux:input.group.suffix>ms</flux:input.group.suffix>
                                    </flux:input.group>
                                    <flux:spacer />
                                </div>
                            </flux:menu.submenu>

                            <flux:menu.submenu heading="Packet loss">
                                <div class="flex flex-col gap-2">
                                    <flux:input.group>
                                        <flux:input placeholder="50" type="number" wire:model="packetLossFilter.minPercent" />
                                        <flux:input placeholder="50" type="number" wire:model="packetLossFilter.maxPercent" />
                                        <flux:input.group.suffix>%</flux:input.group.suffix>
                                    </flux:input.group>
                                </div>
                            </flux:menu.submenu>

                            <flux:menu.submenu heading="Organization">
                                <div class="flex flex-col gap-2">
                                    <flux:input label="ASN" placeholder="13335" type="number" wire:model="organizationFilter.asNumber" />
                                    <flux:menu.separator />
                                    <flux:select label="Organization" variant="listbox" searchable clearable placeholder="Organization..." wire:model="organizationFilter.name">
                                        @foreach($this->organizationNames as $organization)
                                            <flux:option value="{{ $organization }}">{{ $organization }}</flux:option>
                                        @endforeach
                                    </flux:select>
                                </div>
                            </flux:menu.submenu>

                            <div class="flex justify-between gap-2">
                                <flux:button type="submit" size="sm" class="my-2">
                                    @svg('fad-filter', 'h-5 w-5')
                                </flux:button>
                                <flux:button type="button" size="sm" wire:click.prevent="clearFilters" class="my-2">
                                    @svg('fad-filter-slash', 'h-5 w-5')
                                </flux:button>
                            </div>

                        </form>
                    </flux:menu.group>
                    <flux:menu.group heading="Tools">
                        <flux:menu.submenu heading="Ping">
                            <form wire:submit.prevent="pingActive">
                                <flux:input.group label="Sample size">
                                    <flux:input type="number" wire:model="pingSampleSizePercent" />
                                    <flux:input.group.suffix>%</flux:input.group.suffix>
                                </flux:input.group>

                                <flux:button class="mt-3" type="submit">
                                    <span class="flex gap-3">
                                        Ping active
                                        @svg('fad-wifi', 'h-5 w-5')
                                    </span>
                                </flux:button>
                            </form>
                        </flux:menu.submenu>
                        <flux:menu.submenu heading="ASN Lookup">
                            @foreach($asnProviders as $provider)
                                <flux:menu.item wire:click="lookupActiveAsn('{{ $provider }}')">{{ $provider }}</flux:menu.item>
                            @endforeach
                        </flux:menu.submenu>
                    </flux:menu.group>

                    <flux:menu.item wire:click="fileImport">Import from file</flux:menu.item>
                    <flux:menu.item wire:click="deleteActive">Delete selected</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>

        <flux:table class="mt-5">
            <flux:columns>
                <flux:column
                    sortable
                    :sorted="$sortBy === 'ip'"
                    :direction="$sortDirection"
                    wire:click="sort('ip')"
                >
                    <span class="flex gap-2">
                        @svg('fad-network-wired', 'h-5 w-5')
                        IP Address
                    </span>
                </flux:column>

                <flux:column
                    sortable
                    :sorted="$sortBy === 'created_at'"
                    :direction="$sortDirection"
                    wire:click="sort('created_at')"
                >
                    <span class="flex gap-2">
                        @svg('fad-calendar', 'h-5 w-5')
                        Created
                    </span>
                </flux:column>

                <flux:column
                    sortable
                    :sorted="$sortBy === 'average_rtt'"
                    :direction="$sortDirection"
                    wire:click="sort('average_rtt')"
                >
                    <span class="flex gap-2">
                        @svg('fad-calculator', 'h-5 w-5')
                        Average RTT
                    </span>
                </flux:column>

                <flux:column
                    sortable
                    :sorted="$sortBy === 'loss_percent'"
                    :direction="$sortDirection"
                    wire:click="sort('loss_percent')"
                >
                    <span class="flex gap-2">
                        @svg('fad-badge-percent', 'h-5 w-5')
                        Loss
                    </span>
                </flux:column>

                <flux:column
                    sortable
                    :sorted="$sortBy === 'organization'"
                    :direction="$sortDirection"
                    wire:click="sort('organization')"
                >
                    <span class="flex gap-2">
                        @svg('fad-building', 'h-5 w-5')
                        Organization
                    </span>
                </flux:column>

                <flux:column
                    sortable
                    :sorted="$sortBy === 'as_number'"
                    :direction="$sortDirection"
                    wire:click="sort('as_number')"
                >
                    <span class="flex gap-2">
                        @svg('fad-globe', 'h-5 w-5')
                        ASN
                    </span>
                </flux:column>
            </flux:columns>

            <flux:rows>
                @foreach ($this->ipAddresses as $ipAddress)
                    <flux:row :key="$ipAddress->id">
                        <flux:cell class="flex items-center gap-3">
                            {{ $ipAddress->ip }}
                        </flux:cell>
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
                        <flux:cell class="whitespace-nowrap">{{ $ipAddress->loss_percent }}% ({{ $ipAddress->lost_sequences }}/{{ $ipAddress->total_sequences }})</flux:cell>
                        <flux:cell class="whitespace-nowrap">{{ $ipAddress->organization }}</flux:cell>
                        <flux:cell class="whitespace-nowrap">{{ $ipAddress->as_number }}</flux:cell>
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


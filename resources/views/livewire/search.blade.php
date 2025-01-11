<div
    wire:keydown.escape="closeCommandPalette"
>
    <flux:command>
        <flux:command.input autofocus placeholder="Search..." />

        <flux:command.items>
            <flux:command.item wire:click="openPing">
                <div class="flex items-center">
                    @svg('fad-wifi', 'h-5 w-5')
                    <span class="ml-2">Ping</span>
                </div>
            </flux:command.item>
            <flux:command.item wire:click="openIpAddresses">
                <div class="flex items-center">
                    @svg('fad-chart-network', 'h-5 w-5')
                    <span class="ml-2">My IP Addresses</span>
                </div>
            </flux:command.item>
            <flux:command.item wire:click="openRangeToIp">
                <div class="flex items-center">
                    @svg('fad-swap', 'h-5 w-5')
                    <span class="ml-2">Range to IP</span>
                </div>
            </flux:command.item>
            <flux:command.item wire:click="openOrganizationToRange">
                <div class="flex items-center">
                    @svg('fad-building', 'h-5 w-5')
                    <span class="ml-2">Organization to Range</span>
                </div>
            </flux:command.item>
            <flux:command.item wire:click="openLocationToRange">
                <div class="flex items-center">
                    @svg('fad-map-pin', 'h-5 w-5')
                    <span class="ml-2">Location to Range</span>
                </div>
            </flux:command.item>
        </flux:command.items>
    </flux:command>
</div>

<div>
    <div class="w-full">
        <form wire:submit="convert">
            <flux:field>
                <flux:textarea
                    label="Ranges"
                    placeholder="10.0.0.1/24
192.168.1.1-192.168.1.255
192.168.1.1"
                    wire:model="rangeList"
                    rows="7"
                    resize="vertical"
                />

                <flux:error name="rangeList" />
            </flux:field>

            <flux:button type="submit" class="w-full mt-3">
                <span class="flex gap-3">
                    Convert
                    @svg('fad-arrow-right-arrow-left', 'h-5 w-5')
                </span>
            </flux:button>
        </form>
        <flux:textarea
            class="mt-3"
            placeholder="1.1.1.1"
            wire:model="ipList"
            rows="7"
        />
        <div class="flex flex-row gap-3">
            <flux:button wire:click="addToMyIpAddresses" class="w-full mt-3">
            <span class="flex gap-3">
                Add to database
                @svg('fad-database', 'h-5 w-5')
            </span>
            </flux:button>
            <flux:button wire:click="show" class="w-full mt-3">
            <span class="flex gap-3">
                Show converted ranges
                @svg('fad-eye', 'h-5 w-5')
            </span>
            </flux:button>
        </div>
    </div>
</div>

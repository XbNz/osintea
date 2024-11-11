<div>
    <flux:card class="space-y-6 w-1/2">
        <div>
            <flux:heading size="lg">Ping a host!</flux:heading>
            <flux:subheading>Type an IP or hostname</flux:subheading>
        </div>

        <form wire:submit.prevent="ping">
            <div class="space-y-6">
                <flux:field>
                    <flux:label class="flex justify-between">Target</flux:label>

                    <flux:input type="text" wire:model.debounce="target" placeholder="192.168.1.1..." />

                    <flux:error name="target" />
                </flux:field>

                <flux:field>
                    <flux:label class="flex justify-between">Count</flux:label>

                    <flux:input class="w-2/12 min-w-[4rem]" type="number" wire:model.live.debounce="count" placeholder="3" />

                    <flux:error name="count" />
                </flux:field>

                <div class="flex justify-center">
                    <flux:button type="submit" variant="primary">Save</flux:button>
                </div>
            </div>
        </form>
    </flux:card>
</div>

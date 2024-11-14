<div class="w-full min-w-[60rem]">
    <flux:card class="space-y-6 w-1/2">
        <div>
            <flux:heading size="lg">Ping a host!</flux:heading>
            <flux:subheading>Type an IP or hostname</flux:subheading>
        </div>

        <form wire:submit.prevent="ping">

            <flux:field>
                <flux:label class="flex justify-between">Target</flux:label>

                <flux:input type="text" wire:model.blur="target" placeholder="192.168.1.1..." />

                <flux:error name="target" />
            </flux:field>

            <div class="flex space-x-6 mt-5">
                <flux:field>
                    <flux:label class="flex justify-between">Count</flux:label>

                    <flux:input type="number" wire:model.blur="count" />

                    <flux:error name="count" />
                </flux:field>

                <flux:field>
                    <flux:label class="flex justify-between">Time between requests</flux:label>

                    <flux:input required type="number" wire:model.blur="timeBetweenRequests" />

                    <flux:error name="timeBetweenRequests" />
                </flux:field>
            </div>

            <div class="flex justify-center mt-5">
                <flux:button type="submit" variant="primary">Ping</flux:button>
            </div>
        </form>
    </flux:card>
</div>

<div>
    <flux:tab.group>
        <flux:tabs wire:model="tab">
            <flux:tab name="fping">Fping</flux:tab>
            <flux:tab name="masscan">Masscan</flux:tab>
        </flux:tabs>

        <flux:tab.panel name="fping">
            <div>
                <div class="flex gap-4">
                    <flux:field>
                        <flux:label>Size</flux:label>
                        <flux:input.group>
                            <flux:input wire:model="size" />
                            <flux:input.group.suffix>bytes</flux:input.group.suffix>
                        </flux:input.group>
                        <flux:error name="size" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Exponential back-off</flux:label>
                        <flux:input.group>
                            <flux:input wire:model="backoff" />
                            <flux:input.group.suffix>x</flux:input.group.suffix>
                        </flux:input.group>
                        <flux:error name="backoff" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Count per target</flux:label>
                        <flux:input.group>
                            <flux:input wire:model="count" />
                        </flux:input.group>
                        <flux:error name="count" />
                    </flux:field>
                </div>

                <div class="absolute bottom-0 right-0">
                    <flux:button variant="ghost">
                        <span class="flex gap-2">
                            @svg('fad-arrows-rotate', 'h-5 w-5')
                            Reset
                        </span>
                    </flux:button>
                </div>
            </div>
        </flux:tab.panel>
        <flux:tab.panel name="masscan">
            Todo
        </flux:tab.panel>
    </flux:tab.group>
</div>

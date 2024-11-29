<div>
    <flux:tab.group>
        <flux:tabs wire:model="tab">
            <flux:tab name="fping">Fping</flux:tab>
            <flux:tab name="masscan">Masscan</flux:tab>
        </flux:tabs>

        <flux:tab.panel name="fping">
            <div>
                <div class="grid grid-cols-3 gap-4">
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
                            <flux:input wire:model="count" type="number" />
                        </flux:input.group>
                        <flux:error name="count" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Time to live</flux:label>
                        <flux:input.group>
                            <flux:input wire:model="ttl" type="number" />
                        </flux:input.group>
                        <flux:error name="ttl" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Interval</flux:label>
                        <flux:input.group>
                            <flux:input wire:model="interval" />
                            <flux:input.group.suffix>ms</flux:input.group.suffix>
                        </flux:input.group>
                        <flux:error name="interval" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Interval per target</flux:label>
                        <flux:input.group>
                            <flux:input wire:model="intervalPerTarget" />
                            <flux:input.group.suffix>ms</flux:input.group.suffix>
                        </flux:input.group>
                        <flux:error name="intervalPerTarget" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Service type</flux:label>
                        <flux:input.group>
                            <flux:input wire:model="typeOfService" />
                        </flux:input.group>
                        <flux:error name="typeOfService" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Retries</flux:label>
                        <flux:input.group>
                            <flux:input wire:model="retries" type="number" />
                        </flux:input.group>
                        <flux:error name="retries" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Timeout</flux:label>
                        <flux:input.group>
                            <flux:input wire:model="timeout" />
                            <flux:input.group.suffix>ms</flux:input.group.suffix>
                        </flux:input.group>
                        <flux:error name="timeout" />
                    </flux:field>
                </div>

                <flux:fieldset class="mt-8">
                    <div class="space-y-3">
                        <flux:field variant="inline" class="w-full flex justify-between">
                            <flux:switch wire:model.live="dontFragment" />
                            <flux:label>Dont fragment</flux:label>
                        </flux:field>

                        <flux:field variant="inline" class="w-full flex justify-between">
                            <flux:switch wire:model.live="sendRandomData" />
                            <flux:label>Send random data</flux:label>
                        </flux:field>
                    </div>
                </flux:fieldset>

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

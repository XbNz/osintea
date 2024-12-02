<div>
    <flux:tab.group>
        <flux:tabs>
            @foreach($this->fpingPreferencesRecords as $fpingPreferencesRecord)
                <flux:tab :name="$fpingPreferencesRecord->id"
                    wire:click="selectTab({{ $fpingPreferencesRecord->id }})"
                >
                    {{ $fpingPreferencesRecord->name }}
                </flux:tab>
            @endforeach

            <flux:tab icon="plus" wire:click="createNewPreferencesRecord" action>Create config</flux:tab>
        </flux:tabs>

        @foreach($this->fpingPreferencesRecords as $fpingPreferencesRecord)
            <flux:tab.panel :name="$fpingPreferencesRecord->id">
                <flux:card class="p-0">
                    <div class="flex justify-between items-center">
                        <div class="flex gap-10">
                            <flux:field>
                                <flux:switch wire:model.live.debounce.500ms.live="form.enabled"
                                             wire:click.prevent="enable"
                                             :disabled="isset($form->enabled) && $form->enabled === true"
                                />
                                <flux:label>Enabled</flux:label>
                            </flux:field>
                            <flux:field>
                                <flux:switch wire:model.boolean.live.debounce.500ms.live="form.dont_fragment"/>
                                <flux:label>Dont fragment</flux:label>
                            </flux:field>
                            <flux:field>
                                <flux:switch wire:model.boolean.live.debounce.500ms.live="form.send_random_data"/>
                                <flux:label>Send random data</flux:label>
                            </flux:field>
                        </div>
                        <flux:button wire:click="delete" :disabled="isset($form->enabled) && $form->enabled === true">
                            <span class="flex gap-2">
                                @svg('fad-trash', 'h-5 w-5')
                                Delete
                            </span>
                        </flux:button>
                    </div>
                    <flux:field class="w-1/2 mt-3">
                        <flux:input wire:model.live.debounce.500ms="form.name"/>
                        <flux:error name="form.name"/>
                    </flux:field>
                </flux:card>
                <div class="grid grid-cols-3 gap-4 mt-7">
                    <flux:field>
                        <flux:label>Size</flux:label>
                        <flux:input.group>
                            <flux:input wire:model.live.debounce.500ms="form.size"/>
                            <flux:input.group.suffix>bytes</flux:input.group.suffix>
                        </flux:input.group>
                        <flux:error name="form.size"/>
                    </flux:field>
                    <flux:field>
                        <flux:label>Exponential back-off</flux:label>
                        <flux:input.group>
                            <flux:input wire:model.live.debounce.500ms="form.backoff"/>
                            <flux:input.group.suffix>x</flux:input.group.suffix>
                        </flux:input.group>
                        <flux:error name="form.backoff"/>
                    </flux:field>
                    <flux:field>
                        <flux:label>Count per target</flux:label>
                        <flux:input.group>
                            <flux:input wire:model.live.debounce.500ms="form.count" type="number"/>
                        </flux:input.group>
                        <flux:error name="form.count"/>
                    </flux:field>
                    <flux:field>
                        <flux:label>Time to live</flux:label>
                        <flux:input.group>
                            <flux:input wire:model.live.debounce.500ms="form.ttl" type="number"/>
                        </flux:input.group>
                        <flux:error name="form.ttl"/>
                    </flux:field>
                    <flux:field>
                        <flux:label>Interval</flux:label>
                        <flux:input.group>
                            <flux:input wire:model.live.debounce.500ms="form.interval"/>
                            <flux:input.group.suffix>ms</flux:input.group.suffix>
                        </flux:input.group>
                        <flux:error name="form.interval"/>
                    </flux:field>
                    <flux:field>
                        <flux:label>Interval per target</flux:label>
                        <flux:input.group>
                            <flux:input wire:model.live.debounce.500ms="form.interval_per_target"/>
                            <flux:input.group.suffix>ms</flux:input.group.suffix>
                        </flux:input.group>
                        <flux:error name="form.interval_per_target"/>
                    </flux:field>
                    <flux:field>
                        <flux:label>Service type</flux:label>
                        <flux:input.group>
                            <flux:input wire:model.live.debounce.500ms="form.type_of_service"/>
                        </flux:input.group>
                        <flux:error name="form.type_of_service"/>
                    </flux:field>
                    <flux:field>
                        <flux:label>Retries</flux:label>
                        <flux:input.group>
                            <flux:input wire:model.live.debounce.500ms="form.retries" type="number"/>
                        </flux:input.group>
                        <flux:error name="form.retries"/>
                    </flux:field>
                    <flux:field>
                        <flux:label>Timeout</flux:label>
                        <flux:input.group>
                            <flux:input wire:model.live.debounce.500ms="form.timeout"/>
                            <flux:input.group.suffix>ms</flux:input.group.suffix>
                        </flux:input.group>
                        <flux:error name="form.timeout"/>
                    </flux:field>
                </div>
            </flux:tab.panel>
        @endforeach
    </flux:tab.group>
</div>

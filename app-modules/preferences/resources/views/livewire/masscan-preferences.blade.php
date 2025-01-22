<div>
    <flux:tab.group>
        <flux:tabs>
            @foreach($this->masscanPreferencesRecords as $masscanPreferencesRecord)
                <flux:tab :name="$masscanPreferencesRecord->id"
                    wire:click="selectTab({{ $masscanPreferencesRecord->id }})"
                >
                    {{ $masscanPreferencesRecord->name }}
                </flux:tab>
            @endforeach

            <flux:tab icon="plus" wire:click="createNewPreferencesRecord" action>Create config</flux:tab>
        </flux:tabs>

        @foreach($this->masscanPreferencesRecords as $masscanPreferencesRecord)
            <flux:tab.panel :name="$masscanPreferencesRecord->id">
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
                        <flux:label>Time to live</flux:label>
                        <flux:input.group>
                            <flux:input wire:model.live.debounce.500ms="form.ttl" type="number"/>
                        </flux:input.group>
                        <flux:error name="form.ttl"/>
                    </flux:field>
                    <flux:field>
                        <flux:label>Retries</flux:label>
                        <flux:input.group>
                            <flux:input wire:model.live.debounce.500ms="form.retries" type="number"/>
                        </flux:input.group>
                        <flux:error name="form.retries"/>
                    </flux:field>
                    <flux:field>
                        <flux:label>Rate</flux:label>
                        <flux:input.group>
                            <flux:input wire:model.live.debounce.500ms="form.rate" type="number"/>
                        </flux:input.group>
                        <flux:error name="form.rate"/>
                    </flux:field>
                    <flux:field>
                        <flux:label>Adapter</flux:label>
                        <flux:input.group>
                            <flux:input wire:model.live.debounce.500ms="form.adapter"/>
                        </flux:input.group>
                        <flux:error name="form.adapter"/>
                    </flux:field>
                </div>
            </flux:tab.panel>
        @endforeach
    </flux:tab.group>
</div>

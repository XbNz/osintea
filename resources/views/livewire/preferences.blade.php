<div>
    <flux:tab.group>
        <flux:tabs wire:model="tab">
            <flux:tab name="fping">Fping</flux:tab>
            <flux:tab name="masscan">Masscan</flux:tab>
        </flux:tabs>

        <flux:tab.panel name="fping">
            @livewire('fping::fping-preferences')
        </flux:tab.panel>
        <flux:tab.panel name="masscan">
            Todo
        </flux:tab.panel>
    </flux:tab.group>
</div>

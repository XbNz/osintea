<div>
    <flux:tab.group>
        <div class="flex items-center justify-between">
            <flux:tabs wire:model="tab">
                <flux:tab name="fping">Fping</flux:tab>
                <flux:tab name="databases">Databases</flux:tab>
                <flux:tab name="masscan">Masscan</flux:tab>
            </flux:tabs>
            <flux:radio.group variant="segmented" x-model="$flux.appearance">
                <flux:radio value="light">
                    <div class="flex items-center">
                        @svg('fad-sun', 'h-5 w-5')
                        <span class="ml-2">Light</span>
                    </div>
                </flux:radio>
                <flux:radio value="dark">
                    <div class="flex items-center">
                        @svg('fad-moon', 'h-5 w-5')
                        <span class="ml-2">Dark</span>
                    </div>
                </flux:radio>
                <flux:radio value="system">
                    <div class="flex items-center">
                        @svg('fad-computer', 'h-5 w-5')
                        <span class="ml-2">System</span>
                    </div>
                </flux:radio>
            </flux:radio.group>
        </div>
        <flux:tab.panel name="fping">
            @livewire('preferences::fping-preferences')
        </flux:tab.panel>
        <flux:tab.panel name="databases">
            @livewire('preferences::database-preferences')
        </flux:tab.panel>
        <flux:tab.panel name="masscan">
            @livewire('preferences::masscan-preferences')
        </flux:tab.panel>
    </flux:tab.group>
</div>

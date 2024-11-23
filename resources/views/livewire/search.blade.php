<div
    wire:keydown.escape="closeCommandPalette"
>
    <flux:command>
        <flux:command.input autofocus placeholder="Search..." />

        <flux:command.items>
            <flux:command.item wire:click="openPing" icon="wifi">Ping</flux:command.item>
            <flux:command.empty />
            <flux:command.empty />
        </flux:command.items>
    </flux:command>
</div>

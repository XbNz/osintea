<div wire:keydown.escape="closeCommandPalette">
    <flux:command>
        <flux:command.input autofocus placeholder="Search..." />

        <flux:command.items>
            <flux:command.item icon="bolt" kbd="⌘A">Quick Lookup</flux:command.item>
            <flux:command.item wire:click="openPing" icon="wifi" kbd="⌘B">Ping</flux:command.item>
            <flux:command.item icon="circle-stack" kbd="⌘C">New Project</flux:command.item>
            <flux:command.empty />
            <flux:command.empty />
        </flux:command.items>
    </flux:command>
</div>

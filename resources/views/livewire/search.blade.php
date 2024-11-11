<div>
    <flux:command>
        <flux:command.input placeholder="Search..." closable />

        <flux:command.items>
            <flux:command.item icon="bolt" kbd="⌘A">Quick Lookup</flux:command.item>
            <flux:command.item href="{{ route('ping') }}" wire:navigate.hover icon="wifi" kbd="⌘B">Ping</flux:command.item>
            <flux:command.item icon="circle-stack" kbd="⌘C">New Project</flux:command.item>
        </flux:command.items>
    </flux:command>
</div>

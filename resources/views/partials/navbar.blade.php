<flux:navbar class="-mb-px">
    <flux:separator vertical variant="subtle" class="my-2"/>

    <flux:spacer />

    <flux:modal.trigger name="search" shortcut="cmd.k">
        <flux:input as="button" placeholder="Search..." icon="magnifying-glass" kbd="⌘K" />
    </flux:modal.trigger>

    <flux:modal name="search" variant="bare" class="min-h-[30rem] w-full max-w-[30rem] px-6" x-on:keydown.cmd.k.document="$el.showModal()">
        <livewire:search />
    </flux:modal>
</flux:navbar>

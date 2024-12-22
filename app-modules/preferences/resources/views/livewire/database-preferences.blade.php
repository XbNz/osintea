<div>
    <div class="w-full grid grid-cols-3 gap-6">
        @foreach($updatableDatabases as $databaseValue => $friendlyName)
            <div :key="$databaseValue">
                <flux:card class="space-y-6">
                    <flux:heading size="lg">{{ $friendlyName }}</flux:heading>
                    <flux:button class="w-full" wire:click="updateDatabase('{{ $databaseValue }}')">Download fresh database</flux:button>
                    <div x-show="true">
                        <div class="w-full max-w-md mx-auto">
                            <div class="relative mt-2 h-4 w-full bg-gray-200 rounded-full dark:bg-gray-700 overflow-hidden">
                                <div id="{{ $databaseValue }}-download-progress"
                                     class="absolute top-0 left-0 h-full bg-blue-500 dark:bg-blue-400"
                                     style="width: 0%;"
                                >
                                </div>
                            </div>
                        </div>
                    </div>
                </flux:card>
            </div>
        @endforeach
    </div>
</div>

@script
<script>
    Native.on('XbNz\\Shared\\Events\\UpdateProgressReportEvent', (payload, event) => {
        $wire.el.querySelector('#' + payload.database + '-download-progress').style.width = payload.percentage + '%';
    });
</script>
@endscript



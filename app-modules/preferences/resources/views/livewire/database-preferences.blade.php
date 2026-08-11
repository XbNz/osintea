<div>
    <div class="w-full grid grid-cols-3 gap-6">
        @foreach($updatableDatabases as $databaseValue => $friendlyName)
            <div
                wire:key="database-updater-{{ $databaseValue }}"
                x-data="{ status: 'idle', progress: 0, error: null }"
                x-on:database-update-progress.window="
                    if ($event.detail.database === '{{ $databaseValue }}') {
                        status = 'running';
                        progress = $event.detail.percentage;
                    }
                "
                x-on:database-update-status.window="
                    if ($event.detail.database === '{{ $databaseValue }}') {
                        status = $event.detail.status;
                        error = $event.detail.message;
                        if (status === 'completed') progress = 100;
                    }
                "
            >
                <flux:card class="space-y-6">
                    <flux:heading size="lg">{{ $friendlyName }}</flux:heading>
                    <flux:button
                        class="w-full"
                        wire:click="updateDatabase('{{ $databaseValue }}')"
                        x-on:click="status = 'queued'; progress = 0; error = null"
                        x-bind:disabled="status === 'queued' || status === 'running'"
                    >
                        <span x-show="status === 'idle' || status === 'completed' || status === 'failed'">Download fresh database</span>
                        <span x-show="status === 'queued'">Queued…</span>
                        <span x-show="status === 'running'">Downloading…</span>
                    </flux:button>
                    <div x-show="status !== 'idle'" x-cloak>
                        <div class="w-full max-w-md mx-auto">
                            <div class="relative mt-2 h-4 w-full bg-gray-200 rounded-full dark:bg-gray-700 overflow-hidden">
                                <div class="absolute top-0 left-0 h-full bg-blue-500 dark:bg-blue-400 transition-[width]"
                                     x-bind:style="'width: ' + progress + '%'"
                                >
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400" x-show="status === 'queued'">Waiting for the updater worker…</p>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400" x-show="status === 'running'" x-text="progress > 0 ? Math.round(progress) + '%' : 'Downloading…'"></p>
                            <p class="mt-2 text-sm text-green-600 dark:text-green-400" x-show="status === 'completed'">Database updated successfully.</p>
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400" x-show="status === 'failed'" x-text="error || 'Database update failed.'"></p>
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
        window.dispatchEvent(new CustomEvent('database-update-progress', { detail: payload }));
    });

    Native.on('XbNz\\Shared\\Events\\DatabaseUpdateStatusEvent', (payload, event) => {
        window.dispatchEvent(new CustomEvent('database-update-status', { detail: payload }));
    });
</script>
@endscript


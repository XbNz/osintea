<div class="w-full min-w-[90rem]">
    @vite('resources/js/chart.js')
    <div class="w-1/2">
        <div class="flex justify-end gap-2">
            <form class="flex gap-2" wire:submit.prevent="ping">
                <div>
                    <flux:field>
                        <flux:input size="sm" wire:model="target" placeholder="IP Address" copyable />

                        <flux:error name="target" />
                    </flux:field>
                </div>
                <div>
                    <flux:field>
                        <flux:input size="sm" wire:model="interval" placeholder="Interval" type="number" />

                        <flux:error name="interval" />
                    </flux:field>
                </div>
                <div>
                    <flux:button size="sm" variant="ghost" type="submit">
                        @svg('fad-wifi', 'h-5 w-5')
                    </flux:button>
                </div>
            </form>
            <flux:button size="sm" variant="ghost" wire:click="stop">
                @svg('fad-power-off', 'h-5 w-5')
            </flux:button>
            <flux:button size="sm" variant="ghost" wire:click="deleteSequences">
                @svg('fad-trash', 'h-5 w-5')
            </flux:button>
        </div>
    </div>
    <div class="w-1/2 mt-7">
        <div class="grid grid-cols-5 gap-3">
            <div>
                <div class="flex items-center">
                    <flux:subheading class="mr-1">Average</flux:subheading>
                    @svg('fad-calculator', 'h-5 w-5')
                </div>
                <flux:heading size="xl">{{ $averageRoundTripTime }} ms</flux:heading>
            </div>
            <div>
                <div class="flex items-center">
                    <flux:subheading class="mr-1">Minimum</flux:subheading>
                    @svg('fad-arrow-down', 'h-5 w-5')
                </div>
                <flux:heading size="xl">{{ $minimumRoundTripTime }} ms</flux:heading>
            </div>
            <div>
                <div class="flex items-center">
                    <flux:subheading class="mr-1">Maximum</flux:subheading>
                    @svg('fad-arrow-up', 'h-5 w-5')
                </div>
                <flux:heading size="xl">{{ $maximumRoundTripTime }} ms</flux:heading>
            </div>
            <div>
                <div class="flex items-center">
                    <flux:subheading class="mr-1">Loss</flux:subheading>
                    @svg('fad-badge-percent', 'h-5 w-5')
                </div>
                <flux:heading size="xl">{{ $packetLossPercentage }}%</flux:heading>
            </div>
            <div>
                <div class="flex items-center">
                    <flux:subheading class="mr-1">Loss Count</flux:subheading>
                    @svg('fad-ban', 'h-5 w-5')
                </div>
                <flux:heading size="xl">{{ $lossCount }} / {{ $totalCount }}</flux:heading>
            </div>
        </div>

    </div>

    <div class="w-1/2 mt-8">
        <flux:card>
            <div class="flex justify-between items-center">
                @svg('fad-chart-line', 'h-7 w-7')
                <div>
                    <flux:button size="sm" variant="ghost" wire:click="populateChart">
                        @svg('fad-database', 'h-5 w-5')
                    </flux:button>
                    <flux:button size="sm" variant="ghost" wire:click="$dispatch('resetChart')">
                        @svg('fad-rotate', 'h-5 w-5')
                    </flux:button>
                </div>
            </div>
            <flux:subheading>Standard Deviation</flux:subheading>
            <flux:heading size="xl">{{ $standardDeviation }}</flux:heading>
            <div class="mt-8" wire:ignore>
                <canvas id="chart" width="100" height="18"></canvas>
            </div>
        </flux:card>
    </div>
</div>

@script
<script>
    const chart = new Chart(
        $wire.el.querySelector('canvas'),
        {
            type: 'line'
        }
    );

    $wire.on('newDataPoint', (event) => {
        const label = event[0].label;
        const newData = event[0].newData;

        chart.data.labels.push(label);
        chart.data.datasets.forEach((dataset) => {
            dataset.data.push(newData);
        });
        chart.update();
    })

    $wire.on('resetChart', () => {
        chart.data.datasets.forEach((dataset) => {
            dataset.data = [];
        });

        chart.data.labels = [];

        chart.options = @json($chartOptions);
        chart.update();
    })

    $wire.on('populateChart', (event) => {
        chart.data = event[0].data;
        chart.options = event[0].options;
        chart.update();
    })
</script>
@endscript

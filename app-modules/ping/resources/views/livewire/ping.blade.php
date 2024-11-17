<div class="w-full min-w-[90rem]">
    @vite('resources/js/chart.js')

    <div class="w-1/2">
        <div class="flex justify-end gap-2">
            <form class="flex gap-2" wire:submit.prevent="ping">
                <div>
                    <flux:field>
                        <flux:input size="sm" wire:model="target" placeholder="IP Address" />

                        <flux:error name="target" />
                    </flux:field>
                </div>
                <div>
                    <flux:button size="sm" variant="ghost" type="submit">
                        @svg('fad-wifi', 'h-5 w-5')
                    </flux:button>
                </div>
            </form>
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
                <flux:heading size="xl">{{ $this->averageRoundTripTime }} ms</flux:heading>
            </div>
            <div>
                <div class="flex items-center">
                    <flux:subheading class="mr-1">Minimum</flux:subheading>
                    @svg('fad-arrow-down', 'h-5 w-5')
                </div>
                <flux:heading size="xl">{{ $this->minimumRoundTripTime }} ms</flux:heading>
            </div>
            <div>
                <div class="flex items-center">
                    <flux:subheading class="mr-1">Maximum</flux:subheading>
                    @svg('fad-arrow-up', 'h-5 w-5')
                </div>
                <flux:heading size="xl">{{ $this->maximumRoundTripTime }} ms</flux:heading>
            </div>
            <div>
                <div class="flex items-center">
                    <flux:subheading class="mr-1">Loss</flux:subheading>
                    @svg('fad-badge-percent', 'h-5 w-5')
                </div>
                <flux:heading size="xl">{{ $this->packetLossPercentage }}%</flux:heading>
            </div>
            <div>
                <div class="flex items-center">
                    <flux:subheading class="mr-1">Loss Count</flux:subheading>
                    @svg('fad-ban', 'h-5 w-5')
                </div>
                <flux:heading size="xl">{{ $this->lossCount }} / {{ $this->totalCount }}</flux:heading>
            </div>
        </div>

    </div>

    <flux:card class="w-1/2 mt-8">
        @svg('fad-chart-line', 'h-7 w-7')
        <flux:subheading>Standard Deviation</flux:subheading>
        <flux:heading size="xl">{{ $this->standardDeviation }}</flux:heading>
        <div class="mt-8" wire:ignore>
            <canvas id="chart" width="100" height="18"></canvas>
        </div>
    </flux:card>
</div>

@script
<script>
    const chart = new Chart(
        $wire.el.querySelector('canvas'),
        {
            type: 'line',
            data: [],
            options: []
        }
    );

    Livewire.on('newDataPoint', (event) => {
        const label = event[0].label;
        const newData = event[0].newData;

        chart.data.labels.push(label);
        chart.data.datasets.forEach((dataset) => {
            dataset.data.push(newData);
        });
        chart.update();
    })

    Livewire.on('resetChart', () => {
        chart.data = @json($this->dataset);
        chart.options = @json($this->options);
        chart.update();
    })
</script>
@endscript

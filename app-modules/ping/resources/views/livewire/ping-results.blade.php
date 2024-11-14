<div class="w-full min-w-[90rem]">
    @livewireChartsScripts

    <flux:heading size="lg">{{ $this->ip }}</flux:heading>

    <div class="mt-5">
        <div class="w-1/2 grid grid-cols-5 gap-3">
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
        <livewire:livewire-line-chart :line-chart-model="$this->lineChartModel" class="dark:text-white"/>
    </flux:card>
</div>

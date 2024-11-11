<div>
    @livewireChartsScripts

    <flux:heading size="lg">{{ $this->pingResult->ip }}</flux:heading>

    <div class="w-full mt-5">
        <div class="w-1/2 grid grid-cols-5 gap-3">
            <div>
                <div class="flex items-center">
                    <flux:subheading class="mr-1">Average</flux:subheading>
                    <flux:icon.calculator />
                </div>
                <flux:heading size="xl">{{ $this->averageRoundTripTime }} ms</flux:heading>
            </div>
            <div>
                <div class="flex items-center">
                    <flux:subheading class="mr-1">Minimum</flux:subheading>
                    <flux:icon.minus-circle />
                </div>
                <flux:heading size="xl">{{ $this->minimumRoundTripTime }} ms</flux:heading>
            </div>
            <div>
                <div class="flex items-center">
                    <flux:subheading class="mr-1">Maximum</flux:subheading>
                    <flux:icon.plus-circle />
                </div>
                <flux:heading size="xl">{{ $this->maximumRoundTripTime }} ms</flux:heading>
            </div>
            <div>
                <div class="flex items-center">
                    <flux:subheading class="mr-1">Loss</flux:subheading>
                    <flux:icon.percent-badge />
                </div>
                <flux:heading size="xl">{{ $this->packetLossPercentage }}%</flux:heading>
            </div>
            <div>
                <div class="flex items-center">
                    <flux:subheading class="mr-1">Loss Count</flux:subheading>
                    <flux:icon.x-circle />
                </div>
                <flux:heading size="xl">{{ $this->lossCount }} / {{ $this->totalCount }}</flux:heading>
            </div>
        </div>
    </div>

    <flux:card class="w-1/2 mt-8">
        <flux:icon.arrow-trending-up />
        <flux:subheading>Standard Deviation</flux:subheading>
        <flux:heading size="xl">{{ $this->standardDeviation }}</flux:heading>
        <livewire:livewire-line-chart :line-chart-model="$this->lineChartModel" class="dark:text-white"/>
    </flux:card>
</div>

<div class="mt-4">
    @vite('resources/js/map.js')

    <flux:card>
        <div x-show="$wire.selectedProvider !== null">
            <div wire:ignore id="map" class="h-96 rounded rounded-sm"></div>
        </div>

        <div class="w-1/2">

        </div>

        <div class="flex items-center gap-3 mt-3">
            <flux:select variant="listbox" placeholder="Providers..." wire:model.live="selectedProvider">
                @foreach ($providers as $provider)
                    <flux:select.option value="{{ $provider }}">{{ $provider }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:tabs variant="segmented">
                <flux:tab selected wire:click="limitV4" name="ipv4">IPv4</flux:tab>
                <flux:tab wire:click="limitV6" name="ipv6">IPv6</flux:tab>
                <flux:tab wire:click="clearIpTypeLimits" name="all">Both</flux:tab>
            </flux:tabs>
        </div>
    </flux:card>

    <flux:textarea
        class="mt-3"
        placeholder="1.1.1.0 - 1.1.1.255"
        wire:model="ranges"
        rows="7"
    />

    <div x-show="$wire.ipTypeMask === {{ \XbNz\Asn\Contracts\AsnToRangeInterface::FILTER_IPV4 }}">
        <div class="flex gap-2 mt-3">
            <flux:field>
                <flux:input type="number" wire:model.live.debounce.500ms="sampleSizeTotal" placeholder="Sample size" />
                <flux:error name="sampleSizeTotal" />
            </flux:field>
            <flux:button wire:click="addToMyIpAddresses" class="w-full">
                <span class="flex justify-between items-center gap-2">
                    Add to database
                    @svg('fad-database', 'h-5 w-5')
                </span>
            </flux:button>
        </div>
    </div>
</div>

@script
<script>
    const vectorSource = new VectorSource();
    const map = new Map({
        target: 'map',
        layers: [
            new TileLayer({
                source: new OSM(),
            }),
            new VectorLayer({
                source: vectorSource,
            }),
        ],
        view: new View({
            center: [0, 0],
            zoom: 2,
        }),
    });

    map.render();

    const draw = new Draw({
        source: vectorSource,
        type: 'Polygon',
    });

    map.addInteraction(draw);

    draw.on('drawend', (event) => {
        const geojsonFormat = new GeoJSON();
        const featureGeojson = geojsonFormat.writeFeaturesObject([event.feature], {
            dataProjection: 'EPSG:4326',
            featureProjection: 'EPSG:3857',
        });
        $wire.call('addPolygon', featureGeojson);
    });
</script>
@endscript

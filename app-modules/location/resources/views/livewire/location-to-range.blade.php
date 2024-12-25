<div>
    @vite('resources/js/map.js')

    <flux:card>
        <div id="map" class="h-96 rounded rounded-sm"></div>
    </flux:card>
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
        $wire.call('addPolygon', new GeoJSON().writeFeaturesObject([event.feature]));
    });
</script>
@endscript

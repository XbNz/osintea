import { Map, View } from 'ol';
import { Tile as TileLayer } from 'ol/layer';
import { OSM } from 'ol/source';
import GeoJSON from 'ol/format/GeoJSON';
import { Draw } from 'ol/interaction';
import { Vector as VectorSource } from 'ol/source';
import { Vector as VectorLayer } from 'ol/layer';
import 'ol/ol.css';

window.OsinteaMap = {
    Draw,
    GeoJSON,
    Map,
    OSM,
    TileLayer,
    VectorLayer,
    VectorSource,
    View,
};

window.withOsinteaMap = (callback) => {
    if (window.OsinteaMap !== undefined) {
        callback(window.OsinteaMap);

        return;
    }

    window.addEventListener('osintea-map-ready', () => callback(window.OsinteaMap), { once: true });
};

window.dispatchEvent(new Event('osintea-map-ready'));

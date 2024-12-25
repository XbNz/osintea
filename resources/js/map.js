import { Map, View } from 'ol';
import { Tile as TileLayer } from 'ol/layer';
import { OSM } from 'ol/source';
import GeoJSON from 'ol/format/GeoJSON';
import { Draw } from 'ol/interaction';
import { Vector as VectorSource } from 'ol/source';
import { Vector as VectorLayer } from 'ol/layer';
import 'ol/ol.css';

window.Map = Map;
window.View = View;
window.TileLayer = TileLayer;
window.OSM = OSM;
window.GeoJSON = GeoJSON;
window.Draw = Draw;
window.VectorSource = VectorSource;
window.VectorLayer = VectorLayer;

import { Chart, registerables } from "chart.js";
import zoomPlugin from "chartjs-plugin-zoom";

Chart.register(...registerables, zoomPlugin);

window.Chart = Chart;

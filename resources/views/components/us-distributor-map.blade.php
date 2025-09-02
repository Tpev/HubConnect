@props([
    // Postal codes to highlight, e.g. ['NC','SC','GA']
    'targetStates' => [],
    // Height in px
    'height' => 420,
])

@php($mapId = 'us-map-'.uniqid())

<div class="w-full">
    <div class="flex items-center justify-between mb-2">
        <h3 class="text-base font-semibold">Distributor Target Map (US)</h3>
        <span class="text-xs text-slate-500">Highlighted = actively searching</span>
    </div>

    <div class="relative" style="height: {{ (int)$height }}px;">
        <canvas id="{{ $mapId }}" style="width:100%; height:100%"></canvas>
    </div>
</div>

<script type="module">
import { Chart, registerables } from "https://esm.sh/chart.js";
import { topojson, ChoroplethController, GeoFeature, ProjectionScale, ColorScale } from "https://esm.sh/chartjs-chart-geo";

Chart.register(ChoroplethController, GeoFeature, ProjectionScale, ColorScale, ...registerables);

// Blade → JS
const TARGET_STATES = @json($targetStates);

// FIPS → postal
const FIPS_TO_POSTAL = {
  "01":"AL","02":"AK","04":"AZ","05":"AR","06":"CA","08":"CO","09":"CT","10":"DE","11":"DC",
  "12":"FL","13":"GA","15":"HI","16":"ID","17":"IL","18":"IN","19":"IA","20":"KS","21":"KY",
  "22":"LA","23":"ME","24":"MD","25":"MA","26":"MI","27":"MN","28":"MS","29":"MO","30":"MT",
  "31":"NE","32":"NV","33":"NH","34":"NJ","35":"NM","36":"NY","37":"NC","38":"ND","39":"OH",
  "40":"OK","41":"OR","42":"PA","44":"RI","45":"SC","46":"SD","47":"TN","48":"TX","49":"UT",
  "50":"VT","51":"VA","53":"WA","54":"WV","55":"WI","56":"WY"
};

const baseColor   = "rgba(148,163,184,0.35)"; // slate-400-ish
const targetColor = "rgba(34,197,94,0.85)";   // green-500
const hoverColor  = "rgba(34,197,94,1.0)";

(async () => {
  const us = await fetch("https://unpkg.com/us-atlas/states-10m.json").then(r => r.json());

  const nation = topojson.feature(us, us.objects.nation).features[0];
  const states = topojson.feature(us, us.objects.states).features;

  const data = states.map((d) => {
    const code = FIPS_TO_POSTAL[String(d.id).padStart(2, "0")];
    const isTarget = TARGET_STATES.includes(code);
    return { feature: d, code, value: isTarget ? 1 : 0 };
  });

  const ctx = document.getElementById(@json($mapId)).getContext("2d");
  new Chart(ctx, {
    type: "choropleth",
    data: {
      labels: states.map((d) => d.properties.name),
      datasets: [{
        label: "States",
        outline: nation,
        data,
        // Per-feature color via scriptable options
        backgroundColor: (c) => c.raw?.value ? targetColor : baseColor,
        hoverBackgroundColor: (c) => c.raw?.value ? hoverColor : baseColor,
        borderColor: "rgba(100,116,139,0.8)", // slate-500
        borderWidth: 0.6,
      }],
    },
    options: {
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: (ctx) => {
              const name = ctx.raw?.feature?.properties?.name ?? "";
              const code = ctx.raw?.code ?? "";
              const status = ctx.raw?.value ? "Actively Searching" : "Not Targeted";
              return `${name} (${code}) — ${status}`;
            }
          }
        }
      },
      scales: {
        projection: {
          axis: "x",
          projection: "albersUsa"
        },
        color: {
          axis: "x",
          quantize: 2,
          legend: { display: false }
        }
      },
      maintainAspectRatio: false,
    }
  });
})();
</script>

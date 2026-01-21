export function initChartCalls7Days() {
  const el = document.querySelector('#chartCalls7Days');
  if (!el || !window.ApexCharts) return;
  let data = {};
  const attr = el.getAttribute('data-calls-7days');
  if (attr) {
    try { data = JSON.parse(attr); } catch (_) { data = {}; }
  } else {
    data = window.calls7DaysData || {};
  }
  const categories = Object.keys(data);
  const seriesData = categories.map((d) => data[d]);

  const options = {
    chart: { type: 'area', height: 180, toolbar: { show: false }, parentHeightOffset: 0 },
    series: [{ name: 'Chamadas', data: seriesData }],
    xaxis: { categories },
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    colors: ['#465fff'],
    fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
    grid: { strokeDashArray: 3, padding: { left: 0, right: 0 } },
    tooltip: { enabled: true },
    responsive: [
      { breakpoint: 1024, options: { chart: { height: 170 } } },
      { breakpoint: 768, options: { chart: { height: 160 } } },
      { breakpoint: 480, options: { chart: { height: 150 } } },
    ],
  };

  const chart = new ApexCharts(el, options);
  chart.render();
}

export function initChartCallsStatus() {
  const el = document.querySelector('#chartCallsStatus');
  if (!el || !window.ApexCharts) return;
  let data = {};
  const attr = el.getAttribute('data-calls-status');
  if (attr) {
    try { data = JSON.parse(attr); } catch (_) { data = {}; }
  } else {
    data = window.callsStatusData || {};
  }
  const labels = Object.keys(data);
  const series = labels.map((k) => data[k]);

  const options = {
    chart: { type: 'donut', height: 220, toolbar: { show: false }, parentHeightOffset: 0 },
    series,
    labels,
    dataLabels: { enabled: true, dropShadow: { enabled: false } },
    legend: { position: 'bottom' },
    colors: ['#12b76a', '#f04438', '#3641f5'],
    stroke: { width: 1 },
    plotOptions: { pie: { donut: { size: '70%' } } },
    grid: { padding: { left: 0, right: 0 } },
    responsive: [
      { breakpoint: 1024, options: { chart: { height: 210 } } },
      { breakpoint: 768, options: { chart: { height: 200 } } },
      { breakpoint: 480, options: { chart: { height: 190 } } },
    ],
  };

  const chart = new ApexCharts(el, options);
  chart.render();
}

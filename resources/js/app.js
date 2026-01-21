import './bootstrap';
import Alpine from 'alpinejs';

// flatpickr
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';



window.Alpine = Alpine;
window.flatpickr = flatpickr;

// Global stores
Alpine.store('dashboard', {
    isFullscreen: false,
    enter() {
        const el = document.getElementById('calls-dashboard');
        if (el && !document.fullscreenElement) {
            el.requestFullscreen().catch(() => {});
        }
        this.isFullscreen = true;
    },
    exit() {
        if (document.fullscreenElement) {
            document.exitFullscreen().catch(() => {});
        }
        this.isFullscreen = false;
    },
    toggle() {
        if (this.isFullscreen) this.exit(); else this.enter();
    }
});

Alpine.start();

// Initialize components on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // Map imports
    if (document.querySelector('#mapOne')) {
        import('./components/map').then(module => module.initMap());
    }

    // Chart imports (lazy-load ApexCharts only if needed)
    const chartSelectors = ['#chartOne', '#chartTwo', '#chartThree', '#chartSix', '#chartEight', '#chartThirteen', '#chartCalls7Days', '#chartCallsStatus'];
    if (chartSelectors.some(sel => document.querySelector(sel))) {
        import('apexcharts').then(({ default: ApexCharts }) => {
            window.ApexCharts = ApexCharts;
            if (document.querySelector('#chartOne')) {
                import('./components/chart/chart-1').then(module => module.initChartOne());
            }
            if (document.querySelector('#chartTwo')) {
                import('./components/chart/chart-2').then(module => module.initChartTwo());
            }
            if (document.querySelector('#chartThree')) {
                import('./components/chart/chart-3').then(module => module.initChartThree());
            }
            if (document.querySelector('#chartSix')) {
                import('./components/chart/chart-6').then(module => module.initChartSix());
            }
            if (document.querySelector('#chartEight')) {
                import('./components/chart/chart-8').then(module => module.initChartEight());
            }
            if (document.querySelector('#chartThirteen')) {
                import('./components/chart/chart-13').then(module => module.initChartThirteen());
            }
            if (document.querySelector('#chartCalls7Days')) {
                import('./components/chart/chart-calls-7days').then(module => module.initChartCalls7Days());
            }
            if (document.querySelector('#chartCallsStatus')) {
                import('./components/chart/chart-calls-status').then(module => module.initChartCallsStatus());
            }
        });
    }
    // Auto-refresh for Calls dashboard: align to minute boundaries
    const contentEl = document.getElementById('calls-dashboard-content');
    if (contentEl) {
        const refreshCallsDashboard = () => {
            fetch('/dashboard/calls/fragment', { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                .then(r => r.text())
                .then(html => {
                    contentEl.innerHTML = html;
                    const needCharts = contentEl.querySelector('#chartCalls7Days') || contentEl.querySelector('#chartCallsStatus');
                    const initCharts = () => {
                        Promise.resolve().then(() => {
                            import('./components/chart/chart-calls-7days').then(m => m.initChartCalls7Days());
                            import('./components/chart/chart-calls-status').then(m => m.initChartCallsStatus());
                        });
                    };
                    if (!window.ApexCharts && needCharts) {
                        import('apexcharts').then(({ default: ApexCharts }) => { window.ApexCharts = ApexCharts; initCharts(); });
                    } else if (needCharts) {
                        initCharts();
                    }
                })
                .catch(() => {});
        };

        // Align first run to the next full minute, then every 60s
        const now = new Date();
        const msToNextMinute = (60 - now.getSeconds()) * 1000 - now.getMilliseconds();
        setTimeout(() => {
            refreshCallsDashboard();
            setInterval(refreshCallsDashboard, 60000);
        }, Math.max(500, msToNextMinute));
    }

    // Calendar init (lazy-load library only if needed)
    if (document.querySelector('#calendar')) {
        import('@fullcalendar/core').then(({ Calendar }) => {
            window.FullCalendar = Calendar;
            import('./components/calendar-init').then(module => module.calendarInit());
        });
    }

    // Date pickers: attach flatpickr to marked inputs
    const dateInputs = document.querySelectorAll('input.datepicker');
    dateInputs.forEach(el => {
        flatpickr(el, {
            dateFormat: 'Y-m-d',
            allowInput: true,
        });
    });

    const datetimeInputs = document.querySelectorAll('input.datetimepicker');
    datetimeInputs.forEach(el => {
        flatpickr(el, {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            time_24hr: true,
            allowInput: true,
        });
    });

    // Money mask: format BRL currency on input and normalize on submit
    const moneyFormatter = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
    const attachMoneyMask = (el) => {
        const formatDisplay = () => {
            const digits = (el.value || '').replace(/\D/g, '');
            if (!digits) { el.value = ''; return; }
            const number = parseInt(digits, 10) / 100;
            el.value = moneyFormatter.format(isNaN(number) ? 0 : number);
        };
        const normalizeValue = () => {
            const digits = (el.value || '').replace(/\D/g, '');
            if (!digits) { el.value = ''; return; }
            const number = parseInt(digits, 10) / 100;
            el.value = isNaN(number) ? '' : number.toFixed(2);
        };

        // Initial format if pre-filled
        if (el.value) { formatDisplay(); }
        el.addEventListener('input', formatDisplay);
        el.addEventListener('blur', formatDisplay);

        // On parent form submit, normalize to numeric string
        const form = el.closest('form');
        if (form) {
            form.addEventListener('submit', () => normalizeValue());
        }
    };

    // Attach to explicit money class or name="value"
    const moneyInputs = document.querySelectorAll('input.money, input[name="value"]');
    moneyInputs.forEach(attachMoneyMask);
});

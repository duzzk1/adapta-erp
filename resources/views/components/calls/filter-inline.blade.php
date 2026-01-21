@php
  $mode = $mode ?? 'desktop';
@endphp

@if ($mode === 'desktop')
  <form x-data="{
          interval: 0,
          timer: null,
          url: '{{ route('dashboard.calls.fragment') }}',
          load() {
            const saved = localStorage.getItem('calls_refresh_interval');
            this.interval = saved ? Number(saved) : 0;
            this.apply();
          },
          refresh() {
            fetch(this.url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
              .then(r => r.text())
              .then(html => {
                const content = document.getElementById('calls-dashboard-content');
                if (content) { content.innerHTML = html; }
              })
              .catch(() => {});
          },
          apply() {
            if (this.timer) { clearInterval(this.timer); this.timer = null; }
            if (this.interval && this.interval > 0) {
              this.timer = setInterval(() => { this.refresh(); }, this.interval * 1000);
            }
          }
        }" x-init="load()" action="{{ route('dashboard.calls.filter') }}" method="POST"
        class="hidden xl:flex items-center gap-2">
    @csrf
    <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">Filtro de Chamadas</span>
    <input type="date" name="start_date"
           value="{{ session('filter_start_date') }}"
           class="h-10 w-32 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 shadow-theme-xs focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
    <input type="date" name="end_date"
           value="{{ session('filter_end_date') }}"
           class="h-10 w-32 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 shadow-theme-xs focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
    <button type="submit"
            class="inline-flex h-10 items-center rounded-lg bg-gray-900 px-4 text-sm font-medium text-white shadow-theme-xs hover:bg-gray-800">
      Filtrar
    </button>
    <div class="flex items-center gap-2 ml-2">
      <label class="text-sm text-gray-500 dark:text-gray-400">Atualização</label>
      <select x-model.number="interval" @change="localStorage.setItem('calls_refresh_interval', interval); apply()"
              class="h-10 w-32 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 shadow-theme-xs focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
        <option value="0">Desligado</option>
        <option value="15">15s</option>
        <option value="30">30s</option>
        <option value="60">1m</option>
        <option value="300">5m</option>
      </select>
    </div>
  </form>
@elseif ($mode === 'mobile')
  <form x-data="{
          interval: 0,
          timer: null,
          url: '{{ route('dashboard.calls.fragment') }}',
          load() {
            const saved = localStorage.getItem('calls_refresh_interval');
            this.interval = saved ? Number(saved) : 0;
            this.apply();
          },
          refresh() {
            fetch(this.url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
              .then(r => r.text())
              .then(html => {
                const content = document.getElementById('calls-dashboard-content');
                if (content) { content.innerHTML = html; }
              })
              .catch(() => {});
          },
          apply() {
            if (this.timer) { clearInterval(this.timer); this.timer = null; }
            if (this.interval && this.interval > 0) {
              this.timer = setInterval(() => { this.refresh(); }, this.interval * 1000);
            }
          }
        }" x-init="load()" action="{{ route('dashboard.calls.filter') }}" method="POST"
        class="flex xl:hidden items-center gap-2 w-full">
    @csrf
    <span class="text-sm text-gray-500 dark:text-gray-400">Filtro de Chamadas</span>
    <input type="date" name="start_date"
           value="{{ session('filter_start_date') }}"
           class="h-10 flex-1 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 shadow-theme-xs focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
    <input type="date" name="end_date"
           value="{{ session('filter_end_date') }}"
           class="h-10 flex-1 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 shadow-theme-xs focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
    <button type="submit"
            class="inline-flex h-10 items-center rounded-lg bg-gray-900 px-4 text-sm font-medium text-white shadow-theme-xs hover:bg-gray-800">
      Filtrar
    </button>
    <div class="flex items-center gap-2">
      <label class="text-sm text-gray-500 dark:text-gray-400">Atualização</label>
      <select x-model.number="interval" @change="localStorage.setItem('calls_refresh_interval', interval); apply()"
              class="h-10 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 shadow-theme-xs focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
        <option value="0">Desligado</option>
        <option value="15">15s</option>
        <option value="30">30s</option>
        <option value="60">1m</option>
        <option value="300">5m</option>
      </select>
    </div>
  </form>
@endif

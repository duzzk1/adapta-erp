<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800 h-full">
  <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Ranking de Ligações</h3>
  <div class="mt-4 custom-scrollbar overflow-y-auto" style="max-height: 28rem;">
    @foreach ($ranking as $item)
      <div class="flex items-center justify-between border-b border-gray-100 py-3 last:border-b-0 dark:border-gray-800 {{ $loop->first ? 'bg-brand-50/60 dark:bg-brand-500/10 rounded-lg px-3' : '' }}">
        <div class="flex items-center gap-3">
          <div class="flex h-8 w-8 items-center justify-center rounded-full {{ $loop->first ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' }}">
            {{ $loop->iteration }}
          </div>
          <span class="font-medium text-gray-800 dark:text-gray-200">{{ $item->user_name }}</span>
          @if ($loop->first)
            <span class="inline-flex items-center rounded bg-brand-100 px-2 py-0.5 text-xs font-semibold text-brand-700 dark:bg-brand-500/20 dark:text-brand-300">Top</span>
          @endif
        </div>
        <div class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $item->total }} ligações</div>
      </div>
    @endforeach
  </div>
</div>

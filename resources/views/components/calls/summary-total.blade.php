<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
  <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Total de ligações</h3>
  <div class="mt-4 flex justify-center">
    <div class="rounded-2xl border border-gray-200 px-8 py-10 text-5xl font-bold text-gray-900 dark:border-gray-700 dark:text-white">{{ $total }}</div>
  </div>
  <div class="mt-6 grid grid-cols-3 gap-4 text-center">
    <div class="rounded-xl border border-gray-200 p-5 dark:border-gray-700">
      <p class="text-sm text-gray-500 dark:text-gray-400">Atendidas</p>
      <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $callStatus['atendidas']->count() }}</p>
    </div>
    <div class="rounded-xl border border-gray-200 p-5 dark:border-gray-700">
      <p class="text-sm text-gray-500 dark:text-gray-400">Não atendidas</p>
      <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $callStatus['nao_atendidas']->count() }}</p>
    </div>
    <div class="rounded-xl border border-gray-200 p-5 dark:border-gray-700">
      <p class="text-sm text-gray-500 dark:text-gray-400">Agendadas</p>
      <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $callStatus['agendadas']->count() }}</p>
    </div>
  </div>
</div>

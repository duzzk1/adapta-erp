@extends('layouts.app')

@section('content')
<div id="calls-dashboard"
   class="relative space-y-6 transition-all ease-in-out duration-500"
      :class="{
        'fixed inset-0 z-[1000000] w-screen h-screen p-6 bg-white dark:bg-gray-900 overflow-auto': $store.dashboard.isFullscreen
      }">
  <!-- Floating fullscreen toggle -->
  <button
    @click="$store.dashboard.toggle()"
    class="absolute top-3 right-3 inline-flex items-center justify-center h-10 w-10 rounded-lg border border-gray-200 bg-white text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
    title="Tela cheia"
    x-transition>
    <!-- Expand icon -->
    <svg x-show="!$store.dashboard.isFullscreen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path d="M3 7a1 1 0 011-1h3V3a1 1 0 112 0v4a1 1 0 01-1 1H4a1 1 0 01-1-1z" />
      <path d="M17 13a1 1 0 01-1 1h-3v3a1 1 0 11-2 0v-4a1 1 0 011-1h4a1 1 0 011 1z" />
    </svg>
    <!-- Compress icon -->
    <svg x-show="$store.dashboard.isFullscreen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path d="M7 3a1 1 0 011 1v3h3a1 1 0 110 2H7a1 1 0 01-1-1V4a1 1 0 011-1z" />
      <path d="M13 17a1 1 0 01-1-1v-3H9a1 1 0 110-2h4a1 1 0 011 1v4a1 1 0 01-1 1z" />
    </svg>
  </button>

  <!-- Inner content wrapper for partial refresh -->
  <div id="calls-dashboard-content">
    @include('pages.dashboard._calls-content')
  </div>
  </div>
@endsection

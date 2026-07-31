@props(['title' => ''])

<div x-data="{ show: @entangle($attributes->wire('model')) }" x-show="show" x-cloak
     class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none;">
    <div x-show="show" x-transition.opacity x-on:click="show = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

    <div x-show="show" x-transition
         class="relative w-full max-w-md rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-2xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold">{{ $title }}</h3>
            <button x-on:click="show = false" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">✕</button>
        </div>

        <div>{{ $slot }}</div>

        @isset($footer)
            <div class="mt-5 flex justify-end gap-2">{{ $footer }}</div>
        @endisset
    </div>
</div>

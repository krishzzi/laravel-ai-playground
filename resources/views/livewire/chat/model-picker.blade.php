<div class="relative" x-data="{ open: @entangle('open') }">
    <button x-on:click="open = !open" class="flex items-center gap-2 text-sm px-3 py-1.5 rounded-lg border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800">
        <span class="font-medium">{{ $model }}</span>
        <span class="text-zinc-400">·</span>
        <span class="text-zinc-500">{{ $thinkingModes[$thinkingMode]['label'] }}</span>
        <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>

    <div x-show="open" x-on:click.outside="open = false" x-transition
         class="absolute right-0 mt-2 w-96 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xl z-50 overflow-hidden"
         style="display: none;">

        <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Thinking mode</p>
            <div class="grid grid-cols-4 gap-1 mt-2">
                @foreach($thinkingModes as $key => $mode)
                    <button wire:click="chooseThinkingMode('{{ $key }}')"
                            class="text-xs px-2 py-1.5 rounded-lg border {{ $thinkingMode === $key ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-300' : 'border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                        {{ $mode['label'] }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="max-h-96 overflow-y-auto">
            @foreach($models as $providerKey => $providerModels)
                <div class="px-4 pt-3 pb-1 text-xs font-semibold uppercase tracking-wide text-zinc-500">
                    {{ $providerModels->first()['provider_label'] }}
                </div>
                @foreach($providerModels as $m)
                    <button wire:click="choose('{{ $providerKey }}', '{{ $m['id'] }}')"
                            @disabled(!$m['usable'])
                            class="w-full flex items-center justify-between px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800 disabled:opacity-40 disabled:cursor-not-allowed">
                        <span class="flex items-center gap-2">
                            {{ $m['label'] }}
                            @if($m['thinking'] ?? false) <span class="text-[10px] px-1.5 py-0.5 rounded bg-violet-100 dark:bg-violet-500/10 text-violet-600 dark:text-violet-300">thinking</span> @endif
                        </span>
                        <span class="text-xs text-zinc-400">{{ number_format($m['context']) }} ctx</span>
                    </button>
                @endforeach
            @endforeach
        </div>

        <div class="px-4 py-2.5 border-t border-zinc-100 dark:border-zinc-800 text-xs">
            <a href="{{ route('settings.providers') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">+ Add your own API key or custom server</a>
        </div>
    </div>
</div>

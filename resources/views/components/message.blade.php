@props(['message'])

<div class="flex gap-3 max-w-3xl {{ $message->role === 'user' ? 'ml-auto flex-row-reverse' : '' }}" x-data="{ editing: false, draft: @js($message->content) }">

    @if($message->role !== 'user')
        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-fuchsia-500 shrink-0 flex items-center justify-center text-[10px] text-white font-bold">
            AI
        </div>
    @endif

    <div class="min-w-0 {{ $message->role === 'user' ? 'items-end' : 'items-start' }} flex flex-col">

        {{-- Thinking trace (collapsible) --}}
        @if($message->thinking_trace)
            <details class="mb-1.5 text-xs text-zinc-500 max-w-lg">
                <summary class="cursor-pointer select-none hover:text-zinc-700 dark:hover:text-zinc-300">💭 Thinking ({{ $message->thinking_mode }})</summary>
                <div class="mt-1 p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800/60 whitespace-pre-wrap">{{ $message->thinking_trace }}</div>
            </details>
        @endif

        <div class="rounded-2xl px-4 py-2.5 text-sm leading-relaxed
            {{ $message->role === 'user'
                ? 'bg-indigo-600 text-white'
                : 'bg-zinc-100 dark:bg-zinc-800/70 text-zinc-800 dark:text-zinc-100' }}">

            <template x-if="!editing">
                <div class="prose prose-sm dark:prose-invert max-w-none">{!! \Illuminate\Support\Str::markdown($message->content ?? '') !!}</div>
            </template>

            <template x-if="editing">
                <div>
                    <textarea x-model="draft" rows="3" class="w-full text-sm rounded-lg text-zinc-900 p-2"></textarea>
                    <div class="flex gap-2 mt-2">
                        <button x-on:click="$wire.editAndBranch({{ $message->id }}, draft); editing = false" class="text-xs px-3 py-1 rounded-lg bg-white text-indigo-700">Save & regenerate</button>
                        <button x-on:click="editing = false" class="text-xs px-3 py-1 rounded-lg bg-white/20">Cancel</button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Attachments --}}
        @if(!empty($message->attachments))
            <div class="flex flex-wrap gap-1.5 mt-1.5">
                @foreach($message->attachments as $att)
                    <span class="text-[11px] px-2 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500">📎 {{ $att['name'] ?? 'file' }}</span>
                @endforeach
            </div>
        @endif

        {{-- Citations --}}
        @if(!empty($message->citations))
            <div class="flex flex-wrap gap-1.5 mt-1.5">
                @foreach($message->citations as $i => $c)
                    <a href="{{ $c['url'] }}" target="_blank" class="text-[11px] px-2 py-0.5 rounded-full bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-300 hover:underline">[{{ $i + 1 }}] {{ $c['title'] ?? $c['url'] }}</a>
                @endforeach
            </div>
        @endif

        {{-- Generated artifacts → split view trigger --}}
        @foreach($message->artifacts as $artifact)
            <button
                x-on:click="$wire.openInSplitView('{{ $artifact->uuid }}'); $dispatch('open-split-view')"
                class="mt-2 flex items-center gap-2 text-xs px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 max-w-xs">
                <span>{{ match($artifact->type) { 'code' => '🧩', 'zip' => '🗜️', 'image' => '🖼️', default => '📄' } }}</span>
                <span class="truncate font-medium">{{ $artifact->filename }}</span>
                <span class="ml-auto text-zinc-400">Open ↗</span>
            </button>
        @endforeach

        {{-- Row actions --}}
        <div class="flex items-center gap-2 mt-1 text-[11px] text-zinc-400">
            @if($message->role === 'user')
                <button x-on:click="editing = true" class="hover:text-zinc-600 dark:hover:text-zinc-300">Edit</button>
            @else
                @if($message->gems_charged)
                    <span>💎 {{ $message->gems_charged }}</span>
                @endif
                <button class="hover:text-zinc-600 dark:hover:text-zinc-300">Copy</button>
                <button class="hover:text-zinc-600 dark:hover:text-zinc-300">Regenerate</button>
            @endif
        </div>
    </div>
</div>

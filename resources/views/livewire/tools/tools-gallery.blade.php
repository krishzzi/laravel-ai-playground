<div class="max-w-5xl mx-auto py-10 px-4">
    <div class="flex items-center justify-between mb-1">
        <h1 class="text-2xl font-semibold">Tools</h1>
        <button wire:click="$set('showCreateModal', true)" class="text-sm px-4 py-2 rounded-xl bg-zinc-900 dark:bg-white text-white dark:text-zinc-900">+ New tool</button>
    </div>
    <p class="text-sm text-zinc-500 mb-6">Invoke any tool in chat with <code>@slug</code>. Built-ins, your own prompt tools, and third-party MCP tools all live here.</p>

    <input wire:model.live.debounce.300ms="search" placeholder="Search tools…" class="w-full max-w-sm mb-6 rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 text-sm" />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($tools as $tool)
            <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-medium text-sm">{{ $tool->icon ?? '🧰' }} {{ $tool->name }}</p>
                        <p class="text-xs text-zinc-400">@{{ $tool->slug }} · {{ ucfirst($tool->kind) }}</p>
                    </div>
                    @if(!$tool->is_public)
                        <button wire:click="toggleActive({{ $tool->id }})" class="text-xs px-2 py-0.5 rounded-full {{ $tool->is_active ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-400' }}">
                            {{ $tool->is_active ? 'On' : 'Off' }}
                        </button>
                    @endif
                </div>
                <p class="text-xs text-zinc-500 mt-2 line-clamp-3">{{ $tool->description }}</p>
            </div>
        @endforeach
    </div>

    @if($showCreateModal)
        <x-modal wire:model="showCreateModal" title="New tool">
            <div class="space-y-3 text-sm">
                <input wire:model="newSlug" placeholder="slug (e.g. sql_explainer)" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800" />
                <input wire:model="newName" placeholder="Display name" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800" />
                <textarea wire:model="newDescription" rows="2" placeholder="What it does (shown to the model too)" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800"></textarea>
                <textarea wire:model="newPromptTemplate" rows="4" placeholder="Prompt template — use {{input}} for the argument" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 font-mono text-xs"></textarea>
            </div>
            <x-slot:footer>
                <button wire:click="create" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm">Create</button>
            </x-slot:footer>
        </x-modal>
    @endif
</div>

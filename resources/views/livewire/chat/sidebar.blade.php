<div class="w-[280px] shrink-0 h-full flex flex-col bg-zinc-100/70 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800">

    {{-- Brand + New Chat --}}
    <div class="p-3 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="font-semibold text-lg tracking-tight flex items-center gap-2">
            <span class="w-6 h-6 rounded-md bg-gradient-to-br from-indigo-500 via-fuchsia-500 to-amber-400"></span>
            Nova
        </a>
    </div>

    <div class="px-3">
        <button wire:click="newChat"
                class="w-full flex items-center gap-2 justify-center rounded-xl bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 py-2.5 text-sm font-medium hover:opacity-90 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New chat
        </button>
    </div>

    {{-- Quick nav: T2I / Video / Tools / Skills --}}
    <nav class="px-3 mt-4 space-y-1 text-sm">
        <a href="{{ route('studio.image') }}" class="flex items-center gap-2 px-2.5 py-2 rounded-lg hover:bg-zinc-200/70 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
            🎨 <span>Image Studio</span>
        </a>
        <a href="{{ route('studio.video') }}" class="flex items-center gap-2 px-2.5 py-2 rounded-lg hover:bg-zinc-200/70 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
            🎬 <span>Video Studio</span>
        </a>
        <a href="{{ route('tools.index') }}" class="flex items-center gap-2 px-2.5 py-2 rounded-lg hover:bg-zinc-200/70 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
            🧰 <span>Tools</span>
        </a>
        <a href="{{ route('skills.index') }}" class="flex items-center gap-2 px-2.5 py-2 rounded-lg hover:bg-zinc-200/70 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
            🧠 <span>Skills</span>
        </a>
    </nav>

    {{-- Search --}}
    <div class="px-3 mt-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search chats…"
               class="w-full text-sm rounded-lg border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 focus:ring-2 focus:ring-indigo-500" />
    </div>

    {{-- Projects --}}
    <div class="px-3 mt-5">
        <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-wide text-zinc-500">
            <span>Projects</span>
            <button wire:click="$set('showNewProjectModal', true)" class="hover:text-zinc-800 dark:hover:text-zinc-200">+</button>
        </div>
        <div class="mt-2 space-y-0.5">
            <button wire:click="$set('activeProjectId', null)"
                    class="w-full text-left text-sm px-2 py-1.5 rounded-lg {{ !$activeProjectId ? 'bg-indigo-100 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300' : 'hover:bg-zinc-200/70 dark:hover:bg-zinc-800' }}">
                All chats
            </button>
            @foreach($projects as $project)
                <button wire:click="$set('activeProjectId', {{ $project->id }})"
                        class="w-full text-left text-sm px-2 py-1.5 rounded-lg flex justify-between items-center {{ $activeProjectId === $project->id ? 'bg-indigo-100 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300' : 'hover:bg-zinc-200/70 dark:hover:bg-zinc-800' }}">
                    <span class="truncate">📁 {{ $project->name }}</span>
                    <span class="text-xs text-zinc-400">{{ $project->chat_sessions_count }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Sessions --}}
    <div class="flex-1 overflow-y-auto px-3 mt-5 space-y-0.5 pb-3">
        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 mb-1">Recent</div>
        @foreach($sessions as $session)
            <div class="group relative">
                <a href="{{ route('chat.show', $session) }}" wire:navigate
                   class="flex items-center gap-2 px-2.5 py-2 rounded-lg text-sm hover:bg-zinc-200/70 dark:hover:bg-zinc-800 truncate">
                    @if($session->pinned) <span class="text-amber-500">📌</span> @endif
                    <span class="truncate">{{ $session->title }}</span>
                </a>
                <div class="hidden group-hover:flex absolute right-1 top-1.5 gap-1">
                    <button wire:click="pin({{ $session->id }})" class="text-xs px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-700">📌</button>
                    <button wire:click="archive({{ $session->id }})" class="text-xs px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-700">🗄</button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Gems + Avatar / Settings --}}
    <div class="border-t border-zinc-200 dark:border-zinc-800 p-3 flex items-center justify-between">
        <a href="{{ route('billing.gems') }}" class="text-xs font-medium flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300">
            💎 {{ number_format(app(\App\Services\GemBillingService::class)->balance(auth()->user())) }}
        </a>
        <livewire:user.avatar-menu />
    </div>

    @if($showNewProjectModal)
        <x-modal wire:model="showNewProjectModal" title="New project">
            <input wire:model="newProjectName" placeholder="Project name" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800" />
            <x-slot:footer>
                <button wire:click="createProject" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm">Create</button>
            </x-slot:footer>
        </x-modal>
    @endif
</div>

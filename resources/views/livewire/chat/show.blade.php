<x-app-layout>
    <div class="flex h-screen w-full bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 overflow-hidden">

        {{-- Left rail: sessions, projects, tools, skills, settings, avatar --}}
        <livewire:chat.sidebar />

        {{-- Center: the chat window itself --}}
        <main class="flex-1 flex flex-col min-w-0 relative">
            <livewire:chat.chat-window :session="$session" :key="$session->uuid" />
        </main>

        {{-- Right: split view, opens only when an artifact is being previewed --}}
        <div
            x-data="{ open: false }"
            x-on:open-split-view.window="open = true"
            x-on:close-split-view.window="open = false"
            x-show="open"
            x-transition
            class="w-[45%] max-w-[820px] border-l border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex flex-col"
            style="display: none;"
        >
            <livewire:chat.split-view-panel />
        </div>
    </div>
</x-app-layout>

<div
    x-data="{
        sidebarOpen: false,
        init() {
            this.$nextTick(() => this.scrollToBottom(false));

            document.addEventListener('livewire:initialized', () => {
                if (!window.Livewire) return;

                Livewire.hook('commit', ({ succeed }) => {
                    succeed(() => {
                        this.$nextTick(() => this.scrollToBottom());
                    });
                });
            });
        },
        scrollToBottom(smooth = true) {
            const container = this.$refs.chatWindow;
            if (!container) return;

            container.scrollTo({
                top: container.scrollHeight,
                behavior: smooth ? 'smooth' : 'auto'
            });
        }
    }"
    class="flex h-screen overflow-hidden bg-[#0b0f19] text-slate-100"
>
    <!-- Mobile overlay -->
    <div
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-slate-950/70 backdrop-blur-sm md:hidden">
    </div>

    <!-- Sidebar -->
    <aside
        class="fixed inset-y-0 left-0 z-40 flex w-72 shrink-0 flex-col border-r border-white/10 bg-[#0f172a] shadow-2xl transform transition-transform duration-300 ease-out md:static md:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <div class="flex h-16 items-center justify-between border-b border-white/10 px-5">
            <div>
                <h2 class="text-base font-semibold tracking-wide text-white">Chats</h2>
                <p class="text-xs text-slate-400">Recent conversations</p>
            </div>

            <button
                @click="sidebarOpen = false"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition hover:bg-white/5 hover:text-white md:hidden">
                <x-heroicon-m-x-mark class="h-5 w-5" />
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4">
            <div class="mb-3 px-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">
                Recent
            </div>

            <ul class="space-y-2">
                <li class="group flex items-center justify-between rounded-2xl border border-transparent bg-white/[0.03] px-3 py-3 transition hover:border-white/10 hover:bg-white/[0.06]">
                    <div class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-medium text-slate-200">Past Chat 1</span>
                        <span class="block truncate text-xs text-slate-500">Conversation item</span>
                    </div>

                    <div class="ml-3 flex items-center gap-1 opacity-80 transition group-hover:opacity-100">
                        <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-white/5 hover:text-white">
                            <x-heroicon-c-pencil-square class="h-4 w-4" />
                        </button>
                        <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-white/5 hover:text-rose-400">
                            <x-heroicon-c-trash class="h-4 w-4" />
                        </button>
                    </div>
                </li>

                <li class="group flex items-center justify-between rounded-2xl border border-transparent bg-white/[0.03] px-3 py-3 transition hover:border-white/10 hover:bg-white/[0.06]">
                    <div class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-medium text-slate-200">Past Chat 2</span>
                        <span class="block truncate text-xs text-slate-500">Conversation item</span>
                    </div>

                    <div class="ml-3 flex items-center gap-1 opacity-80 transition group-hover:opacity-100">
                        <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-white/5 hover:text-white">
                            <x-heroicon-c-pencil-square class="h-4 w-4" />
                        </button>
                        <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-white/5 hover:text-rose-400">
                            <x-heroicon-c-trash class="h-4 w-4" />
                        </button>
                    </div>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Chat Area -->
    <div class="flex min-w-0 flex-1 flex-col bg-[radial-gradient(circle_at_top,rgba(59,130,246,0.12),transparent_30%)]">
        <!-- Navbar -->
        <header class="flex h-16 items-center justify-between border-b border-white/10 bg-[#0b1220]/80 px-4 backdrop-blur-xl md:px-6">
            <div class="flex items-center gap-3">
                <button
                    @click="sidebarOpen = !sidebarOpen"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition hover:bg-white/5 hover:text-white md:hidden">
                    <x-heroicon-m-bars-3 class="h-5 w-5" />
                </button>

                <div>
                    <div class="text-sm font-semibold tracking-wide text-white">
                        {{ config('app.name').' Chat' }}
                    </div>
                    <div class="text-xs text-slate-400">
                        Smart conversation workspace
                    </div>
                </div>
            </div>
        </header>

        <!-- Chat Window -->
        <main class="flex min-h-0 flex-1 flex-col p-3 md:p-5">
            <div class="mx-auto flex min-h-0 w-full max-w-6xl flex-1 flex-col">
                <div
                    id="chat-window"
                    x-ref="chatWindow"
                    class="flex-1 overflow-y-auto overscroll-contain rounded-[28px] border border-white/10 bg-[#0f172a]/70 p-4 shadow-[0_20px_80px_rgba(0,0,0,0.45)] backdrop-blur-xl md:p-6"
                >
                    @if(count($conversations))
                        <div class="space-y-6">
                            @foreach($conversations as $index => $message)

                                @if($message['role'] === 'user')
                                    <div class="flex justify-end">
                                        <div class="max-w-3xl">
                                            <div class="flex items-end justify-end gap-3">
                                                <div class="space-y-2">
                                                    <div class="flex justify-end">
                                                        <span class="rounded-full bg-white/5 px-3 py-1 text-[11px] font-medium tracking-wide text-slate-300">
                                                            You
                                                        </span>
                                                    </div>

                                                    <div class="rounded-[24px] rounded-br-md bg-gradient-to-br from-blue-500 to-indigo-600 px-4 py-3 text-sm leading-7 text-white shadow-[0_16px_40px_rgba(37,99,235,0.30)] md:px-5">
                                                        {{ $message['content'] }}
                                                    </div>

                                                    @if($index === $this->lastUserIndex)
                                                        <div class="flex justify-end">
                                                            <div class="flex items-center gap-1 rounded-full border border-white/10 bg-white/[0.03] p-1">
                                                                <button
                                                                    wire:click="editPrompt({{ $index }})"
                                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition hover:bg-white/5 hover:text-white">
                                                                    <x-heroicon-o-pencil-square class="h-4 w-4"/>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 ring-1 ring-white/10">
                                                    <x-heroicon-o-user class="h-5 w-5 text-white"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex justify-start">
                                        <div x-data="{ copied:false }" class="max-w-4xl">
                                            <div class="flex items-start gap-3">
                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 ring-1 ring-white/10">
                                                    <x-heroicon-o-cpu-chip class="h-5 w-5 text-white"/>
                                                </div>

                                                <div class="min-w-0 flex-1 space-y-2">
                                                    <div class="flex items-center gap-2">
                                                        <span class="rounded-full bg-emerald-500/10 px-3 py-1 text-[11px] font-medium tracking-wide text-emerald-300">
                                                            Assistant
                                                        </span>
                                                    </div>

                                                    <div class="rounded-[24px] rounded-tl-md border border-white/10 bg-[#111827] px-4 py-3 text-sm leading-7 text-slate-200 shadow-[0_10px_30px_rgba(0,0,0,0.22)] md:px-5">
                                                        {!! $message['content'] !!}
                                                    </div>

                                                    <div class="flex items-center gap-2">
                                                        <div class="flex items-center gap-1 rounded-full border border-white/10 bg-white/[0.03] p-1">
                                                            <button
                                                                x-on:click="
                                                                    navigator.clipboard.writeText(@js($message['content']));
                                                                    copied = true;
                                                                    setTimeout(() => copied = false, 1500);
                                                                "
                                                                class="inline-flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition hover:bg-white/5 hover:text-white">

                                                                <template x-if="!copied">
                                                                    <x-heroicon-o-document-duplicate class="h-4 w-4"/>
                                                                </template>

                                                                <template x-if="copied">
                                                                    <x-heroicon-o-check class="h-4 w-4 text-emerald-400"/>
                                                                </template>
                                                            </button>

                                                            @if($loop->last)
                                                                <button
                                                                    wire:click="regenerate"
                                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition hover:bg-white/5 hover:text-white">
                                                                    <x-heroicon-o-arrow-path class="h-4 w-4"/>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            @endforeach

                            <div id="chat-bottom" class="h-1"></div>
                        </div>
                    @else
                        <div class="flex h-full min-h-[420px] items-center justify-center">
                            <div class="mx-auto w-full max-w-3xl text-center">
                                <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-500/20 to-indigo-500/20 ring-1 ring-white/10">
                                    <x-heroicon-o-sparkles class="h-8 w-8 text-blue-400" />
                                </div>

                                <h2 class="text-2xl font-semibold tracking-tight text-white">
                                    Start a new chat
                                </h2>

                                <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-slate-400">
                                    Ask anything, attach files, or use one of the quick prompts below to begin your conversation.
                                </p>

                                <div class="mt-8 grid gap-3 text-left md:grid-cols-2">
                                    <button
                                        type="button"
                                        wire:click="useQuickPrompt('Summarize the uploaded files and give me key action points.')"
                                        class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 transition hover:border-blue-400/30 hover:bg-blue-500/10">
                                        <div class="text-sm font-semibold text-white">Summarize files</div>
                                        <div class="mt-1 text-xs leading-6 text-slate-400">Use uploaded files to produce a clean summary and next steps.</div>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="useQuickPrompt('Help me write production-ready code for this task.')"
                                        class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 transition hover:border-blue-400/30 hover:bg-blue-500/10">
                                        <div class="text-sm font-semibold text-white">Write code</div>
                                        <div class="mt-1 text-xs leading-6 text-slate-400">Generate implementation-focused code with practical structure.</div>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="useQuickPrompt('Review my approach and suggest improvements.')"
                                        class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 transition hover:border-blue-400/30 hover:bg-blue-500/10">
                                        <div class="text-sm font-semibold text-white">Review approach</div>
                                        <div class="mt-1 text-xs leading-6 text-slate-400">Get feedback, cleaner architecture, and better implementation choices.</div>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="useQuickPrompt('Create a step-by-step plan for this task.')"
                                        class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 transition hover:border-blue-400/30 hover:bg-blue-500/10">
                                        <div class="text-sm font-semibold text-white">Plan task</div>
                                        <div class="mt-1 text-xs leading-6 text-slate-400">Break work into clear ordered steps before execution.</div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Response Loading --}}
                <div
                    wire:loading.flex
                    wire:target="ask,regenerate"
                    class="mt-4 items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 ring-1 ring-white/10">
                        <x-heroicon-o-cpu-chip class="h-5 w-5 text-white"/>
                    </div>

                    <div class="rounded-[20px] border border-white/10 bg-[#111827] px-4 py-3 shadow-lg">
                        <div class="flex gap-1.5">
                            <div class="h-2 w-2 rounded-full bg-slate-400 animate-bounce"></div>
                            <div class="h-2 w-2 rounded-full bg-slate-400 animate-bounce [animation-delay:120ms]"></div>
                            <div class="h-2 w-2 rounded-full bg-slate-400 animate-bounce [animation-delay:240ms]"></div>
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <form wire:submit="ask" class="mt-4">
                    <div
                        class="rounded-[28px] border border-white/10 bg-[#0f172a] p-2 shadow-[0_20px_50px_rgba(0,0,0,0.35)]"
                        x-data="{ uploading: false, progress: 0 }"
                        x-on:livewire-upload-start="uploading = true"
                        x-on:livewire-upload-finish="uploading = false; progress = 0"
                        x-on:livewire-upload-error="uploading = false; progress = 0"
                        x-on:livewire-upload-cancel="uploading = false; progress = 0"
                        x-on:livewire-upload-progress="progress = $event.detail.progress"
                    >
                        <div wire:loading wire:target="attachments" class="mb-2">
                            <div class="inline-flex items-center gap-2 rounded-full border border-blue-500/20 bg-blue-500/10 px-3 py-1.5 text-xs font-medium text-blue-300">
                                <svg class="h-3.5 w-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"></circle>
                                    <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="3"></path>
                                </svg>
                                Uploading files...
                            </div>
                        </div>

                        <div x-show="uploading" x-cloak class="mb-3">
                            <div class="mb-1 flex items-center justify-between text-[11px] text-slate-400">
                                <span>Upload progress</span>
                                <span x-text="progress + '%'"></span>
                            </div>

                            <div class="h-2 overflow-hidden rounded-full bg-white/5">
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 transition-all duration-200"
                                    :style="`width: ${progress}%`"
                                ></div>
                            </div>
                        </div>

                        @if(!empty($attachments))
                            <div class="mb-3 rounded-[22px] border border-white/10 bg-[#111827] p-3">
                                <div class="mb-2 flex items-center gap-2">
                                    <x-heroicon-o-paper-clip class="h-4 w-4 text-blue-400" />
                                    <span class="text-xs font-medium tracking-wide text-slate-300">
                                        {{ count($attachments) }} file(s) ready to send
                                    </span>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @foreach($attachments as $fileIndex => $file)
                                        <div class="inline-flex max-w-full items-center gap-2 rounded-full border border-white/10 bg-white/[0.04] px-3 py-1.5 text-xs text-slate-200">
                                            <x-heroicon-o-document class="h-4 w-4 shrink-0 text-slate-400" />

                                            <span class="max-w-[180px] truncate">
                                                {{ $file->getClientOriginalName() }}
                                            </span>

                                            <button
                                                type="button"
                                                wire:click="removeAttachment({{ $fileIndex }})"
                                                class="inline-flex h-5 w-5 items-center justify-center rounded-full text-slate-400 transition hover:bg-rose-500/20 hover:text-rose-300"
                                                title="Remove file">
                                                <x-heroicon-m-x-mark class="h-3.5 w-3.5" />
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="flex items-end gap-2">
                            <label for="fileUpload" class="inline-flex h-12 w-12 cursor-pointer items-center justify-center rounded-2xl text-slate-400 transition hover:bg-white/5 hover:text-white">
                                <x-heroicon-o-paper-clip class="h-5 w-5" />
                            </label>

                            <input type="file" wire:model="attachments" multiple class="hidden" id="fileUpload">

                            <div class="flex-1 rounded-[22px] border border-white/10 bg-[#111827] px-3">
                                <textarea
                                    class="min-h-[52px] max-h-40 w-full resize-none bg-transparent py-3 text-sm leading-6 text-slate-200 placeholder:text-slate-500 focus:outline-none"
                                    rows="1"
                                    wire:model.defer="prompt"
                                    placeholder="Type your message..."></textarea>
                            </div>

                            <button
                                type="button"
                                class="inline-flex h-12 w-12 items-center justify-center rounded-2xl text-slate-400 transition hover:bg-white/5 hover:text-white">
                                <x-heroicon-o-microphone class="h-5 w-5" />
                            </button>

                            <button
                                type="submit"
                                class="inline-flex h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-600 px-5 text-sm font-semibold text-white shadow-[0_12px_30px_rgba(37,99,235,0.28)] transition hover:from-blue-400 hover:to-indigo-500">
                                Send
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<div
    x-data="{ showLeft: true, showRight: true }"
    class="min-h-screen bg-slate-950 text-slate-100 antialiased"
>
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,_rgba(59,130,246,0.18),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(168,85,247,0.12),_transparent_25%)]"></div>

    <nav class="sticky top-0 z-30 border-b border-white/10 bg-slate-950/80 backdrop-blur-xl">
        <div class="flex items-center justify-between px-4 py-3 lg:px-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-violet-600 shadow-lg shadow-blue-500/20">
                    <span class="text-sm font-bold">AI</span>
                </div>
                <div>
                    <h1 class="text-sm font-semibold tracking-wide text-white">Chat Application</h1>
                    <p class="text-xs text-slate-400">Private workspace • Fast • Clean</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button
                    @click="showLeft = !showLeft"
                    class="hidden rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-medium text-slate-200 transition hover:bg-white/10 lg:inline-flex"
                >
                    <span x-show="showLeft">Hide Left</span>
                    <span x-show="!showLeft">Show Left</span>
                </button>

                <button
                    @click="showRight = !showRight"
                    class="hidden rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-medium text-slate-200 transition hover:bg-white/10 lg:inline-flex"
                >
                    <span x-show="showRight">Hide Right</span>
                    <span x-show="!showRight">Show Right</span>
                </button>
            </div>
        </div>
    </nav>

    <div class="flex min-h-[calc(100vh-73px)]">
        <aside
            x-show="showLeft"
            x-transition
            class="hidden w-[280px] shrink-0 border-r border-white/10 bg-slate-950/70 p-4 backdrop-blur-xl lg:block"
        >
            <div class="rounded-3xl border border-white/10 bg-white/5 p-4 shadow-2xl shadow-black/20">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-white">Tools</h2>
                    <span class="rounded-full bg-blue-500/15 px-2 py-1 text-[10px] font-medium text-blue-300">Online</span>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="rounded-2xl border border-white/5 bg-white/5 px-3 py-3 text-slate-200 transition hover:bg-white/10">Chat History 1</div>
                    <div class="rounded-2xl border border-white/5 bg-white/5 px-3 py-3 text-slate-200 transition hover:bg-white/10">Chat History 2</div>
                    <div class="rounded-2xl border border-white/5 bg-white/5 px-3 py-3 text-slate-200 transition hover:bg-white/10">Chat History 3</div>
                </div>
            </div>
        </aside>

        <section class="flex min-w-0 flex-1 flex-col">
            <div class="flex-1 overflow-y-auto px-4 py-6 lg:px-8">
                <div class="mx-auto flex w-full max-w-4xl flex-col gap-4">
                    @foreach($messages as $msg)
                        @php($isUser = $msg['role'] === 'user')
                        @php($isAssistant = $msg['role'] === 'assistant')

                        <div class="flex {{ $isUser ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%] rounded-3xl border px-4 py-3 shadow-lg backdrop-blur-xl
                                {{ $isUser
                                    ? 'border-blue-500/20 bg-gradient-to-br from-blue-600 to-violet-600 text-white shadow-blue-500/10'
                                    : 'border-white/10 bg-white/5 text-slate-100 shadow-black/20' }}">
                                <div class="mb-1 flex items-center gap-2 text-[11px] font-medium uppercase tracking-wider {{ $isUser ? 'text-blue-100/80' : 'text-slate-400' }}">
                                    <span>{{ ucfirst($msg['role']) }}</span>
                                    <span class="h-1 w-1 rounded-full bg-current opacity-50"></span>
                                    <span>{{ $msg['created_at'] ?? now()->format('h:i A') }}</span>

                                    @if($isAssistant && !empty($msg['streaming']))
                                        <span class="ml-2 inline-flex items-center gap-1 text-emerald-300">
                                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-300"></span>
                                            typing
                                        </span>
                                    @endif
                                </div>

                                <div
                                    @if($isAssistant && !empty($msg['streaming']))
                                        wire:stream.replace="assistant-{{ $msg['id'] }}"
                                    @endif
                                    class="whitespace-pre-wrap text-sm leading-6"
                                >
                                    {{ $msg['content'] }}
                                </div>

                                @if (!empty($msg['attachments']))
                                    <div class="mt-3 grid gap-2 sm:grid-cols-2">
                                        @foreach($msg['attachments'] as $file)
                                            <div class="rounded-2xl border border-white/10 bg-black/20 px-3 py-2 text-xs text-slate-200">
                                                {{ $file->getClientOriginalName() }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-white/10 bg-slate-950/80 p-4 backdrop-blur-xl">
                <div class="mx-auto max-w-4xl rounded-3xl border border-white/10 bg-white/5 p-4 shadow-2xl shadow-black/20">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="text-xs text-slate-400">
                            @if($isStreaming)
                                Assistant is responding...
                            @else
                                Ready to chat
                            @endif
                        </div>
                        <div class="text-xs text-slate-500">Model: ChatAgent</div>
                    </div>

                    <textarea
                        wire:model.defer="prompt"
                        class="w-full resize-none rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/60 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                        rows="4"
                        placeholder="Type your message..."
                    ></textarea>

                    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-2">
                            <button
                                wire:click="send"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/20 transition hover:scale-[1.01] hover:from-blue-500 hover:to-violet-500 disabled:cursor-not-allowed disabled:opacity-70"
                            >
                                <span wire:loading.remove wire:target="send">Send</span>
                                <span wire:loading wire:target="send">Sending...</span>
                            </button>

                            <button
                                type="button"
                                class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-200 transition hover:bg-white/10"
                            >
                                🎤
                            </button>
                        </div>

                        <div class="flex-1 sm:max-w-md">
                            <input
                                type="file"
                                multiple
                                wire:model="attachments"
                                class="block w-full cursor-pointer rounded-2xl border border-dashed border-white/15 bg-white/5 px-3 py-2 text-xs text-slate-300 file:mr-4 file:rounded-xl file:border-0 file:bg-white/10 file:px-4 file:py-2 file:text-xs file:font-medium file:text-white hover:bg-white/8"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <aside
            x-show="showRight"
            x-transition
            class="hidden w-[320px] shrink-0 border-l border-white/10 bg-slate-950/70 p-4 backdrop-blur-xl xl:block"
        >
            <div class="rounded-3xl border border-white/10 bg-white/5 p-4 shadow-2xl shadow-black/20">
                <h2 class="text-sm font-semibold text-white">Right Sidebar</h2>
                <p class="mt-2 text-xs leading-5 text-slate-400">Use this space for context, prompt controls, model settings, attachments, or chat metadata.</p>

                <div class="mt-4 space-y-3">
                    <div class="rounded-2xl border border-white/10 bg-slate-950/50 p-3">
                        <div class="text-[11px] uppercase tracking-wider text-slate-500">Model</div>
                        <div class="mt-1 text-sm text-white">ChatAgent</div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-slate-950/50 p-3">
                        <div class="text-[11px] uppercase tracking-wider text-slate-500">Status</div>
                        <div class="mt-1 text-sm text-emerald-400">Ready</div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-slate-950/50 p-3">
                        <div class="text-[11px] uppercase tracking-wider text-slate-500">Streaming</div>
                        <div class="mt-1 text-sm text-white">{{ $isStreaming ? 'Active' : 'Idle' }}</div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

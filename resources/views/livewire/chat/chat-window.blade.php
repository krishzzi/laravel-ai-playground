<div class="flex flex-col h-full" x-data="chatStream(@js($session->uuid))" x-init="init()">

    {{-- Top bar --}}
    <header class="flex items-center justify-between px-5 py-3 border-b border-zinc-200 dark:border-zinc-800 bg-white/70 dark:bg-zinc-950/70 backdrop-blur">
        <div class="flex items-center gap-2 min-w-0">
            <h1 class="font-medium truncate">{{ $session->title }}</h1>
            @if($session->project)
                <span class="text-xs px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500">📁 {{ $session->project->name }}</span>
            @endif
        </div>
        <livewire:chat.model-picker :provider="$provider" :model="$model" :thinking-mode="$thinkingMode" />
    </header>

    {{-- Messages --}}
    <div class="flex-1 overflow-y-auto px-4 md:px-10 py-6 space-y-6" id="message-list">
        @forelse($messages as $message)
            <x-chat.message :message="$message" />
        @empty
            <div class="h-full flex flex-col items-center justify-center text-center text-zinc-400">
                <p class="text-xl font-medium mb-1">What are we building today?</p>
                <p class="text-sm">Pick a model, attach a file, or type <code>@</code> for tools and <code>#</code> for skills.</p>
            </div>
        @endforelse

        @if($isStreaming)
            <div id="streaming-bubble" class="flex gap-3 max-w-3xl">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-fuchsia-500 shrink-0"></div>
                <div class="prose dark:prose-invert prose-sm" id="stream-target"></div>
            </div>
        @endif
    </div>

    {{-- Composer --}}
    <div class="border-t border-zinc-200 dark:border-zinc-800 px-4 md:px-10 py-4 bg-white dark:bg-zinc-950">

        {{-- Pending attachments (client-ingested, nothing uploaded yet) --}}
        @if(count($pendingAttachments))
            <div class="flex flex-wrap gap-2 mb-2">
                @foreach($pendingAttachments as $i => $att)
                    <div class="flex items-center gap-1.5 text-xs bg-zinc-100 dark:bg-zinc-800 rounded-full pl-2 pr-1 py-1">
                        <span>📎 {{ $att['name'] }}</span>
                        <button wire:click="removeAttachment({{ $i }})" class="w-4 h-4 rounded-full hover:bg-zinc-300 dark:hover:bg-zinc-700">×</button>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="relative rounded-2xl border border-zinc-300 dark:border-zinc-700 focus-within:ring-2 focus-within:ring-indigo-500 bg-zinc-50 dark:bg-zinc-900">
            <textarea
                wire:model="draft"
                x-ref="composer"
                x-on:input="autoMention($event); autoGrow($event)"
                x-on:keydown.enter.prevent="submitOnEnter($event)"
                rows="1"
                placeholder="Message… use @ for tools, # for skills"
                class="w-full resize-none bg-transparent border-0 focus:ring-0 text-sm px-4 pt-3 pb-1 max-h-56"
            ></textarea>

            <div class="flex items-center justify-between px-3 pb-2 pt-1">
                <div class="flex items-center gap-1">
                    <label class="cursor-pointer p-2 rounded-lg hover:bg-zinc-200/70 dark:hover:bg-zinc-800" title="Attach file (processed in your browser)">
                        <input type="file" class="hidden" x-on:change="ingestFile($event)" multiple>
                        📎
                    </label>
                    <button type="button" x-on:click="startVoiceInput()" class="p-2 rounded-lg hover:bg-zinc-200/70 dark:hover:bg-zinc-800" title="Voice input">🎙️</button>
                    <button type="button" x-on:click="$dispatch('open-image-mode')" class="p-2 rounded-lg hover:bg-zinc-200/70 dark:hover:bg-zinc-800" title="Generate image">🎨</button>
                </div>

                <button wire:click="send" wire:loading.attr="disabled" :disabled="!$wire.draft.trim()"
                        class="rounded-xl bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 px-4 py-2 text-sm font-medium disabled:opacity-40">
                    Send
                </button>
            </div>
        </div>
        <p class="text-[11px] text-center text-zinc-400 mt-2">
            Files are read in your browser only — nothing is uploaded unless the model truly needs the raw bytes. · {{ $gemsBalance }} 💎 remaining
        </p>
    </div>
</div>

@script
<script>
    Alpine.data('chatStream', (sessionUuid) => ({
        init() {
            this.$wire.on('start-stream', ({ sessionId, messageId }) => this.stream(sessionId, messageId));
            this.scrollToBottom();
        },
        autoGrow(e) { e.target.style.height = 'auto'; e.target.style.height = e.target.scrollHeight + 'px'; },
        submitOnEnter(e) { if (!e.shiftKey) this.$wire.send(); },
        scrollToBottom() {
            const list = document.getElementById('message-list');
            if (list) list.scrollTop = list.scrollHeight;
        },
        async ingestFile(e) {
            for (const file of e.target.files) {
                // Client-side extraction only — see resources/js/file-ingest.js.
                // PDFs -> pdf.js text extraction, images -> base64 for vision,
                // docx -> mammoth.js, csv/txt -> read directly. Nothing is
                // POSTed anywhere at this stage.
                const ingested = await window.ingestFileInBrowser(file);
                this.$wire.dispatch('file-ingested', { file: ingested });
            }
        },
        startVoiceInput() {
            window.startBrowserSTT((partialText) => { this.$refs.composer.value = partialText; });
        },
        async stream(sessionId, messageId) {
            const target = document.getElementById('stream-target');
            const res = await fetch(`/chat/${sessionId}/stream`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                body: JSON.stringify({ message_id: messageId }),
            });

            const reader = res.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';

            while (true) {
                const { value, done } = await reader.read();
                if (done) break;
                buffer += decoder.decode(value, { stream: true });
                if (target) target.innerHTML = window.renderMarkdown(buffer);
                this.scrollToBottom();
            }

            this.$wire.call('onStreamComplete', { });
        },
    }));
</script>
@endscript
</div>

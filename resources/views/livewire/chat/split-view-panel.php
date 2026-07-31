<div class="flex flex-col h-full" x-data="{ content: @entangle('artifact.content') }">
    @if($artifact)
    <header class="flex items-center justify-between px-4 py-3 border-b border-zinc-200 dark:border-zinc-800">
        <div class="min-w-0">
            <p class="text-sm font-medium truncate">{{ $artifact->filename }}</p>
            <p class="text-xs text-zinc-400">{{ strtoupper($artifact->type) }} @if($artifact->language) · {{ $artifact->language }} @endif</p>
        </div>
        <div class="flex items-center gap-2">
            <button x-on:click="downloadArtifact()" class="text-xs px-3 py-1.5 rounded-lg bg-zinc-900 dark:bg-white text-white dark:text-zinc-900">Download</button>
            <button x-on:click="$dispatch('close-split-view')" class="text-xs px-2 py-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800">✕</button>
        </div>
    </header>

    <div class="flex-1 overflow-auto">
        @if($artifact->type === 'image')
        <img src="data:{{ $artifact->mime }};base64,{{ $artifact->content }}" class="w-full h-auto" />
        @else
        <textarea x-model="content" class="w-full h-full font-mono text-xs p-4 bg-zinc-950 text-zinc-100 resize-none border-0 focus:ring-0" spellcheck="false"></textarea>
        @endif
    </div>

    <footer class="px-4 py-2 border-t border-zinc-200 dark:border-zinc-800 text-xs text-zinc-400">
        Edits here don't touch chat history — use "Send as follow-up" to steer the conversation with your changes.
    </footer>
    @else
    <div class="flex-1 flex items-center justify-center text-sm text-zinc-400">Nothing open yet.</div>
    @endif
</div>

@script
<script>
    Alpine.data('splitView', () => ({
        async downloadArtifact() {
            const artifact = @js($artifact);
            if (!artifact) return;

            if (artifact.type === 'zip') {
                const zip = new window.JSZip();
                // `content` is expected to be a JSON map of {path: fileContent}
                // for multi-file zip artifacts.
                const files = JSON.parse(artifact.content);
                Object.entries(files).forEach(([path, body]) => zip.file(path, body));
                const blob = await zip.generateAsync({ type: 'blob' });
                this.triggerDownload(blob, artifact.filename);
                return;
            }

            const blob = artifact.type === 'image'
                ? await (await fetch(`data:${artifact.mime};base64,${artifact.content}`)).blob()
                : new Blob([this.content ?? artifact.content], { type: artifact.mime || 'text/plain' });

            this.triggerDownload(blob, artifact.filename);
        },
        triggerDownload(blob, filename) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
            URL.revokeObjectURL(url);
        },
    }));
</script>
@endscript

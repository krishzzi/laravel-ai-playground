<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'AI Chat' }}</title>

    {{--
    ─────────────────────────────────────────────────────────────
    CSS Dependencies (CDN – swap to Vite in production builds)
    ─────────────────────────────────────────────────────────────
    • Tailwind CSS v3    – utility classes
    • highlight.js       – server-rendered <pre><code> blocks get
                           client-side syntax colouring after load
    --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Inter"', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', '"Fira Code"', 'monospace'],
                    },
                    colors: {
                        // Brand accent – used for CTAs, active states, streaming glow
                        brand: {
                            50:  '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>

    {{-- Inter + JetBrains Mono from Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    {{-- highlight.js (code syntax highlighting) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js" defer></script>

    {{-- marked.js (client-side markdown → HTML for streaming preview) --}}
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js" defer></script>

    {{-- DOMPurify (sanitise marked HTML output to prevent XSS) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.1.5/purify.min.js" defer></script>

    {{-- Livewire styles --}}
    @livewireStyles

    <style>
        /* ── Scrollbar styling ────────────────────────────────── */
        ::-webkit-scrollbar          { width: 6px; }
        ::-webkit-scrollbar-track    { background: transparent; }
        ::-webkit-scrollbar-thumb    { background: #374151; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #4b5563; }

        /* ── Prose overrides for AI message rendering ─────────── */
        .ai-prose { line-height: 1.7; color: #e5e7eb; }
        .ai-prose h1,.ai-prose h2,.ai-prose h3 { color:#f9fafb; font-weight:600; margin:1.2em 0 .5em; }
        .ai-prose h1 { font-size:1.5rem; }
        .ai-prose h2 { font-size:1.25rem; }
        .ai-prose h3 { font-size:1.1rem; }
        .ai-prose p  { margin: .6em 0; }
        .ai-prose ul,.ai-prose ol { padding-left:1.5rem; margin:.5em 0; }
        .ai-prose li { margin:.25em 0; }
        .ai-prose a  { color:#60a5fa; text-decoration:underline; }
        .ai-prose blockquote { border-left:3px solid #4b5563; padding-left:1rem; color:#9ca3af; margin:1em 0; }
        .ai-prose hr { border-color:#374151; margin:1.5em 0; }
        .ai-prose table { width:100%; border-collapse:collapse; margin:1em 0; font-size:.875rem; }
        .ai-prose th { background:#1f2937; color:#f9fafb; padding:.5rem .75rem; text-align:left; border:1px solid #374151; }
        .ai-prose td { padding:.5rem .75rem; border:1px solid #374151; }
        .ai-prose tr:nth-child(even) { background:#111827; }

        /* ── Code blocks ─────────────────────────────────────── */
        .ai-prose pre  { position:relative; background:#0d1117!important; border:1px solid #30363d;
            border-radius:.5rem; margin:1em 0; overflow:hidden; }
        .ai-prose pre code { display:block; overflow-x:auto; padding:1rem; font-family:'JetBrains Mono',monospace;
            font-size:.82rem; line-height:1.6; background:transparent!important; }
        .ai-prose code:not(pre code) { background:#1f2937; color:#f472b6; padding:.15em .4em;
            border-radius:.25rem; font-family:'JetBrains Mono',monospace;
            font-size:.85em; }
        /* Copy-code button injected by Alpine.js */
        .copy-code-btn { position:absolute; top:.5rem; right:.5rem; opacity:0; transition:opacity .15s;
            background:#374151; color:#d1d5db; border:none; border-radius:.3rem;
            padding:.2rem .45rem; font-size:.7rem; cursor:pointer; }
        .ai-prose pre:hover .copy-code-btn { opacity:1; }
        .copy-code-btn:hover { background:#4b5563; }

        /* ── Streaming glow on the active AI bubble ───────────── */
        @keyframes stream-pulse { 0%,100%{box-shadow:0 0 0 0 rgba(59,130,246,.15)} 50%{box-shadow:0 0 0 6px rgba(59,130,246,.0)} }
        .streaming-bubble { animation: stream-pulse 1.8s ease-in-out infinite; }

        /* ── Typing dots ──────────────────────────────────────── */
        @keyframes dot-bounce { 0%,80%,100%{transform:scale(0)} 40%{transform:scale(1)} }
        .dot-bounce span { display:inline-block; width:6px; height:6px; border-radius:50%;
            background:#6b7280; margin:0 1px;
            animation:dot-bounce 1.2s infinite ease-in-out both; }
        .dot-bounce span:nth-child(1){animation-delay:-.32s}
        .dot-bounce span:nth-child(2){animation-delay:-.16s}

        /* ── Textarea auto-resize baseline ───────────────────── */
        .auto-textarea { resize:none; overflow-y:hidden; min-height:44px; max-height:220px; }

        /* ── Mobile sidebar slide ─────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar-mobile { position:fixed; z-index:50; inset-y:0; left:0; width:80vw; max-width:320px; }
        }
    </style>
</head>

<body class="bg-gray-950 text-gray-100 font-sans antialiased overflow-hidden">

{{-- ── Page slot ─────────────────────────────────────────── --}}
{{ $slot }}

{{-- ── Global toast notifications ──────────────────────── --}}
<div
    x-data="toastManager()"
    @notify.window="addToast($event.detail)"
    class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 pointer-events-none"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-end="opacity-0 translate-y-2"
            :class="{
                    'bg-green-800 border-green-700': toast.type === 'success',
                    'bg-red-900  border-red-700':   toast.type === 'error',
                    'bg-gray-800 border-gray-700':  toast.type === 'info' || !toast.type,
                }"
            class="flex items-center gap-2 px-4 py-2.5 rounded-lg border text-sm text-white shadow-xl pointer-events-auto"
        >
            <span x-text="toast.message"></span>
            <button @click="removeToast(toast.id)" class="ml-2 text-gray-400 hover:text-white">✕</button>
        </div>
    </template>
</div>

{{-- ── Scripts ───────────────────────────────────────────── --}}
@livewireScripts

<script>
    // ─────────────────────────────────────────────────────────────
    // Alpine.js global components
    // Registered via document.addEventListener('alpine:init') so
    // they are available before Alpine boots.
    // ─────────────────────────────────────────────────────────────
    document.addEventListener('alpine:init', () => {

        // ── Toast notification manager ─────────────────────────
        Alpine.data('toastManager', () => ({
            toasts: [],
            addToast({ message, type = 'info' }) {
                const id = Date.now();
                this.toasts.push({ id, message, type, visible: true });
                setTimeout(() => this.removeToast(id), 4000);
            },
            removeToast(id) {
                const t = this.toasts.find(t => t.id === id);
                if (t) { t.visible = false; setTimeout(() => this.toasts = this.toasts.filter(x => x.id !== id), 200); }
            }
        }));

        // ── Markdown message renderer ──────────────────────────
        // Used by completed AI messages (not the live stream).
        // Injects HTML rendered by marked.js + sanitised by DOMPurify.
        Alpine.data('markdownMessage', (rawContent) => ({
            init() {
                this.$nextTick(() => {
                    if (typeof marked === 'undefined') {
                        this.$el.textContent = rawContent;
                        return;
                    }

                    marked.setOptions({ breaks: true, gfm: true });

                    const html = typeof DOMPurify !== 'undefined'
                        ? DOMPurify.sanitize(marked.parse(rawContent || ''))
                        : marked.parse(rawContent || '');

                    this.$el.innerHTML = html;
                    this.$el.classList.add('ai-prose');

                    // Syntax-highlight all <pre><code> blocks
                    if (typeof hljs !== 'undefined') {
                        this.$el.querySelectorAll('pre code').forEach(b => {
                            hljs.highlightElement(b);
                            this._addCopyButton(b.parentElement);
                        });
                    }
                });
            },

            // Inject a copy button into a <pre> element
            _addCopyButton(pre) {
                if (!pre || pre.querySelector('.copy-code-btn')) return;
                const btn = document.createElement('button');
                btn.className = 'copy-code-btn';
                btn.textContent = 'Copy';
                btn.addEventListener('click', () => {
                    const code = pre.querySelector('code')?.textContent ?? '';
                    navigator.clipboard.writeText(code).then(() => {
                        btn.textContent = 'Copied!';
                        setTimeout(() => btn.textContent = 'Copy', 2000);
                    });
                });
                pre.appendChild(btn);
            }
        }));

        // ── Audio recorder (microphone input) ─────────────────
        Alpine.data('audioRecorder', () => ({
            recording: false,
            mediaRecorder: null,
            chunks: [],

            async startRecording() {
                if (!navigator.mediaDevices?.getUserMedia) {
                    alert('Microphone not supported in this browser.'); return;
                }
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.mediaRecorder = new MediaRecorder(stream);
                this.chunks = [];
                this.mediaRecorder.ondataavailable = e => this.chunks.push(e.data);
                this.mediaRecorder.onstop = () => this._sendAudio(stream);
                this.mediaRecorder.start();
                this.recording = true;
            },

            stopRecording() {
                this.mediaRecorder?.stop();
                this.recording = false;
            },

            toggleRecording() {
                this.recording ? this.stopRecording() : this.startRecording();
            },

            _sendAudio(stream) {
                // Stop all tracks to release the mic indicator
                stream.getTracks().forEach(t => t.stop());
                const blob = new Blob(this.chunks, { type: 'audio/webm' });
                const reader = new FileReader();
                reader.onloadend = () => {
                    const b64 = reader.result.split(',')[1];
                    // Dispatch to Livewire component via #[On('transcribe-audio')]
                    this.$dispatch('transcribe-audio', { data: b64 });
                };
                reader.readAsDataURL(blob);
            }
        }));

        // ── Auto-expanding textarea ────────────────────────────
        Alpine.data('autoTextarea', () => ({
            resize(el) {
                el.style.height = 'auto';
                el.style.height = Math.min(el.scrollHeight, 220) + 'px';
            }
        }));

    }); // end alpine:init

    // ── Initialise hljs after page load ───────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof hljs !== 'undefined') hljs.configure({ ignoreUnescapedHTML: true });
    });
</script>
</body>
</html>

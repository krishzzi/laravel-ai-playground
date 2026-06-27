<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Premium Chat</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dompurify@3.1.6/dist/purify.min.js"></script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: Inter, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(124,58,237,.18), transparent 28%),
                radial-gradient(circle at top right, rgba(34,211,238,.12), transparent 22%),
                linear-gradient(180deg, #020617 0%, #0f172a 100%);
            color: #e2e8f0;
        }
        .glass {
            background: rgba(15, 23, 42, .72);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.08);
        }
        .scrollbar::-webkit-scrollbar { width: 10px; }
        .scrollbar::-webkit-scrollbar-thumb { background: rgba(148,163,184,.28); border-radius: 999px; }
        .scrollbar { scrollbar-width: thin; scrollbar-color: rgba(148,163,184,.28) transparent; }
        .bubble { white-space: pre-wrap; word-break: break-word; }
        .typing span {
            display: inline-block;
            width: 7px;
            height: 7px;
            margin-right: 4px;
            border-radius: 999px;
            background: #94a3b8;
            animation: bounce 1.2s infinite ease-in-out;
        }
        .typing span:nth-child(2) { animation-delay: .16s; }
        .typing span:nth-child(3) { animation-delay: .32s; }
        @keyframes bounce {
            0%,80%,100% { transform: translateY(0); opacity: .35; }
            40% { transform: translateY(-4px); opacity: 1; }
        }
    </style>
</head>
<body>
<div x-data="chatUI()" x-init="init()" class="min-h-screen p-3 sm:p-4 lg:p-6">
    <div class="mx-auto flex min-h-[calc(100vh-1.5rem)] max-w-6xl overflow-hidden rounded-3xl border border-white/10 bg-slate-950/70 shadow-2xl">
        <main class="flex min-w-0 flex-1 flex-col">
            <header class="glass flex items-center justify-between gap-3 border-b border-white/10 px-4 py-4 sm:px-6">
                <div>
                    <h1 class="text-lg font-semibold text-white sm:text-xl">Premium Chat</h1>
                    <p class="text-sm text-slate-400">Fast, clean, and no page reload</p>
                </div>
                <button type="button" id="clearBtn" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm hover:bg-white/10">
                    <i class="fa-solid fa-broom mr-2"></i>Clear
                </button>
            </header>

            <div id="chatArea" class="scrollbar flex-1 overflow-y-auto px-3 py-4 sm:px-4 lg:px-6">
                <div id="messages" class="mx-auto flex max-w-4xl flex-col gap-4">
                    <div class="flex items-end gap-3">
                        <div class="grid h-10 w-10 flex-none place-items-center rounded-2xl border border-white/10 bg-white/5 text-cyan-300">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <div class="max-w-[92%] sm:max-w-[80%]">
                            <div class="bubble rounded-3xl rounded-bl-md border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-100">
                                Hello! Send a message to start chatting.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-white/10 bg-slate-950/90 p-3 sm:p-4 lg:p-5">
                <div class="mx-auto max-w-4xl">
                    <div class="glass rounded-3xl p-3 sm:p-4">
                        <div class="mb-3 flex flex-wrap items-center gap-2">
                            <button type="button" id="uploadBtn" class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-sm hover:bg-slate-800">
                                <i class="fa-solid fa-paperclip mr-2 text-cyan-400"></i>Upload
                            </button>
                            <button type="button" id="micBtn" class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-sm hover:bg-slate-800">
                                <i class="fa-solid fa-microphone mr-2 text-violet-400"></i>Mic
                            </button>
                            <div id="preview" class="flex flex-wrap gap-2"></div>
                        </div>

                        <form id="chatForm" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                            <textarea id="message" rows="1" placeholder="Type your message..."
                                      class="min-h-[58px] flex-1 resize-none rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-4 text-sm text-white outline-none placeholder:text-slate-500 focus:border-cyan-400/40"></textarea>

                            <button id="sendBtn" type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-violet-600 to-cyan-500 px-5 py-4 text-sm font-semibold text-white hover:opacity-95 disabled:cursor-not-allowed disabled:opacity-60 sm:min-w-[120px]">
                                <span>Send</span>
                                <span id="spinner" class="hidden"><i class="fa-solid fa-spinner fa-spin"></i></span>
                            </button>
                        </form>

                        <div class="mt-3 flex items-center justify-between text-xs text-slate-400">
                            <span id="status">Ready</span>
                            <span>Enter to send · Shift+Enter for newline</span>
                        </div>

                        <input id="fileInput" type="file" class="hidden" multiple>
                        <input id="audioInput" type="file" class="hidden" accept="audio/*" capture="microphone">
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    function chatUI() {
        return {
            files: [],
            audioFile: null,
            init() {
                this.$chatArea = document.getElementById('chatArea');
                this.$messages = document.getElementById('messages');
                this.$form = document.getElementById('chatForm');
                this.$message = document.getElementById('message');
                this.$sendBtn = document.getElementById('sendBtn');
                this.$spinner = document.getElementById('spinner');
                this.$status = document.getElementById('status');
                this.$clearBtn = document.getElementById('clearBtn');
                this.$uploadBtn = document.getElementById('uploadBtn');
                this.$micBtn = document.getElementById('micBtn');
                this.$fileInput = document.getElementById('fileInput');
                this.$audioInput = document.getElementById('audioInput');
                this.$preview = document.getElementById('preview');
                this.csrf = document.querySelector('meta[name="csrf-token"]').content;

                this.$form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.send();
                });

                this.$clearBtn.addEventListener('click', () => this.clear());
                this.$uploadBtn.addEventListener('click', () => this.$fileInput.click());
                this.$micBtn.addEventListener('click', () => this.$audioInput.click());

                this.$fileInput.addEventListener('change', e => {
                    this.files = [...this.files, ...Array.from(e.target.files || [])].slice(0, 5);
                    e.target.value = '';
                    this.renderPreview();
                });

                this.$audioInput.addEventListener('change', e => {
                    this.audioFile = (e.target.files || [])[0] || null;
                    e.target.value = '';
                    this.renderPreview();
                });

                this.$message.addEventListener('keydown', e => {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        this.$form.requestSubmit();
                    }
                });

                this.$message.addEventListener('input', () => {
                    this.$message.style.height = 'auto';
                    this.$message.style.height = Math.min(this.$message.scrollHeight, 160) + 'px';
                });

                this.scrollBottom();
                this.$message.focus();
            },

            scrollBottom() {
                this.$chatArea.scrollTop = this.$chatArea.scrollHeight;
            },

            escapeHtml(s) {
                return (s || '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
            },

            renderPreview() {
                this.$preview.innerHTML = '';
                this.files.forEach((file, index) => {
                    const chip = document.createElement('span');
                    chip.className = 'inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-cyan-400/10 px-3 py-2 text-xs text-cyan-100';
                    chip.innerHTML = `<i class="fa-solid fa-file"></i><span>${this.escapeHtml(file.name)}</span><button type="button">×</button>`;
                    chip.querySelector('button').addEventListener('click', () => {
                        this.files.splice(index, 1);
                        this.renderPreview();
                    });
                    this.$preview.appendChild(chip);
                });

                if (this.audioFile) {
                    const chip = document.createElement('span');
                    chip.className = 'inline-flex items-center gap-2 rounded-full border border-violet-400/20 bg-violet-400/10 px-3 py-2 text-xs text-violet-100';
                    chip.innerHTML = `<i class="fa-solid fa-microphone"></i><span>${this.escapeHtml(this.audioFile.name)}</span><button type="button">×</button>`;
                    chip.querySelector('button').addEventListener('click', () => {
                        this.audioFile = null;
                        this.renderPreview();
                    });
                    this.$preview.appendChild(chip);
                }
            },

            addBubble(role, text) {
                const row = document.createElement('div');
                row.className = `flex items-end gap-3 ${role === 'user' ? 'justify-end' : 'justify-start'}`;
                if (role === 'assistant') {
                    row.innerHTML = `
                    <div class="grid h-10 w-10 flex-none place-items-center rounded-2xl border border-white/10 bg-white/5 text-cyan-300">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <div class="max-w-[92%] sm:max-w-[80%]">
                        <div class="bubble rounded-3xl rounded-bl-md border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-100">${DOMPurify.sanitize(marked.parse(text || ''))}</div>
                    </div>`;
                } else {
                    row.innerHTML = `
                    <div class="max-w-[92%] sm:max-w-[80%]">
                        <div class="bubble rounded-3xl rounded-br-md border border-cyan-400/20 bg-gradient-to-r from-violet-600 to-cyan-500 px-4 py-3 text-sm text-white">${this.escapeHtml(text || '')}</div>
                    </div>
                    <div class="grid h-10 w-10 flex-none place-items-center rounded-2xl bg-gradient-to-br from-violet-600 to-cyan-500 text-white">
                        <i class="fa-solid fa-user"></i>
                    </div>`;
                }
                this.$messages.appendChild(row);
                this.scrollBottom();
            },

            setLoading(on) {
                this.$sendBtn.disabled = on;
                this.$message.disabled = on;
                this.$spinner.classList.toggle('hidden', !on);
                this.$status.textContent = on ? 'Sending...' : 'Ready';
            },

            async send() {
                const text = this.$message.value.trim();
                if (!text && !this.files.length && !this.audioFile) return;

                this.addBubble('user', text || 'Voice / file message');
                this.$message.value = '';
                this.$message.style.height = 'auto';
                this.setLoading(true);

                const fd = new FormData();
                fd.append('_token', this.csrf);
                fd.append('message', text);
                this.files.forEach(file => fd.append('attachments[]', file));
                if (this.audioFile) fd.append('audio', this.audioFile);

                try {
                    const res = await fetch('{{ route('chat.inference') }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: fd
                    });

                    const data = await res.json().catch(() => ({}));

                    if (!res.ok) {
                        this.addBubble('assistant', data.message || 'Request failed.');
                        return;
                    }

                    const reply = data.response ?? data.message ?? data.answer ?? '';
                    this.addBubble('assistant', reply || 'No response received.');
                    this.files = [];
                    this.audioFile = null;
                    this.renderPreview();
                } catch (e) {
                    this.addBubble('assistant', 'Something went wrong while sending the message.');
                } finally {
                    this.setLoading(false);
                    this.$message.focus();
                }
            },

            clear() {
                this.$messages.innerHTML = '';
                this.addBubble('assistant', 'Conversation cleared. How can I help you?');
            }
        }
    }
</script>
</body>
</html>

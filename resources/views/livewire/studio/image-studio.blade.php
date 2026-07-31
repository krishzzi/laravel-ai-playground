<div>
    <h1 class="text-2xl font-semibold mb-1">Image Studio</h1>
    <p class="text-sm text-zinc-500 mb-6">Text-to-image and image-to-image, across every image provider you've enabled.</p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Controls --}}
        <div class="lg:col-span-1 space-y-4">
            <div>
                <label class="text-xs text-zinc-500">Prompt</label>
                <textarea wire:model="prompt" rows="4" placeholder="A neon-lit cyberpunk alley in the rain, cinematic…"
                          class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 text-sm"></textarea>
                @error('prompt') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-xs text-zinc-500">Provider / model</label>
                <select wire:model="provider" class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 text-sm mb-2">
                    @foreach($models as $providerKey => $providerModels)
                        <option value="{{ $providerKey }}">{{ config("ai_models.providers.$providerKey.label") }}</option>
                    @endforeach
                </select>
                <select wire:model="model" class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 text-sm">
                    @foreach(($models[$provider] ?? []) as $m)
                        <option value="{{ $m['id'] }}">{{ $m['label'] }} · 💎{{ $m['gems_each'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs text-zinc-500">Aspect</label>
                <div class="flex gap-1.5">
                    @foreach(['square' => '1:1', 'portrait' => '3:4', 'landscape' => '16:9'] as $key => $label)
                        <button wire:click="$set('aspect', '{{ $key }}')"
                                class="text-xs px-3 py-1.5 rounded-lg border {{ $aspect === $key ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10' : 'border-zinc-200 dark:border-zinc-700' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="text-xs text-zinc-500">Reference images (for image-to-image)</label>
                <input type="file" wire:model="references" multiple accept="image/*"
                       class="w-full text-xs file:mr-2 file:rounded-lg file:border-0 file:bg-zinc-100 dark:file:bg-zinc-800 file:px-3 file:py-1.5" />
            </div>

            <button wire:click="generate" wire:loading.attr="disabled"
                    class="w-full rounded-xl bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 py-2.5 text-sm font-medium disabled:opacity-50">
                <span wire:loading.remove>Generate</span>
                <span wire:loading>Generating…</span>
            </button>
            @error('gems') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        {{-- Gallery --}}
        <div class="lg:col-span-2 grid grid-cols-2 md:grid-cols-3 gap-3">
            @foreach(array_reverse($gallery) as $i => $img)
                <div class="rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-800 group relative">
                    <img src="data:image/png;base64,{{ $img['base64'] }}" class="w-full aspect-square object-cover" />
                    <div class="absolute inset-x-0 bottom-0 bg-black/60 opacity-0 group-hover:opacity-100 transition p-2 text-[10px] text-white flex justify-between">
                        <span>💎{{ $img['gems_charged'] }}</span>
                        <button wire:click="useAsReference({{ $i }})">Use as reference</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

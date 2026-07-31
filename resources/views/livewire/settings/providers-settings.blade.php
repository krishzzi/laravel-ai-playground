<div class="max-w-2xl mx-auto py-10 px-4">
    <h1 class="text-xl font-semibold mb-1">Model providers</h1>
    <p class="text-sm text-zinc-500 mb-6">Bring your own API keys, or point at a self-hosted server (RunPod, AWS GPU box, vLLM). Platform-managed providers work out of the box with no key needed — you're billed in gems instead.</p>

    <div class="space-y-3 mb-6">
        @foreach($credentials as $cred)
            <div class="flex items-center justify-between rounded-xl border border-zinc-200 dark:border-zinc-800 px-4 py-3">
                <div>
                    <p class="text-sm font-medium">{{ $providerOptions[$cred->provider]['label'] ?? $cred->provider }} @if($cred->label) <span class="text-zinc-400">· {{ $cred->label }}</span> @endif</p>
                    <p class="text-xs text-zinc-400">{{ $cred->base_url ?? 'Default endpoint' }}</p>
                </div>
                <button wire:click="remove({{ $cred->id }})" class="text-xs text-red-500 hover:underline">Remove</button>
            </div>
        @endforeach
    </div>

    <button wire:click="$set('showForm', true)" class="text-sm px-4 py-2 rounded-xl bg-zinc-900 dark:bg-white text-white dark:text-zinc-900">+ Add provider / custom server</button>

    @if($showForm)
        <div class="mt-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 space-y-3">
            <div>
                <label class="text-xs text-zinc-500">Provider</label>
                <select wire:model="provider" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-sm">
                    @foreach($providerOptions as $key => $opt)
                        <option value="{{ $key }}">{{ $opt['label'] }}</option>
                    @endforeach
                </select>
            </div>

            @if($provider === 'custom')
                <div>
                    <label class="text-xs text-zinc-500">Server label</label>
                    <input wire:model="label" placeholder="e.g. RunPod A100" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-sm" />
                </div>
                <div>
                    <label class="text-xs text-zinc-500">Base URL</label>
                    <input wire:model="baseUrl" placeholder="https://xxxx.runpod.net/v1" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-sm" />
                </div>
            @endif

            <div>
                <label class="text-xs text-zinc-500">API key</label>
                <input wire:model="apiKey" type="password" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-sm" />
            </div>

            <div class="flex justify-end gap-2">
                <button wire:click="$set('showForm', false)" class="text-xs px-3 py-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800">Cancel</button>
                <button wire:click="save" class="text-xs px-3 py-1.5 rounded-lg bg-indigo-600 text-white">Save</button>
            </div>
        </div>
    @endif
</div>

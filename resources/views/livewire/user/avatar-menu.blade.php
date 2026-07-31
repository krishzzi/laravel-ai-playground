<div class="relative" x-data="{ open: @entangle('open') }">
    <button x-on:click="open = !open" class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-rose-500 flex items-center justify-center text-white text-xs font-bold ring-2 ring-white dark:ring-zinc-900">
        {{ strtoupper(substr($user->name, 0, 1)) }}
    </button>

    <div x-show="open" x-on:click.outside="open = false" x-transition
         class="absolute bottom-10 left-0 w-64 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xl z-50 p-2"
         style="display:none;">

        <div class="px-2 py-2">
            <p class="text-sm font-medium truncate">{{ $user->name }}</p>
            <p class="text-xs text-zinc-400 truncate">{{ $user->email }}</p>
        </div>

        <div class="px-2 py-1.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 text-xs mb-1">
            <div class="flex justify-between"><span>Plan</span><span class="font-medium">{{ $subscription->plan_name ?? 'Free' }}</span></div>
            <div class="flex justify-between text-zinc-400"><span>Gems</span><span>💎 {{ number_format($wallet->balance) }}</span></div>
        </div>

        <a href="{{ route('settings.providers') }}" class="block px-2 py-1.5 rounded-lg text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800">⚙️ Settings</a>
        <a href="{{ route('billing.gems') }}" class="block px-2 py-1.5 rounded-lg text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800">💎 Buy gems</a>
        <button x-on:click="$wire.showAccountModal = true; open = false" class="w-full text-left px-2 py-1.5 rounded-lg text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800">👤 Manage account</button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full text-left px-2 py-1.5 rounded-lg text-sm hover:bg-red-50 dark:hover:bg-red-500/10 text-red-600">Log out</button>
        </form>
    </div>

    @if($showAccountModal)
        <x-modal wire:model="showAccountModal" title="Account & Subscription">
            <div class="space-y-4 text-sm">
                <div>
                    <label class="text-xs text-zinc-500">Name</label>
                    <input value="{{ $user->name }}" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800" />
                </div>
                <div>
                    <label class="text-xs text-zinc-500">Email</label>
                    <input value="{{ $user->email }}" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800" />
                </div>
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 p-3">
                    <p class="font-medium">{{ $subscription->plan_name ?? 'Free plan' }}</p>
                    @if($subscription)
                        <p class="text-xs text-zinc-400">
                            {{ $subscription->is_valid ? 'Active' : 'Expired' }} · renews {{ $subscription->expires_on?->diffForHumans() }}
                        </p>
                    @endif
                    <a href="{{ route('billing.gems') }}" class="inline-block mt-2 text-xs px-3 py-1.5 rounded-lg bg-indigo-600 text-white">Manage plan</a>
                </div>
            </div>
        </x-modal>
    @endif
</div>

<?php

namespace App\Livewire\User;

use App\Services\GemBillingService;
use Livewire\Component;

class AvatarMenu extends Component
{
    public bool $open = false;

    public bool $showAccountModal = false;

    public function render(GemBillingService $billing)
    {
        $user = auth()->user();

        return view('livewire.user.avatar-menu', [
            'user' => $user,
            'wallet' => $billing->walletFor($user),
            'subscription' => $user->subscription ?? null,
        ]);
    }
}

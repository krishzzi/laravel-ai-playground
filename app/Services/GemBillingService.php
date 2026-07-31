<?php

namespace App\Services;

use App\Models\GemTransaction;
use App\Models\GemWallet;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * All gem debits/credits go through here. Never mutate `gem_wallets.balance`
 * directly anywhere else in the app — this keeps balance changes atomic and
 * auditable via the gem_transactions ledger.
 */
class GemBillingService
{
    public function walletFor(User $user): GemWallet
    {
        return GemWallet::firstOrCreate(['user_id' => $user->id]);
    }

    public function balance(User $user): int
    {
        return $this->walletFor($user)->balance;
    }

    public function canAfford(User $user, int $estimatedGems): bool
    {
        return $this->balance($user) >= $estimatedGems;
    }

    /**
     * Estimate the gem cost of a text generation call before it's sent, so
     * the UI can pre-flight-check affordability and warn the user.
     */
    public function estimateTextCost(array $modelMeta, int $estimatedInputTokens, int $estimatedOutputTokens, string $thinkingMode): int
    {
        $multiplier = config("ai_models.thinking_modes.{$thinkingMode}.gems_multiplier", 1.0);

        $cost = ($estimatedInputTokens / 1000 * $modelMeta['gems_in'])
            + ($estimatedOutputTokens / 1000 * $modelMeta['gems_out']);

        return (int) ceil($cost * $multiplier);
    }

    /**
     * Debit gems for a completed request. Uses a row lock so concurrent
     * requests from the same user (e.g. two browser tabs) can't race past a
     * zero balance.
     */
    public function debit(User $user, int $amount, string $reason, ?int $chatMessageId = null, array $meta = []): GemTransaction
    {
        return DB::transaction(function () use ($user, $amount, $reason, $chatMessageId, $meta) {
            $wallet = GemWallet::where('user_id', $user->id)->lockForUpdate()->firstOrCreate(['user_id' => $user->id]);

            $newBalance = max(0, $wallet->balance - $amount);

            $wallet->update([
                'balance' => $newBalance,
                'lifetime_spent' => $wallet->lifetime_spent + $amount,
            ]);

            return GemTransaction::create([
                'user_id' => $user->id,
                'chat_message_id' => $chatMessageId,
                'type' => 'debit',
                'amount' => -$amount,
                'balance_after' => $newBalance,
                'reason' => $reason,
                'meta' => $meta,
            ]);
        });
    }

    public function credit(User $user, int $amount, string $type, string $reason, array $meta = []): GemTransaction
    {
        return DB::transaction(function () use ($user, $amount, $type, $reason, $meta) {
            $wallet = GemWallet::where('user_id', $user->id)->lockForUpdate()->firstOrCreate(['user_id' => $user->id]);

            $newBalance = $wallet->balance + $amount;

            $wallet->update([
                'balance' => $newBalance,
                'lifetime_purchased' => $type === 'purchase' ? $wallet->lifetime_purchased + $amount : $wallet->lifetime_purchased,
            ]);

            return GemTransaction::create([
                'user_id' => $user->id,
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reason' => $reason,
                'meta' => $meta,
            ]);
        });
    }

    /**
     * Grant the monthly gem allowance for a subscription plan. Called from a
     * scheduled job on the user's renewal date (see App\Console\Commands).
     */
    public function grantMonthlyAllowance(User $user, int $gems, string $planSlug): GemTransaction
    {
        return $this->credit($user, $gems, 'subscription_grant', "plan:{$planSlug}");
    }
}

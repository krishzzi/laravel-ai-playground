<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One wallet per user. Kept separate from `users` so it can be locked
        // with SELECT ... FOR UPDATE during a debit without locking the whole
        // user row.
        Schema::create('gem_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->unsignedBigInteger('balance')->default(0);
            $table->unsignedBigInteger('lifetime_purchased')->default(0);
            $table->unsignedBigInteger('lifetime_spent')->default(0);
            $table->timestamps();
        });

        Schema::create('gem_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chat_message_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // purchase | subscription_grant | debit | refund | bonus
            $table->bigInteger('amount'); // positive = credit, negative = debit
            $table->unsignedBigInteger('balance_after');
            $table->string('reason')->nullable(); // "text:anthropic/claude-sonnet-5" etc.
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // Subscription plans (paired with a `subscriptions` table you likely
        // already have via Cashier — this is a thin platform-specific layer
        // that maps a plan to a monthly gem grant + rate limits).
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->unsignedInteger('monthly_gems');
            $table->unsignedInteger('max_context_tokens')->default(200000);
            $table->json('allowed_providers')->nullable(); // null = all
            $table->boolean('allow_byo_key')->default(true);
            $table->timestamps();
        });

        // Per-user, per-provider encrypted API credentials (BYO key support).
        Schema::create('user_provider_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider'); // matches Lab enum value or 'custom'
            $table->string('label')->nullable(); // for multiple custom servers
            $table->text('api_key_encrypted')->nullable();
            $table->string('base_url')->nullable(); // for openai-compatible / custom / ollama
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'provider', 'label']);
        });

        // Structured user preferences the AI can read (with consent) to
        // personalize responses — e.g. tone, language, default thinking mode.
        Schema::create('user_ai_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('preferred_language')->default('en');
            $table->string('response_tone')->default('balanced'); // concise | balanced | detailed
            $table->string('default_thinking_mode')->default('balanced');
            $table->boolean('allow_profile_context')->default(true); // let AI use name/plan/etc.
            $table->json('custom_instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_ai_preferences');
        Schema::dropIfExists('user_provider_credentials');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('gem_transactions');
        Schema::dropIfExists('gem_wallets');
    }
};

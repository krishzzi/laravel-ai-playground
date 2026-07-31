<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First-party + user-created "Tools" — the @tool_name gallery.
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete(); // null = built-in / global
            $table->string('slug')->unique(); // used for @slug mention parsing
            $table->string('name');
            $table->string('icon')->nullable();
            $table->text('description');
            $table->text('prompt_template')->nullable(); // injected instructions when invoked
            $table->string('kind')->default('prompt'); // prompt | mcp | provider_tool | webhook
            $table->foreignId('mcp_server_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider_tool_class')->nullable(); // e.g. Laravel\Ai\Providers\Tools\WebSearch
            $table->json('config')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Skills — persona/technique packs applied like tools (#skill_name).
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description');
            $table->longText('instructions'); // system-prompt fragment merged into the agent
            $table->json('linked_tool_ids')->nullable(); // skills can auto-attach tools
            $table->boolean('is_public')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Remote MCP servers a user (or the platform) has registered — first
        // party, third-party, or the user's own project MCP endpoint.
        Schema::create('mcp_servers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('transport'); // web | local
            $table->string('url')->nullable();
            $table->string('command')->nullable();
            $table->json('args')->nullable();
            $table->string('auth_type')->default('none'); // none | bearer | oauth
            $table->text('token_encrypted')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot for tools/skills actually attached to a specific message so we
        // can show "used @web_search, #deep_research" chips under a reply.
        Schema::create('chat_message_augmentations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_message_id')->constrained()->cascadeOnDelete();
            $table->string('augmentable_type'); // App\Models\Tool | App\Models\Skill
            $table->unsignedBigInteger('augmentable_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_message_augmentations');
        Schema::dropIfExists('mcp_servers');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('tools');
    }
};

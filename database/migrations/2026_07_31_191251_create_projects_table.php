<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Folders that group chat sessions, like Claude's "Projects".
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color', 20)->nullable();
            $table->text('instructions')->nullable(); // project-level system prompt
            $table->json('default_tool_ids')->nullable();
            $table->json('default_skill_ids')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->default('New Chat');
            $table->string('mode')->default('chat'); // chat | image | video | voice
            $table->string('default_provider')->nullable();
            $table->string('default_model')->nullable();
            $table->string('thinking_mode')->default('balanced');
            $table->boolean('pinned')->default(false);
            $table->boolean('archived')->default(false);
            $table->json('meta')->nullable(); // ephemeral UI state, split-view file refs, etc.
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'archived', 'last_activity_at']);
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('chat_session_id')->constrained()->cascadeOnDelete();
            $table->string('role'); // user | assistant | system | tool
            $table->longText('content')->nullable();
            $table->json('attachments')->nullable(); // [{type, name, mime, url|blob_ref, extracted_text_ref}]
            $table->json('tool_calls')->nullable();
            $table->json('citations')->nullable();
            $table->string('provider')->nullable();
            $table->string('model')->nullable();
            $table->string('thinking_mode')->nullable();
            $table->longText('thinking_trace')->nullable(); // stored, UI-collapsible
            $table->unsignedInteger('input_tokens')->nullable();
            $table->unsignedInteger('output_tokens')->nullable();
            $table->unsignedInteger('gems_charged')->nullable();
            $table->boolean('edited')->default(false);
            $table->foreignId('branched_from_message_id')->nullable(); // supports "edit + regenerate a new branch"
            $table->timestamps();

            $table->index(['chat_session_id', 'created_at']);
        });

        // Every generated artifact (code file, zip, image, audio, video) so the
        // split-view panel and download endpoint can serve it without
        // re-uploading anything the browser already generated.
        Schema::create('chat_artifacts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('chat_message_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // code | zip | image | audio | video | document
            $table->string('filename');
            $table->string('mime')->nullable();
            $table->string('language')->nullable(); // for code artifacts
            $table->longText('content')->nullable(); // inline text/code content
            $table->string('disk_path')->nullable(); // set only if it must be persisted server-side
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_artifacts');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
        Schema::dropIfExists('projects');
    }
};

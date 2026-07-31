<x-app-layout>
    <div class="flex h-screen">
        <livewire:chat.sidebar />
        <main class="flex-1 overflow-y-auto p-6 md:p-10 max-w-3xl">
            <h1 class="text-2xl font-semibold mb-1">Video Studio</h1>
            <p class="text-sm text-zinc-500 mb-2">Text-to-video generation.</p>
            <div class="text-xs rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 px-3 py-2 mb-6">
                Video isn't part of the core Laravel AI SDK's provider table — this studio is wired as a plug-in HTTP integration point.
                Pick a vendor (Runway, Luma, Kling, Pika…), add its key to <code>config/services.php</code>, and complete
                <code>VideoStudio::generate()</code>.
            </div>
            <livewire:studio.video-studio />
        </main>
    </div>
</x-app-layout>

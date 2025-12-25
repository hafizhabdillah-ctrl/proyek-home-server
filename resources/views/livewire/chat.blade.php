<div class="flex h-screen bg-zinc-900 text-white overflow-hidden">

    <aside class="w-64 bg-zinc-950 flex flex-col border-r border-zinc-800">
        <div class="p-4">
            <button wire:click="newChat" class="w-full flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-3 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                <span class="font-medium">New Chat</span>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-2 space-y-2">
            <div class="text-xs font-medium text-zinc-500 px-2 py-2">Today</div>
            <button class="w-full text-left px-3 py-2 text-sm text-zinc-300 hover:bg-zinc-800 rounded-lg truncate">
                Belajar Laravel Livewire
            </button>
            <button class="w-full text-left px-3 py-2 text-sm text-zinc-300 hover:bg-zinc-800 rounded-lg truncate">
                Resep Nasi Goreng
            </button>
        </div>

        <div class="p-4 border-t border-zinc-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center font-bold">
                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="text-sm font-medium">
                    {{ auth()->user()->name ?? 'Guest' }}
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col relative">

        <div class="p-4 border-b border-zinc-800 flex items-center justify-between lg:hidden">
            <span>Llama3:Latest</span>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-6 scroll-smooth" id="chat-container">

            @if(count($messages) == 0)
                <div class="h-full flex flex-col items-center justify-center text-center text-zinc-500">
                    <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <h2 class="text-xl font-semibold text-white mb-2">How can I help you today?</h2>
                </div>
            @else
                @foreach($messages as $msg)
                    <div class="flex gap-4 {{ $msg['role'] === 'user' ? 'flex-row-reverse' : '' }}">
                        <div class="w-8 h-8 rounded-sm flex-shrink-0 flex items-center justify-center {{ $msg['role'] === 'assistant' ? 'bg-emerald-600' : 'bg-zinc-600' }}">
                            @if($msg['role'] === 'assistant')
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M6 12c0 .667-.083 1.167-.25 1.5H5c-.667 0-1.167-.5-1.5-1.5a1.5 1.5 0 0 0-3 0c-.333.333-1 .333-1 0S-.333 11 0 11h6zm9-3a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-9 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
                            @else
                                <span class="font-bold text-xs">U</span>
                            @endif
                        </div>

                        <div class="max-w-2xl px-4 py-2 rounded-2xl text-sm leading-relaxed {{ $msg['role'] === 'user' ? 'bg-zinc-700 text-white' : 'text-zinc-100' }}">
                            {{ $msg['content'] }}
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="p-4 w-full max-w-3xl mx-auto">
            <form wire:submit.prevent="sendMessage" class="relative">
                <input
                    wire:model="userMessage"
                    type="text"
                    placeholder="Message Llama..."
                    class="w-full bg-zinc-800 text-white placeholder-zinc-400 border border-zinc-700 rounded-xl px-4 py-3 pr-12 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-lg"
                    autofocus
                >
                <button type="submit" class="absolute right-2 top-2 p-1.5 bg-emerald-600 hover:bg-emerald-700 rounded-lg text-white transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </form>
            <div class="text-center mt-2">
                <span class="text-xs text-zinc-500">AI can make mistakes. Please verify important information.</span>
            </div>
        </div>

    </main>
</div>

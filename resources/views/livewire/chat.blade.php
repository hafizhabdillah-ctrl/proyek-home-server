<div class="flex h-screen bg-zinc-900 text-white overflow-hidden">

    {{-- sidebar sebelah kiri --}}
    <aside class="w-64 bg-zinc-950 flex flex-col border-r border-zinc-800">
        <div class="p-4">
            <button wire:click="newChat" class="w-full flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-3 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                <span class="font-medium">New Chat</span>
            </button>
        </div>

        {{-- riwayat chat --}}
        <div class="flex-1 overflow-y-auto px-2 space-y-2">
            <div class="text-xs font-medium text-zinc-500 px-2 py-2">Today</div>
            <button class="w-full text-left px-3 py-2 text-sm text-zinc-300 hover:bg-zinc-800 rounded-lg truncate">
                Learn Livewire
            </button>
            <button class="w-full text-left px-3 py-2 text-sm text-zinc-300 hover:bg-zinc-800 rounded-lg truncate">
                How to make Friend Rice
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-2 space-y-2">
        </div>

        {{-- tombol settings --}}
        <div class="px-2 pt-2 mb-2">
            <button class="w-full flex items-center gap-3 px-3 py-2 text-sm text-zinc-300 hover:bg-zinc-800 hover:text-white rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.47a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <span>Settings</span>
            </button>
        </div>

        {{-- username --}}
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

        {{-- tulisan ollama atas --}}
        <div class="p-4 border-b border-zinc-800 flex items-center justify-between">
            <span>Ollama</span>
        </div>

        {{-- display chat --}}
        <div class="flex-1 overflow-y-auto scroll-smooth" id="chat-container">
            <div class="max-w-4xl mx-auto w-full p-6 space-y-6">
                @if(count($messages) == 0)
                    <div class="min-h-[60vh] flex flex-col items-center justify-center text-center text-zinc-500">
                        <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-white mb-2">How can I help you today?</h2>
                    </div>
                @else
                    @foreach($messages as $key => $msg)
                        <div class="group/row flex gap-4 {{ $msg['role'] === 'user' ? 'flex-row-reverse' : '' }}">

                            {{-- AVATAR --}}
                            <div class="w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center {{ $msg['role'] === 'assistant' ? 'bg-emerald-600 shadow-lg shadow-emerald-900/20' : 'bg-zinc-600' }}">
                                @if($msg['role'] === 'assistant')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M6 12c0 .667-.083 1.167-.25 1.5H5c-.667 0-1.167-.5-1.5-1.5a1.5 1.5 0 0 0-3 0c-.333.333-1 .333-1 0S-.333 11 0 11h6zm9-3a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-9 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                    </svg>
                                @else
                                    <span class="font-bold text-xs">U</span>
                                @endif
                            </div>

                            {{-- BUBBLE TEXT --}}
                            <div id="msg-{{ $key }}" class="max-w-[85%] px-4 py-2.5 rounded-2xl text-base leading-relaxed {{ $msg['role'] === 'user' ? 'bg-zinc-800 text-white border border-zinc-700/50' : 'text-zinc-200' }}">
                                {{ $msg['content'] }}
                            </div>

                            {{-- 3. ACTION BUTTONS --}}
                            <div class="flex items-start pt-1 gap-1 opacity-0 group-hover/row:opacity-100 transition-opacity duration-200">

                                {{-- Tombol Copy --}}
                                <div x-data="{ copied: false }" class="relative group/btn">
                                    <button
                                        @click="navigator.clipboard.writeText(document.getElementById('msg-{{ $key }}').innerText); copied = true; setTimeout(() => copied = false, 2000);"
                                        class="p-1.5 text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-md transition">

                                        {{-- Ikon Copy (Muncul saat belum di-copy) --}}
                                        <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                        </svg>

                                        {{-- Ikon Checklist (Muncul saat sudah di-copy) --}}
                                        <svg x-show="copied" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </button>

                                    {{-- Tooltip Copy --}}
                                    <div class="absolute top-full mt-2 left-1/2 -translate-x-1/2 hidden group-hover/btn:block bg-gray-200 text-zinc-900 text-xs py-1 px-2 rounded shadow-lg whitespace-nowrap z-50 font-medium">
                                        <span x-text="copied ? 'Copied!' : 'Copy prompt'"></span>
                                    </div>
                                </div>

                                {{-- Tombol Edit --}}
                                @if($msg['role'] === 'user')
                                    <div class="relative group/btn">
                                        <button
                                            type="button"
                                            wire:click="$set('userMessage', '{{ addslashes($msg['content']) }}')"
                                            class="p-1.5 text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-md transition"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 20h9"></path>
                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                            </svg>
                                        </button>

                                        <div class="absolute top-full mt-2 left-1/2 -translate-x-1/2 hidden group-hover/btn:block bg-gray-200 text-zinc-900 text-xs py-1 px-2 rounded shadow-lg whitespace-nowrap z-50 font-medium">
                                            Edit prompt
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <script>
            function copyToClipboard(elementId) {
                const text = document.getElementById(elementId).innerText;
                navigator.clipboard.writeText(text).then(() => {
                    console.log('Text copied to clipboard');
                });
            }
        </script>
        {{-- input chat --}}
        <div class="p-6 w-full max-w-4xl mx-auto">
            <form wire:submit.prevent="sendMessage" class="relative">
                <input
                    wire:model="userMessage"
                    type="text"
                    placeholder="Message Llama..."
                    class="w-full bg-zinc-800 text-white placeholder-zinc-400 border border-zinc-700 rounded-xl px-6 py-4 pr-12 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-lg"
                    autofocus
                >
                <button type="submit"
                    class="absolute rounded-2xl right-2 top-2 p-3 bg-emerald-600 hover:bg-emerald-700 text-white transition disabled:opacity-50 disabled:cursor-not-allowed active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </form>
            <div class="text-center mt-3">
                <span class="text-xs text-zinc-500">AI can make mistakes. Please verify important information.</span>
            </div>
        </div>

    </main>
</div>

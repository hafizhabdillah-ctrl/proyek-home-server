<div class="flex h-screen text-white overflow-hidden relative z-0" style="background-color: #18181b;">

    {{-- sidebar sebelah kiri --}}
    <aside class="h-screen w-64 bg-zinc-950 flex flex-col border-r border-zinc-800">

        <div class="p-4">
            <button
                type="button" wire:click="newChat"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded mb-4 transition">
                + New Chat
            </button>
        </div>

        {{-- Chat History --}}
        <div
            x-data="{ activeMenuId: null }"
            class="flex-1 overflow-y-auto px-2 space-y-1 mt-2
            [&::-webkit-scrollbar]:w-2
            [&::-webkit-scrollbar-track]:bg-transparent
            [&::-webkit-scrollbar-thumb]:bg-zinc-700
            [&::-webkit-scrollbar-thumb]:rounded-full"
        >
            {{-- PINNED CHATS --}}
            @if(count($this->pinnedChats) > 0)
                <div class="text-xs font-medium text-zinc-500 px-2 py-2 mb-1 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="17" x2="12" y2="22"></line><path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"></path></svg>
                    Pinned
                </div>

                @foreach($this->pinnedChats as $history)
                    {{-- ITEM CHAT (COPY 1 UNTUK PINNED) --}}
                    <div class="group relative flex items-center rounded-lg hover:bg-zinc-800 transition-colors {{ $chatId === $history->id ? 'bg-zinc-800' : '' }}">
                        <button
                            wire:click="loadChat({{ $history->id }})"
                            class="flex-1 text-left px-3 py-2 text-sm truncate w-full relative z-0 focus:outline-none {{ $chatId === $history->id ? 'text-white' : 'text-zinc-400 group-hover:text-zinc-200' }}"
                        >
                            <svg class="inline-block w-3 h-3 mr-1 text-emerald-500 rotate-45" fill="currentColor" viewBox="0 0 24 24"><path d="M16,12V4H17V2H7V4H8V12L6,14V16H11.2V22H12.8V16H18V14L16,12Z" /></svg>
                            {{ $history->title ?? 'New Chat' }}
                        </button>

                        <div class="absolute right-1 z-10">
                            <button
                                @click="activeMenuId = (activeMenuId === {{ $history->id }} ? null : {{ $history->id }})"
                                class="p-1 rounded-md transition-opacity duration-200 focus:outline-none"
                                :class="{
                                    'opacity-100 bg-zinc-700 text-white': activeMenuId === {{ $history->id }},
                                    'opacity-0 group-hover:opacity-100 text-zinc-400 hover:text-white hover:bg-zinc-700': activeMenuId === null,
                                    'opacity-0': activeMenuId !== null && activeMenuId !== {{ $history->id }}
                                }"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                            </button>

                            <div
                                x-show="activeMenuId === {{ $history->id }}"
                                @click.outside="activeMenuId = null"
                                x-transition.origin.top.right
                                class="absolute right-0 top-full mt-1 w-32 bg-zinc-900 border border-zinc-700 rounded-lg shadow-xl z-50 overflow-hidden"
                                style="display: none;"
                            >
                                <div class="flex flex-col py-1">
                                    <button wire:click="togglePin({{ $history->id }})" @click="activeMenuId = null" class="text-left px-3 py-2 text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white flex items-center gap-2 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="17" x2="12" y2="22"></line><path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"></path></svg>
                                        Unpin
                                    </button>
                                    <button wire:click="confirmRename({{ $history->id }}, '{{ $history->title }}')" @click="activeMenuId = null" class="text-left px-3 py-2 text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                        Rename
                                    </button>
                                    <button wire:click="confirmDelete({{ $history->id }})" @click="activeMenuId = null" class="text-left px-3 py-2 text-xs text-red-400 hover:bg-red-900/30 hover:text-red-300 flex items-center gap-2 border-t border-zinc-800 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="border-b border-zinc-800 my-2 mx-2"></div>
            @endif


            {{-- Unpinned --}}
            @if(count($this->histories) > 0)
                <div class="text-xs font-medium text-zinc-500 px-2 py-2 mb-1">History</div>

                @foreach($this->histories as $history)
                    {{-- ITEM CHAT (COPY 2 UNTUK HISTORY BIASA) --}}
                    <div class="group relative flex items-center rounded-lg hover:bg-zinc-800 transition-colors {{ $chatId === $history->id ? 'bg-zinc-800' : '' }}">
                        <button
                            wire:click="loadChat({{ $history->id }})"
                            class="flex-1 text-left px-3 py-2 text-sm truncate w-full relative z-0 focus:outline-none {{ $chatId === $history->id ? 'text-white' : 'text-zinc-400 group-hover:text-zinc-200' }}"
                        >
                            {{-- Tidak ada icon pin disini --}}
                            {{ $history->title ?? 'New Chat' }}
                        </button>

                        <div class="absolute right-1 z-10">
                            <button
                                @click="activeMenuId = (activeMenuId === {{ $history->id }} ? null : {{ $history->id }})"
                                class="p-1 rounded-md transition-opacity duration-200 focus:outline-none"
                                :class="{
                                    'opacity-100 bg-zinc-700 text-white': activeMenuId === {{ $history->id }},
                                    'opacity-0 group-hover:opacity-100 text-zinc-400 hover:text-white hover:bg-zinc-700': activeMenuId === null,
                                    'opacity-0': activeMenuId !== null && activeMenuId !== {{ $history->id }}
                                }"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                            </button>

                            <div
                                x-show="activeMenuId === {{ $history->id }}"
                                @click.outside="activeMenuId = null"
                                x-transition.origin.top.right
                                class="absolute right-0 top-full mt-1 w-32 bg-zinc-900 border border-zinc-700 rounded-lg shadow-xl z-50 overflow-hidden"
                                style="display: none;"
                            >
                                <div class="flex flex-col py-1">
                                    <button wire:click="togglePin({{ $history->id }})" @click="activeMenuId = null" class="text-left px-3 py-2 text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white flex items-center gap-2 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="17" x2="12" y2="22"></line><path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"></path></svg>
                                        Pin
                                    </button>
                                    <button wire:click="confirmRename({{ $history->id }}, '{{ $history->title }}')" @click="activeMenuId = null" class="text-left px-3 py-2 text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                        Rename
                                    </button>
                                    <button wire:click="confirmDelete({{ $history->id }})" @click="activeMenuId = null" class="text-left px-3 py-2 text-xs text-red-400 hover:bg-red-900/30 hover:text-red-300 flex items-center gap-2 border-t border-zinc-800 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            @if(count($this->pinnedChats) == 0 && count($this->histories) == 0)
                <div class="text-xs text-zinc-600 px-4 mt-4 text-center">
                    Belum ada riwayat chat.
                </div>
            @endif
        </div>

        {{-- Settings Button --}}
        <div class="px-2 pt-2 mb-2 mt-auto">
            <button class="w-full flex items-center gap-3 px-3 py-2 text-sm text-zinc-300 hover:bg-zinc-800 hover:text-white rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.47a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <span>Settings</span>
            </button>
        </div>

        {{-- username --}}
        <div class="p-4 border-t border-zinc-800 relative" x-data="{ open: false }">
            {{-- Dropdown Menu --}}
            <div
                x-show="open"
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="absolute bottom-full left-0 w-full px-4 mb-2 z-50"
                style="display: none;"
            >
                <div class="bg-zinc-800 border border-zinc-700 rounded-xl shadow-xl overflow-hidden">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-zinc-300 hover:bg-zinc-700 hover:text-white transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            <span>Log out</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Username Dropdown Trigger --}}
            <button
                @click="open = !open"
                class="w-full flex items-center gap-3 hover:bg-zinc-800 p-2 rounded-lg transition text-left group"
            >
                <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center font-bold text-white shadow-sm">
                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-white truncate group-hover:text-emerald-400 transition-colors">
                        {{ auth()->user()->name ?? 'Guest' }}
                    </div>
                </div>
            </button>
        </div>
    </aside>

    <main class="flex-1 flex flex-col relative">

        {{-- tulisan ollama atas --}}
        <div class="p-4 border-b border-zinc-800 flex items-center justify-between">
            <span>Ollama</span>
        </div>

        {{-- Display Chat --}}
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

                            {{-- Bubble Text --}}
                            <div id="msg-{{ $key }}" class="max-w-[85%] px-4 py-2.5 rounded-2xl text-base leading-relaxed {{ $msg['role'] === 'user' ? 'bg-zinc-800 text-white border border-zinc-700/50' : 'text-zinc-200' }}">
                                {{ $msg['content'] }}
                            </div>

                            {{-- Action Button --}}
                            <div class="flex items-start pt-1 gap-1 opacity-0 group-hover/row:opacity-100 transition-opacity duration-200">
                                {{-- Tombol Copy --}}
                                <div x-data="{ copied: false }" class="relative group/btn">
                                    <button
                                        @click="navigator.clipboard.writeText(document.getElementById('msg-{{ $key }}').innerText); copied = true; setTimeout(() => copied = false, 2000);"
                                        class="p-1.5 text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-md transition">

                                        <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                        </svg>

                                        <svg x-show="copied" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </button>
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
                    <div wire:loading wire:target="generateAiResponse" class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center bg-emerald-600/50 animate-pulse">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="animate-spin">
                                <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
                                <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/>
                            </svg>
                        </div>

                        {{-- Teks Loading --}}
                        <div class="flex items-center gap-2 text-zinc-400 mt-1 animate-pulse">
                            <span class="text-sm font-medium">Deciphering the Noise...</span>
                        </div>
                    </div>
            </div>
        </div>

        {{-- Input Chat --}}
        <div class="p-6 w-full max-w-4xl mx-auto">
            <form wire:submit.prevent="sendMessage" class="relative">
                <input
                    wire:model="userMessage"
                    type="text"
                    placeholder="Message Ollama..."
                    class="w-full bg-zinc-800 text-white placeholder-zinc-400 border border-zinc-700 rounded-xl px-6 py-4 pr-12 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-lg"
                    autofocus
                    wire:loading.attr="disabled"
                    wire:target="generateAiResponse"
                >
                <button type="submit"
                        class="absolute rounded-2xl right-2 top-2 p-3 bg-emerald-600 hover:bg-emerald-700 text-white transition disabled:opacity-50 disabled:cursor-not-allowed active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </form>
            <div class="text-center mt-3">
                <span class="text-xs text-zinc-500">AI can make mistakes. Please verify important information.</span>
            </div>

            {{-- Skenario Delete Chat --}}
            @if($confirmingDeletion)
            <div
                class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6 sm:px-0"
                wire:keydown.escape="$set('confirmingDeletion', false)"
            >
                {{-- Backdrop Gelap --}}
                <div
                    wire:click="$set('confirmingDeletion', false)"
                    class="fixed inset-0 transform transition-all bg-black/80 backdrop-blur-sm"
                ></div>

                {{-- Confirmation Box --}}
                <div class="relative bg-zinc-900 rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full border border-zinc-700">
                    <div class="bg-zinc-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-white">Delete Chat?</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-zinc-400">
                                        Are you sure you want to delete this chat? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-zinc-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-zinc-800 gap-2">
                        {{-- Confirm Delete --}}
                        <button wire:click="deleteChat" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm items-center">
                            <svg wire:loading wire:target="deleteChat" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Delete
                        </button>
                        <button wire:click="$set('confirmingDeletion', false)" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-zinc-600 shadow-sm px-4 py-2 bg-zinc-800 text-base font-medium text-zinc-300 hover:text-white hover:bg-zinc-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- Skenario Rename Chat --}}
            @if($isRenaming)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm"
                     x-data @keydown.escape.window="$wire.cancelRename()">

                    <div class="bg-[#1a1a1a] border border-gray-700 w-full max-w-md rounded-xl shadow-2xl overflow-hidden transform transition-all"
                         @click.away="$wire.cancelRename()">

                        <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-white">Rename this chat</h3>
                            <button wire:click="cancelRename" class="text-gray-400 hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-6">
                            <form wire:submit.prevent="updateTitle">
                                <label class="block text-sm text-gray-400 mb-2">Chat Name</label>

                                <input type="text"
                                       wire:model="newName"
                                       autofocus
                                       class="w-full bg-[#0a0a0a] border border-gray-600 rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition"
                                       placeholder="Enter new name...">

                                @error('newName') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror

                                <div class="flex justify-end gap-3 mt-6">
                                    <button type="button"
                                            wire:click="cancelRename"
                                            class="px-4 py-2 rounded-lg text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                        Cancel
                                    </button>

                                    <button type="submit"
                                            class="px-4 py-2 rounded-lg text-sm bg-green-600 hover:bg-green-500 text-white font-medium shadow-lg transition">
                                        Rename
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('start-generating-reply', () => {
                const chatContainer = document.getElementById('chat-container');
                setTimeout(() => {
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }, 100);

                @this.generateAiResponse();
            });
        });
        document.addEventListener('livewire:updated', () => {
            const chatContainer = document.getElementById('chat-container');
            if(chatContainer.scrollTop + chatContainer.clientHeight >= chatContainer.scrollHeight - 200) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        });
    </script>
</div>

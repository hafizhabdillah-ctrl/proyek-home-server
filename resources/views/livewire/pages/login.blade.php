<div class="fixed inset-0 min-h-screen w-full flex items-center justify-center bg-gradient-to-tr from-emerald-300 via-green-400 to-teal-500 p-6 z-[9999]">
    <div class="w-full max-w-md flex flex-col items-center">

        {{-- Logo --}}
        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-2xl mb-12 border-4 border-white/50 relative">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-emerald-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15a2.25 2.25 0 0 0 2.25-2.25V9.574c0-1.067-.75-1.994-1.802-2.169a48.324 48.324 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
            </svg>
        </div>

        <div class="w-full space-y-6">

            {{-- Input Email Addrss --}}
            <div class="relative">
                <input wire:model="email" type="email" placeholder="Email Address"
                       class="w-full bg-white/20 border-white/30 rounded-full py-4 px-12 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 backdrop-blur-md transition outline-none">
            </div>

            {{-- Input Password --}}
            <div class="relative">
                <input wire:model="password" type="password" placeholder="Password"
                       class="w-full bg-white/20 border-white/30 rounded-full py-4 px-12 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 backdrop-blur-md transition outline-none">
            </div>

            {{-- Button Login --}}
            <button wire:click="login"
                    class="w-full bg-white text-emerald-700 font-bold py-4 rounded-full shadow-lg hover:bg-emerald-50 transition transform active:scale-95 uppercase tracking-widest">
                LOGIN
            </button>

            {{-- Button Register --}}
            <button wire:click="register"
                    class="w-full bg-white text-emerald-700 font-bold py-4 rounded-full shadow-lg hover:bg-emerald-50 transition transform active:scale-95 uppercase tracking-widest">
                REGISTER
            </button>
        </div>
    </div>
</div>

{{-- KONTAINER UTAMA: Latar belakang putih penuh --}}
<div class="min-h-screen w-full flex items-center justify-center bg-gray-50 p-4">

    {{-- KARTU LOGIN: Kotak gradien hijau di tengah --}}
    <div class="w-full max-w-lg bg-gradient-to-tr from-emerald-300 via-green-400 to-teal-500 p-8 rounded-3xl shadow-2xl flex flex-col items-center relative overflow-hidden">

        {{-- Hiasan latar belakang (opsional, agar lebih dinamis) --}}
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-xl mb-8 border-4 border-white/50 relative z-10">
            <img src="{{ asset('images/logo.png') }}" class="w-20 h-20 object-contain" alt="Logo">
        </div>

        <div class="w-full space-y-5 relative z-10">

            {{-- Name --}}
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-white z-10 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </span>
                <input wire:model="name" type="text" placeholder="Name"
                       class="w-full bg-black/10 border-none rounded-full py-4 pl-12 pr-4 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 backdrop-blur-sm shadow-inner transition">
                @error('email')
                <span class="text-white text-xs mt-1 block ml-4">{{ $message }}</span>
                @enderror
            </div>

            {{-- Bagian Email Address --}}
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-white z-10 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0-9 6.75-9-6.75" />
                    </svg>
                </span>
                <input wire:model="email" type="email" placeholder="Email Address"
                       class="w-full bg-black/10 border-none rounded-full py-4 pl-12 pr-4 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 backdrop-blur-sm shadow-inner transition">
                @error('email')
                <span class="text-white text-xs mt-1 block ml-4">{{ $message }}</span>
                @enderror
            </div>

            {{-- Bagian Password --}}
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-white z-10 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0V10.5m12.75 0v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V10.5m12.75 0h-15" />
                    </svg>
                </span>
                <input wire:model="password" type="password" placeholder="Password" class="w-full bg-black/10 border-none rounded-full py-4 pl-12 pr-4 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 backdrop-blur-sm shadow-inner transition">
                @error('email')
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            {{-- Bagian Konfirmasi Password --}}
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-white z-10 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0V10.5m12.75 0v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V10.5m12.75 0h-15" />
                    </svg>
                </span>
                <input wire:model="confirmpassword" type="password" placeholder="Confirm Password" class="w-full bg-black/10 border-none rounded-full py-4 pl-12 pr-4 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 backdrop-blur-sm shadow-inner transition">
                @error('email')
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            {{-- Already have an account --}}
            <div class="flex items-center justify-between text-white text-sm px-2 py-1">
                <a href="{{ route('login') }}" wire:navigate class="font-bold hover:underline">
                    Already have an account?
                </a>
            </div>

            {{-- REGISTER --}}
            <button wire:click="register" wire:loading.attr="disabled"
                    class="w-full bg-white text-emerald-700 font-bold py-4 rounded-full shadow-lg hover:bg-emerald-50 hover:shadow-xl transition transform active:scale-[0.98] uppercase tracking-widest text-lg mt-4 disabled:opacity-70">
                <span wire:loading.remove>REGISTER</span>
                <span wire:loading>
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-emerald-700 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    LOADING...
                </span>
            </button>
        </div>
    </div>
</div>

<?php

namespace App\Livewire;

use Livewire\Component;

class Chat extends Component
{
    // Variabel untuk menampung teks yang diketik user
    public $userMessage = '';

    // Array untuk menyimpan riwayat chat (Pesan User & Pesan Bot)
    public $messages = [];

    // Fungsi: Dijalankan saat tombol "New Chat" ditekan
    public function newChat()
    {
        $this->messages = []; // Kosongkan array pesan
        $this->userMessage = ''; // Kosongkan input
    }

    // Fungsi: Dijalankan saat kirim pesan
    public function sendMessage()
    {
        // 1. Validasi: Jangan kirim jika kosong
        if (trim($this->userMessage) === '') {
            return;
        }

        // 2. Simpan pesan User ke array
        $this->messages[] = [
            'role' => 'user',
            'content' => $this->userMessage
        ];

        // 3. (SEMENTARA) Buat balasan bot otomatis (Dummy)
        // Nanti di sini kita ganti dengan koneksi ke Ollama
        $this->messages[] = [
            'role' => 'assistant',
            'content' => 'Halo! Saya menerima pesan Anda: "' . $this->userMessage . '". Saat ini saya belum terhubung ke otak AI, tapi fitur chat sudah jalan!'
        ];

        // 4. Kosongkan input setelah kirim
        $this->userMessage = '';
    }

    public function render()
    {
        return view('livewire.chat');
    }
}

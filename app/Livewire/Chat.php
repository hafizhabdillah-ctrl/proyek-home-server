<?php

namespace App\Livewire;

use Livewire\Component;

class Chat extends Component
{
    // Variabel untuk menampung teks yang diketik user
    public $userMessage = '';

    // Array untuk menyimpan riwayat chat (Pesan User & Pesan Bot)
    public $messages = [];

    // Dijalankan saat tombol "New Chat" ditekan
    public function newChat()
    {
        $this->messages = []; // Kosongkan array pesan
        $this->userMessage = ''; // Kosongkan input
    }

    // Dijalankan saat kirim pesan
    public function sendMessage()
    {
        // Validasi: Jangan kirim jika kosong
        if (trim($this->userMessage) === '') {
            return;
        }

        // Simpan pesan User ke array
        $this->messages[] = [
            'role' => 'user',
            'content' => $this->userMessage
        ];

        // (SEMENTARA) Buat balasan bot otomatis (Dummy)
        // Nanti di sini kita ganti dengan koneksi ke Ollama
        $this->messages[] = [
            'role' => 'assistant',
            'content' => 'Halo! Saya menerima pesan Anda: "' . $this->userMessage . '". Saat ini saya belum terhubung ke otak AI, tapi fitur chat sudah jalan!'
        ];

        // 4. Kosongkan input setelah kirim
        $this->userMessage = '';
    }

    public function editMessage($index, $newContent)
    {
        // Update pesan di array lokal
        $this->messages[$index]['content'] = $newContent;

        if ($this->messages[$index]['role'] === 'user') {
            // Hapus semua pesan setelah pesan yang diedit
            $this->messages = array_slice($this->messages, 0, $index + 1);

            $this->sendMessage();
        }
    }

    public function render()
    {
        return view('livewire.chat');
    }
}

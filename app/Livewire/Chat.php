<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Chat as ChatModel;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class Chat extends Component
{
    public $userMessage = '';
    public $messages = [];
    public $chatId = null;
    public $histories = [];

    // --- Tambahkan Query String agar URL rapi (Opsional tapi bagus) ---
    // protected $queryString = ['chatId'];

    public function mount()
    {
        // Pastikan saat pertama buka, history termuat
        $this->loadHistories();

        // Jika ada chatId di URL/Session, load chatnya (Opsional logic)
    }

    public function loadHistories()
    {
        $this->histories = ChatModel::where('user_id', Auth::id())
            ->latest()
            ->get();
    }

    public function newChat()
    {
        // 1. Reset semua variabel state
        $this->chatId = null;
        $this->messages = [];
        $this->userMessage = '';

        // 2. (Opsional) Reset validasi jika ada
        $this->resetValidation();
    }

    public function loadChat($id)
    {
        $chat = ChatModel::where('id', $id)->where('user_id', Auth::id())->first();

        if ($chat) {
            $this->chatId = $chat->id;
            // Load messages
            $this->messages = $chat->messages()->oldest()->get()
                ->map(fn($msg) => ['role' => $msg->role, 'content' => $msg->content])
                ->toArray();
        }
    }

    public function sendMessage()
    {
        // Validasi input kosong
        if (trim($this->userMessage) === '') return;

        // Simpan pesan user ke array tampilan sementara
        $this->messages[] = ['role' => 'user', 'content' => $this->userMessage];

        // LOGIC PENTING: Cek apakah ini chat baru?
        if (!$this->chatId) {
            // Buat Chat Baru di Database
            $createdChat = ChatModel::create([
                'user_id' => Auth::id(),
                'title' => substr($this->userMessage, 0, 30) // Judul otomatis dari pesan pertama
            ]);

            // Set chatId ke ID yang baru dibuat
            $this->chatId = $createdChat->id;

            // Refresh sidebar history agar judul baru muncul
            $this->loadHistories();
        }

        // Simpan pesan ke database (sekarang $this->chatId pasti sudah ada isinya)
        Message::create([
            'chat_id' => $this->chatId,
            'role' => 'user',
            'content' => $this->userMessage
        ]);

        // Reset input field
        $currentMessage = $this->userMessage;
        $this->userMessage = '';

        // --- SIMULASI BALASAN AI ---
        $responseText = "Balasan untuk: " . $currentMessage;

        $this->messages[] = ['role' => 'assistant', 'content' => $responseText];

        Message::create([
            'chat_id' => $this->chatId,
            'role' => 'assistant',
            'content' => $responseText
        ]);
    }

    public function render()
    {
        return view('livewire.chat');
    }
}

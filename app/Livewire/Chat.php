<?php

namespace App\Livewire;

use App\Models\Chat as ChatModel;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Http;

class Chat extends Component
{
    public $userMessage = '';
    public $messages = [];
    public $chatId = null;
    public $isRenaming = false;
    public $renamingId = null;
    public $newName = '';
    public $confirmingDeletion = false;
    public $chatIdToDelete = null;

    public function mount()
    {
        // $this->loadHistories();
    }

    public function newChat()
    {
        $this->chatId = null;
        $this->messages = [];
        $this->userMessage = '';
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
        if (trim($this->userMessage) === '') return;
        set_time_limit(120);

        $this->messages[] = ['role' => 'user', 'content' => $this->userMessage];
        $currentMessage = $this->userMessage;
        $this->userMessage = ''; // Reset input box

        if (!$this->chatId) {
            $createdChat = ChatModel::create([
                'user_id' => Auth::id(),
                'title' => substr($currentMessage, 0, 30) // Judul otomatis dari 30 huruf pertama
            ]);
            $this->chatId = $createdChat->id;

            // Refresh sidebar
            unset($this->histories);
            unset($this->pinnedChats);
        }

        // Simpan pesan User ke Database
        Message::create([
            'chat_id' => $this->chatId,
            'role' => 'user',
            'content' => $currentMessage
        ]);

        $response = Http::timeout(120)->post( ... );

        $context = Message::where('chat_id', $this->chatId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($msg) => [
                'role' => $msg->role,
                'content' => $msg->content
            ])
            ->toArray();

        $responseText = '';

        try {
            // API Ollama
            $response = Http::timeout(300) // Timeout 5 menit (Gemma di PC biasa mungkin butuh waktu)
            ->post(env('OLLAMA_API_URL', 'http://localhost:11434/api/chat'), [
                'model' => env('OLLAMA_MODEL', 'gemma3:8b'), // Pastikan sesuai .env
                'messages' => $context,
                'stream' => false, // Matikan streaming agar lebih mudah ditangani
            ]);

            if ($response->successful()) {
                // Ambil jawaban dari JSON Ollama
                $responseText = $response->json()['message']['content'];
            } else {
                $responseText = "Error dari Ollama: " . $response->status();
            }

        } catch (\Exception $e) {
            $responseText = "Gagal terhubung ke Ollama. Pastikan aplikasi Ollama sudah berjalan. Error: " . $e->getMessage();
        }

        // Tampilkan Balasan AI di Layar
        $this->messages[] = ['role' => 'assistant', 'content' => $responseText];

        // Simpan Balasan AI ke Database
        Message::create([
            'chat_id' => $this->chatId,
            'role' => 'assistant',
            'content' => $responseText
        ]);

        // Update waktu chat agar naik ke paling atas di History
        ChatModel::where('id', $this->chatId)->touch();

        // Refresh sidebar
        unset($this->histories);
        unset($this->pinnedChats);
    }

    public function renameChat($id, $newTitle)
    {
        $chat = ChatModel::where('id', $id)->where('user_id', Auth::id())->first();
        if ($chat && trim($newTitle) !== '') {
            $chat->title = $newTitle;
            $chat->save();

            unset($this->histories);
            unset($this->pinnedChats);
        }
    }

    public function deleteChat()
    {
        $chat = ChatModel::find($this->chatIdToDelete);

        if ($chat && $chat->user_id == auth()->id()) {
            $chat->delete();

            if ($this->chatId == $this->chatIdToDelete) {
                return redirect()->route('chat');
            }
        }

        unset($this->histories);
        unset($this->pinnedChats);

        $this->confirmingDeletion = false;
        $this->chatIdToDelete = null;
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->chatIdToDelete = $id;
    }

    public function confirmRename($id, $currentTitle)
    {
        $this->renamingId = $id;
        $this->newName = $currentTitle;
        $this->isRenaming = true;
    }

    public function updateTitle()
    {
        $this->validate([
            'newName' => 'required|string|max:50',
        ]);

        $chat = ChatModel::find($this->renamingId);

        if ($chat) {
            $chat->title = $this->newName;
            $chat->save();
            unset($this->histories);

            unset($this->histories);
            unset($this->pinnedChats);
        }

        $this->cancelRename();
    }
    public function cancelRename()
    {
        $this->isRenaming = false;
        $this->renamingId = null;
        $this->newName = '';
    }

    #[Computed]
    public function histories()
    {
        return \App\Models\Chat::where('user_id', auth()->id())
            ->where('is_pinned', false) // exclude yang di pin
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    #[Computed]
    public function pinnedChats()
    {
        return \App\Models\Chat::where('user_id', auth()->id())
            ->where('is_pinned', true) // ambil khusus yang dipin
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function togglePin($chatId)
    {
        $chat = ChatModel::find($chatId);
        if ($chat) {
            $chat->is_pinned = !$chat->is_pinned;
            $chat->save();

            // Hapus cache kedua computed property agar UI langsung update
            unset($this->pinnedChats);
            unset($this->histories);
        }
    }

    public function render()
    {
        return view('livewire.chat');
    }
}

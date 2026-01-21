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
        $this->newChat();
    }

    public function render()
    {
        return view('livewire.chat');
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
            $this->messages = $chat->messages()->oldest()->get()
                ->map(fn($msg) => ['role' => $msg->role, 'content' => $msg->content])
                ->toArray();
        }
    }

    public function loadMessages()
    {
        $this->messages = Message::orderBy('created_at', 'asc')->get();
    }

    public function sendMessage()
    {
        $this->validate([
            'userMessage' => 'required|string'
        ]);

        if (!$this->chatId) {
            $chat = ChatModel::create([
                'user_id' => Auth::id(),
                'title' => 'New Chat'
            ]);
            $this->chatId = $chat->id;
        }

        Message::create([
            'chat_id' => $this->chatId,
            'role' => 'user',
            'content' => $this->userMessage
        ]);

        $chat = ChatModel::find($this->chatId);
        if ($chat) {
            $chat->touch();
        }

        unset($this->histories);
        unset($this->pinnedChats);

        $this->userMessage = '';
        $this->loadChat($this->chatId);
        $this->dispatch('start-generating-reply');
    }

    public function generateAiResponse()
    {
        set_time_limit(3000);
        $lastUserMessage = Message::where('chat_id', $this->chatId)
            ->where('role', 'user')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastUserMessage) return;
        $response = Http::timeout(3000)->post('http://localhost:11434/api/generate', [
            'model' => 'llama3',
            'prompt' => $lastUserMessage->content,
            'stream' => false
        ]);

        $botReply = $response->json()['response'] ?? 'Error: No response from AI';

        // Simpan balasan AI ke database
        Message::create([
            'chat_id' => $this->chatId,
            'role' => 'assistant',
            'content' => $botReply
        ]);

        // Refresh pesan lagi
        $this->loadChat($this->chatId);
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

            unset($this->pinnedChats);
            unset($this->histories);
        }
    }
}

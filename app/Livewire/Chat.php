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
    public $confirmingDeletion = false;
    public $chatIdToDelete = null;

    public function mount()
    {
        $this->loadHistories();
    }

    public function loadHistories()
    {
        $this->histories = ChatModel::where('user_id', Auth::id())
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();
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

        $this->messages[] = ['role' => 'user', 'content' => $this->userMessage];

        if (!$this->chatId) {
            $createdChat = ChatModel::create([
                'user_id' => Auth::id(),
                'title' => substr($this->userMessage, 0, 30)
            ]);

            $this->chatId = $createdChat->id;

            $this->loadHistories();
        }

        Message::create([
            'chat_id' => $this->chatId,
            'role' => 'user',
            'content' => $this->userMessage
        ]);

        $currentMessage = $this->userMessage;
        $this->userMessage = '';

        $responseText = "Balasan untuk: " . $currentMessage;

        $this->messages[] = ['role' => 'assistant', 'content' => $responseText];

        Message::create([
            'chat_id' => $this->chatId,
            'role' => 'assistant',
            'content' => $responseText
        ]);

        ChatModel::where('id', $this->chatId)->touch();
        $this->loadHistories();
    }

    public function togglePin($id)
    {
        $chat = ChatModel::where('id', $id)->where('user_id', Auth::id())->first();
        if ($chat) {
            $chat->is_pinned = !$chat->is_pinned;
            $chat->save();
            $this->loadHistories(); // Refresh list agar posisi berubah
        }
    }

    public function renameChat($id, $newTitle)
    {
        $chat = ChatModel::where('id', $id)->where('user_id', Auth::id())->first();
        if ($chat && trim($newTitle) !== '') {
            $chat->title = $newTitle;
            $chat->save();
            $this->loadHistories(); // Refresh list agar nama berubah
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

        $this->confirmingDeletion = false;
        $this->chatIdToDelete = null;
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->chatIdToDelete = $id;
    }

    public function render()
    {
        return view('livewire.chat');
    }
}

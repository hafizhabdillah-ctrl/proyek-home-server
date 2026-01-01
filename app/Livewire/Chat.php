<?php

namespace App\Livewire;

use App\Models\Chat as ChatModel;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

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

        $this->messages[] = ['role' => 'user', 'content' => $this->userMessage];

        if (!$this->chatId) {
            $createdChat = ChatModel::create([
                'user_id' => Auth::id(),
                'title' => substr($this->userMessage, 0, 30)
            ]);

            $this->chatId = $createdChat->id;

            unset($this->histories);
            unset($this->pinnedChats);
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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Untuk nembak API Ollama
use Illuminate\Support\Facades\Auth;
use App\Models\Chat;
use App\Models\Message;

class ChatApiController extends Controller
{
    public function sendMessage(Request $request)
    {
        // Validasi Input
        $request->validate([
            'message' => 'required|string',
            'chat_id' => 'nullable|exists:chats,id',
            'model'   => 'nullable|string'
        ]);

        $user = Auth::user(); // Ambil user yang login
        // Untuk testing awal tanpa login bisa hardcode user_id sementara atau pastikan route dilindungi middleware

        // Siapkan Chat ID (Buat baru jika belum ada)
        $chatId = $request->chat_id;
        if (!$chatId) {
            $chat = Chat::create([
                'user_id' => $user ? $user->id : 1, // Fallback ke ID 1 jika testing tanpa login
                'title'   => substr($request->message, 0, 30)
            ]);
            $chatId = $chat->id;
        }

        // Simpan Pesan User ke Database
        Message::create([
            'chat_id' => $chatId,
            'role'    => 'user',
            'content' => $request->message
        ]);

        // Kirim Request ke Ollama (Lokal)
        try {
            $response = Http::timeout(120)->post('http://127.0.0.1:11434/api/chat', [
                'model' => $request->model ?? 'llama3', // Ganti sesuai model Anda (misal: mistral)
                'messages' => [
                    ['role' => 'user', 'content' => $request->message]
                ],
                'stream' => false, // Kita matikan stream dulu biar gampang
            ]);

            // Ambil jawaban dari JSON Ollama
            $aiContent = $response->json()['message']['content'] ?? 'Error: No response from AI';

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal konek ke Ollama. Pastikan Ollama jalan!'], 500);
        }

        // Simpan Jawaban AI ke Database
        Message::create([
            'chat_id' => $chatId,
            'role'    => 'assistant',
            'content' => $aiContent
        ]);

        // Return JSON ke User
        return response()->json([
            'status' => 'success',
            'data' => [
                'chat_id' => $chatId,
                'user_message' => $request->message,
                'ai_response'  => $aiContent
            ]
        ]);
    }
}

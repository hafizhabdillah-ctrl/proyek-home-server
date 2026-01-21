<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChatApiController extends Controller
{
    public function chat(Request $request)
    {
        // 1. Validasi Input
        // Kita tidak butuh 'username' di input karena pengecekan via email & password
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
            'prompt'   => 'required|string'
        ]);

        // 2. LOGIKA AUTENTIKASI KETAT

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // SKENARIO 1: Email Tidak Ditemukan
        if (!$user) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Akses Ditolak: Email tidak terdaftar. Silakan registrasi dahulu.'
            ], 404); // 404 Not Found
        }

        // SKENARIO 2: Email Ada, Cek Password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses Ditolak: Password salah.'
            ], 401); // 401 Unauthorized
        }

        // SKENARIO 3: Lolos Validasi (User Valid) -> Lanjut ke Ollama

        $prompt = $request->input('prompt');
        $model = env('OLLAMA_MODEL', 'tinyllama');
        $apiUrl = env('OLLAMA_API_URL', 'http://127.0.0.1:11434/api/generate');

        try {
            $response = Http::timeout(600)->post($apiUrl, [
                'model'  => $model,
                'prompt' => $prompt,
                'stream' => false
            ]);

            if ($response->successful()) {
                $botReply = $response->json()['response'];

                return response()->json([
                    'status' => 'success',
                    'user_info' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'status' => 'Authenticated'
                    ],
                    'response' => $botReply
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ollama sibuk/error',
                    'details' => $response->body()
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Koneksi Timeout atau Error Server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

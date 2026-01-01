<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatApiController;

// Route Test Sederhana
Route::get('/test', function () {
    return response()->json(['message' => 'API Berhasil Terhubung!']);
});

// Route Chat
Route::post('/chat/send', [ChatApiController::class, 'sendMessage']);

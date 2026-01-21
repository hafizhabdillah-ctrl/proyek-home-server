<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatApiController;

// Route Test
Route::get('/test', function () {
    return response()->json(['message' => 'API Berhasil Terhubung!']);
});

// Route Chat (Perhatikan: Pakai ChatApiController)
Route::post('/chat', [ChatApiController::class, 'chat']);

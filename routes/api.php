<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;

Route::post('chatbot/preguntar', [ChatbotController::class, 'preguntar']);

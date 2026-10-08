<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/chat', function () {
    return view('chat');
});

use App\Http\Controllers\KnowledgeFileController;

Route::get('/knowledge', [KnowledgeFileController::class, 'index']);
Route::post('/knowledge/upload', [KnowledgeFileController::class, 'upload']);
Route::post('/knowledge/update-text', [KnowledgeFileController::class, 'updateText']);
Route::post('/knowledge/delete', [KnowledgeFileController::class, 'destroy']);
Route::post('/knowledge/re-extract', [KnowledgeFileController::class, 'reExtract']);
Route::post('/knowledge/save-ai-limits', [KnowledgeFileController::class, 'saveAiLimits']);
Route::post('/knowledge/save-links', [KnowledgeFileController::class, 'saveLinks']);
Route::post('/api/simulator/chat', [KnowledgeFileController::class, 'simulateChat']);

use App\Http\Controllers\ChatController;
Route::get('/api/chats', [ChatController::class, 'getChats']);
Route::get('/api/chats/{id}', [ChatController::class, 'getMessages']);
Route::post('/api/chats/{id}/send', [ChatController::class, 'sendMessage']);
Route::post('/api/chats/{id}/takeover', [ChatController::class, 'toggleTakeover']);
Route::post('/api/chats/{id}/clear-history', [ChatController::class, 'clearChatHistory']);

Route::get('/leads', function () {
    return view('leads');
});

Route::get('/followup', function () {
    return view('followup');
});

Route::get('/booking', function () {
    return view('booking');
});

Route::get('/invoice', function () {
    return view('invoice');
});

Route::get('/quotations', function () {
    return view('quotation');
});

Route::get('/finance', function () {
    return view('finance');
});

Route::get('/production', function () {
    return view('production');
});

Route::get('/activity', function () {
    return view('activity');
});

use App\Http\Controllers\SettingsController;

Route::get('/settings', [SettingsController::class, 'index']);
Route::post('/settings/token', [SettingsController::class, 'saveToken']);
Route::get('/settings/device', [SettingsController::class, 'getDevice']);
Route::post('/settings/disconnect', [SettingsController::class, 'disconnect']);
Route::post('/settings/notifications', [SettingsController::class, 'saveNotifications']);
Route::post('/settings/business-profile', [SettingsController::class, 'saveBusinessProfile']);

use App\Http\Controllers\WebhookController;
Route::match(['get', 'post'], '/api/public/wa/{secret}', [WebhookController::class, 'handle']);

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('/api/debug/logs', function() {
    $logPath = storage_path('logs/laravel.log');
    if (!file_exists($logPath)) return "No logs";
    
    $lines = file($logPath);
    $lastLines = array_slice($lines, -500);
    return response("<pre>" . implode("", $lastLines) . "</pre>");
});

Route::get('/api/debug/users', function() {
    $dbHost = env('DB_HOST');
    $dbDatabase = env('DB_DATABASE');
    $users = \App\Models\User::all();
    return response()->json(['host' => $dbHost, 'db' => $dbDatabase, 'users' => $users]);
});

Route::get('/api/debug/ai', function() {
    $groqApiKey = env('GROQ_API_KEY');
    if (!$groqApiKey) return response()->json(['error' => 'No GROQ key']);
    
    $model = 'llama3-8b-8192'; // Using a REAL Groq model
    $response = Http::withToken($groqApiKey)
        ->timeout(15)
        ->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => 'Test 123']],
        ]);
        
    return response()->json([
        'status' => $response->status(),
        'json' => $response->json(),
        'old_models' => ['qwen/qwen3.8-27b', 'openai/gpt-oss-120b']
    ]);
});

Route::get('/api/debug/users', function() {
    $dbHost = env('DB_HOST');
    $dbDatabase = env('DB_DATABASE');
    $users = \Illuminate\Support\Facades\DB::table('users')->get();
    return response()->json(['host' => $dbHost, 'db' => $dbDatabase, 'users' => $users]);
});

Route::get('/api/debug/auto-login', function() {
    $user = \App\Models\User::first();
    auth()->login($user);
    return redirect('/chat');
});

Route::get('/api/debug/chats', function() {
    $chats = \App\Models\Chat::with('messages')->get();
    return response()->json($chats);
});

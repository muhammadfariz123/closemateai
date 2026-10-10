<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\KnowledgeFileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\SystemActivityController;

// Public Auth Routes
Route::get('/', function () {
    return view('welcome');
})->name('login');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');

// Public API Routes
Route::get('/api/public/calendar/{token}.ics', [CalendarController::class, 'export']);
Route::get('/q/{id}', [QuotationController::class, 'showPublic']);
Route::post('/q/{id}/approve', [QuotationController::class, 'approveQuotation']);
Route::match(['get', 'post'], '/api/public/wa/{secret}', [WebhookController::class, 'handle']);
Route::get('/progress/{uuid}', [App\Http\Controllers\ProgressController::class, 'showPublic']);

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () { return view('dashboard'); });
    Route::get('/api/dashboard/stats', [DashboardController::class, 'stats']);

    Route::get('/api/bookings', [BookingController::class, 'getBookings']);
    Route::post('/api/bookings', [BookingController::class, 'addBooking']);
    Route::delete('/api/bookings/{id}', [BookingController::class, 'deleteBooking']);

    Route::get('/api/activities', [SystemActivityController::class, 'getActivities']);
    Route::post('/api/activities', [SystemActivityController::class, 'addActivity']);

    Route::get('/chat', function () { return view('chat'); });
    Route::get('/knowledge', [KnowledgeFileController::class, 'index']);
    Route::post('/knowledge/upload', [KnowledgeFileController::class, 'upload']);
    Route::post('/knowledge/update-text', [KnowledgeFileController::class, 'updateText']);
    Route::post('/knowledge/delete', [KnowledgeFileController::class, 'destroy']);
    Route::post('/knowledge/re-extract', [KnowledgeFileController::class, 'reExtract']);
    Route::post('/knowledge/save-ai-limits', [KnowledgeFileController::class, 'saveAiLimits']);
    Route::post('/knowledge/save-links', [KnowledgeFileController::class, 'saveLinks']);
    Route::post('/api/simulator/chat', [KnowledgeFileController::class, 'simulateChat']);

    Route::get('/api/chats', [ChatController::class, 'getChats']);
    Route::get('/api/chats/{id}', [ChatController::class, 'getMessages']);
    Route::post('/api/chats/{id}/send', [ChatController::class, 'sendMessage']);
    Route::post('/api/chats/{id}/takeover', [ChatController::class, 'toggleTakeover']);
    Route::post('/api/chats/{id}/clear-history', [ChatController::class, 'clearChatHistory']);
    Route::post('/api/chats/{id}/handler', [ChatController::class, 'setHandler']);
    Route::post('/api/chats', [ChatController::class, 'addChat']);
    Route::post('/api/chats/{id}/edit', [ChatController::class, 'editChat']);
    Route::delete('/api/chats/{id}', [ChatController::class, 'deleteChat']);

    Route::get('/leads', function () { return view('leads'); });
    Route::get('/followup', [\App\Http\Controllers\FollowUpController::class, 'index']);
    Route::post('/api/followup/settings', [\App\Http\Controllers\FollowUpController::class, 'saveSettings']);
    Route::post('/api/followup/process/{level}', [\App\Http\Controllers\FollowUpController::class, 'processManual']);
    Route::get('/booking', function () { return view('booking'); });
    Route::get('/invoice', function () { return view('invoice'); });

    Route::get('/quotations', function () { return view('quotation'); });
    Route::get('/api/quotations', [QuotationController::class, 'getQuotations']);
    Route::post('/api/quotations', [QuotationController::class, 'addQuotation']);
    Route::delete('/api/quotations/{id}', [QuotationController::class, 'deleteQuotation']);

    Route::get('/finance', function () { return view('finance'); });
    Route::get('/production', function () { return view('production'); });
    Route::get('/activity', function () { return view('activity'); });

    Route::get('/settings', [SettingsController::class, 'index']);
    Route::post('/settings/token', [SettingsController::class, 'saveToken']);
    Route::get('/settings/device', [SettingsController::class, 'getDevice']);
    Route::post('/settings/disconnect', [SettingsController::class, 'disconnect']);
    Route::post('/settings/notifications', [SettingsController::class, 'saveNotifications']);
    Route::post('/settings/business-profile', [SettingsController::class, 'saveBusinessProfile']);

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Debug Routes
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
    $model = 'llama3-8b-8192';
    $response = Illuminate\Support\Facades\Http::withToken($groqApiKey)
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
Route::get('/api/debug/auto-login', function() {
    $user = \App\Models\User::first();
    auth()->login($user);
    return redirect('/chat');
});
Route::get('/api/debug/chats', function() {
    $chats = \App\Models\Chat::with('messages')->get();
    return response()->json($chats);
});

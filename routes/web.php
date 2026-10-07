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

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$r = Illuminate\Support\Facades\Http::withToken(env('GROQ_API_KEY'))->post('https://api.groq.com/openai/v1/chat/completions', ['model' => 'qwen/qwen3.8-27b', 'messages' => [['role' => 'user', 'content' => 'tes']]]);
echo $r->status() . "\n" . $r->body() . "\n";

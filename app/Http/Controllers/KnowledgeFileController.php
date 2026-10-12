<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\KnowledgeFile;
use App\Models\Faq;
use App\Services\AiBrainService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class KnowledgeFileController extends Controller
{
    public function index()
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) {
            return redirect('/');
        }
        $file = KnowledgeFile::where('user_id', $user->id)->latest()->first();

        // Seed default FAQs if user has none
        if ($user->faqs()->count() === 0) {
            $defaultFaqs = [
                [
                    'question' => 'Berapa DP untuk booking tanggal?',
                    'answer' => 'DP sebesar 30% dari total paket untuk mengunci tanggal acara.',
                    'category' => 'Payment',
                    'sort_order' => 1
                ],
                [
                    'question' => 'Apakah bisa reschedule?',
                    'answer' => 'Bisa 1x reschedule maksimal H-60 sebelum acara tanpa biaya tambahan.',
                    'category' => 'Schedule',
                    'sort_order' => 2
                ],
                [
                    'question' => 'Apakah melayani luar kota?',
                    'answer' => 'Ya, dengan biaya transport & akomodasi tim ditanggung klien.',
                    'category' => 'Location',
                    'sort_order' => 3
                ],
                [
                    'question' => 'Berapa lama file jadi?',
                    'answer' => 'Foto edit 14 hari kerja, video cinematic 30 hari kerja.',
                    'category' => 'General',
                    'sort_order' => 4
                ],
            ];
            foreach ($defaultFaqs as $df) {
                $user->faqs()->create($df);
            }
        }

        $faqs = $user->faqs()->get();

        if (empty($user->default_terms)) {
            $defaultTerms = "1. Booking tanggal dianggap sah setelah DP 30% diterima.\n2. Pelunasan dilakukan maksimal H-7 sebelum acara.\n3. DP tidak dapat dikembalikan, namun dapat dipindah 1x reschedule (maks. H-60).\n4. Biaya transport & akomodasi tim di luar Jabodetabek ditanggung klien.\n5. File mentah (RAW) tidak diberikan kecuali ada kesepakatan tertulis.";
            try {
                $user->default_terms = $defaultTerms;
                $user->save();
            } catch (\Throwable $e) {
                try {
                    if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
                        \Illuminate\Support\Facades\DB::statement('ALTER TABLE users ALTER COLUMN default_terms TYPE text USING default_terms::text');
                        $user->default_terms = $defaultTerms;
                        $user->save();
                    }
                } catch (\Throwable $ex) {
                    \Illuminate\Support\Facades\Log::warning('default_terms save error: ' . $ex->getMessage());
                }
            }
        }

        if (empty($user->ai_prompt)) {
            try {
                $user->ai_prompt = AiBrainService::getDefaultPrompt($user->ai_personality_type ?: 'casual');
                $user->save();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('ai_prompt save error: ' . $e->getMessage());
            }
        }
        
        return view('knowledge', compact('file', 'user', 'faqs'));
    }

    public function saveCalendar(Request $request)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $url = trim((string)$request->input('google_calendar_url'));
        $user->google_calendar_url = $url;
        $user->save();

        if ($url) {
            cache()->forget('gcal_events_' . md5($url));
        }

        return response()->json(['success' => true, 'message' => 'Google Calendar berhasil disimpan']);
    }

    public function getFaqs()
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $faqs = $user->faqs()->get();
        return response()->json(['success' => true, 'data' => $faqs]);
    }

    public function storeFaq(Request $request)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        $maxOrder = $user->faqs()->max('sort_order') ?? 0;
        $faq = $user->faqs()->create([
            'question' => trim($request->input('question')),
            'answer' => trim($request->input('answer')),
            'category' => $request->input('category', 'General') ?: 'General',
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FAQ berhasil ditambahkan',
            'data' => $faq
        ]);
    }

    public function updateFaq(Request $request, $id)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        $faq = $user->faqs()->find($id);
        if (!$faq) {
            return response()->json(['success' => false, 'message' => 'FAQ tidak ditemukan'], 404);
        }

        $faq->update([
            'question' => trim($request->input('question')),
            'answer' => trim($request->input('answer')),
            'category' => $request->input('category', 'General') ?: 'General',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FAQ diperbarui',
            'data' => $faq
        ]);
    }

    public function destroyFaq($id)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $faq = $user->faqs()->find($id);
        if (!$faq) {
            return response()->json(['success' => false, 'message' => 'FAQ tidak ditemukan'], 404);
        }

        $faq->delete();

        return response()->json([
            'success' => true,
            'message' => 'FAQ berhasil dihapus'
        ]);
    }

    public function saveTerms(Request $request)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $terms = $request->input('default_terms', '');
        try {
            $user->default_terms = $terms;
            $user->save();
        } catch (\Throwable $e) {
            try {
                if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
                    \Illuminate\Support\Facades\DB::statement('ALTER TABLE users ALTER COLUMN default_terms TYPE text USING default_terms::text');
                    $user->default_terms = $terms;
                    $user->save();
                }
            } catch (\Throwable $ex) {
                \Illuminate\Support\Facades\Log::error('saveTerms error: ' . $ex->getMessage());
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $ex->getMessage()], 500);
            }
        }

        return response()->json(['success' => true, 'message' => 'Terms & Conditions berhasil disimpan']);
    }

    public function saveAiPrompt(Request $request)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $user->ai_prompt = $request->input('ai_prompt');
        $user->ai_personality_type = $request->input('ai_personality_type', 'casual');
        $user->save();

        return response()->json(['success' => true, 'message' => 'Pengaturan AI berhasil disimpan']);
    }

    public function saveAiLimits(Request $request)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $user->ai_limit_enabled = $request->boolean('ai_limit_enabled');
        $user->ai_max_replies = $request->input('ai_max_replies', 3);
        $user->ai_multi_bubble_enabled = $request->boolean('ai_multi_bubble_enabled');
        $user->ai_max_bubbles = $request->input('ai_max_bubbles', 3);
        $user->ai_require_data_before_price = $request->boolean('ai_require_data_before_price');
        
        if ($request->has('ai_required_data')) {
            $user->ai_required_data = json_decode($request->input('ai_required_data'), true);
        } else {
            $user->ai_required_data = [];
        }
        
        $user->ai_custom_questions = $request->input('ai_custom_questions');
        if ($request->has('ai_limit_reached_message')) {
            $user->ai_limit_reached_message = $request->input('ai_limit_reached_message');
        }
        $user->save();

        return response()->json(['success' => true, 'message' => 'Batasan Balasan AI berhasil disimpan']);
    }

    public function saveLinks(Request $request)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $links = $request->input('links', []);
        
        // Bersihkan data kosong
        $filteredLinks = array_filter($links, function($link) {
            return !empty($link['title']) || !empty($link['url']);
        });

        $user->price_list_links = json_encode(array_values($filteredLinks));
        $user->save();

        return response()->json(['success' => true, 'message' => 'Link price list berhasil disimpan']);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,jpg,jpeg,png,webp|max:10240', // 10MB
        ]);

        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            
            // Format size
            $bytes = $file->getSize();
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            $bytes /= (1 << (10 * $pow));
            $fileSize = round($bytes, 2) . ' ' . $units[$pow];

            $path = $file->store('knowledge_files', 'public');
            
            // AI Extraction Using Gemini 1.5 Flash (if API key is set)
            $extractedText = "";
            $apiKey = env('GEMINI_API_KEY');
            
            if ($apiKey) {
                try {
                    $mimeType = $file->getMimeType();
                    $fileContent = file_get_contents($file->getRealPath());
                    
                    // OPTIMIZATION: Resize & Compress image to drastically speed up AI payload upload & vision token processing
                    if (in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'webp'])) {
                        $img = @imagecreatefromstring($fileContent);
                        if ($img !== false) {
                            $width = imagesx($img);
                            $height = imagesy($img);
                            
                            // Scale down to max 800px to reduce Gemini visual tiles (massive speedup)
                            if ($width > 800 || $height > 800) {
                                $ratio = min(800 / $width, 800 / $height);
                                $newWidth = $width * $ratio;
                                $newHeight = $height * $ratio;
                                $resized = imagecreatetruecolor($newWidth, $newHeight);
                                
                                // Gunakan imagecopyresized karena jauh lebih cepat di CPU daripada imagecopyresampled
                                imagecopyresized($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                                imagedestroy($img);
                                $img = $resized;
                            }
                            
                            ob_start();
                            imagejpeg($img, null, 50); // Compress to 50% JPEG
                            $fileContent = ob_get_clean();
                            imagedestroy($img);
                            $mimeType = 'image/jpeg';
                        }
                    }
                    
                    $base64Data = base64_encode($fileContent);
                    
                    $prompt = "Tolong ekstrak semua teks dari dokumen pricelist/katalog ini. Susun dengan rapi menggunakan Markdown. Pisahkan dengan jelas bagian-bagian seperti: Nama Paket, Harga, Fasilitas/Benefit, Syarat & Ketentuan, dan Informasi Kontak (jika ada). PENTING: JANGAN tambahkan kalimat pengantar atau penutup apapun (seperti 'Berikut adalah ekstraksinya...'). Kembalikan HANYA teks isi dokumen yang di-transcript seakurat dan semirip mungkin dengan aslinya.";

                    $response = Http::retry(4, 2000)->timeout(60)->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-3.7-flash:generateContent?key=' . $apiKey, [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt],
                                    [
                                        'inline_data' => [
                                            'mime_type' => $mimeType,
                                            'data' => $base64Data
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]);

                    if ($response->successful()) {
                        $resData = $response->json();
                        if (isset($resData['candidates'][0]['content']['parts'][0]['text'])) {
                            $extractedText = $resData['candidates'][0]['content']['parts'][0]['text'];
                        } else {
                            $extractedText = "Gagal memparsing respons dari AI. Pastikan format file didukung.";
                        }
                    } else {
                        $extractedText = "Error dari AI: " . $response->body();
                    }
                } catch (\Exception $e) {
                    $extractedText = "Terjadi kesalahan saat menghubungi AI: " . $e->getMessage();
                }
            } else {
                // Fallback jika belum ada API Key
                $extractedText = "# " . pathinfo($fileName, PATHINFO_FILENAME) . "\n\n";
                $extractedText .= "> ⚠️ **GEMINI_API_KEY belum dikonfigurasi di .env**\n> Sistem belum bisa mengekstrak file Anda secara otomatis. Silakan atur API Key untuk mengaktifkan fitur ini.\n\n";
                $extractedText .= "## INFORMASI VENDOR\n- Nama: {$user->business_name}\n- Kontak: {$user->business_wa_number}";
            }

            // Delete old file if exists
            $oldFiles = KnowledgeFile::where('user_id', $user->id)->get();
            foreach($oldFiles as $oldFile) {
                if(Storage::disk('public')->exists($oldFile->file_path)) {
                    Storage::disk('public')->delete($oldFile->file_path);
                }
                $oldFile->delete();
            }

            $knowledgeFile = KnowledgeFile::create([
                'user_id' => $user->id,
                'file_name' => $fileName,
                'file_path' => $path,
                'file_size' => $fileSize,
                'file_type' => $file->getClientOriginalExtension(),
                'extracted_text' => $extractedText
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'data' => [
                    'id' => $knowledgeFile->id,
                    'file_name' => $knowledgeFile->file_name,
                    'file_size' => $knowledgeFile->file_size,
                    'file_path' => asset('storage/' . $knowledgeFile->file_path),
                    'created_at' => $knowledgeFile->created_at->format('j/n/Y'),
                    'extracted_text' => $knowledgeFile->extracted_text,
                    'file_type' => $knowledgeFile->file_type
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada file yang diupload']);
    }

    public function updateText(Request $request)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $file = KnowledgeFile::where('user_id', $user->id)->latest()->first();
        if (!$file) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan']);
        }

        $file->extracted_text = $request->extracted_text;
        $file->save();

        return response()->json(['success' => true, 'message' => 'Rincian teks price list tersimpan']);
    }

    public function destroy()
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $files = KnowledgeFile::where('user_id', $user->id)->get();
        if ($files->count() > 0) {
            foreach($files as $file) {
                if(Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }
                $file->delete();
            }
            return response()->json(['success' => true, 'message' => 'File dan teks terkait dihapus']);
        }

        return response()->json(['success' => false, 'message' => 'File tidak ditemukan']);
    }

    public function reExtract()
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $file = KnowledgeFile::where('user_id', $user->id)->latest()->first();
        if (!$file) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan']);
        }

        try {
            $apiKey = env('GEMINI_API_KEY');
            
            $fileData = file_get_contents(storage_path('app/public/' . $file->file_path));
            $extension = pathinfo($file->file_path, PATHINFO_EXTENSION);
            $mimeType = 'image/jpeg';
            if (strtolower($extension) === 'png') $mimeType = 'image/png';
            if (strtolower($extension) === 'webp') $mimeType = 'image/webp';
            if (strtolower($extension) === 'pdf') $mimeType = 'application/pdf';

            // OPTIMIZATION: Resize & Compress image to drastically speed up AI payload upload & vision token processing
            if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp'])) {
                $img = @imagecreatefromstring($fileData);
                if ($img !== false) {
                    $width = imagesx($img);
                    $height = imagesy($img);
                    
                    if ($width > 800 || $height > 800) {
                        $ratio = min(800 / $width, 800 / $height);
                        $newWidth = $width * $ratio;
                        $newHeight = $height * $ratio;
                        $resized = imagecreatetruecolor($newWidth, $newHeight);
                        
                        imagecopyresized($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagedestroy($img);
                        $img = $resized;
                    }

                    ob_start();
                    imagejpeg($img, null, 50); // Compress to 50% JPEG
                    $fileData = ob_get_clean();
                    imagedestroy($img);
                    $mimeType = 'image/jpeg';
                }
            }
            
            $base64Data = base64_encode($fileData);
            
            $prompt = "Ekstrak teks dari dokumen ini. Pertahankan tata letaknya persis seperti aslinya. Jangan ubah strukturnya, jangan ubah format barisnya. Tuliskan persis seperti yang tertulis di gambar. JANGAN ada kalimat pembuka/penutup dari AI. Berikan murni isi teks gambarnya saja.";

            $groqApiKey = env('GROQ_API_KEY');
            $response = null;

            if ($groqApiKey) {
                try {
                    $response = Http::withToken($groqApiKey)->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => 'qwen/qwen3.8-27b',
                        'messages' => [
                            [
                                'role' => 'user',
                                'content' => [
                                    ['type' => 'text', 'text' => $prompt],
                                    [
                                        'type' => 'image_url',
                                        'image_url' => [
                                            'url' => "data:{$mimeType};base64,{$base64Data}"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]);
                } catch (\Exception $e) {
                    $response = null; // Biarkan jatuh ke fallback Gemini
                }
            }

            // Jika Groq tidak ada, tidak support (misal PDF), atau gagal, fallback ke Gemini
            if (!$response || !$response->successful() || !isset($response->json()['choices'][0]['message']['content'])) {
                $response = Http::retry(4, 2000)->timeout(60)->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-3.7-flash:generateContent?key=' . $apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                                [
                                    'inlineData' => [
                                        'mimeType' => $mimeType,
                                        'data' => $base64Data
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);
            }
            if ($response && $response->successful()) {
                $responseData = $response->json();
                $extractedText = '';

                // Cek format Groq
                if (isset($responseData['choices'][0]['message']['content'])) {
                    $extractedText = $responseData['choices'][0]['message']['content'];
                }
                // Cek format Gemini
                elseif (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    $extractedText = $responseData['candidates'][0]['content']['parts'][0]['text'];
                }

                if (!empty($extractedText)) {
                    if (str_starts_with(trim($extractedText), '```markdown')) {
                        $extractedText = preg_replace('/^```markdown\s*/', '', trim($extractedText));
                        $extractedText = preg_replace('/\s*```$/', '', $extractedText);
                    }

                    $file->extracted_text = trim($extractedText);
                    $file->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Teks berhasil diekstrak ulang, cek & simpan bila sudah sesuai',
                        'data' => [
                            'extracted_text' => $file->extracted_text
                        ]
                    ]);
                }
            }
            
            return response()->json(['success' => false, 'message' => 'Gagal mengekstrak teks dari Gemini. Coba lagi.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function simulateChat(Request $request)
    {
        try {
            $user = auth()->user() ?? \App\Models\User::first();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            $message = $request->input('message');
            $clientName = $request->input('client_name', 'Klien');
            $apiKey = env('GEMINI_API_KEY');

            if (!$apiKey) {
                return response()->json(['success' => false, 'message' => 'API Key belum diatur di .env']);
            }

            $systemPrompt = AiBrainService::buildSystemPrompt($user, $clientName);

            $groqApiKey = env('GROQ_API_KEY');

            // ==== OPSI 1: JIKA MENGGUNAKAN GROQ (SUPER CEPAT & GRATIS) ====
            if ($groqApiKey) {
                $groqModels = ['openai/gpt-oss-120b', 'qwen/qwen3.8-27b', 'llama-3.1-70b-versatile', 'llama-3.1-8b-instant'];
                $groqLastError = '';
                
                foreach ($groqModels as $groqModel) {
                    try {
                        $response = Http::withToken($groqApiKey)
                            ->timeout(15)
                            ->post('https://api.groq.com/openai/v1/chat/completions', [
                                'model' => $groqModel,
                                'messages' => [
                                    ['role' => 'system', 'content' => $systemPrompt],
                                    ['role' => 'user', 'content' => $message],
                                ],
                                'temperature' => 0.8,
                                'max_tokens' => 800,
                            ]);

                        if ($response->successful()) {
                            $responseData = $response->json();
                            if (isset($responseData['choices'][0]['message']['content'])) {
                                return response()->json([
                                    'success' => true,
                                    'reply' => trim((string)$responseData['choices'][0]['message']['content'])
                                ]);
                            }
                        }
                        
                        $groqLastError = $response->body();
                    } catch (\Throwable $e) {
                        $groqLastError = $e->getMessage();
                    }
                }

                // Jika semua model Groq gagal
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menggunakan Groq AI (Semua model dicoba): ' . $groqLastError
                ]);
            }

            // ==== OPSI 2: JIKA MENGGUNAKAN GEMINI (LAMA) ====
            $modelsToTry = ['gemini-1.5-flash', 'gemini-1.5-flash-latest', 'gemini-1.5-pro', 'gemini-3.8-flash', 'gemini-3.7-flash'];
            $lastExceptionMessage = 'Gagal mendapatkan balasan dari AI.';

            $finalPrompt = "INSTRUKSI SISTEM:\n" . $systemPrompt . "\n\nPESAN KLIEN:\n" . $message;

            foreach ($modelsToTry as $modelName) {
                try {
                    // Gunakan timeout 15 detik.
                    $response = Http::timeout(15)->post('https://generativelanguage.googleapis.com/v1beta/models/' . $modelName . ':generateContent?key=' . $apiKey, [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    ['text' => $finalPrompt]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.8,
                            'maxOutputTokens' => 800,
                        ]
                    ]);

                    if ($response->successful()) {
                        $responseData = $response->json();
                        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                            $reply = $responseData['candidates'][0]['content']['parts'][0]['text'];
                            return response()->json([
                                'success' => true,
                                'reply' => trim((string)$reply)
                            ]);
                        }
                    } else {
                        $lastExceptionMessage = $response->body();
                        
                        if ($response->status() !== 503 && $response->status() !== 429) {
                            break;
                        }
                        continue; 
                    }
                } catch (\Throwable $e) {
                    $lastExceptionMessage = $e->getMessage();
                    continue;
                }
            }

            // JIKA SEMUA MODEL GAGAL
            throw new \Exception($lastExceptionMessage);

        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            $friendlyMsg = 'Terjadi kesalahan sistem: ' . $msg . ' (' . $e->getFile() . ':' . $e->getLine() . ')';
            
            if (strpos($msg, '503') !== false || strpos($msg, 'high demand') !== false) {
                $friendlyMsg = "Maaf Kak, server AI kami sedang sangat penuh (High Demand). Coba tunggu beberapa detik lalu kirim ulang ya! 🙏";
            } elseif (strpos($msg, '429') !== false || strpos($msg, 'quota') !== false) {
                $friendlyMsg = "Waduh, kuota gratis AI Kakak hari ini sudah habis. Silakan pakai API Key lain atau aktifkan billing ya Kak! 😅";
            }
            
            return response()->json(['success' => false, 'message' => $friendlyMsg]);
        }
    }
}

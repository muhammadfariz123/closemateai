<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\KnowledgeFile;
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
        
        return view('knowledge', compact('file'));
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

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    $extractedText = $responseData['candidates'][0]['content']['parts'][0]['text'];
                    
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

        $file = KnowledgeFile::where('user_id', $user->id)->latest()->first();
        $knowledge = $file ? $file->extracted_text : 'Tidak ada data price list / katalog.';

        $systemPrompt = "Kamu adalah asisten CS (Customer Service) WhatsApp yang sangat ramah, natural, dan luwes bernama CloseMateAI, mewakili bisnis '{$user->business_name}'.\n"
            . "Tugasmu adalah menjawab pesan dari calon klien bernama '{$clientName}' berdasarkan KNOWLEDGE BASE berikut ini:\n\n"
            . "-- KNOWLEDGE BASE MULAI --\n"
            . "{$knowledge}\n"
            . "-- KNOWLEDGE BASE SELESAI --\n\n"
            . "ATURAN PENTING:\n"
            . "1. Jawablah dengan bahasa Indonesia yang sangat natural, santai, sopan, layaknya manusia biasa chatting di WhatsApp. Jangan kaku atau seperti robot.\n"
            . "2. Gunakan sapaan 'aku' untuk dirimu dan 'Kak {$clientName}' untuk klien.\n"
            . "3. Selalu sisipkan 1-2 emoji yang ramah (contoh: 😊, ✨, 🙏).\n"
            . "4. Jika klien meminta Price List (PL), JANGAN langsung mengirim isi pricelist. Kamu HARUS membalas dengan gaya SEPERTI INI (gunakan ini sebagai referensi gaya bahasamu):\n"
            . "   \"Halo Kak {$clientName}! 😊\n\nBoleh banget Kak, dengan senang hati. Untuk keperluan pengiriman detailnya, boleh dibantu informasikan nama calon pengantin serta kota dan lokasi venue acaranya ya Kak? Supaya aku bisa sesuaikan informasinya buat Kakak. ✨\"\n"
            . "5. JANGAN memberikan harga atau paket yang tidak ada di Knowledge Base.\n"
            . "6. Balaslah hanya sebagai respon untuk pesan klien, jangan tambahkan embel-embel format aneh.";

        // Daftar model AI yang akan dicoba berurutan jika terjadi High Demand (503)
        $modelsToTry = ['gemini-3.8-flash', 'gemini-3.7-flash', 'gemini-flash-latest'];
        $lastExceptionMessage = 'Gagal mendapatkan balasan dari AI.';

        $finalPrompt = "INSTRUKSI SISTEM:\n" . $systemPrompt . "\n\nPESAN KLIEN:\n" . $message;

        foreach ($modelsToTry as $modelName) {
            try {
                // Gunakan timeout 15 detik. Terlalu cepat (5 detik) justru akan gagal prematur karena AI butuh waktu berpikir.
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
                        'temperature' => 0.8, // Kembalikan ke 0.8 agar natural
                        'maxOutputTokens' => 800, // Kembalikan ke 800 agar pesan tidak terpotong
                    ]
                ]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                        $reply = $responseData['candidates'][0]['content']['parts'][0]['text'];
                        return response()->json([
                            'success' => true,
                            'reply' => trim($reply)
                        ]);
                    }
                } else {
                    $lastExceptionMessage = $response->body();
                    
                    // Jika error bukan 503/429 (misal error fatal dari Google API), jangan retry model lain
                    if ($response->status() !== 503 && $response->status() !== 429) {
                        break;
                    }
                    
                    // Jika 503/429, lanjut coba ke model berikutnya di array
                    continue; 
                }
            } catch (\Exception $e) {
                // Jika jaringan putus, lanjut coba
                $lastExceptionMessage = $e->getMessage();
                continue;
            }
        }

        // JIKA SEMUA MODEL GAGAL, baru lempar error yang akan ditangkap di bawah
        try {
            throw new \Exception($lastExceptionMessage);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $friendlyMsg = 'Terjadi kesalahan sistem: ' . $msg;
            
            if (strpos($msg, '503') !== false || strpos($msg, 'high demand') !== false) {
                $friendlyMsg = "Maaf Kak, server AI kami sedang sangat penuh (High Demand). Coba tunggu beberapa detik lalu kirim ulang ya! 🙏";
            } elseif (strpos($msg, '429') !== false || strpos($msg, 'quota') !== false) {
                $friendlyMsg = "Waduh, kuota gratis AI Kakak hari ini sudah habis. Silakan pakai API Key lain atau aktifkan billing ya Kak! 😅";
            }
            
            return response()->json(['success' => false, 'message' => $friendlyMsg]);
        }
    }
}

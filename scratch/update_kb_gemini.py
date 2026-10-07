import re

with open('app/Http/Controllers/KnowledgeFileController.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add Http facade if not there
if 'use Illuminate\Support\Facades\Http;' not in content:
    content = content.replace('use Illuminate\Support\Facades\Auth;', 'use Illuminate\Support\Facades\Auth;\nuse Illuminate\Support\Facades\Http;')

old_logic = """            // Dummy Extraction (Since we don't have an AI endpoint/PDF parser here)
            // In a real scenario, we'd send this to an OCR/Document AI service
            $extractedText = "# " . pathinfo($fileName, PATHINFO_FILENAME) . "\\n\\n";
            $extractedText .= "Teks ini adalah hasil ekstraksi otomatis dari file yang diupload.\\n";
            $extractedText .= "Di sini akan berisi informasi pricelist, paket, add-on, dan fasilitas lainnya dari vendor.\\n\\n";
            $extractedText .= "## INFORMASI VENDOR\\n- Nama: {$user->business_name}\\n- Kontak: {$user->business_wa_number}";"""

new_logic = """            // AI Extraction Using Gemini 1.5 Flash (if API key is set)
            $extractedText = "";
            $apiKey = env('GEMINI_API_KEY');
            
            if ($apiKey) {
                try {
                    $mimeType = $file->getMimeType();
                    $base64Data = base64_encode(file_get_contents($file->getRealPath()));
                    
                    $prompt = "Tolong ekstrak semua teks dari dokumen pricelist/katalog ini. Susun dengan rapi menggunakan Markdown. Pisahkan dengan jelas bagian-bagian seperti: Nama Paket, Harga, Fasilitas/Benefit, Syarat & Ketentuan, dan Informasi Kontak (jika ada). Jangan mengarang informasi, cukup ekstrak apa yang ada di dokumen saja.";

                    $response = Http::timeout(60)->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey, [
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
                } catch (\\Exception $e) {
                    $extractedText = "Terjadi kesalahan saat menghubungi AI: " . $e->getMessage();
                }
            } else {
                // Fallback jika belum ada API Key
                $extractedText = "# " . pathinfo($fileName, PATHINFO_FILENAME) . "\\n\\n";
                $extractedText .= "> ⚠️ **GEMINI_API_KEY belum dikonfigurasi di .env**\\n> Sistem belum bisa mengekstrak file Anda secara otomatis. Silakan atur API Key untuk mengaktifkan fitur ini.\\n\\n";
                $extractedText .= "## INFORMASI VENDOR\\n- Nama: {$user->business_name}\\n- Kontak: {$user->business_wa_number}";
            }"""

content = content.replace(old_logic, new_logic)

with open('app/Http/Controllers/KnowledgeFileController.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("KnowledgeFileController updated with Gemini API logic.")

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat;
use App\Models\Message;
use App\Models\KnowledgeFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WebhookController extends Controller
{
    public function handle(Request $request, $secret)
    {
        // 1. Cari user berdasarkan webhook_secret
        $user = User::where('webhook_secret', $secret)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Invalid webhook secret'], 404);
        }

        if ($request->isMethod('get')) {
            return response()->json(['status' => true, 'message' => 'Webhook URL is active']);
        }

        $data = $request->all();
        if (empty($data)) {
            return response()->json(['status' => true, 'message' => 'Webhook ready']);
        }

        Log::info('Fonnte Webhook Received for User ID ' . $user->id, $data);

        // 2. Extract payload dari Fonnte
        $sender = $data['sender'] ?? null;
        $messageText = $data['message'] ?? null;
        $name = $data['name'] ?? $sender;
        
        // Fonnte mengirim pesan dari diri sendiri dengan parameter 'sender' yang merupakan nomor Fonnte atau berisi status
        // Kita hanya proses pesan masuk, biasanya Fonnte mengirim parameter 'device' juga
        if (!$sender || !$messageText || $sender == $user->wa_number || strpos($sender, '-') !== false) {
            return response()->json(['status' => true, 'message' => 'Not a valid incoming message']);
        }

        // 3. Find or Create Chat
        try {
            $chat = Chat::firstOrCreate(
                ['user_id' => $user->id, 'client_wa_number' => $sender],
                ['client_name' => $name, 'status' => 'Belum Dihandle', 'ai_reply_count' => 0, 'is_human_takeover' => false]
            );
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika tabel tidak ditemukan (biasanya karena migrasi belum jalan di server gratisan Render)
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            
            // Coba lagi setelah migrasi
            $chat = Chat::firstOrCreate(
                ['user_id' => $user->id, 'client_wa_number' => $sender],
                ['client_name' => $name, 'status' => 'Belum Dihandle', 'ai_reply_count' => 0, 'is_human_takeover' => false]
            );
        }

        // Update nama jika belum ada
        if ($chat->client_name === $sender && $name !== $sender) {
            $chat->client_name = $name;
            $chat->save();
        }

        // 4. Save incoming message
        Message::create([
            'chat_id' => $chat->id,
            'sender' => 'client',
            'message' => $messageText
        ]);

        // 5. Cek apakah Human Takeover aktif
        if ($chat->is_human_takeover) {
            return response()->json(['status' => true, 'message' => 'Human takeover active, AI paused']);
        }

        // 6. Cek Batasan Balasan AI
        if ($user->ai_limit_enabled && $chat->ai_reply_count >= ($user->ai_max_replies ?? 3)) {
            $chat->is_human_takeover = true;
            $chat->save();

            // Kirim notifikasi ke nomor bisnis User (Admin)
            $this->sendFonnteMessage($user->fonnte_token, $user->business_wa_number ?? $user->wa_number, "🚨 *HUMAN TAKEOVER AKTIF*\nAda calon klien baru yang butuh di-handle langsung oleh admin!\n\nNama: {$chat->client_name}\nNomor: {$chat->client_wa_number}\nStatus: Chat AI sudah mencapai batas maksimal ({$user->ai_max_replies} balasan). Silakan buka menu Live Chat Inbox.");
            
            return response()->json(['status' => true, 'message' => 'AI Limit reached. Handed over to human.']);
        }

        // 7. Panggil AI
        $aiReply = $this->generateAiReply($user, $chat, $messageText);
        
        if ($aiReply) {
            // Cek Multiple Bubble
            $bubbles = [$aiReply];
            if ($user->ai_multi_bubble_enabled) {
                // Pisahkan pesan berdasarkan double newline
                $bubbles = explode("\n\n", $aiReply);
                // Batasi jumlah bubble
                $maxBubbles = $user->ai_max_bubbles ?? 3;
                if (count($bubbles) > $maxBubbles) {
                    // Gabungkan sisanya jika melebihi max bubble
                    $sliced = array_slice($bubbles, 0, $maxBubbles - 1);
                    $sliced[] = implode("\n\n", array_slice($bubbles, $maxBubbles - 1));
                    $bubbles = $sliced;
                }
            }

            foreach ($bubbles as $bubbleText) {
                if (trim($bubbleText) === '') continue;
                
                // Simpan balasan AI ke database
                Message::create([
                    'chat_id' => $chat->id,
                    'sender' => 'ai',
                    'message' => trim($bubbleText)
                ]);

                // Kirim balasan via Fonnte
                $this->sendFonnteMessage($user->fonnte_token, $chat->client_wa_number, trim($bubbleText));
                
                // Jeda sedikit antar bubble agar terlihat natural
                if (count($bubbles) > 1) {
                    sleep(2); 
                }
            }

            // Increment reply count
            $chat->ai_reply_count += 1;
            $chat->save();
        } else {
            // Jika AI gagal memberikan balasan, kirim pesan error agar tidak silent fail
            $this->sendFonnteMessage($user->fonnte_token, $chat->client_wa_number, "Mohon maaf, layanan AI kami sedang mengalami gangguan koneksi. (Sistem: Gagal memproses prompt)");
        }

        return response()->json(['status' => true, 'message' => 'Processed successfully']);
    }

    private function generateAiReply($user, $chat, $incomingMessage)
    {
        $file = KnowledgeFile::where('user_id', $user->id)->latest()->first();
        $knowledge = $file ? $file->extracted_text : 'Tidak ada data price list / katalog.';
        
        $aiRequireData = $user->ai_require_data_before_price ?? true;
        $aiRequiredDataList = is_array($user->ai_required_data) ? $user->ai_required_data : [];
        $customQuestions = $user->ai_custom_questions ?? '';
        
        $links = json_decode((string)($user->price_list_links ?? '[]'), true);
        $formattedLinks = "";
        if (!empty($links)) {
            foreach ($links as $link) {
                $title = $link['title'] ?: 'Link Price List';
                $url = $link['url'] ?: '#';
                $formattedLinks .= "{$title}: {$url}\n";
            }
        } else {
            $formattedLinks = $file ? asset('storage/' . $file->file_path) : '[Link Price List belum diatur]';
        }

        $priceListRule = "";
        if ($aiRequireData) {
            $requirementsText = implode(', ', $aiRequiredDataList);
            if ($customQuestions) {
                $customQuestionsStr = str_replace("\n", ", ", trim($customQuestions));
                $requirementsText .= ($requirementsText ? ', dan ' : '') . "($customQuestionsStr)";
            }
            if (empty($requirementsText)) {
                $requirementsText = "Nama dan Detail Acara";
            }

            $priceListRule = "4. Jika klien meminta Price List (PL), JANGAN langsung mengirim isi pricelist atau file/link-nya. Kamu HARUS menggali informasi berikut ini terlebih dahulu dari klien: [{$requirementsText}].\n"
                . "   Balaslah dengan gaya SEPERTI INI:\n"
                . "   \"Halo Kak {$chat->client_name}! 😊\n\nBoleh banget Kak, dengan senang hati. Untuk keperluan pengiriman detailnya, boleh dibantu informasikan {$requirementsText} ya Kak? Supaya aku bisa sesuaikan informasinya buat Kakak. ✨\"\n";
        } else {
            $priceListRule = "4. Jika klien meminta Price List (PL), BERIKAN link file price list berikut ini SECARA LANGSUNG:\n{$formattedLinks}\n"
                . "   Lalu di akhir pesan tanyakan detail acara dengan sopan.\n";
        }

        $systemPrompt = "Kamu adalah asisten CS (Customer Service) WhatsApp yang sangat ramah, natural, dan luwes bernama CloseMateAI, mewakili bisnis '{$user->business_name}'.\n"
            . "Tugasmu adalah menjawab pesan dari calon klien bernama '{$chat->client_name}' berdasarkan KNOWLEDGE BASE berikut ini:\n\n"
            . "-- KNOWLEDGE BASE MULAI --\n"
            . "{$knowledge}\n"
            . "-- KNOWLEDGE BASE SELESAI --\n\n"
            . "ATURAN PENTING:\n"
            . "1. Jawablah dengan bahasa Indonesia yang sangat natural, santai, sopan, layaknya manusia biasa chatting di WhatsApp. Jangan kaku atau seperti robot.\n"
            . "2. Gunakan sapaan 'aku' untuk dirimu dan 'Kak {$chat->client_name}' untuk klien.\n"
            . "3. Selalu sisipkan 1-2 emoji yang ramah (contoh: 😊, ✨, 🙏).\n"
            . $priceListRule
            . "5. JANGAN memberikan harga atau paket yang tidak ada di Knowledge Base.\n"
            . "6. Balaslah hanya sebagai respon untuk pesan klien, jangan tambahkan embel-embel format aneh.";

        // Ambil history chat agar AI mengerti konteks
        $messages = Message::where('chat_id', $chat->id)->orderBy('created_at', 'asc')->take(10)->get();
        $apiMessages = [['role' => 'system', 'content' => $systemPrompt]];
        
        foreach ($messages as $msg) {
            $role = $msg->sender === 'client' ? 'user' : 'assistant';
            $apiMessages[] = ['role' => $role, 'content' => $msg->message];
        }

        $groqApiKey = env('GROQ_API_KEY');
        $geminiApiKey = env('GEMINI_API_KEY');
        
        if (!$groqApiKey && !$geminiApiKey) {
            return "Maaf, API AI belum dikonfigurasi.";
        }

        // ==== OPSI 1: JIKA MENGGUNAKAN GROQ ====
        if ($groqApiKey) {
            $groqModels = ['llama-3.1-8b-instant', 'llama-3.1-70b-versatile', 'mixtral-8x7b-32768'];
            foreach ($groqModels as $model) {
                try {
                    $response = Http::withToken($groqApiKey)
                        ->timeout(15)
                        ->post('https://api.groq.com/openai/v1/chat/completions', [
                            'model' => $model,
                            'messages' => $apiMessages,
                            'temperature' => 0.8,
                            'max_tokens' => 800,
                        ]);

                    if ($response->successful() && isset($response->json()['choices'][0]['message']['content'])) {
                        return trim($response->json()['choices'][0]['message']['content']);
                    }
                } catch (\Exception $e) {
                    Log::error("Groq Error ($model): " . $e->getMessage());
                }
            }
        }

        // ==== OPSI 2: JIKA MENGGUNAKAN GEMINI (LAMA) ====
        if ($geminiApiKey) {
            $finalPrompt = "INSTRUKSI SISTEM:\n" . $systemPrompt;
            foreach ($apiMessages as $msg) {
                if ($msg['role'] !== 'system') {
                    $finalPrompt .= "\n\n" . ($msg['role'] == 'user' ? 'KLIEN' : 'AI') . ": " . $msg['content'];
                }
            }
            
            $geminiModels = ['gemini-1.5-flash', 'gemini-1.5-flash-latest', 'gemini-1.5-pro', 'gemini-3.8-flash', 'gemini-3.7-flash'];
            foreach ($geminiModels as $model) {
                try {
                    $response = Http::timeout(15)->post('https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $geminiApiKey, [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [['text' => $finalPrompt]]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.8,
                            'maxOutputTokens' => 800,
                        ]
                    ]);

                    if ($response->successful() && isset($response->json()['candidates'][0]['content']['parts'][0]['text'])) {
                        return trim($response->json()['candidates'][0]['content']['parts'][0]['text']);
                    }
                } catch (\Exception $e) {
                    Log::error("Gemini Error ($model): " . $e->getMessage());
                }
            }
        }

        return null;
    }

    private function sendFonnteMessage($token, $target, $message)
    {
        $token = trim($token);
        if (!$token) return false;

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
            ]);
            
            Log::info('Fonnte Send Response', $response->json());
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Fonnte Send Error: ' . $e->getMessage());
            return false;
        }
    }
}

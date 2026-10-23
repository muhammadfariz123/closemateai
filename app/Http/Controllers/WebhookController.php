<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Booking;
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
            return response()->json(['status' => false, 'message' => 'Invalid webhook secret: ' . $secret], 404);
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
                ['client_name' => $name, 'status' => 'New Inquiry', 'ai_reply_count' => 0, 'is_human_takeover' => false]
            );
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika tabel tidak ditemukan (biasanya karena migrasi belum jalan di server gratisan Render)
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            
            // Coba lagi setelah migrasi
            $chat = Chat::firstOrCreate(
                ['user_id' => $user->id, 'client_wa_number' => $sender],
                ['client_name' => $name, 'status' => 'New Inquiry', 'ai_reply_count' => 0, 'is_human_takeover' => false]
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

        \App\Models\SystemActivity::create([
            'user_id' => $user->id,
            'type' => 'Chat Masuk',
            'actor' => 'Sistem',
            'title' => 'Pesan baru dari ' . $name,
            'desc' => \Illuminate\Support\Str::limit($messageText, 40),
            'icon' => 'fa-regular fa-comment',
            'color' => 'blue'
        ]);

        // ============================================================
        // 4b. CEK APAKAH PESAN INI ADALAH FORM BOOKING WA
        // Jika ada header "Paket yang Dipilih" atau indikator form booking
        // ============================================================
        if ($this->isBookingForm($messageText)) {
            $parsed = $this->parseBookingForm($messageText);
            
            // Cek kelengkapan field WAJIB
            $missingFields = $this->checkRequiredFields($parsed);

            if (empty($missingFields)) {
                // Simpan booking ke database
                $booking = $this->saveWaBooking($user, $chat, $parsed, $sender, $name);
                
                // Kirim konfirmasi ke customer
                $confirmMsg = $this->buildBookingConfirmation($parsed, $booking);
                Message::create([
                    'chat_id' => $chat->id,
                    'sender' => 'ai',
                    'message' => $confirmMsg
                ]);
                if ($user->fonnte_token) {
                    $this->sendFonnteMessage($user->fonnte_token, $sender, $confirmMsg);
                }

                // Notifikasi ke admin/owner
                $ownerNumber = $user->notification_number ?: ($user->owner_whatsapp ?: ($user->business_wa_number ?: $user->wa_number));
                if ($ownerNumber && $user->fonnte_token) {
                    $adminMsg = "📋 *FORM BOOKING BARU MASUK!*\n\n"
                        . "Nama: {$name}\n"
                        . "WA: {$sender}\n"
                        . "Paket: " . ($parsed['package'] ?? '-') . "\n"
                        . "Tanggal: " . ($parsed['event_date_raw'] ?? '-') . "\n"
                        . "Pengantin: " . ($parsed['groom_name'] ?? '-') . " & " . ($parsed['bride_name'] ?? '-') . "\n\n"
                        . "✅ Data booking telah otomatis tersimpan di sistem.\nSilakan cek menu Booking & Operasional.";
                    $this->sendFonnteMessage($user->fonnte_token, $ownerNumber, $adminMsg);
                }

                \App\Models\SystemActivity::create([
                    'user_id' => $user->id,
                    'type' => 'Booking Baru',
                    'actor' => 'Sistem (WA Form)',
                    'title' => 'Booking WA dari ' . $name,
                    'desc' => 'Paket: ' . ($parsed['package'] ?? '-') . ' | ' . ($parsed['event_date_raw'] ?? '-'),
                    'icon' => 'fa-solid fa-calendar-check',
                    'color' => 'green'
                ]);

                return response()->json(['status' => true, 'message' => 'Booking form processed and saved']);
            } else {
                // Ada field wajib yang kosong - minta customer melengkapi
                $missingText = implode(', ', $missingFields);
                $replyMsg = "Terima kasih Kak sudah mengisi form booking! 🙏\n\n"
                    . "Tapi ada beberapa informasi *wajib* yang masih kosong:\n"
                    . "❗ *{$missingText}*\n\n"
                    . "Mohon lengkapi dan kirim ulang form bookingnya ya Kak, supaya jadwal Kakak bisa segera kami proses. 😊✨";
                
                Message::create([
                    'chat_id' => $chat->id,
                    'sender' => 'ai',
                    'message' => $replyMsg
                ]);
                if ($user->fonnte_token) {
                    $this->sendFonnteMessage($user->fonnte_token, $sender, $replyMsg);
                }

                return response()->json(['status' => true, 'message' => 'Booking form incomplete, asked for completion']);
            }
        }
        // ============================================================

        // 5. Cek apakah Human Takeover aktif
        if ($chat->is_human_takeover) {
            return response()->json(['status' => true, 'message' => 'Human takeover active, AI paused']);
        }

        // 6. Cek Batasan Balasan AI
        $aiMaxReplies = (int)($user->ai_max_replies ?? 3);
        $aiLimitEnabled = $user->ai_limit_enabled;
        $currentReplyCount = (int)$chat->ai_reply_count;

        if ($aiLimitEnabled && $currentReplyCount >= $aiMaxReplies) {
            $businessName = $user->business_name ?: ($user->name ?: 'Kami');

            // Pesan penutup pengalihan natural ke tim admin
            $handoverMsg = "Baik Kak, pertanyaan & detail obrolan kita sudah aku catat yaa ✨ Sebentar ya Kak, chat ini langsung aku sambungkan ke tim admin kami untuk bantu info lebih lengkap & proses selanjutnya. Mohon tunggu sebentar ya Kakak 🙏😊";

            Message::create(['chat_id' => $chat->id, 'sender' => 'ai', 'message' => $handoverMsg]);
            if ($user->fonnte_token) {
                $this->sendFonnteMessage($user->fonnte_token, $chat->client_wa_number, $handoverMsg);
            }

            // Set human takeover
            $chat->is_human_takeover = true;
            $chat->save();

            // Kirim notifikasi ke admin
            if ($user->notify_human_takeover) {
                $targetNumber = $user->notification_number ?: ($user->owner_whatsapp ?: ($user->business_wa_number ?: $user->wa_number));
                if ($targetNumber && $user->fonnte_token) {
                    $this->sendFonnteMessage($user->fonnte_token, $targetNumber, "📋 *HANDOVER KE ADMIN*\nAI sudah mencapai batas balasan ({$aiMaxReplies}x) dan percakapan dialihkan ke tim admin.\n\nNama: {$chat->client_name}\nNomor: {$chat->client_wa_number}\nSilakan standby di Live Chat Inbox.");
                }
            }

            return response()->json(['status' => true, 'message' => 'AI limit reached. Chat handed over to admin.']);
        }

        // 7. Panggil AI
        $aiReply = $this->generateAiReply($user, $chat, $messageText);
        $responseText = $aiReply ?? '';
        
        if ($aiReply) {
            $businessName = $user->business_name ?: ($user->name ?: 'Kami');

            // Deteksi apakah AI ingin mengirim form booking sesuai kondisi percakapan klien
            $formDetection = \App\Services\AiBrainService::extractBookingFormIntent($aiReply);
            $aiDecidedToSendForm = $formDetection['should_send_form'];
            $aiTextToSend = $formDetection['clean_reply'];

            // Cek Multiple Bubble HANYA untuk pesan percakapan ramah AI (agar form booking tidak terpotong)
            $bubbles = [$aiTextToSend];
            if ($user->ai_multi_bubble_enabled) {
                // Pisahkan pesan berdasarkan double newline
                $bubbles = explode("\n\n", $aiTextToSend);
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

            // Jika AI memutuskan kirim form booking (karena kondisi klien mantap booking/minta form):
            // Kirim form booking resmi secara UTUH per-baris sebagai bubble tersendiri!
            $formSentThisTurn = false;
            if ($aiDecidedToSendForm) {
                sleep(2);
                $formMsg = $this->getBookingFormText($businessName, $user);
                Message::create([
                    'chat_id' => $chat->id,
                    'sender' => 'ai',
                    'message' => $formMsg
                ]);
                if ($user->fonnte_token) {
                    $this->sendFonnteMessage($user->fonnte_token, $chat->client_wa_number, $formMsg);
                }
                $formSentThisTurn = true;
            }

            // Increment reply count
            $chat->ai_reply_count += 1;
            $chat->save();

            \App\Models\SystemActivity::create([
                'user_id' => $user->id,
                'type' => 'Balasan AI',
                'actor' => 'Sistem',
                'title' => 'AI membalas ' . $chat->client_name,
                'desc' => \Illuminate\Support\Str::limit($responseText, 40),
                'icon' => 'fa-solid fa-robot',
                'color' => 'purple'
            ]);

            // ============================================================
            // 7b. CEK APAKAH INI BALASAN TERAKHIR AI (tepat di batas limit)
            // ============================================================
            if ($aiLimitEnabled && $chat->ai_reply_count >= $aiMaxReplies) {
                // Jika pada giliran ini form booking BELUM dikirim, kirimkan form booking terlebih dahulu
                if (!$formSentThisTurn) {
                    sleep(2);
                    $formMsg = $this->getBookingFormText($businessName, $user);
                    Message::create(['chat_id' => $chat->id, 'sender' => 'ai', 'message' => $formMsg]);
                    if ($user->fonnte_token) {
                        $this->sendFonnteMessage($user->fonnte_token, $chat->client_wa_number, $formMsg);
                        sleep(2);
                    }
                } else {
                    sleep(2);
                }

                // Pesan penutup natural pengalihan ke tim admin
                $handoverMsg = "Baik Kak, pertanyaan & detail obrolan kita sudah aku catat yaa ✨ Sebentar ya Kak, chat ini langsung aku sambungkan ke tim admin kami untuk bantu info lebih lengkap & proses selanjutnya. Mohon tunggu sebentar ya Kakak 🙏😊";

                Message::create(['chat_id' => $chat->id, 'sender' => 'ai', 'message' => $handoverMsg]);
                if ($user->fonnte_token) {
                    $this->sendFonnteMessage($user->fonnte_token, $chat->client_wa_number, $handoverMsg);
                }

                // Set human takeover setelah limit tercapai
                $chat->is_human_takeover = true;
                $chat->save();

                if ($user->notify_human_takeover) {
                    $targetNumber = $user->notification_number ?: ($user->owner_whatsapp ?: ($user->business_wa_number ?: $user->wa_number));
                    if ($targetNumber && $user->fonnte_token) {
                        $this->sendFonnteMessage($user->fonnte_token, $targetNumber, "📋 *HANDOVER KE ADMIN*\nAI sudah selesai ({$aiMaxReplies}x balasan) dan chat sudah dialihkan ke admin.\n\nNama: {$chat->client_name}\nNomor: {$chat->client_wa_number}\nSilakan standby di Live Chat Inbox.");
                    }
                }
            }
            // ============================================================

        } else {
            // Jika AI gagal memberikan balasan, kirim pesan error agar tidak silent fail
            $this->sendFonnteMessage($user->fonnte_token, $chat->client_wa_number, "Mohon maaf, layanan AI kami sedang mengalami gangguan koneksi. (Sistem: Gagal memproses prompt)");
        }

        return response()->json(['status' => true, 'message' => 'Processed successfully']);
    }

    /**
     * Deteksi apakah pesan merupakan form booking WA.
     * Cek berdasarkan marker kunci seperti "Paket yang Dipilih" dan "Nama Mempelai"
     */
    private function isBookingForm(string $text): bool
    {
        $text = mb_strtolower($text);
        // Harus ada setidaknya 2 dari 3 marker ini untuk dikategorikan sebagai form booking
        $markers = [
            'paket yang dipilih',
            'nama mempelai',
            'form booking',
            'jam akad',
            'jam resepsi',
            'sortir foto',
            'nama orang tua',
        ];
        $found = 0;
        foreach ($markers as $marker) {
            if (str_contains($text, $marker)) {
                $found++;
            }
        }
        return $found >= 2;
    }

    /**
     * Parse form booking dari teks WA.
     * Mengekstrak semua field berdasarkan pola "Label : Nilai"
     */
    private function parseBookingForm(string $text): array
    {
        $data = [];

        // Definisi mapping: key => array of possible label variants (case insensitive)
        $fieldMap = [
            'package'         => ['paket yang dipilih', 'paket'],
            'groom_name'      => ['nama mempelai laki-laki', 'nama mempelai laki laki', 'mempelai laki-laki', 'mempelai laki laki'],
            'bride_name'      => ['nama mempelai perempuan', 'mempelai perempuan'],
            'parents_name'    => ['nama orang tua', 'orang tua', 'nama ortu'],
            'event_date_raw'  => ['tanggal acara', 'tanggal'],
            'akad_time'       => ['jam akad', 'akad'],
            'reception_time'  => ['jam resepsi', 'resepsi'],
            'event_address'   => ['alamat lengkap acara', 'alamat acara', 'alamat lengkap'],
            'maps_link'       => ['link maps lokasi', 'link maps', 'maps'],
            'client_wa'       => ['kontak whatsapp', 'whatsapp', 'no. whatsapp', 'nomor wa'],
            'client_email'    => ['email'],
            'client_instagram'=> ['instagram'],
            'vendor_mua'      => ['vendor mua', 'mua'],
            'vendor_henna'    => ['vendor henna', 'henna'],
            'vendor_busana'   => ['vendor busana', 'busana'],
            'vendor_hiburan'  => ['vendor hiburan', 'hiburan'],
            'vendor_mc'       => ['vendor mc', 'mc'],
            'vendor_dekorasi' => ['vendor dekorasi', 'dekorasi'],
            'vendor_wo'       => ['vendor wo', 'wo'],
            'special_request' => ['request khusus', 'request'],
            'sort_foto'       => ['pilihan sortir foto', 'sortir foto'],
            'total_dp'        => ['total dp dibayarkan', 'total dp'],
            'dp_amount'       => ['dp 20%', 'dp', 'uang muka'],
            'transport_cost'  => ['biaya transport', 'transport'],
        ];

        // Bagi teks per baris
        $lines = preg_split('/\r\n|\n|\r/', $text);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Cari pola "Label : Nilai" atau "Label: Nilai"
            if (!preg_match('/^(.+?)\s*:\s*(.*)$/', $line, $m)) continue;

            $rawLabel = trim($m[1]);
            $rawValue = trim($m[2]);
            $labelLower = mb_strtolower($rawLabel);

            // Bersihkan kata wajib/required, emoji & tanda baca dari label agar selalu match dengan varian
            $labelLower = preg_replace('/\b(wajib|required)\b/u', '', $labelLower);
            $labelLower = preg_replace('/[^\p{L}\p{N}\s\-]/u', '', $labelLower);
            $labelLower = trim(preg_replace('/\s+/', ' ', $labelLower));

            foreach ($fieldMap as $key => $variants) {
                foreach ($variants as $variant) {
                    if (str_contains($labelLower, $variant) || str_contains($variant, $labelLower)) {
                        // Jangan overwrite jika sudah ada nilai
                        if (empty($data[$key]) && $rawValue !== '') {
                            $data[$key] = $rawValue;
                        }
                        break 2;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Cek apakah semua field WAJIB sudah terisi
     */
    private function checkRequiredFields(array $parsed): array
    {
        $required = [
            'package'        => 'Paket yang Dipilih *(Wajib)*',
            'groom_name'     => 'Nama Mempelai Laki-laki *(Wajib)*',
            'bride_name'     => 'Nama Mempelai Perempuan *(Wajib)*',
            'parents_name'   => 'Nama Orang Tua *(Wajib)*',
            'event_date_raw' => 'Tanggal Acara *(Wajib)*',
            'client_email'   => 'Email *(Wajib)*',
            'client_instagram'=> 'Instagram *(Wajib)*',
            'total_dp'       => 'Total DP Dibayarkan *(Wajib)*',
        ];

        $missing = [];
        foreach ($required as $key => $label) {
            if ($key === 'total_dp') {
                if (empty($parsed['total_dp']) && empty($parsed['dp_amount'])) {
                    $missing[] = $label;
                }
            } elseif (empty($parsed[$key])) {
                $missing[] = $label;
            }
        }
        return $missing;
    }

    /**
     * Simpan data form booking WA ke database
     */
    private function saveWaBooking($user, $chat, array $parsed, string $senderWa, string $clientName): Booking
    {
        // Parse tanggal acara
        $eventDate = null;
        if (!empty($parsed['event_date_raw'])) {
            try {
                // Coba format otomatis
                $eventDate = \Carbon\Carbon::parse($parsed['event_date_raw'])->format('Y-m-d');
            } catch (\Throwable $e) {
                // Coba format Indonesia: "14 November 2025"
                try {
                    $bulanMap = [
                        'januari' => '01', 'februari' => '02', 'maret' => '03',
                        'april' => '04', 'mei' => '05', 'juni' => '06',
                        'juli' => '07', 'agustus' => '08', 'september' => '09',
                        'oktober' => '10', 'november' => '11', 'desember' => '12',
                    ];
                    $raw = mb_strtolower($parsed['event_date_raw']);
                    foreach ($bulanMap as $idr => $num) {
                        $raw = str_replace($idr, $num, $raw);
                    }
                    $parts = preg_split('/[\s\-\/]+/', trim($raw));
                    if (count($parts) === 3) {
                        $eventDate = sprintf('%04d-%02d-%02d', $parts[2], $parts[1], $parts[0]);
                    }
                } catch (\Throwable $e2) {
                    $eventDate = null;
                }
            }
        }

        // Parse nominal uang (hapus titik, koma, Rp, spasi)
        $cleanAmount = function ($val) {
            if (empty($val)) return 0;
            $cleaned = preg_replace('/[^0-9]/', '', (string)$val);
            return $cleaned ? (float)$cleaned : 0;
        };

        $dpAmount     = $cleanAmount($parsed['dp_amount'] ?? 0);
        $transportCost = $cleanAmount($parsed['transport_cost'] ?? 0);
        $totalDp      = $cleanAmount($parsed['total_dp'] ?? 0);
        
        // Fallback: total_dp = dp + transport jika tidak terisi
        if ($totalDp == 0 && ($dpAmount > 0 || $transportCost > 0)) {
            $totalDp = $dpAmount + $transportCost;
        }

        // Buat nama klien dari nama mempelai
        $groomName = $parsed['groom_name'] ?? '';
        $brideName = $parsed['bride_name'] ?? '';
        $bookingClientName = $groomName && $brideName
            ? "{$groomName} & {$brideName}"
            : ($clientName ?: $senderWa);

        $booking = Booking::create([
            'user_id'          => $user->id,
            'chat_id'          => $chat->id,
            'source'           => 'wa_form',
            'client_name'      => $bookingClientName,
            'groom_name'       => $groomName,
            'bride_name'       => $brideName,
            'parents_name'     => $parsed['parents_name'] ?? null,
            'client_wa_number' => $parsed['client_wa'] ?? $senderWa,
            'client_email'     => $parsed['client_email'] ?? null,
            'client_instagram' => $parsed['client_instagram'] ?? null,
            'event_date'       => $eventDate,
            'akad_time'        => $parsed['akad_time'] ?? null,
            'reception_time'   => $parsed['reception_time'] ?? null,
            'event_address'    => $parsed['event_address'] ?? null,
            'maps_link'        => $parsed['maps_link'] ?? null,
            'package_name'     => $parsed['package'] ?? null,
            'vendor_mua'       => $parsed['vendor_mua'] ?? null,
            'vendor_henna'     => $parsed['vendor_henna'] ?? null,
            'vendor_busana'    => $parsed['vendor_busana'] ?? null,
            'vendor_hiburan'   => $parsed['vendor_hiburan'] ?? null,
            'vendor_mc'        => $parsed['vendor_mc'] ?? null,
            'vendor_dekorasi'  => $parsed['vendor_dekorasi'] ?? null,
            'vendor_wo'        => $parsed['vendor_wo'] ?? null,
            'sort_foto'        => $parsed['sort_foto'] ?? null,
            'dp_amount'        => $dpAmount,
            'transport_cost'   => $transportCost,
            'total_dp'         => $totalDp,
            'paid_amount'      => $totalDp,
            'notes'            => $parsed['special_request'] ?? null,
            'payment_status'   => 'DP 1',
            'production_status'=> 'Pre-Event',
            'wa_form_filled_at'=> now(),
        ]);

        // Update status chat
        $chat->status = 'Booking';
        $chat->save();

        return $booking;
    }

    /**
     * Buat pesan konfirmasi booking untuk customer
     */
    /**
     * Generate teks form booking WA dengan format literal/hardcoded
     * agar tampilan di WA persis sesuai format yang diinginkan (per baris, rapi)
     */
    private function getBookingFormText(string $businessName, $user): string
    {
        return \App\Services\AiBrainService::getBookingFormTemplate($businessName, $user);
    }

    private function buildBookingConfirmation(array $parsed, Booking $booking): string
    {
        $groomName = $parsed['groom_name'] ?? '-';
        $brideName = $parsed['bride_name'] ?? '-';
        $eventDate = $parsed['event_date_raw'] ?? '-';
        $package   = $parsed['package'] ?? '-';
        $totalDp   = $parsed['total_dp'] ?? '-';

        return "✅ *Form Booking Diterima!*\n\n"
            . "Halo Kak, form booking untuk *{$groomName} & {$brideName}* sudah kami terima dan tersimpan di sistem kami ya! 🎉\n\n"
            . "📋 *Ringkasan Booking:*\n"
            . "• Paket      : {$package}\n"
            . "• Tanggal    : {$eventDate}\n"
            . "• Total DP   : {$totalDp}\n\n"
            . "📌 *Langkah Selanjutnya:*\n"
            . "Mohon segera kirimkan *bukti transfer DP* ke nomor ini ya Kak, supaya tanggal acara Kakak bisa langsung kami amankan di kalender. 📅\n\n"
            . "Rekening transfer:\n"
            . "• BNI: 0431063867 (a.n. Umi Salamah)\n"
            . "• BRI: 003201028500532 (a.n. Umi Salamah)\n\n"
            . "Terima kasih sudah mempercayakan momen spesial ke kami Kak! ✨🫶";
    }

    private function generateAiReply($user, $chat, $incomingMessage)
    {
        $clientName = $chat->client_name ?: 'Klien';
        $systemPrompt = \App\Services\AiBrainService::buildSystemPrompt($user, $clientName);

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
            $groqModels = ['openai/gpt-oss-120b', 'qwen/qwen3.8-27b', 'llama-3.1-70b-versatile', 'llama-3.1-8b-instant'];
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
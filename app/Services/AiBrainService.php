<?php

namespace App\Services;

use App\Models\KnowledgeFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AiBrainService
{
    /**
     * Default prompt templates according to Knowledge Base settings.
     */
    public static function getDefaultPrompt($type = 'casual'): string
    {
        switch ($type) {
            case 'friendly':
                return <<<PROMPT
[PROFIL VENDOR]
- Nama Bisnis/Vendor : [Nama Bisnis/Vendor]
- Alamat Lengkap    : [Alamat Lengkap]
- Jenis Jasa : [Jenis Jasa]

[PERAN & PERSONA]
Anda adalah [Nama AI], seorang Customer Success Partner yang sangat ramah dan ceria di [Nama Brand/Perusahaan]. Misi utama Anda adalah membantu setiap calon klien atau pelanggan menemukan solusi terbaik untuk kebutuhan mereka dengan cara yang menyenangkan, tanpa tekanan, dan terasa seperti berbincang dengan teman sendiri.

[GAYA BAHASA & TONE OF VOICE]
- Tone: Warm, welcoming, helpful, dan bernada positif.
- Sapaan Utama: Selalu sapa klien dengan "Kak [Nama Klien]" atau "Kakak".
- Gaya Bahasa: Gunakan bahasa Indonesia kasual yang santun dan mengalir alami. Hindari bahasa yang terlalu formal/kaku seperti "Anda" atau "Merupakan suatu kehormatan", gantilah dengan kata yang lebih ramah seperti "Aku", "Kita", atau "Kakak".

Gaya Tulisan & Format:
1. Pilihan Kata: Tetap gunakan ejaan kata yang jelas (hindari singkatan berlebihan seperti "yg", "utk", "dgn"), namun gunakan gaya tutur percakapan santai.
2. Emoji: Gunakan 1–3 emoji ekspresif di setiap pesan (misal: 😊, ✨, 🙌, 💖) untuk menunjukkan keramahan.
3. Struktur Pesan:
- Pembuka: Sapaan hangat + respon antusias atas pertanyaan klien.
- Isi: Penjelasan atau rekomendasi solutif yang ringkas (gunakan bullet points jika lebih dari 2 pilihan).
- Penutup: Pertanyaan terbuka yang ramah untuk memandu langkah berikutnya.

[ATURAN OPERASIONAL & ATURAN MAIN]
1. Fokus pada Kebutuhan Klien: Dengarkan dulu apa yang dicari klien, baru berikan saran yang paling cocok.
2. Menangani Harga/Pricelist:
Sampaikan harga dengan nada yang santai dan sebutkan benefit utama yang akan didapatkan klien agar terasa bernilai.
3. Menghadapi Klien Ragu: Berikan dorongan positif dan tawarkan bantuan tanpa bersikap memaksa (non-pushy).
4. Jika Kamu Ditanya AI atau Bukan:
Jangan mengaku-ngaku kamu AI atau asisten virtual, bilang saja memang kecepatan merespon sudah menjadi SOP wajib.
5. Sapaan Awal (Greeting): 
- Jika klien hanya menyapa ("Halo", "P", "Selamat pagi", "Permisi"): Balas sapaan sesuai dengan gaya bahasa.

[CONTOH CONVERSATION]
Klien: "Halo, boleh tahu pricelist layanannya?"
AI: "Halo Kak! Wah, senang banget Kakak tertarik dengan layanan kita 😊✨
Untuk pilihannya, kita punya beberapa paket favorit nih Kak:

• Paket Basic: Cocok banget kalau Kakak butuh solusi cepat dan simpel.

• Paket Pro: Paling pas buat Kakak yang mau hasil maksimal dengan fasilitas lengkap!

Boleh tau dulu nggak nih Kak, rencana kebutuhan Kakak seperti apa? Nanti aku bantu paskan dengan paket yang paling hemat dan sesuai buat Kakak! 🙌";

BATASAN & KEAMANAN (GUARDRAILS & HUMAN TAKEOVER)
- DILARANG KERAS berhalusinasi/membuat-buat harga, nama tempat, atau diskon yang tidak ada di Knowledge Base.
- NEGOSIASI & CUSTOM PACKAGE: Jika klien menawar harga atau meminta paket kustom di luar ketentuan, sampaikan bahwa detail kustom akan dikonfirmasikan lebih lanjut ke tim.
- Jika kamu di tanya AI atau bukan, jangan mengaku-ngaku kamu AI atau asisten virtual, bilang saja memang kecepatan merespon sudah menjadi SOP wajib.
PROMPT;

            case 'jaksel':
                return <<<PROMPT
[PERAN & PERSONA]

Anda adalah [Nama AI], seorang Customer Success Specialist di [Nama Brand/Perusahaan].

Tugas utama Anda adalah membimbing calon klien atau pelanggan dengan pendekatan yang modern, ramah, dan solutif. Anda hadir untuk memastikan experience klien dari awal sampai akhir berjalan super smooth dan no-hassle.

[GAYA BAHASA & TONE OF VOICE]

- Tone: Friendly, insightful, energetic, and solution-oriented.

- Sapaan Utama: "Kak [Nama Klien]" atau "Kak".

- Gaya Bahasa: Jaksel Style (Code-switching Indonesia - Inggris secara alami). 

  Gunakan istilah kekinian seperti: basically, literally, which is, make sense, honestly, preferable, align, vibe, seamless, no worries.

Gaya Tulisan & Format:

1. Diksi & Ejaan: Tetap gunakan ejaan kata yang rapi dan mudah dibaca (bukan alay), namun kombinasikan istilah Inggris sehari-hari secara fleksibel.

2. Emoji: Gunakan 1–2 emoji trendy per pesan (misal: ✨, 💡, 🙌, ☕, 🚀).

3. Struktur Pesan:

   - Pembuka: Respon antusias & validasi pertanyaan/concern klien.

   - Isi: Penjelasan ringkas atau opsi solutif (gunakan bullet points jika memberi rekomendasi).

   - Penutup: Call to Action (CTA) santai yang mengundang kelanjutan diskusi.

[ATURAN OPERASIONAL & GUARDRAILS]

1. Jika Informasi Tidak Ada di Knowledge Base:

   "Honestly, untuk detail spesifik yang ini aku perlu double-check ke tim internal dulu ya Kak. Which is biar informasinya tetep akurat. Boleh tunggu sebentar, nanti langsung aku update ke Kakak!"

2. Menangani Komplain / Concern Klien:

   "I really understand concern Kakak. That’s totally valid sih. Tapi no worries, let me handle this dan kita cari solusi paling seamless buat Kakak ya."

3. Menjelaskan Harga / Option:

   Jabarkan pilihan yang ada secara objektif lalu berikan rekomendasi terbaik yang paling 'makes sense' sesuai kebutuhan klien.

[FEW-SHOT EXAMPLES]

Contoh 1: Pertanyaan Pricelist / Service

Klien: "Halo, mau tanya pricelist untuk paket jasanya dong."

AI: "Hi Kak! Thank you udah reach out ke kita ya ✨

Basically, kita punya beberapa pilihan package yang bisa di-adjust sama kebutuhan Kakak:

• Option A (Starter): Ini cocok banget kalau Kakak butuh solusi yang simpel dan fast-execution.

• Option B (Pro Level): Which is ini paling recommended kalau Kakak mau hasil yang lebih komprehensif dan fully-assisted dari tim kita.

Btw, kalau boleh tau preference Kakak lebih ke arah mana nih? Nanti aku bantu rekomendasiin paket yang paling makes sense buat budget dan goal Kakak! 🙌"

Contoh 2: Pertanyaan Teknis / Kendala

Klien: "Ini aplikasinya kok agak lambat ya pas di-load?"

AI: "I see, sorry banget atas ketidaknyamanannya ya Kak 🥹

Honestly, hal ini biasanya terjadi karena kendala cache atau jaringan yang lagi diproses. But no worries, Kakak bisa coba beberapa quick-steps ini dulu:

1. Try to refresh atau clear cache browser Kakak.

2. Pastiin koneksi internetnya tetep stable.

Kalau udah dicoba tapi rasanya masih kurang seamless, kabarin aku ya Kak biar langsung aku bantu escalate ke tim tech kita. I'm here to help! ✨"

Jika kamu di tanya AI atau bukan, jangan mengaku-ngaku kamu AI atau asisten virtual, bilang saja memang kecepatan merespon sudah menjadi SOP wajib.

[SAPAAN AWAL (GREETING)]
- Jika klien hanya menyapa ("Halo", "P", "Selamat pagi", "Permisi"):
  Balas sapaan sesuai dengan gaya bahasa.
PROMPT;

            case 'casual':
            default:
                return <<<PROMPT
[PROFIL VENDOR]
- Nama Bisnis/Vendor : [Nama Bisnis/Vendor]
- Alamat Lengkap    : [Alamat Lengkap]
- Jenis Jasa : [Jenis Jasa]

[PERAN & PERSONA]
- Kamu adalah [NAMA KAMU] Admin CS resmi dari [NAMA BRAND ANDA] yang sedang membalas chat secara live langsung dari HP/Smartphone.
- Karakter: Ramah, santai, ekspresif khas anak muda/Gen-Z, sangat membantu, dan mengobrol seperti admin manusia asli (bukan bot otomatis).
- Gaya Ketikan: Alami khas obrolan WhatsApp, menggunakan gaya bahasa santai dan beberapa ketikan singkatan wajar agar terasa real & humanis.

[GAYA BAHASA & TONE OF VOICE]
1. Nada Bicara: Santai, ramah, hangat, dan mengalir seperti mengobrol dengan teman.
2. Kata Ganti Orang (WAJIB):
- Diri sendiri : "Aku"
- Calon klien : "Kak" atau "Kakak"
- DILARANG KERAS menggunakan kata: "Kamu", "Saya", "Anda", "Kami", "Mimin", atau "Bro/Sist".
3. Ketikan Singkatan WhatsApp (NATURAL CHAT ABBREVIATIONS):
- Gunakan singkatan-singkatan ringan khas ketikan HP secara alami:
* "yg" (yang)
* "utk" (untuk)
* "bgt" (banget)
* "udh" / "sdh" (udah/sudah)
* "tp" (tapi)
* "dr" (dari)
* "dgn" (dengan)
* "krn" (karena)
* "klo" (kalau)
* "gak" / "nggak" (tidak)
- LARANGAN SINGKATAN: DILARANG mengaburkan atau menyingkat Nama Klien, Nama Paket, Angka Harga, dan Tanggal Acara.

[GAYA TULISAN & FORMAT]
- Tulis dalam paragraf pendek (1-2 kalimat per paragraf).
- Gunakan huruf kecil di beberapa kata penyambung agar terkesan mengetik cepat di HP.
- Boleh menggunakan perpanjangan vokal ramah (contoh: "yaaa", "ajaah", "bgt").
- Sisipkan 1-2 emoji ramah per balasan (✨, 😊, 🫶, 🥹).

[SAPAAN AWAL (GREETING)]
- Jika klien hanya menyapa ("Halo", "P", "Selamat pagi", "Permisi"): Balas sapaan sesuai dengan gaya bahasa.

BATASAN & KEAMANAN (GUARDRAILS & HUMAN TAKEOVER)
- DILARANG KERAS berhalusinasi/membuat-buat harga, nama tempat, atau diskon yang tidak ada di Knowledge Base.
- NEGOSIASI & CUSTOM PACKAGE: Jika klien menawar harga atau meminta paket kustom di luar ketentuan, sampaikan bahwa kamu akan konsultasikan dengan tim terlebih dahulu.
- Jika kamu di tanya AI atau bukan, jangan mengaku-ngaku kamu AI atau asisten virtual, bilang saja memang kecepatan merespon sudah menjadi SOP wajib.
PROMPT;
        }
    }

    /**
     * Parse Google Calendar iCal (.ics) feed string into list of event strings.
     */
    public static function parseIcal(string $icalContent): array
    {
        $events = [];
        $lines = preg_split("/\r\n|\n|\r/", $icalContent);
        
        $insideEvent = false;
        $curSummary = '';
        $curDate = '';

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === 'BEGIN:VEVENT') {
                $insideEvent = true;
                $curSummary = '';
                $curDate = '';
                continue;
            }
            if ($line === 'END:VEVENT') {
                if ($insideEvent && $curDate) {
                    $events[] = [
                        'date' => $curDate,
                        'summary' => $curSummary ?: 'Acara Terisi'
                    ];
                }
                $insideEvent = false;
                continue;
            }

            if ($insideEvent) {
                if (preg_match('/^DTSTART(?:;VALUE=DATE)?:(\d{8})/i', $line, $m)) {
                    $raw = $m[1];
                    try {
                        $curDate = Carbon::createFromFormat('Ymd', $raw)->locale('id')->isoFormat('D MMMM Y');
                    } catch (\Throwable $e) {
                        $curDate = $raw;
                    }
                } elseif (preg_match('/^SUMMARY:(.+)$/i', $line, $m)) {
                    $curSummary = trim($m[1]);
                }
            }
        }

        return $events;
    }

    /**
     * Get parsed calendar events with 15-minute caching.
     */
    public static function getCalendarEvents(?string $calendarUrl): array
    {
        $calendarUrl = trim((string)$calendarUrl);
        if (!$calendarUrl || !filter_var($calendarUrl, FILTER_VALIDATE_URL)) {
            return [];
        }

        $cacheKey = 'gcal_events_' . md5($calendarUrl);
        return cache()->remember($cacheKey, 900, function () use ($calendarUrl) {
            try {
                $resp = Http::timeout(8)->get($calendarUrl);
                if ($resp->successful()) {
                    return self::parseIcal($resp->body());
                }
            } catch (\Throwable $e) {
                Log::warning("GCal iCal fetch error: " . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * Build the entire AI System Prompt combining all Knowledge Base elements:
     * 1. AI Personality & Prompt (Casual, Friendly, Jaksel or Custom)
     * 2. Vendor Profile
     * 3. Price List & Knowledge Base Document
     * 4. Price List Rules & Questions (Lead filter mode vs Quick mode)
     * 5. Google Calendar (Booked dates)
     * 6. FAQs (Official Q&A)
     * 7. Terms & Conditions
     */
    public static function buildSystemPrompt($user, string $clientName = 'Klien'): string
    {
        $businessName = $user->business_name ?: ($user->name ?: 'Vendor');
        $adminName = $user->name ?: 'Admin';
        $vendorAddress = $user->inv_vendor_address ?: ($user->business_address ?: 'Kombas purwokerto');
        $vendorCategory = $user->category ?: 'Jasa Dokumentasi & Fotografi';

        // 1. Base Prompt / Personality
        $activePersonality = $user->ai_personality_type ?: 'casual';
        $basePrompt = !empty(trim((string)$user->ai_prompt))
            ? $user->ai_prompt
            : self::getDefaultPrompt($activePersonality);

        // Replace placeholders
        $replacements = [
            '[NAMA KAMU]' => $adminName,
            '[Nama AI]' => $adminName,
            '[NAMA BRAND ANDA]' => $businessName,
            '[Nama Brand/Perusahaan]' => $businessName,
            '[Nama Klien]' => $clientName,
            '[Nama Bisnis/Vendor]' => $businessName,
            '[Alamat Lengkap]' => $vendorAddress,
            '[Jenis Jasa]' => $vendorCategory,
        ];
        $prompt = str_replace(array_keys($replacements), array_values($replacements), $basePrompt);

        // 2. Knowledge File (Price list & catalog details)
        $file = KnowledgeFile::where('user_id', $user->id)->latest()->first();
        $knowledgeText = $file && !empty(trim((string)$file->extracted_text))
            ? trim($file->extracted_text)
            : "Tidak ada dokumen rincian teks price list khusus. Jawab berdasarkan informasi umum paket {$businessName}.";

        // 3. Price List Links & Requirement Rules
        $links = json_decode((string)($user->price_list_links ?? '[]'), true);
        $formattedLinks = "";
        if (!empty($links) && is_array($links)) {
            foreach ($links as $link) {
                $title = !empty($link['title']) ? $link['title'] : 'Link Price List';
                $url = !empty($link['url']) ? $link['url'] : '#';
                $formattedLinks .= "• {$title}: {$url}\n";
            }
        } else {
            $formattedLinks = $file ? asset('storage/' . $file->file_path) : '[Link Price List belum diisi]';
        }

        $aiRequireData = $user->ai_require_data_before_price ?? true;
        $aiRequiredDataList = is_array($user->ai_required_data) ? $user->ai_required_data : [];
        $customQuestions = $user->ai_custom_questions ?? '';

        $priceListRule = "";
        if ($aiRequireData) {
            $requirementsText = implode(', ', $aiRequiredDataList);
            if ($customQuestions) {
                $customQuestionsStr = str_replace("\n", ", ", trim($customQuestions));
                $requirementsText .= ($requirementsText ? ', dan ' : '') . "($customQuestionsStr)";
            }
            if (empty($requirementsText)) {
                $requirementsText = "Nama Calon Pengantin, Tanggal Acara, dan Lokasi Acara";
            }

            $priceListRule = "ATURAN PENGIRIMAN PRICE LIST (FILTER LEADS):\n"
                . "- Jika klien meminta Price List (PL), JANGAN langsung memberikan file atau link price list.\n"
                . "- Kamu HARUS menggali informasi penting berikut terlebih dahulu dari klien: [{$requirementsText}].\n"
                . "- Tanyakan secara santun dan natural, contoh:\n"
                . "  \"Boleh banget Kak {$clientName}! ✨ Sebelum aku kirimkan detail pricelist lengkapnya, boleh bantu infokan {$requirementsText} ya Kak? Supaya aku bisa cek jadwal dan sesuaikan paket terbaiknya buat Kakak. 😊\"\n";
        } else {
            $priceListRule = "ATURAN PENGIRIMAN PRICE LIST (MODUS CEPAT):\n"
                . "- Jika klien meminta Price List (PL), BERIKAN link price list berikut secara langsung:\n{$formattedLinks}\n"
                . "- Di akhir balasan, tanyakan detail rencana acara (nama, tanggal, lokasi) dengan ramah untuk mengecek ketersediaan jadwal tim.\n";
        }

        // 4. Google Calendar Schedule
        $calendarSection = "";
        $gcalEvents = self::getCalendarEvents($user->google_calendar_url);
        if (!empty($gcalEvents)) {
            $calendarSection = "\n\n-- JADWAL TERISI / TANGGAL ACARA (GOOGLE CALENDAR) --\n"
                . "Berikut adalah daftar tanggal acara yang SUDAH TERISI / FULL BOOKED:\n";
            foreach (array_slice($gcalEvents, 0, 30) as $ev) {
                $calendarSection .= "• {$ev['date']}: {$ev['summary']} (TERISI / BOOKED)\n";
            }
            $calendarSection .= "\nATURAN KETERSEDIAAN JADWAL:\n"
                . "1. Jika klien menanyakan tanggal yang ADA di daftar di atas: Jawab dengan sopan dan ramah bahwa tanggal tersebut SUDAH TERISI / TIDAK BISA DIBOOKING.\n"
                . "2. Jika klien menanyakan tanggal yang TIDAK ADA di daftar di atas: Informasikan dengan antusias bahwa tanggal tersebut MASIH TERSEDIA (AVAILABLE), dan ajak untuk segera kunci tanggal/booking DP agar tidak diambil calon klien lain.\n";
        } else {
            $calendarSection = "\n\n-- JADWAL KALENDER --\n"
                . "Belum ada sinkronisasi jadwal terisi. Jika calon klien bertanya apakah tanggal tertentu tersedia, sampaikan bahwa tanggal tersebut bisa dicek dan ajak klien untuk menyampaikan detailnya.\n";
        }

        // 5. FAQs
        $faqsSection = "";
        $faqs = $user->faqs()->get();
        if ($faqs->isNotEmpty()) {
            $faqsSection = "\n\n-- TANYA JAWAB RESMI / FAQ --\n";
            foreach ($faqs as $faq) {
                $faqsSection .= "[Kategori: {$faq->category}]\n"
                    . "Q: {$faq->question}\n"
                    . "A: {$faq->answer}\n\n";
            }
            $faqsSection .= "ATURAN FAQ:\n"
                . "Jika pertanyaan klien berkaitan dengan salah satu FAQ di atas (seperti DP, reschedule, luar kota, lama pengiriman file, dsb.), prioritaskan jawaban resmi sesuai FAQ di atas.\n";
        }

        // 6. Terms & Conditions
        $termsSection = "";
        $termsText = is_array($user->default_terms) ? implode("\n", $user->default_terms) : (string)$user->default_terms;
        if (!empty(trim($termsText))) {
            $termsSection = "\n\n-- SYARAT & KETENTUAN (TERMS & CONDITIONS) --\n"
                . trim($termsText) . "\n"
                . "\nATURAN SYARAT & KETENTUAN:\n"
                . "Gunakan aturan resmi di atas jika klien menanyakan tentang sistem pembayaran, DP, pembatalan, pelunasan, atau hak atas file mentah.\n";
        }

        // 7. WA Booking Form Instructions (always injected)
        $bookingFormSection = "\n\n=== INSTRUKSI PENGIRIMAN FORM BOOKING VIA WA (SANGAT PENTING) ===\n"
            . "Kamu BISA & HARUS memicu pengiriman form booking kepada klien secara dinamis berdasarkan kondisi/balasan klien, TANPA harus menunggu batas giliran chat!\n"
            . "Picu pengiriman form booking KETIKA:\n"
            . "1. Klien sudah mantap / setuju dengan paket atau harga yang ditawarkan.\n"
            . "2. Klien secara eksplisit mengatakan ingin booking, pesan, amankan tanggal, atau deal.\n"
            . "3. Klien bertanya bagaimana cara booking, langkah selanjutnya, atau meminta form pendaftaran.\n"
            . "4. Klien menanyakan nomor rekening untuk transfer DP atau menyatakan siap membayar DP.\n\n"
            . "CARA KAMU MENGIRIMKAN FORM BOOKING:\n"
            . "- Tulis balasan ramah, hangat, dan antusias sesuai persona kamu (misal berterima kasih & memberikan panduan singkat).\n"
            . "- Di akhir balasanmu, CANTUMKAN TAG KHUSUS: [KIRIM_FORM_BOOKING]\n"
            . "- JANGAN menulis ulang baris-baris form atau merangkum field form menjadi kalimat panjang berjejer! Sistem kami akan secara otomatis melampirkan form booking resmi berstruktur rapi persis ke WhatsApp klien.\n"
            . "Contoh balasan kamu:\n"
            . "\"Alhamdulillah siap Kak! Senang sekali kami bisa menjadi bagian dari hari bahagia Kakak. 😊💖 Untuk penguncian jadwal dan pendataan, silakan lengkapi form booking berikut ya Kak:\n[KIRIM_FORM_BOOKING]\"\n\n";

        // Assemble Final System Prompt
        $systemPrompt = "KAMU ADALAH ASISTEN BOT WA CERDAS & HUMANIS DARI '{$businessName}'.\n\n"
            . "=== PANDUAN GAYA BICARA & PERSONA (OTAK AI) ===\n"
            . $prompt . "\n\n"
            . "=== KNOWLEDGE BASE (PRICE LIST & KATALOG) ===\n"
            . $knowledgeText . "\n\n"
            . "=== ATURAN PENGIRIMAN PRICE LIST ===\n"
            . $priceListRule
            . $calendarSection
            . $faqsSection
            . $termsSection
            . $bookingFormSection . "\n"
            . "=== INSTRUKSI AKHIR ===\n"
            . "1. Jawablah pesan dari Kak {$clientName} dengan gaya yang telah ditentukan di atas.\n"
            . "2. Tetap konsisten dengan persona dan JANGAN PERNAH melanggar batasan guardrails yang telah ditetapkan.\n"
            . "3. Jika klien sudah siap atau meminta booking, SEGERA cantumkan tag [KIRIM_FORM_BOOKING] di akhir pesanmu.\n";

        return $systemPrompt;
    }

    /**
     * Template Form Booking WA resmi yang berstruktur rapi per baris
     */
    public static function getBookingFormTemplate(string $businessName, $user = null): string
    {
        $bankLines = "BNI: 0431063867 (a.n. Umi Salamah)\nBRI: 003201028500532 (a.n. Umi Salamah)";
        if ($user && !empty($user->bank_name) && !empty($user->bank_account_number)) {
            $bankLines = "{$user->bank_name}: {$user->bank_account_number} (a.n. {$user->bank_account_name})";
        }

        $form  = "📋 *Form Booking {$businessName}*\n";
        $form .= "_(Mohon diisi dengan lengkap ya Kak. Kolom bertanda *(Wajib)* mohon dilengkapi, kolom lainnya boleh dikosongkan jika belum ada 😊)_\n";
        $form .= "\n";
        $form .= "Paket yang Dipilih *(Wajib)* :\n";
        $form .= "Nama Mempelai Laki-laki *(Wajib)* :\n";
        $form .= "Nama Mempelai Perempuan *(Wajib)* :\n";
        $form .= "Nama Orang Tua *(Wajib)* :\n";
        $form .= "Tanggal Acara *(Wajib)* :\n";
        $form .= "Jam Akad :\n";
        $form .= "Jam Resepsi :\n";
        $form .= "Alamat Lengkap Acara :\n";
        $form .= "Link Maps Lokasi :\n";
        $form .= "Kontak WhatsApp :\n";
        $form .= "Email *(Wajib)* :\n";
        $form .= "Instagram *(Wajib)* :\n";
        $form .= "Vendor MUA :\n";
        $form .= "Vendor Henna :\n";
        $form .= "Vendor Busana :\n";
        $form .= "Vendor Hiburan :\n";
        $form .= "Vendor MC :\n";
        $form .= "Vendor Dekorasi :\n";
        $form .= "Vendor WO :\n";
        $form .= "Request Khusus :\n";
        $form .= "Pilihan Sortir Foto : _(Sortir Sendiri / Diserahkan Tim {$businessName})_\n";
        $form .= "\n";
        $form .= "DP 20% :\n";
        $form .= "Biaya Transport :\n";
        $form .= "Total DP Dibayarkan *(Wajib)* :\n";
        $form .= "\n";
        $form .= "Transfer ke Rekening:\n";
        $form .= "{$bankLines}\n";
        $form .= "\n";
        $form .= "📌 *Catatan Penting:*\n";
        $form .= "\n";
        $form .= "• *Kolom Wajib:* Pastikan kolom bertanda *(Wajib)* terisi lengkap ya Kak, agar data booking bisa langsung tersimpan otomatis di sistem kami.\n";
        $form .= "\n";
        $form .= "• *Sortir Foto:* Jika request sortir sendiri, estimasi konfirmasi maksimal 3 hari setelah acara. Jika melebihi batas waktu tersebut, maka akan otomatis disortir oleh tim produksi {$businessName}.\n";
        $form .= "\n";
        $form .= "• *Booking Jadwal:* Setelah form ini diisi, mohon kirimkan bukti transfer DP ya Kak, supaya tanggalnya bisa langsung kami amankan. 📅💌\n";
        $form .= "\n";
        $form .= "✨ Terima kasih";

        return $form;
    }

    /**
     * Deteksi apakah AI ingin mengirim form booking (via tag [KIRIM_FORM_BOOKING] atau teks form buatan LLM)
     * dan pisahkan teks intro ramah AI agar terbebas dari draft form yang berantakan.
     */
    public static function extractBookingFormIntent(string $aiReply): array
    {
        $hasTag = str_contains($aiReply, '[KIRIM_FORM_BOOKING]');
        $lower = mb_strtolower($aiReply);

        $hasFormText = (str_contains($lower, 'form booking') && str_contains($lower, 'paket yang dipilih'))
            || (str_contains($lower, 'paket yang dipilih') && str_contains($lower, 'nama mempelai'));

        if (!$hasTag && !$hasFormText) {
            return [
                'should_send_form' => false,
                'clean_reply' => $aiReply
            ];
        }

        // Bersihkan tag
        $clean = str_ireplace('[KIRIM_FORM_BOOKING]', '', $aiReply);

        // Jika AI sempat menulis draft form booking sendiri, bersihkan agar tidak dobel / berantakan
        if (preg_match('/(\n\s*(?:📋|--+|\*?Form Booking\b[^\n]*\n|Paket yang Dipilih\s*:).*)/is', $clean, $matches, PREG_OFFSET_CAPTURE)) {
            $pos = $matches[1][1];
            $clean = mb_substr($clean, 0, $pos);
        } elseif (preg_match('/^\s*(?:📋|--+|\*?Form Booking|Paket yang Dipilih\s*:)/i', $clean)) {
            $clean = "Siap Kak! Berikut form booking-nya ya, mohon diisi dan dikirim kembali ke chat ini. 😊✨";
        }

        $clean = trim($clean);
        if ($clean === '') {
            $clean = "Siap Kak! Silakan lengkapi form booking berikut ya, nanti sistem kami langsung proses otomatis. 😊✨";
        }

        return [
            'should_send_form' => true,
            'clean_reply' => $clean
        ];
    }
}


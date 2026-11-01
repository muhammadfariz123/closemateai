<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    protected function getUser()
    {
        $user = auth()->user() ?? \App\Models\User::first();
        return $user ? $user->getEffectiveOwner() : null;
    }

    public static function getDefaultTemplates()
    {
        return [
            1 => 'Halo Kak {nama}, mau tanya apakah ada yang ingin didiskusikan lagi terkait paket {paket} untuk tanggal {tanggal}? 😊',
            2 => 'Halo Kak {nama}, berikut kami share link portofolio hasil dokumentasi & katalog paket kami: {link}. Boleh dicek-cek dulu ya Kak, jika ada konsep yang disukai atau ingin ditanyakan kami siap bantu! ✨',
            3 => 'Halo Kak {nama}, kami juga menyediakan penawaran paket kolaborasi spesial dengan vendor rekanan (MUA, Dekorasi, Wedding Organizer, maupun Entertainment) untuk melengkapi kebutuhan acara di tanggal {tanggal}. Apakah berkenan kami kirimkan detail paket kerjasamanya? 🙏😊',
        ];
    }

    protected function ensureSchemaReady()
    {
        static $ready = false;
        if ($ready) return;
        $ready = true;

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('users') && !\Illuminate\Support\Facades\Schema::hasColumn('users', 'fu_1_template')) {
                \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'fu_is_active')) $table->boolean('fu_is_active')->default(false);
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'fu_1_days')) $table->integer('fu_1_days')->default(1);
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'fu_1_template')) $table->text('fu_1_template')->nullable();
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'fu_2_days')) $table->integer('fu_2_days')->default(3);
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'fu_2_template')) $table->text('fu_2_template')->nullable();
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'fu_3_days')) $table->integer('fu_3_days')->default(7);
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'fu_3_template')) $table->text('fu_3_template')->nullable();
                });
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('FollowUp schema check notice: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $this->ensureSchemaReady();
        $user = $this->getUser();
        if (!$user) return redirect('/login');
        
        $defaultTemplates = self::getDefaultTemplates();

        $oldDefault2 = 'Halo Kak {nama}, sekadar mengabarkan bahwa slot tanggal {tanggal} sedang ada beberapa calon pengantin lain yang menanyakan. Apakah Kakak mau keep slot dulu?';
        $oldDefault3 = 'Halo Kak {nama}, apakah ada opsi penyesuaian budget atau isi paket {paket} yang ingin disesuaikan dengan kebutuhan Kakak?';

        $tpl1 = !empty($user->fu_1_template) ? $user->fu_1_template : $defaultTemplates[1];
        $tpl2 = (!empty($user->fu_2_template) && $user->fu_2_template !== $oldDefault2) ? $user->fu_2_template : $defaultTemplates[2];
        $tpl3 = (!empty($user->fu_3_template) && $user->fu_3_template !== $oldDefault3) ? $user->fu_3_template : $defaultTemplates[3];

        // Default values if null or matching old defaults
        $settings = [
            'fu_is_active' => $user->fu_is_active ?? 0,
            'fu_1_days' => $user->fu_1_days ?? 1,
            'fu_1_template' => $tpl1,
            'fu_2_days' => $user->fu_2_days ?? 3,
            'fu_2_template' => $tpl2,
            'fu_3_days' => $user->fu_3_days ?? 7,
            'fu_3_template' => $tpl3,
        ];

        // Fetch contacts for each follow-up stage
        $fu1_contacts = \App\Models\Chat::where('user_id', $user->id)->where('status', 'Follow Up')->get();
        $fu2_contacts = \App\Models\Chat::where('user_id', $user->id)->where('status', 'Done Follow-up 1')->get();
        $fu3_contacts = \App\Models\Chat::where('user_id', $user->id)->where('status', 'Done Follow-up 2')->get();
        
        $activities = \App\Models\SystemActivity::where('user_id', $user->id)->where('type', 'follow_up')->orderBy('created_at', 'desc')->take(20)->get();

        return view('followup', compact('settings', 'fu1_contacts', 'fu2_contacts', 'fu3_contacts', 'activities'));
    }

    public function saveSettings(Request $request)
    {
        $this->ensureSchemaReady();
        $user = $this->getUser();
        if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        
        $data = [];
        if ($request->has('fu_is_active')) {
            $data['fu_is_active'] = $request->fu_is_active ? 1 : 0;
        }
        if ($request->has('fu_1_days')) {
            $data['fu_1_days'] = (int) $request->fu_1_days;
        }
        if ($request->has('fu_1_template')) {
            $data['fu_1_template'] = $request->fu_1_template;
        }
        if ($request->has('fu_2_days')) {
            $data['fu_2_days'] = (int) $request->fu_2_days;
        }
        if ($request->has('fu_2_template')) {
            $data['fu_2_template'] = $request->fu_2_template;
        }
        if ($request->has('fu_3_days')) {
            $data['fu_3_days'] = (int) $request->fu_3_days;
        }
        if ($request->has('fu_3_template')) {
            $data['fu_3_template'] = $request->fu_3_template;
        }

        if (!empty($data)) {
            $user->update($data);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Template dan pengaturan follow-up berhasil disimpan'
        ]);
    }

    public function toggleSettings(Request $request)
    {
        $this->ensureSchemaReady();
        $user = $this->getUser();
        if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        
        $isActive = $request->fu_is_active ? 1 : 0;
        $user->update([
            'fu_is_active' => $isActive
        ]);

        $statusText = $isActive ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json(['success' => true, 'message' => "Sistem otomatis memperbarui: Follow-up otomatis $statusText"]);
    }

    public function processManual(Request $request, $level)
    {
        $this->ensureSchemaReady();
        $user = $this->getUser();
        if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);

        // If template was explicitly passed from frontend, save it immediately
        if ($request->filled('template')) {
            $user->update(["fu_{$level}_template" => $request->input('template')]);
        }

        if (!$user->fonnte_token) {
            return response()->json(['success' => false, 'message' => 'WhatsApp belum terhubung', 'wa_connected' => false]);
        }

        // Check Fonnte API real status
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->withHeaders([
                'Authorization' => $user->fonnte_token
            ])->post('https://api.fonnte.com/device');
            $data = $response->json();
            
            if (!isset($data['device_status']) || $data['device_status'] !== 'connect') {
                if ($user->wa_status == 'connected') {
                    $user->wa_status = 'disconnected';
                    $user->save();
                }
                return response()->json(['success' => false, 'message' => 'WhatsApp belum terhubung', 'wa_connected' => false]);
            } else {
                if ($user->wa_status != 'connected') {
                    $user->wa_status = 'connected';
                    $user->save();
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengecek status WhatsApp', 'wa_connected' => false]);
        }

        // If connected, proceed with sending
        $defaultTemplates = self::getDefaultTemplates();
        $status_from = '';
        $status_to = '';
        $label = '';
        $template = '';
        
        if ($level == 1) {
            $status_from = 'Follow Up';
            $status_to = 'Done Follow-up 1';
            $label = 'Follow Up';
            $template = $user->fu_1_template ?? $defaultTemplates[1];
        } else if ($level == 2) {
            $status_from = 'Done Follow-up 1';
            $status_to = 'Done Follow-up 2';
            $label = 'Done Follow-up 1 (Share Link)';
            $template = $user->fu_2_template ?? $defaultTemplates[2];
        } else if ($level == 3) {
            $status_from = 'Done Follow-up 2';
            $status_to = 'Done Follow-up 2'; 
            $label = 'Done Follow-up 2 (Penawaran Kerjasama)';
            $template = $user->fu_3_template ?? $defaultTemplates[3];
        }

        // If template was explicitly passed from the frontend (e.g. current edited content), save and use it
        if ($request->filled('template')) {
            $template = $request->input('template');
            $user->update(["fu_{$level}_template" => $template]);
        }

        if (empty($template)) {
            $template = $defaultTemplates[$level] ?? '';
        }

        // Resolve {link} if used in template
        $shareLink = '';
        if (str_contains($template, '{link}')) {
            if (!empty($user->price_list_links)) {
                $links = is_array($user->price_list_links) 
                    ? $user->price_list_links 
                    : json_decode((string)$user->price_list_links, true);
                if (!empty($links) && is_array($links)) {
                    $first = reset($links);
                    $shareLink = $first['url'] ?? ($first['link'] ?? '');
                }
            }
            if (empty($shareLink) && !empty($user->vendor_website)) {
                $shareLink = $user->vendor_website;
            }
        }

        $chats = \App\Models\Chat::where('user_id', $user->id)->where('status', $status_from)->get();
        $count = $chats->count();
        $sentCount = 0;
        
        if ($count > 0) {
            // Log main activity first
            $mainActivity = \App\Models\SystemActivity::create([
                'title' => "Proses follow-up label " . $label,
                'desc' => "Sedang memproses...",
                'type' => 'follow_up',
                'user_id' => $user->id,
                'actor' => 'Sistem',
                'icon' => 'fa-clock-rotate-left',
                'color' => 'purple'
            ]);

            foreach($chats as $chat) {
                $message = $template;
                $message = str_replace('{nama}', $chat->client_name ?: $chat->client_wa_number, $message);
                $message = str_replace('{paket}', $chat->package ?: '-', $message);
                $message = str_replace('{tanggal}', $chat->event_date ?: '-', $message);
                $message = str_replace('{link}', $shareLink ?: ($user->vendor_website ?? ''), $message);

                try {
                    \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => $user->fonnte_token
                    ])->asForm()->post('https://api.fonnte.com/send', [
                        'target' => $chat->client_wa_number,
                        'message' => $message,
                    ]);
                    $sentCount++;

                    // Log individual send
                    \App\Models\SystemActivity::create([
                        'title' => "Follow-up terkirim ke " . ($chat->client_name ?: $chat->client_wa_number),
                        'desc' => "Label \"$status_from\" -> \"$status_to\"",
                        'type' => 'follow_up',
                        'user_id' => $user->id,
                        'actor' => 'Sistem',
                        'icon' => 'fa-paper-plane',
                        'color' => 'orange'
                    ]);

                    if ($status_from != $status_to) {
                        $chat->update(['status' => $status_to]);
                    }
                } catch (\Exception $e) {
                    // skip this one
                }
            }
            
            $skipped = $count - $sentCount;
            $mainActivity->update([
                'desc' => "$sentCount pesan terkirim, $skipped dilewati dari $count kontak"
            ]);
        }

        return response()->json(['success' => true, 'message' => "$sentCount pesan diproses", 'wa_connected' => true]);
    }
}

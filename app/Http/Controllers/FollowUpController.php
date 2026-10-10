<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function index()
    {
        $user = auth()->user() ?? \App\Models\User::first();
        
        // Default values if null
        $settings = [
            'fu_is_active' => $user->fu_is_active ?? 0,
            'fu_1_days' => $user->fu_1_days ?? 1,
            'fu_1_template' => $user->fu_1_template ?? 'Halo Kak {nama}, mau tanya apakah ada yang ingin didiskusikan lagi terkait paket {paket} untuk tanggal {tanggal}? 😊',
            'fu_2_days' => $user->fu_2_days ?? 3,
            'fu_2_template' => $user->fu_2_template ?? 'Halo Kak {nama}, sekadar mengabarkan bahwa slot tanggal {tanggal} sedang ada beberapa calon pengantin lain yang menanyakan. Apakah Kakak mau keep slot dulu?',
            'fu_3_days' => $user->fu_3_days ?? 7,
            'fu_3_template' => $user->fu_3_template ?? 'Halo Kak {nama}, apakah ada opsi penyesuaian budget atau isi paket {paket} yang ingin disesuaikan dengan kebutuhan Kakak?',
        ];

        // Fetch contacts for each follow-up stage
        $fu1_contacts = \App\Models\Chat::where('status', 'Follow Up')->get();
        $fu2_contacts = \App\Models\Chat::where('status', 'Done Follow-up 1')->get();
        $fu3_contacts = \App\Models\Chat::where('status', 'Done Follow-up 2')->get();
        
        // Fetch follow up activity history (from system_activities or create a new table, or just filter system activities for type follow_up)
        // Let's use system_activities for now, or just chats updated recently
        $activities = \App\Models\SystemActivity::where('type', 'follow_up')->orderBy('created_at', 'desc')->take(20)->get();

        return view('followup', compact('settings', 'fu1_contacts', 'fu2_contacts', 'fu3_contacts', 'activities'));
    }

    public function saveSettings(Request $request)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        
        $user->update([
            'fu_is_active' => $request->fu_is_active ? 1 : 0,
            'fu_1_days' => $request->fu_1_days,
            'fu_1_template' => $request->fu_1_template,
            'fu_2_days' => $request->fu_2_days,
            'fu_2_template' => $request->fu_2_template,
            'fu_3_days' => $request->fu_3_days,
            'fu_3_template' => $request->fu_3_template,
        ]);

        return response()->json(['success' => true, 'message' => 'Pengaturan follow-up berjenjang disimpan']);
    }

    public function toggleSettings(Request $request)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        
        $isActive = $request->fu_is_active ? 1 : 0;
        $user->update([
            'fu_is_active' => $isActive
        ]);

        $statusText = $isActive ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json(['success' => true, 'message' => "Sistem otomatis memperbarui: Follow-up otomatis $statusText"]);
    }

    public function processManual(Request $request, $level)
    {
        $user = auth()->user() ?? \App\Models\User::first();
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
        $status_from = '';
        $status_to = '';
        $label = '';
        $template = '';
        
        if ($level == 1) {
            $status_from = 'Follow Up';
            $status_to = 'Done Follow-up 1';
            $label = 'Follow Up';
            $template = $user->fu_1_template ?? 'Halo Kak {nama}, mau tanya apakah ada yang ingin didiskusikan lagi terkait paket {paket} untuk tanggal {tanggal}? 😊';
        } else if ($level == 2) {
            $status_from = 'Done Follow-up 1';
            $status_to = 'Done Follow-up 2';
            $label = 'Done Follow-up 1';
            $template = $user->fu_2_template ?? 'Halo Kak {nama}, sekadar mengabarkan bahwa slot tanggal {tanggal} sedang ada beberapa calon pengantin lain yang menanyakan. Apakah Kakak mau keep slot dulu?';
        } else if ($level == 3) {
            $status_from = 'Done Follow-up 2';
            $status_to = 'Done Follow-up 2'; 
            $label = 'Done Follow-up 2';
            $template = $user->fu_3_template ?? 'Halo Kak {nama}, apakah ada opsi penyesuaian budget atau isi paket {paket} yang ingin disesuaikan dengan kebutuhan Kakak?';
        }

        $chats = \App\Models\Chat::where('status', $status_from)->get();
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

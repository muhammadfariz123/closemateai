<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessFollowUps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-follow-ups';
    protected $description = 'Proses follow up otomatis berbasis jadwal harian per status';

    public function handle()
    {
        $users = \App\Models\User::where('fu_is_active', 1)->whereNotNull('fonnte_token')->get();
        
        foreach ($users as $user) {
            // Check Fonnte connection
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
                    continue; // Skip this user if WA is disconnected
                }
            } catch (\Exception $e) {
                continue;
            }

            $levels = [
                1 => [
                    'from' => 'Follow Up',
                    'to' => 'Done Follow-up 1',
                    'days' => $user->fu_1_days ?? 1,
                    'template' => $user->fu_1_template ?? 'Halo Kak {nama}, mau tanya apakah ada yang ingin didiskusikan lagi terkait paket {paket} untuk tanggal {tanggal}? 😊'
                ],
                2 => [
                    'from' => 'Done Follow-up 1',
                    'to' => 'Done Follow-up 2',
                    'days' => $user->fu_2_days ?? 3,
                    'template' => $user->fu_2_template ?? 'Halo Kak {nama}, sekadar mengabarkan bahwa slot tanggal {tanggal} sedang ada beberapa calon pengantin lain yang menanyakan. Apakah Kakak mau keep slot dulu?'
                ],
                3 => [
                    'from' => 'Done Follow-up 2',
                    'to' => 'Done Follow-up 2',
                    'days' => $user->fu_3_days ?? 7,
                    'template' => $user->fu_3_template ?? 'Halo Kak {nama}, apakah ada opsi penyesuaian budget atau isi paket {paket} yang ingin disesuaikan dengan kebutuhan Kakak?'
                ]
            ];

            foreach ($levels as $level => $config) {
                // Get chats that are due for follow-up
                $dueChats = \App\Models\Chat::where('status', $config['from'])
                    ->where('updated_at', '<=', now()->subDays($config['days']))
                    ->get();
                
                $count = $dueChats->count();
                $sentCount = 0;

                if ($count > 0) {
                    $mainActivity = \App\Models\SystemActivity::create([
                        'title' => "Proses otomatis follow-up label " . $config['from'],
                        'desc' => "Sedang memproses otomatis...",
                        'type' => 'follow_up',
                        'user_id' => $user->id,
                        'actor' => 'Sistem',
                        'icon' => 'fa-clock-rotate-left',
                        'color' => 'purple'
                    ]);

                    foreach ($dueChats as $chat) {
                        $message = $config['template'];
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

                            \App\Models\SystemActivity::create([
                                'title' => "Follow-up otomatis terkirim ke " . ($chat->client_name ?: $chat->client_wa_number),
                                'desc' => "Label \"" . $config['from'] . "\" -> \"" . $config['to'] . "\"",
                                'type' => 'follow_up',
                                'user_id' => $user->id,
                                'actor' => 'Sistem',
                                'icon' => 'fa-paper-plane',
                                'color' => 'orange'
                            ]);

                            if ($config['from'] != $config['to']) {
                                $chat->update(['status' => $config['to']]);
                            } else {
                                // Touch updated_at so it doesn't trigger again immediately tomorrow
                                $chat->touch();
                            }
                        } catch (\Exception $e) {
                            // skip
                        }
                    }

                    $skipped = $count - $sentCount;
                    $mainActivity->update([
                        'desc' => "$sentCount pesan terkirim otomatis, $skipped dilewati dari $count kontak"
                    ]);
                }
            }
        }
    }
}

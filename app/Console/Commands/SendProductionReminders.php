<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendProductionReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'production:reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send automatic WA reminders for production tasks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting production reminder check...');

        $bookings = Booking::where(function($q) {
            $q->whereNull('production_status')
              ->orWhere('production_status', '!=', 'Selesai');
        })->get();

        $today = Carbon::today();
        $count = 0;

        foreach ($bookings as $booking) {
            if (!$booking->production_tracks || empty($booking->production_tracks)) {
                continue;
            }

            $user = User::find($booking->user_id);
            if (!$user || empty($user->fonnte_token)) {
                continue; // Cannot send WA without token
            }

            $tracks = $booking->production_tracks;
            $updated = false;

            foreach ($tracks as $index => &$track) {
                // If no reminder date, or already sent, or no PIC WA number, skip
                if (empty($track['reminder_date']) || !empty($track['reminder_sent']) || empty($track['pic_wa'])) {
                    continue;
                }

                $reminderDate = Carbon::parse($track['reminder_date'])->startOfDay();

                if ($reminderDate->equalTo($today)) {
                    // Time to send!
                    $this->sendReminder($user->fonnte_token, $booking, $track);
                    $track['reminder_sent'] = true;
                    $updated = true;
                    $count++;
                }
            }

            if ($updated) {
                $booking->production_tracks = $tracks;
                $booking->save();
            }
        }

        $this->info("Finished. Sent {$count} reminders.");
    }

    private function sendReminder($token, $booking, $track)
    {
        $messageTemplate = $track['reminder_message'] ?? '';
        
        $linkProject = $track['link'] ?? '';
        $defaultMessage = "Halo {pic},\n\nJangan lupa untuk mengerjakan task *{kategori}* (Stage: {stage}) pada project *{klien}*.\n\nTenggat waktu: {deadline}\nLink Project: {link}";
        
        if (trim($messageTemplate) === '') {
            $messageTemplate = $defaultMessage;
        }

        $kategori = $track['kategori'] ?? 'Lainnya';
        $stage = $track['stage'] ?? '-';
        $deadlineStr = !empty($track['deadline_task']) ? Carbon::parse($track['deadline_task'])->translatedFormat('d F Y') : '-';
        $picName = $track['pic_name'] ?? 'Tim';
        $clientName = $booking->client_name ?? 'Tanpa Nama';

        $finalMessage = str_replace(
            ['{pic}', '{klien}', '{kategori}', '{stage}', '{deadline}', '{link}'],
            [$picName, $clientName, $kategori, $stage, $deadlineStr, $linkProject],
            $messageTemplate
        );

        $target = $track['pic_wa'];
        
        // Fix target number format if starting with 0
        if (str_starts_with($target, '0')) {
            $target = '62' . substr($target, 1);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $finalMessage,
            ]);

            Log::info("Fonnte Reminder sent to {$target} for booking {$booking->id}", $response->json());
        } catch (\Exception $e) {
            Log::error("Fonnte Reminder Error: " . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

class PublicBookingController extends Controller
{
    /**
     * Show public booking form for clients
     */
    public function showForm($token = null)
    {
        $owner = null;
        if ($token) {
            $owner = User::where('public_booking_token', $token)->first();
        }
        if (!$owner) {
            $owner = User::where('role', 'owner')->first() ?: User::first();
        }

        if (!$owner) {
            abort(404, 'Studio tidak ditemukan.');
        }

        // Available packages
        $packages = [
            [
                'id' => 'pkg-wedding',
                'name' => 'Wedding Exclusive Package',
                'price' => 3500000,
                'desc' => 'Liputan Full Day Akad + Resepsi, 2 Fotografer, 1 Videografer Cinematic, Album Magnetic 20 Hal, All Master File Flashdisk',
            ],
            [
                'id' => 'pkg-prewed',
                'name' => 'Pre-Wedding Studio & Outdoor',
                'price' => 2000000,
                'desc' => '3 Jam Sesi Foto, 2 Kostum, 10 Retouched Photos, Cetak 16RS + Pigura Premium, Video Teaser 1 Menit',
            ],
            [
                'id' => 'pkg-wisuda',
                'name' => 'Wisuda & Family Studio',
                'price' => 750000,
                'desc' => '1 Jam Sesi Studio AC, Max 8 Orang, 5 Edited Photos, Cetak 12R + Frame Minimalis, Semua File Mentah',
            ],
            [
                'id' => 'pkg-personal',
                'name' => 'Personal / Maternity Shoot',
                'price' => 850000,
                'desc' => '1.5 Jam Sesi Foto, 2 Pilihan Background, 8 Edited Photos, Cetak 10R + Stand Frame',
            ],
        ];

        // Add-ons list
        $addons = [
            ['id' => 'addon-album', 'name' => 'Album Kolase 20x30 Hardcover', 'price' => 450000],
            ['id' => 'addon-sameday', 'name' => 'Fast Same Day Edit Video (Teaser)', 'price' => 350000],
            ['id' => 'addon-pigura', 'name' => 'Cetak 16RS + Pigura Gold/Hitam Minimalis', 'price' => 250000],
            ['id' => 'addon-drone', 'name' => 'Drone Aerial Coverage (Pilot & Footage)', 'price' => 600000],
        ];

        return view('public_booking', compact('owner', 'packages', 'addons', 'token'));
    }

    /**
     * Store public client booking submission
     */
    public function submitForm(Request $request, $token = null)
    {
        $owner = null;
        if ($token) {
            $owner = User::where('public_booking_token', $token)->first();
        }
        if (!$owner) {
            $owner = User::where('role', 'owner')->first() ?: User::first();
        }

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:50',
            'event_date' => 'required|date',
            'package_name' => 'required|string',
            'package_price' => 'required|numeric',
        ]);

        $uuid = (string) Str::uuid();
        $selectedAddons = $request->input('addons', []);
        $addonTotal = 0;
        if (is_array($selectedAddons)) {
            foreach ($selectedAddons as $a) {
                if (isset($a['price'])) $addonTotal += floatval($a['price']);
            }
        }

        $pkgPrice = floatval($validated['package_price']);
        $totalPrice = $pkgPrice + $addonTotal;

        $booking = Booking::create([
            'user_id' => $owner ? $owner->id : 1,
            'uuid' => $uuid,
            'client_name' => $validated['client_name'],
            'client_wa_number' => $validated['client_phone'],
            'client_email' => $request->input('client_email'),
            'client_address' => $request->input('location', 'Studio Foto Utama'),
            'package_name' => $validated['package_name'],
            'package_price' => $pkgPrice,
            'package_qty' => 1,
            'add_on_items' => is_array($selectedAddons) ? json_encode($selectedAddons) : $selectedAddons,
            'add_on_amount' => $addonTotal,
            'total_income' => $totalPrice,
            'paid_amount' => 0,
            'event_date' => $validated['event_date'],
            'start_time' => $request->input('event_time', '10:00'),
            'production_status' => 'Pre-Event',
            'payment_status' => 'Belum DP',
            'lead_source' => 'Website Link Form',
            'notes' => $request->input('notes'),
            'source' => 'Web Public Form',
        ]);

        // WhatsApp confirmation URL
        $ownerPhone = $owner ? ($owner->business_wa_number ?: $owner->wa_number) : '';
        $cleanPhone = preg_replace('/[^0-9]/', '', $ownerPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $waText = urlencode("Halo {$owner->business_name}, saya {$booking->client_name} baru saja mengisi Form Booking di Website untuk paket {$booking->package_name} tanggal {$booking->event_date->format('d M Y')}. Mohon konfirmasinya ya!");
        $waUrl = $cleanPhone ? "https://wa.me/{$cleanPhone}?text={$waText}" : null;

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dikirim! Tim studio kami akan segera menghubungi Anda.',
            'booking' => $booking,
            'wa_url' => $waUrl,
        ]);
    }
}


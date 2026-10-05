<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\User;

class SettingsController extends Controller
{
    private function getUser()
    {
        return Auth::user() ?? User::first();
    }

    public function index()
    {
        $user = $this->getUser();
        return view('settings', compact('user'));
    }

    public function saveToken(Request $request)
    {
        $request->validate([
            'fonnte_token' => 'required|string'
        ]);

        $user = $this->getUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        $user->fonnte_token = $request->fonnte_token;
        
        // Generate a webhook secret if not exists
        if (!$user->webhook_secret) {
            $user->webhook_secret = Str::random(32);
        }
        
        $user->save();

        return response()->json(['success' => true, 'message' => 'Token tersimpan']);
    }

    public function getDevice()
    {
        $user = $this->getUser();
        if (!$user || !$user->fonnte_token) {
            return response()->json(['success' => false, 'message' => 'Token not found']);
        }

        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => $user->fonnte_token
            ])->post('https://api.fonnte.com/device');

            $data = $response->json();

            if (isset($data['status']) && $data['status'] == false && isset($data['reason']) && str_contains(strtolower($data['reason']), 'rate limit')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak permintaan ke Fonnte. Silakan tunggu 1 menit lalu coba klik "Tampilkan QR" lagi.',
                    'data' => $data
                ]);
            }

            if (isset($data['device_status']) && $data['device_status'] == 'connect') {
                $user->wa_status = 'connected';
                if (isset($data['device'])) {
                    $user->wa_number = $data['device'];
                }
                $user->save();
            } elseif (request()->query('get_qr') == '1') {
                $qrResponse = Http::timeout(10)->withHeaders([
                    'Authorization' => $user->fonnte_token
                ])->post('https://api.fonnte.com/qr');
                
                $qrData = $qrResponse->json();
                if (isset($qrData['url'])) {
                    $data['qr'] = $qrData['url'];
                } elseif (isset($qrData['status']) && $qrData['status'] == false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengambil QR dari Fonnte: ' . ($qrData['reason'] ?? 'Unknown error')
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'cURL error 28') || str_contains($msg, 'timeout')) {
                $msg = 'Koneksi ke Fonnte timeout (server Fonnte sedang sibuk). Silakan tunggu sebentar dan coba lagi.';
            }
            return response()->json(['success' => false, 'message' => $msg]);
        }
    }

    public function disconnect()
    {
        $user = $this->getUser();
        if (!$user || !$user->fonnte_token) {
            return response()->json(['success' => false, 'message' => 'Token not found']);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $user->fonnte_token
            ])->post('https://api.fonnte.com/disconnect');

            $user->wa_status = 'disconnected';
            $user->wa_number = null;
            // $user->fonnte_token = null; // optional: keep token, just disconnect
            $user->save();

            return response()->json(['success' => true, 'message' => 'Disconnected successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

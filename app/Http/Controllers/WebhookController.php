<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request, $secret)
    {
        // Cari user berdasarkan webhook_secret
        $user = User::where('webhook_secret', $secret)->first();
        
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Invalid webhook secret'], 404);
        }

        // Fonnte melakukan pengecekan URL menggunakan method GET atau POST kosong
        if ($request->isMethod('get')) {
            return response()->json(['status' => true, 'message' => 'Webhook URL is active']);
        }

        // Fonnte mengirim data via POST ketika ada pesan masuk
        $data = $request->all();
        
        if (empty($data)) {
            return response()->json(['status' => true, 'message' => 'Webhook ready']);
        }

        // Catat payload di log agar mudah di-debug nantinya
        Log::info('Fonnte Webhook Received for User ID ' . $user->id, $data);

        // TODO: Panggil AI di sini untuk membalas pesan masuk
        // ...
        
        return response()->json(['status' => true, 'message' => 'Message received']);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function getChats()
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) return response()->json([]);

        $chats = Chat::with(['messages' => function($query) {
            $query->orderBy('created_at', 'desc')->take(1);
        }])->where('user_id', $user->id)
          ->orderBy('updated_at', 'desc')
          ->get();

        return response()->json($chats);
    }

    public function getMessages($chatId)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) return response()->json([]);

        $chat = Chat::where('id', $chatId)->where('user_id', $user->id)->firstOrFail();
        $messages = Message::where('chat_id', $chat->id)->orderBy('created_at', 'asc')->get();

        return response()->json([
            'chat' => $chat,
            'messages' => $messages
        ]);
    }

    public function sendMessage(Request $request, $chatId)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) return response()->json(['success' => false]);

        $chat = Chat::where('id', $chatId)->where('user_id', $user->id)->firstOrFail();
        $messageText = $request->input('message');

        // Simpan ke DB
        $message = Message::create([
            'chat_id' => $chat->id,
            'sender' => 'admin',
            'message' => $messageText
        ]);

        // Kirim ke WhatsApp via Fonnte
        if ($user->fonnte_token) {
            try {
                Http::withHeaders([
                    'Authorization' => $user->fonnte_token,
                ])->asForm()->post('https://api.fonnte.com/send', [
                    'target' => $chat->client_wa_number,
                    'message' => $messageText,
                    'countryCode' => '62',
                ]);
            } catch (\Exception $e) {
                Log::error('Fonnte Send Error (Admin): ' . $e->getMessage());
            }
        }

        // Kalau admin balas, otomatis status jadi human takeover agar AI tidak membalas lagi
        if (!$chat->is_human_takeover) {
            $chat->is_human_takeover = true;
            $chat->save();
        }

        return response()->json(['success' => true, 'message' => $message, 'is_human_takeover' => $chat->is_human_takeover]);
    }

    public function toggleTakeover(Request $request, $chatId)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) return response()->json(['success' => false]);

        $chat = Chat::where('id', $chatId)->where('user_id', $user->id)->firstOrFail();
        $chat->is_human_takeover = $request->input('is_takeover', false);
        
        // Reset ai_reply_count if AI is reactivated
        if (!$chat->is_human_takeover) {
            $chat->ai_reply_count = 0;
        }
        
        $chat->save();

        return response()->json(['success' => true, 'chat' => $chat]);
    }

    public function clearChatHistory(Request $request, $chatId)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) return response()->json(['success' => false]);

        $chat = Chat::where('id', $chatId)->where('user_id', $user->id)->firstOrFail();
        
        // Delete all messages associated with this chat
        Message::where('chat_id', $chat->id)->delete();
        
        // Reset chat status
        $chat->ai_reply_count = 0;
        $chat->is_human_takeover = false;
        $chat->save();

        return response()->json(['success' => true, 'message' => 'Riwayat chat berhasil dihapus. AI akan menganggap ini sebagai percakapan baru.']);
    }

    public function setHandler(Request $request, $chatId)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) return response()->json(['success' => false]);

        $chat = Chat::where('id', $chatId)->where('user_id', $user->id)->firstOrFail();
        $chat->handled_by = $request->input('handler', null);
        $chat->save();

        return response()->json(['success' => true, 'chat' => $chat]);
    }

    public function editChat(Request $request, $chatId)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) return response()->json(['success' => false]);

        $chat = Chat::where('id', $chatId)->where('user_id', $user->id)->firstOrFail();
        
        $fields = ['client_name', 'quick_notes', 'status', 'event_date', 'location', 'package', 'lead_score', 'client_wa_number'];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $chat->{$field} = $request->input($field);
            }
        }
        
        $chat->save();

        return response()->json(['success' => true, 'chat' => $chat]);
    }
    
    public function deleteChat(Request $request, $chatId)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) return response()->json(['success' => false]);
        
        $chat = Chat::where('id', $chatId)->where('user_id', $user->id)->firstOrFail();
        
        // Messages will be deleted via cascade or we can delete manually
        \App\Models\Message::where('chat_id', $chat->id)->delete();
        $chat->delete();
        
        return response()->json(['success' => true, 'message' => 'Lead dihapus']);
    }
}

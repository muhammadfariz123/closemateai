<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function stats()
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) return response()->json(['error' => 'Not found'], 404);

        $today = Carbon::today();
        
        // Stats 1: Total Conversations Today
        $totalConversations = Chat::where('user_id', $user->id)
            ->whereDate('updated_at', $today)
            ->count();
            
        // Stats 2: Active Hot Leads
        $activeHotLeads = Chat::where('user_id', $user->id)
            ->where('lead_score', 'Hot')
            ->count();
            
        // Stats 3: AI Resolution Rate
        // Simplified: (Chats without human takeover / Total Chats) * 100
        $totalChatsAll = Chat::where('user_id', $user->id)->count();
        $aiResolvedChats = Chat::where('user_id', $user->id)
            ->where('is_human_takeover', false)
            ->count();
            
        $resolutionRate = $totalChatsAll > 0 ? round(($aiResolvedChats / $totalChatsAll) * 100) : 0;
        
        // Stats 4: Pending Human Takeovers
        $pendingTakeovers = Chat::where('user_id', $user->id)
            ->where('is_human_takeover', true)
            ->count();
            
        // WhatsApp Gateway Stats
        $messagesSentToday = Message::whereHas('chat', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('sender_type', 'ai')
            ->whereDate('created_at', $today)
            ->count();
            
        // Quick Activity Feed
        // We will fetch recent messages and new chats as feed items
        $recentMessages = Message::with('chat')
            ->whereHas('chat', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($msg) {
                if ($msg->sender_type == 'ai') {
                    $title = "AI membalas " . ($msg->chat->client_name ?: $msg->chat->client_wa_number);
                    $icon = "fa-solid fa-robot";
                    $color = "var(--primary)";
                } else {
                    $title = "Pesan baru dari " . ($msg->chat->client_name ?: $msg->chat->client_wa_number);
                    $icon = "fa-regular fa-comment";
                    $color = "#009EF7";
                }
                return [
                    'title' => $title,
                    'desc' => \Illuminate\Support\Str::limit($msg->content, 40),
                    'icon' => $icon,
                    'color' => $color,
                    'time' => $msg->created_at->diffForHumans(),
                    'timestamp' => $msg->created_at->timestamp
                ];
            });
            
        $recentChats = Chat::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($chat) {
                return [
                    'title' => "Lead baru: " . ($chat->client_name ?: $chat->client_wa_number),
                    'desc' => $chat->client_wa_number,
                    'icon' => "fa-solid fa-user-plus",
                    'color' => "#50cd89",
                    'time' => $chat->created_at->diffForHumans(),
                    'timestamp' => $chat->created_at->timestamp
                ];
            });
            
        $feed = collect($recentMessages)->concat($recentChats)->sortByDesc('timestamp')->take(10)->values();

        return response()->json([
            'total_conversations' => $totalConversations,
            'active_hot_leads' => $activeHotLeads,
            'resolution_rate' => $resolutionRate,
            'total_chats_all' => $totalChatsAll,
            'ai_resolved_chats' => $aiResolvedChats,
            'pending_takeovers' => $pendingTakeovers,
            'messages_sent_today' => $messagesSentToday,
            'feed' => $feed
        ]);
    }
}

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
            
        try {
            $activities = \App\Models\SystemActivity::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $activities = \App\Models\SystemActivity::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        }

        $activities = $activities->map(function($act) {
                // Map to frontend feed format
                $icon = $act->icon ?: 'fa-solid fa-bell';
                $color = $act->color ?: 'purple';
                if ($color == 'purple') $colorCode = 'var(--primary)';
                else if ($color == 'green') $colorCode = '#50cd89';
                else if ($color == 'orange') $colorCode = '#ffc700';
                else if ($color == 'blue') $colorCode = '#009EF7';
                else $colorCode = '#a1a5b7';
                
                return [
                    'title' => $act->title,
                    'desc' => $act->desc,
                    'icon' => $icon,
                    'color' => $colorCode,
                    'time' => $act->created_at->diffForHumans(),
                    'timestamp' => $act->created_at->timestamp
                ];
            });
            
        $feed = collect($activities)->values();

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

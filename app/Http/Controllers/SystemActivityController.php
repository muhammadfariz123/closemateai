<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemActivity;

class SystemActivityController extends Controller
{
    protected function getUser()
    {
        $user = auth()->user() ?? \App\Models\User::first();
        return $user ? $user->getEffectiveOwner() : null;
    }

    public function getActivities()
    {
        $user = $this->getUser();
        if (!$user) return response()->json([]);

        try {
            $activities = SystemActivity::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(100)
                ->get();
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $activities = SystemActivity::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(100)
                ->get();
        }

        $ownerName = $user->business_name ?: ($user->name ?: 'Owner');

        $activities->transform(function ($item) use ($ownerName) {
            $desc = $item->desc ?: ($item->description ?? '');
            $title = $item->title;
            $actor = $item->actor;
            $icon = $item->icon;
            $color = $item->color;

            if (empty($actor)) {
                $actor = ($item->type === 'chat_in' || str_contains((string)$item->type, 'follow_up')) ? 'Sistem' : $ownerName;
            }

            if (empty($title)) {
                switch ($item->type) {
                    case 'team_invited':
                        $title = 'Undangan Admin CS';
                        $icon = $icon ?: 'fa-paper-plane';
                        $color = $color ?: 'purple';
                        break;
                    case 'team_activated':
                        $title = 'Admin CS Bergabung';
                        $icon = $icon ?: 'fa-user-check';
                        $color = $color ?: 'green';
                        break;
                    case 'team_reset_password':
                        $title = 'Reset Password Admin CS';
                        $icon = $icon ?: 'fa-key';
                        $color = $color ?: 'orange';
                        break;
                    case 'chat_in':
                        $title = 'Chat Masuk';
                        $icon = $icon ?: 'fa-comment-dots';
                        $color = $color ?: 'blue';
                        break;
                    case 'Balasan Admin':
                    case 'chat_out':
                        $title = 'Balasan Admin';
                        $icon = $icon ?: 'fa-user-tie';
                        $color = $color ?: 'green';
                        break;
                    case 'Balasan AI':
                        $title = 'Balasan AI';
                        $icon = $icon ?: 'fa-robot';
                        $color = $color ?: 'purple';
                        break;
                    case 'invoice_created':
                        $title = 'Invoice Baru Dibuat';
                        $icon = $icon ?: 'fa-file-invoice-dollar';
                        $color = $color ?: 'green';
                        break;
                    case 'quotation_created':
                        $title = 'Quotation Baru Dibuat';
                        $icon = $icon ?: 'fa-file-lines';
                        $color = $color ?: 'blue';
                        break;
                    case 'follow_up':
                        $title = 'Follow-Up Otomatis';
                        $icon = $icon ?: 'fa-clock-rotate-left';
                        $color = $color ?: 'purple';
                        break;
                    case 'Lead':
                        $title = 'Aktivitas Lead';
                        $icon = $icon ?: 'fa-bolt';
                        $color = $color ?: 'purple';
                        break;
                    default:
                        $title = ucwords(str_replace('_', ' ', $item->type ?: 'Aktivitas Sistem'));
                        $icon = $icon ?: 'fa-bolt';
                        $color = $color ?: 'purple';
                        break;
                }
            }

            if (empty($desc)) {
                $desc = $title;
            }

            $item->title = $title;
            $item->desc = $desc;
            $item->actor = $actor;
            $item->icon = $icon ?: 'fa-bolt';
            $item->color = $color ?: 'purple';

            return $item;
        });

        return response()->json($activities);
    }

    public function addActivity(Request $request)
    {
        $user = $this->getUser();
        if (!$user) return response()->json(['success' => false], 401);

        $currentUser = auth()->user() ?: $user;
        $actor = $request->input('actor');
        if ($actor !== 'Sistem') {
            $actor = $currentUser->name ?: ($user->business_name ?: 'Owner');
        }

        try {
            $activity = SystemActivity::create([
                'user_id' => $user->id,
                'type' => $request->input('type'),
                'actor' => $actor,
                'title' => $request->input('title'),
                'desc' => $request->input('desc'),
                'icon' => $request->input('icon'),
                'color' => $request->input('color')
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $activity = SystemActivity::create([
                'user_id' => $user->id,
                'type' => $request->input('type'),
                'actor' => $actor,
                'title' => $request->input('title'),
                'desc' => $request->input('desc'),
                'icon' => $request->input('icon'),
                'color' => $request->input('color')
            ]);
        }

        return response()->json(['success' => true, 'activity' => $activity]);
    }
}

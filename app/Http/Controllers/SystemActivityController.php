<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemActivity;

class SystemActivityController extends Controller
{
    public function getActivities()
    {
        $user = auth()->user();
        if (!$user) return response()->json([]);

        try {
            $activities = SystemActivity::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get();
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $activities = SystemActivity::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get();
        }

        return response()->json($activities);
    }

    public function addActivity(Request $request)
    {
        $user = auth()->user();
        if (!$user) return response()->json(['success' => false], 401);

        $actor = $request->input('actor');
        if ($actor !== 'Sistem') {
            $actor = $user->business_name ?: 'Owner';
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

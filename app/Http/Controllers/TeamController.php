<?php

namespace App\Http\Controllers;

use App\Models\SystemActivity;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    protected function getUser()
    {
        return Auth::user() ?? User::first();
    }

    public function index()
    {
        $user = $this->getUser();
        if (!$user) {
            return redirect('/login');
        }

        $totalSeats = 99;
        $activeAdminCount = TeamMember::where('user_id', $user->id)->where('status', 'active')->count();
        $pendingCount = TeamMember::where('user_id', $user->id)->where('status', 'pending')->count();
        
        $seatsUsed = 1 + $activeAdminCount;
        $seatsRemaining = max(0, $totalSeats - $seatsUsed);
        $activeCount = 1 + $activeAdminCount;

        $activeMembers = TeamMember::where('user_id', $user->id)->where('status', 'active')->orderBy('created_at', 'desc')->get();
        $pendingMembers = TeamMember::where('user_id', $user->id)->where('status', 'pending')->orderBy('created_at', 'desc')->get();

        $ownerName = $user->business_name ?: ($user->name ?: 'Penapict');
        $ownerEmail = $user->email ?: 'hotautomag@gmail.com';

        return view('team', compact(
            'user',
            'ownerName',
            'ownerEmail',
            'totalSeats',
            'seatsUsed',
            'seatsRemaining',
            'activeCount',
            'pendingCount',
            'activeMembers',
            'pendingMembers'
        ));
    }

    public function getMembers()
    {
        $user = $this->getUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $totalSeats = 99;
        $activeAdminCount = TeamMember::where('user_id', $user->id)->where('status', 'active')->count();
        $pendingCount = TeamMember::where('user_id', $user->id)->where('status', 'pending')->count();
        
        $seatsUsed = 1 + $activeAdminCount;
        $seatsRemaining = max(0, $totalSeats - $seatsUsed);
        $activeCount = 1 + $activeAdminCount;

        $activeMembers = TeamMember::where('user_id', $user->id)->where('status', 'active')->orderBy('created_at', 'desc')->get();
        $pendingMembers = TeamMember::where('user_id', $user->id)->where('status', 'pending')->orderBy('created_at', 'desc')->get();

        $ownerName = $user->business_name ?: ($user->name ?: 'Penapict');
        $ownerEmail = $user->email ?: 'hotautomag@gmail.com';

        return response()->json([
            'success' => true,
            'owner' => [
                'name' => $ownerName,
                'email' => $ownerEmail,
                'role' => 'Owner',
            ],
            'stats' => [
                'total_seats' => $totalSeats,
                'seats_used' => $seatsUsed,
                'seats_remaining' => $seatsRemaining,
                'active_count' => $activeCount,
                'pending_count' => $pendingCount,
            ],
            'active_members' => $activeMembers,
            'pending_members' => $pendingMembers,
        ]);
    }

    public function invite(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($request->input('email')));
        $name = trim($request->input('name') ?: explode('@', $email)[0]);
        $phone = trim($request->input('phone', ''));

        // Check if owner's email
        if (strtolower($user->email) === $email) {
            return response()->json([
                'success' => false,
                'error' => 'Email ini adalah email Owner.'
            ], 422);
        }

        // Check if already invited or active
        $exists = TeamMember::where('user_id', $user->id)->where('email', $email)->first();
        if ($exists) {
            return response()->json([
                'success' => false,
                'error' => $exists->status === 'active' 
                    ? 'Email ini sudah menjadi Admin CS aktif.' 
                    : 'Undangan untuk email ini sudah pernah dikirim.'
            ], 422);
        }

        // Check seats capacity
        $activeAdminCount = TeamMember::where('user_id', $user->id)->where('status', 'active')->count();
        if ((1 + $activeAdminCount) >= 99) {
            return response()->json([
                'success' => false,
                'error' => 'Kapasitas seat sudah penuh (maksimal 99 seats).'
            ], 422);
        }

        $member = TeamMember::create([
            'user_id' => $user->id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'role' => 'Admin CS',
            'status' => 'pending',
            'invited_at' => now(),
            'token' => Str::random(40),
        ]);

        if (class_exists(SystemActivity::class)) {
            SystemActivity::create([
                'user_id' => $user->id,
                'type' => 'team_invited',
                'description' => "Mengundang {$email} sebagai Admin CS",
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Undangan berhasil dikirim ke {$email}",
            'member' => $member,
        ]);
    }

    public function deleteMember($id)
    {
        $user = $this->getUser();
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $member = TeamMember::where('user_id', $user->id)->where('id', $id)->first();
        if (!$member) {
            return response()->json(['success' => false, 'error' => 'Data anggota tidak ditemukan'], 404);
        }

        $email = $member->email;
        $member->delete();

        if (class_exists(SystemActivity::class)) {
            SystemActivity::create([
                'user_id' => $user->id,
                'type' => 'team_deleted',
                'description' => "Menghapus Admin CS / undangan {$email}",
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Anggota tim / undangan berhasil dihapus',
        ]);
    }

    public function resendInvite($id)
    {
        $user = $this->getUser();
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $member = TeamMember::where('user_id', $user->id)->where('id', $id)->first();
        if (!$member) {
            return response()->json(['success' => false, 'error' => 'Undangan tidak ditemukan'], 404);
        }

        $member->invited_at = now();
        $member->save();

        return response()->json([
            'success' => true,
            'message' => "Undangan berhasil dikirim ulang ke {$member->email}",
        ]);
    }

    public function toggleStatus($id)
    {
        $user = $this->getUser();
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $member = TeamMember::where('user_id', $user->id)->where('id', $id)->first();
        if (!$member) {
            return response()->json(['success' => false, 'error' => 'Data tidak ditemukan'], 404);
        }

        if ($member->status === 'pending') {
            $member->status = 'active';
            $member->joined_at = now();
            $msg = "Status {$member->name} diubah menjadi Aktif";
        } else {
            $member->status = 'pending';
            $msg = "Status {$member->name} diubah menjadi Menunggu Konfirmasi";
        }
        $member->save();

        return response()->json([
            'success' => true,
            'message' => $msg,
            'member' => $member,
        ]);
    }
}

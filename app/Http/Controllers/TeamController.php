<?php

namespace App\Http\Controllers;

use App\Mail\TeamAccountActivatedEmail;
use App\Mail\TeamInvitationEmail;
use App\Mail\TeamResetPasswordEmail;
use App\Models\SystemActivity;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Services\MailDeliveryService;

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

        // If user is Admin CS, restrict access to Team Management
        if ($user->role === 'admin_cs') {
            return redirect('/dashboard')->with('error', 'Akses dibatasi untuk Admin CS.');
        }

        $activeMembers = TeamMember::where('user_id', $user->id)->where('status', '!=', 'pending')->orderBy('created_at', 'desc')->get();
        $pendingMembers = TeamMember::where('user_id', $user->id)->where('status', 'pending')->orderBy('created_at', 'desc')->get();

        $activeAdminCount = TeamMember::where('user_id', $user->id)->where('status', 'active')->count();
        $pendingCount = $pendingMembers->count();
        
        $seatsUsed = 1 + $activeAdminCount;
        $seatsRemaining = max(0, $totalSeats - $seatsUsed);
        $activeCount = 1 + $activeMembers->count();

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
        $activeMembers = TeamMember::where('user_id', $user->id)->where('status', '!=', 'pending')->orderBy('created_at', 'desc')->get();
        $pendingMembers = TeamMember::where('user_id', $user->id)->where('status', 'pending')->orderBy('created_at', 'desc')->get();

        $activeAdminCount = TeamMember::where('user_id', $user->id)->where('status', 'active')->count();
        $pendingCount = $pendingMembers->count();
        
        $seatsUsed = 1 + $activeAdminCount;
        $seatsRemaining = max(0, $totalSeats - $seatsUsed);
        $activeCount = 1 + $activeMembers->count();

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

        if ($user->role === 'admin_cs') {
            return response()->json(['success' => false, 'error' => 'Admin CS tidak dapat mengundang anggota.'], 403);
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($request->input('email')));
        $name = trim($request->input('name') ?: explode('@', $email)[0]);

        // Check if owner's email
        if (strtolower($user->email) === $email) {
            return response()->json([
                'success' => false,
                'error' => 'Email ini adalah email akun Owner.'
            ], 422);
        }

        // Check if already in active team
        $existing = TeamMember::where('user_id', $user->id)->where('email', $email)->first();
        if ($existing && $existing->status === 'active') {
            return response()->json([
                'success' => false,
                'error' => 'Email ini sudah menjadi Admin CS aktif.'
            ], 422);
        }

        // Capacity check
        $activeAdminCount = TeamMember::where('user_id', $user->id)->where('status', 'active')->count();
        if ((1 + $activeAdminCount) >= 99) {
            return response()->json([
                'success' => false,
                'error' => 'Kapasitas seat sudah penuh (maksimal 99 seats).'
            ], 422);
        }

        $token = Str::random(60);

        if ($existing) {
            $existing->name = $name;
            $existing->token = $token;
            $existing->status = 'pending';
            $existing->invited_at = now();
            $existing->save();
            $member = $existing;
        } else {
            $member = TeamMember::create([
                'user_id' => $user->id,
                'name' => $name,
                'email' => $email,
                'role' => 'Admin CS',
                'status' => 'pending',
                'invited_at' => now(),
                'token' => $token,
            ]);
        }

        // Send invitation email
        try {
            $mailable = new TeamInvitationEmail($member, $user);
            MailDeliveryService::send(
                $email,
                $name,
                $mailable->envelope()->subject,
                $mailable->render()
            );
        } catch (\Throwable $e) {
            Log::error('TeamInvitationEmail failed: ' . $e->getMessage());
        }

        if (class_exists(SystemActivity::class)) {
            SystemActivity::create([
                'user_id' => $user->id,
                'type' => 'team_invited',
                'description' => "Mengundang {$email} sebagai Admin CS",
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Undangan terkirim. Admin CS akan menerima email aktivasi akun.',
            'member' => $member,
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

        if (empty($member->token)) {
            $member->token = Str::random(60);
        }
        $member->invited_at = now();
        $member->save();

        try {
            $mailable = new TeamInvitationEmail($member, $user);
            MailDeliveryService::send(
                $member->email,
                $member->name,
                $mailable->envelope()->subject,
                $mailable->render()
            );
        } catch (\Throwable $e) {
            Log::error('Resend TeamInvitationEmail failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Undangan berhasil dikirim ulang.',
        ]);
    }

    public function sendResetPassword($id)
    {
        $user = $this->getUser();
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $member = TeamMember::where('user_id', $user->id)->where('id', $id)->first();
        if (!$member) {
            return response()->json(['success' => false, 'error' => 'Data Admin CS tidak ditemukan'], 404);
        }

        $member->token = Str::random(60);
        $member->reset_token = $member->token;
        $member->reset_sent_at = now();
        $member->save();

        try {
            $mailable = new TeamResetPasswordEmail($member, $user);
            MailDeliveryService::send(
                $member->email,
                $member->name,
                $mailable->envelope()->subject,
                $mailable->render()
            );
        } catch (\Throwable $e) {
            Log::error('TeamResetPasswordEmail failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Email reset password berhasil dikirim.',
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
            return response()->json(['success' => false, 'error' => 'Data anggota tidak ditemukan'], 404);
        }

        $newStatus = ($member->status === 'active') ? 'inactive' : 'active';
        $member->status = $newStatus;
        $member->save();

        if (class_exists(SystemActivity::class)) {
            SystemActivity::create([
                'user_id' => $user->id,
                'type' => 'team_status_changed',
                'description' => ($newStatus === 'active' ? "Mengaktifkan kembali" : "Menonaktifkan") . " Admin CS {$member->email}",
            ]);
        }

        $totalSeats = 99;
        $activeAdminCount = TeamMember::where('user_id', $user->id)->where('status', 'active')->count();
        $seatsUsed = 1 + $activeAdminCount;
        $seatsRemaining = max(0, $totalSeats - $seatsUsed);
        $activeCount = 1 + TeamMember::where('user_id', $user->id)->where('status', '!=', 'pending')->count();
        $pendingCount = TeamMember::where('user_id', $user->id)->where('status', 'pending')->count();

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'is_active' => $newStatus === 'active',
            'stats' => [
                'total_seats' => $totalSeats,
                'seats_used' => $seatsUsed,
                'seats_remaining' => $seatsRemaining,
                'active_count' => $activeCount,
                'pending_count' => $pendingCount,
            ],
            'message' => $newStatus === 'active' ? 'Admin CS berhasil diaktifkan kembali.' : 'Admin CS berhasil dinonaktifkan.',
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
        $wasPending = ($member->status === 'pending');
        $member->delete();

        // Also if user account was created for this admin CS, can remove or unbind
        $adminUser = User::where('email', $email)->where('owner_id', $user->id)->first();
        if ($adminUser) {
            $adminUser->delete();
        }

        if (class_exists(SystemActivity::class)) {
            SystemActivity::create([
                'user_id' => $user->id,
                'type' => 'team_deleted',
                'description' => "Menghapus Admin CS / undangan {$email}",
            ]);
        }

        $totalSeats = 99;
        $activeAdminCount = TeamMember::where('user_id', $user->id)->where('status', 'active')->count();
        $seatsUsed = 1 + $activeAdminCount;
        $seatsRemaining = max(0, $totalSeats - $seatsUsed);
        $activeCount = 1 + TeamMember::where('user_id', $user->id)->where('status', '!=', 'pending')->count();
        $pendingCount = TeamMember::where('user_id', $user->id)->where('status', 'pending')->count();

        return response()->json([
            'success' => true,
            'message' => $wasPending ? 'Undangan berhasil dihapus.' : 'Admin CS berhasil dihapus.',
            'stats' => [
                'total_seats' => $totalSeats,
                'seats_used' => $seatsUsed,
                'seats_remaining' => $seatsRemaining,
                'active_count' => $activeCount,
                'pending_count' => $pendingCount,
            ],
        ]);
    }

    public function showAcceptInvite($token)
    {
        $member = TeamMember::where('token', $token)->first();

        if (!$member || empty($token)) {
            return view('team_accept_invite', [
                'token' => $token,
                'member' => null,
                'error' => 'Tautan undangan ini sudah kedaluwarsa atau sudah pernah digunakan untuk mengaktifkan akun. Silakan login atau hubungi Owner untuk mendapatkan undangan baru.'
            ]);
        }

        return view('team_accept_invite', [
            'token' => $token,
            'member' => $member,
            'error' => null,
        ]);
    }

    public function processAcceptInvite(Request $request, $token)
    {
        $member = TeamMember::where('token', $token)->first();

        if (!$member || empty($token)) {
            return redirect()->back()->with('error', 'Tautan undangan ini sudah tidak berlaku.');
        }

        $request->validate([
            'password' => 'required|min:8|confirmed',
            'name' => 'required|string|max:255',
        ], [
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'name.required' => 'Nama Admin wajib diisi.',
        ]);

        $owner = User::find($member->user_id);
        if (!$owner) {
            $owner = User::first();
        }

        $adminName = trim($request->input('name', $member->name));

        // Create or update user account
        $user = User::where('email', $member->email)->first();
        if (!$user) {
            $user = User::create([
                'name' => $adminName,
                'email' => $member->email,
                'password' => Hash::make($request->input('password')),
                'role' => 'admin_cs',
                'owner_id' => $owner ? $owner->id : null,
                'business_name' => $owner ? $owner->business_name : 'Workspace',
                'email_verified_at' => now(),
            ]);
        } else {
            $user->name = $adminName;
            $user->password = Hash::make($request->input('password'));
            $user->role = 'admin_cs';
            $user->owner_id = $owner ? $owner->id : null;
            $user->business_name = $owner ? $owner->business_name : $user->business_name;
            $user->email_verified_at = $user->email_verified_at ?: now();
            $user->save();
        }

        // Mark member as active and invalidate token so link is one-time use only
        $member->name = $adminName;
        $member->status = 'active';
        $member->joined_at = now();
        $member->token = null;
        $member->reset_token = null;
        $member->save();

        // Send Welcome Email
        try {
            $mailable = new TeamAccountActivatedEmail($member, $user);
            MailDeliveryService::send(
                $user->email,
                $user->name,
                $mailable->envelope()->subject,
                $mailable->render()
            );
        } catch (\Throwable $e) {
            Log::error('TeamAccountActivatedEmail failed: ' . $e->getMessage());
        }

        if (class_exists(SystemActivity::class) && $owner) {
            SystemActivity::create([
                'user_id' => $owner->id,
                'type' => 'team_activated',
                'description' => "Admin CS {$user->name} ({$user->email}) telah mengaktifkan akun dan bergabung ke workspace",
            ]);
        }

        // Login user directly into workspace
        Auth::login($user);

        return redirect('/dashboard');
    }
}

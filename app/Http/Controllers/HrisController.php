<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\HrisSetting;
use App\Models\HrisEmployee;
use App\Models\HrisAttendance;
use App\Models\HrisLeave;
use App\Models\HrisOvertime;
use App\Models\HrisPayroll;
use App\Models\HrisAnnouncement;
use App\Models\User;

class HrisController extends Controller
{
    /**
     * Helper to resolve the owner ID
     */
    protected function getOwnerId(): int
    {
        $user = Auth::user();
        if ($user) {
            return $user->getEffectiveOwnerId();
        }
        // Fallback to first user in database
        $firstUser = User::first();
        return $firstUser ? $firstUser->id : 1;
    }

    /**
     * Helper to ensure default settings and photography studio employees exist
     */
    protected function ensureDefaultData(int $ownerId)
    {
        // 1. Settings (Default Studio Coordinates: Monas / Jakarta Pusat as reference default, can be edited)
        $settings = HrisSetting::where('user_id', $ownerId)->first();
        if (!$settings) {
            $settings = HrisSetting::create([
                'user_id' => $ownerId,
                'studio_name' => 'Penapict Photography Studio',
                'studio_address' => 'Jl. Kemang Raya No. 45, Jakarta Selatan',
                'studio_lat' => -6.2607187,
                'studio_lng' => 106.8166667,
                'radius_meters' => 100,
                'work_start_time' => '08:30',
                'work_end_time' => '17:30',
                'late_tolerance_minutes' => 15,
                'overtime_rate_per_hour' => 25000,
                'late_penalty_per_hour' => 20000,
            ]);
        }

        // 2. Default photography studio team if empty
        $count = HrisEmployee::where('user_id', $ownerId)->count();
        if ($count === 0) {
            $defaultStaff = [
                [
                    'code' => 'KRY-001',
                    'name' => 'Dimas Wicaksono',
                    'email' => 'dimas@penapict.com',
                    'phone' => '081234567890',
                    'division' => 'Fotografer',
                    'position' => 'Senior Photographer',
                    'role' => 'karyawan',
                    'base_salary' => 4500000,
                    'allowance' => 750000,
                    'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
                ],
                [
                    'code' => 'KRY-002',
                    'name' => 'Rian Pratama',
                    'email' => 'rian@penapict.com',
                    'phone' => '081234567891',
                    'division' => 'Videografer',
                    'position' => 'Lead Cinematographer',
                    'role' => 'supervisor',
                    'base_salary' => 5000000,
                    'allowance' => 1000000,
                    'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
                ],
                [
                    'code' => 'KRY-003',
                    'name' => 'Anisa Rahmawati',
                    'email' => 'anisa@penapict.com',
                    'phone' => '081234567892',
                    'division' => 'Editor',
                    'position' => 'Colorist & Album Designer',
                    'role' => 'karyawan',
                    'base_salary' => 3800000,
                    'allowance' => 500000,
                    'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150&auto=format&fit=crop&q=80',
                ],
                [
                    'code' => 'KRY-004',
                    'name' => 'Budi Santoso',
                    'email' => 'budi@penapict.com',
                    'phone' => '081234567893',
                    'division' => 'Produksi',
                    'position' => 'Staff Framing & Cetak',
                    'role' => 'karyawan',
                    'base_salary' => 3500000,
                    'allowance' => 400000,
                    'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80',
                ],
                [
                    'code' => 'KRY-005',
                    'name' => 'Siti Maulida',
                    'email' => 'siti@penapict.com',
                    'phone' => '081234567894',
                    'division' => 'Marketing',
                    'position' => 'Client Relations & Event Hunter',
                    'role' => 'karyawan',
                    'base_salary' => 4000000,
                    'allowance' => 800000,
                    'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150&auto=format&fit=crop&q=80',
                ],
                [
                    'code' => 'KRY-006',
                    'name' => 'Maya Kartika',
                    'email' => 'maya@penapict.com',
                    'phone' => '081234567895',
                    'division' => 'HR',
                    'position' => 'HR & Finance Specialist',
                    'role' => 'hr',
                    'base_salary' => 5500000,
                    'allowance' => 1200000,
                    'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&auto=format&fit=crop&q=80',
                ],
            ];

            foreach ($defaultStaff as $st) {
                HrisEmployee::create([
                    'user_id' => $ownerId,
                    'employee_code' => $st['code'],
                    'name' => $st['name'],
                    'email' => $st['email'],
                    'phone' => $st['phone'],
                    'division' => $st['division'],
                    'position' => $st['position'],
                    'role' => $st['role'],
                    'base_salary' => $st['base_salary'],
                    'allowance' => $st['allowance'],
                    'leave_balance' => 12,
                    'status' => 'active',
                    'avatar_url' => $st['avatar'],
                ]);
            }
        }

        // 3. Default Announcement
        if (HrisAnnouncement::where('user_id', $ownerId)->count() === 0) {
            HrisAnnouncement::create([
                'user_id' => $ownerId,
                'title' => '📸 Jadwal Pemotretan Akbar Wedding Fair Weekend Ini',
                'content' => 'Kepada seluruh tim Fotografer, Videografer, dan Tim Lapangan: Mohon bersiap untuk event foto akbar di Grand Ballroom mulai Sabtu pk 07.00 WIB. Seluruh gear baterai & memory card wajib di-check H-1.',
                'category' => 'Jadwal Event Foto',
                'is_pinned' => true,
            ]);
            HrisAnnouncement::create([
                'user_id' => $ownerId,
                'title' => '⏰ Kebijakan Presensi & Geofence Studio',
                'content' => 'Batas toleransi keterlambatan adalah 15 menit dari jam 08:30. Divisi Marketing diperbolehkan absensi mobile di luar studio sesuai lokasi meeting klien.',
                'category' => 'SOP Studio',
                'is_pinned' => false,
            ]);
        }
    }

    /**
     * Main Mobile App View
     */
    public function index(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $this->ensureDefaultData($ownerId);

        $settings = HrisSetting::where('user_id', $ownerId)->first();
        $employees = HrisEmployee::where('user_id', $ownerId)->where('status', 'active')->orderBy('employee_code')->get();

        // Selected active employee (can be chosen via session or defaults to first employee)
        $activeEmpId = session('hris_active_emp_id');
        $activeEmployee = null;
        if ($activeEmpId) {
            $activeEmployee = HrisEmployee::where('user_id', $ownerId)->find($activeEmpId);
        }
        if (!$activeEmployee && $employees->count() > 0) {
            $activeEmployee = $employees->first();
            session(['hris_active_emp_id' => $activeEmployee->id]);
        }

        return view('hris.index', compact('settings', 'employees', 'activeEmployee'));
    }

    /**
     * API: Switch active employee persona (for easy mobile testing/shared tablet)
     */
    public function switchEmployee(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $empId = $request->input('employee_id');
        $emp = HrisEmployee::where('user_id', $ownerId)->find($empId);

        if (!$emp) {
            return response()->json(['success' => false, 'message' => 'Karyawan tidak ditemukan'], 404);
        }

        session(['hris_active_emp_id' => $emp->id]);
        return response()->json(['success' => true, 'employee' => $emp]);
    }

    /**
     * API: Get Initial Mobile App Data
     */
    public function getInitialData(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $this->ensureDefaultData($ownerId);

        $settings = HrisSetting::where('user_id', $ownerId)->first();
        $employees = HrisEmployee::where('user_id', $ownerId)->where('status', 'active')->orderBy('employee_code')->get();

        $empId = session('hris_active_emp_id') ?: ($employees->first() ? $employees->first()->id : null);
        $employee = $empId ? HrisEmployee::where('user_id', $ownerId)->find($empId) : null;

        $today = Carbon::today()->toDateString();
        $todayAttendance = null;
        if ($employee) {
            $todayAttendance = HrisAttendance::where('user_id', $ownerId)
                ->where('employee_id', $employee->id)
                ->where('date', $today)
                ->first();
        }

        $announcements = HrisAnnouncement::where('user_id', $ownerId)->orderByDesc('is_pinned')->orderByDesc('created_at')->take(5)->get();

        return response()->json([
            'success' => true,
            'settings' => $settings,
            'employee' => $employee,
            'employees' => $employees,
            'today_attendance' => $todayAttendance,
            'announcements' => $announcements,
            'server_time' => Carbon::now()->toIso8601String(),
        ]);
    }

    /**
     * Calculate Distance in meters between 2 coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // in meters
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    /**
     * API: Clock In
     */
    public function clockIn(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $empId = $request->input('employee_id') ?: session('hris_active_emp_id');
        $employee = HrisEmployee::where('user_id', $ownerId)->find($empId);

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Profil karyawan tidak ditemukan.'], 404);
        }

        $today = Carbon::today()->toDateString();
        $existing = HrisAttendance::where('user_id', $ownerId)
            ->where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if ($existing && $existing->clock_in) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan Absen Masuk hari ini pada ' . Carbon::parse($existing->clock_in)->format('H:i:s')], 422);
        }

        $lat = floatval($request->input('lat'));
        $lng = floatval($request->input('lng'));
        $photo = $request->input('photo');

        if (!$lat || !$lng) {
            return response()->json(['success' => false, 'message' => 'Koordinat GPS wajib diaktifkan untuk absensi.'], 422);
        }
        if (!$photo) {
            return response()->json(['success' => false, 'message' => 'Foto selfie scan wajah wajib diambil.'], 422);
        }

        $settings = HrisSetting::where('user_id', $ownerId)->first();
        $studioLat = $settings ? $settings->studio_lat : -6.2607187;
        $studioLng = $settings ? $settings->studio_lng : 106.8166667;
        $radiusAllowed = $settings ? $settings->radius_meters : 100;

        $distance = round($this->calculateDistance($lat, $lng, $studioLat, $studioLng), 1);
        $isMarketing = $employee->isMarketing();

        // Check radius for non-marketing staff
        if (!$isMarketing && $distance > $radiusAllowed) {
            return response()->json([
                'success' => false,
                'message' => "Gagal Absen: Lokasi Anda berjarak {$distance} meter dari Studio (Maksimal {$radiusAllowed} meter). Silakan masuk ke area studio terlebih dahulu.",
                'distance' => $distance,
                'allowed_radius' => $radiusAllowed,
            ], 422);
        }

        // Calculate Late status
        $now = Carbon::now();
        $startTime = Carbon::parse($today . ' ' . ($settings ? $settings->work_start_time : '08:30'));
        $toleranceMins = $settings ? $settings->late_tolerance_minutes : 15;
        $lateThreshold = (clone $startTime)->addMinutes($toleranceMins);

        $status = 'hadir';
        $lateMinutes = 0;
        if ($now->greaterThan($lateThreshold)) {
            $status = 'terlambat';
            $lateMinutes = $now->diffInMinutes($startTime);
        }

        $attendance = HrisAttendance::updateOrCreate(
            ['user_id' => $ownerId, 'employee_id' => $employee->id, 'date' => $today],
            [
                'clock_in' => $now,
                'clock_in_photo' => $photo,
                'clock_in_lat' => $lat,
                'clock_in_lng' => $lng,
                'distance_meters' => $distance,
                'status' => $status,
                'late_minutes' => $lateMinutes,
                'is_marketing_bypass' => $isMarketing && ($distance > $radiusAllowed),
                'notes' => $isMarketing && ($distance > $radiusAllowed) ? 'Absensi Mobile Divisi Marketing (Bebas Radius)' : null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Absen Masuk berhasil dicatat!',
            'attendance' => $attendance,
            'status' => $status,
            'distance' => $distance,
            'late_minutes' => $lateMinutes,
        ]);
    }

    /**
     * API: Clock Out
     */
    public function clockOut(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $empId = $request->input('employee_id') ?: session('hris_active_emp_id');
        $employee = HrisEmployee::where('user_id', $ownerId)->find($empId);

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Profil karyawan tidak ditemukan.'], 404);
        }

        $today = Carbon::today()->toDateString();
        $attendance = HrisAttendance::where('user_id', $ownerId)
            ->where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->clock_in) {
            return response()->json(['success' => false, 'message' => 'Anda belum melakukan Absen Masuk hari ini.'], 422);
        }

        if ($attendance->clock_out) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan Absen Pulang hari ini pada ' . Carbon::parse($attendance->clock_out)->format('H:i:s')], 422);
        }

        $lat = floatval($request->input('lat'));
        $lng = floatval($request->input('lng'));
        $photo = $request->input('photo');

        $now = Carbon::now();
        $attendance->clock_out = $now;
        $attendance->clock_out_photo = $photo;
        $attendance->clock_out_lat = $lat;
        $attendance->clock_out_lng = $lng;
        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => 'Absen Pulang berhasil dicatat! Selamat beristirahat.',
            'attendance' => $attendance,
        ]);
    }

    /**
     * API: Attendance History
     */
    public function getAttendanceHistory(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $empId = $request->input('employee_id') ?: session('hris_active_emp_id');
        $month = $request->input('month', Carbon::now()->format('Y-m'));

        $query = HrisAttendance::where('user_id', $ownerId);
        if ($empId) {
            $query->where('employee_id', $empId);
        }
        $attendances = $query->where('date', 'like', "{$month}%")
            ->orderByDesc('date')
            ->get();

        return response()->json([
            'success' => true,
            'attendances' => $attendances,
        ]);
    }

    /**
     * API: Submit Leave / Cuti
     */
    public function submitLeave(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $empId = $request->input('employee_id') ?: session('hris_active_emp_id');
        $employee = HrisEmployee::where('user_id', $ownerId)->find($empId);

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Karyawan tidak ditemukan.'], 404);
        }

        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));
        $totalDays = max(1, $startDate->diffInDays($endDate) + 1);

        $leave = HrisLeave::create([
            'user_id' => $ownerId,
            'employee_id' => $employee->id,
            'type' => $request->input('type', 'Cuti Tahunan'),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'total_days' => $totalDays,
            'reason' => $request->input('reason'),
            'attachment' => $request->input('attachment'),
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan cuti/izin berhasil dikirim dan menunggu persetujuan HR/Admin.',
            'leave' => $leave,
        ]);
    }

    /**
     * API: Get Leaves
     */
    public function getLeaves(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $empId = $request->input('employee_id') ?: session('hris_active_emp_id');

        $query = HrisLeave::where('user_id', $ownerId)->with('employee');
        if ($empId && !$request->input('all')) {
            $query->where('employee_id', $empId);
        }
        $leaves = $query->orderByDesc('created_at')->get();

        return response()->json([
            'success' => true,
            'leaves' => $leaves,
        ]);
    }

    /**
     * API: Submit Overtime / Lembur
     */
    public function submitOvertime(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $empId = $request->input('employee_id') ?: session('hris_active_emp_id');
        $employee = HrisEmployee::where('user_id', $ownerId)->find($empId);

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Karyawan tidak ditemukan.'], 404);
        }

        $date = $request->input('date', Carbon::today()->toDateString());
        $startTime = $request->input('start_time', '18:00');
        $endTime = $request->input('end_time', '21:00');

        $start = Carbon::parse("{$date} {$startTime}");
        $end = Carbon::parse("{$date} {$endTime}");
        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay(); // Handle past midnight
        }
        $hours = round($end->diffInMinutes($start) / 60, 2);

        $settings = HrisSetting::where('user_id', $ownerId)->first();
        $rate = $settings ? $settings->overtime_rate_per_hour : 25000;
        $estimatedPay = $hours * $rate;

        $overtime = HrisOvertime::create([
            'user_id' => $ownerId,
            'employee_id' => $employee->id,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_hours' => $hours,
            'project_name' => $request->input('project_name'),
            'description' => $request->input('description'),
            'estimated_pay' => $estimatedPay,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan lembur berhasil diajukan.',
            'overtime' => $overtime,
        ]);
    }

    /**
     * API: Get Overtimes
     */
    public function getOvertimes(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $empId = $request->input('employee_id') ?: session('hris_active_emp_id');

        $query = HrisOvertime::where('user_id', $ownerId)->with('employee');
        if ($empId && !$request->input('all')) {
            $query->where('employee_id', $empId);
        }
        $overtimes = $query->orderByDesc('created_at')->get();

        return response()->json([
            'success' => true,
            'overtimes' => $overtimes,
        ]);
    }

    /**
     * API: Get Payrolls / Slip Gaji
     */
    public function getPayrolls(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $empId = $request->input('employee_id') ?: session('hris_active_emp_id');

        $query = HrisPayroll::where('user_id', $ownerId)->with('employee');
        if ($empId && !$request->input('all')) {
            $query->where('employee_id', $empId);
        }
        $payrolls = $query->orderByDesc('period_month')->get();

        return response()->json([
            'success' => true,
            'payrolls' => $payrolls,
        ]);
    }

    /**
     * API / View: Show Printable Payslip
     */
    public function showPayslip($id)
    {
        $ownerId = $this->getOwnerId();
        $payroll = HrisPayroll::where('user_id', $ownerId)->with('employee')->findOrFail($id);
        $settings = HrisSetting::where('user_id', $ownerId)->first();

        return view('hris.payslip_print', compact('payroll', 'settings'));
    }

    // ==========================================
    // ADMIN & MANAGEMENT PORTAL ENDPOINTS
    // ==========================================

    /**
     * API: Admin Dashboard Stats
     */
    public function getAdminStats(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $today = Carbon::today()->toDateString();

        $totalEmployees = HrisEmployee::where('user_id', $ownerId)->where('status', 'active')->count();
        $presentToday = HrisAttendance::where('user_id', $ownerId)->where('date', $today)->whereNotNull('clock_in')->count();
        $lateToday = HrisAttendance::where('user_id', $ownerId)->where('date', $today)->where('status', 'terlambat')->count();
        $leaveToday = HrisLeave::where('user_id', $ownerId)->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->count();
        $absentToday = max(0, $totalEmployees - ($presentToday + $leaveToday));

        $pendingLeaves = HrisLeave::where('user_id', $ownerId)->where('status', 'pending')->count();
        $pendingOvertimes = HrisOvertime::where('user_id', $ownerId)->where('status', 'pending')->count();

        // Today's Attendances list with employees
        $todayAttendances = HrisAttendance::where('user_id', $ownerId)
            ->where('date', $today)
            ->with('employee')
            ->orderByDesc('clock_in')
            ->get();

        return response()->json([
            'success' => true,
            'stats' => [
                'total_employees' => $totalEmployees,
                'present_today' => $presentToday,
                'late_today' => $lateToday,
                'leave_today' => $leaveToday,
                'absent_today' => $absentToday,
                'pending_leaves' => $pendingLeaves,
                'pending_overtimes' => $pendingOvertimes,
            ],
            'today_attendances' => $todayAttendances,
        ]);
    }

    /**
     * API: Approve or Reject Leave
     */
    public function actionLeave(Request $request, $id)
    {
        $ownerId = $this->getOwnerId();
        $leave = HrisLeave::where('user_id', $ownerId)->findOrFail($id);
        $action = $request->input('action'); // 'approved' or 'rejected'

        $leave->status = $action === 'approved' ? 'approved' : 'rejected';
        $leave->admin_notes = $request->input('admin_notes');
        $leave->approved_by = Auth::user() ? Auth::user()->name : 'Admin HR';
        $leave->save();

        // If approved and cuti tahunan, deduct leave balance
        if ($leave->status === 'approved' && strtolower($leave->type) === 'cuti tahunan') {
            $emp = HrisEmployee::find($leave->employee_id);
            if ($emp) {
                $emp->leave_balance = max(0, $emp->leave_balance - $leave->total_days);
                $emp->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pengajuan cuti berhasil diperbarui.',
            'leave' => $leave,
        ]);
    }

    /**
     * API: Approve or Reject Overtime
     */
    public function actionOvertime(Request $request, $id)
    {
        $ownerId = $this->getOwnerId();
        $overtime = HrisOvertime::where('user_id', $ownerId)->findOrFail($id);
        $action = $request->input('action'); // 'approved' or 'rejected'

        $overtime->status = $action === 'approved' ? 'approved' : 'rejected';
        $overtime->admin_notes = $request->input('admin_notes');
        $overtime->approved_by = Auth::user() ? Auth::user()->name : 'Supervisor';
        $overtime->save();

        return response()->json([
            'success' => true,
            'message' => 'Status lembur berhasil diperbarui.',
            'overtime' => $overtime,
        ]);
    }

    /**
     * API: Save / Update Employee
     */
    public function saveEmployee(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $id = $request->input('id');

        $data = [
            'user_id' => $ownerId,
            'employee_code' => $request->input('employee_code') ?: ('KRY-' . str_pad(HrisEmployee::where('user_id', $ownerId)->count() + 1, 3, '0', STR_PAD_LEFT)),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'division' => $request->input('division', 'Fotografer'),
            'position' => $request->input('position', 'Staff'),
            'role' => $request->input('role', 'karyawan'),
            'base_salary' => floatval($request->input('base_salary', 3500000)),
            'allowance' => floatval($request->input('allowance', 500000)),
            'leave_balance' => intval($request->input('leave_balance', 12)),
            'status' => $request->input('status', 'active'),
            'avatar_url' => $request->input('avatar_url'),
        ];

        if ($id) {
            $employee = HrisEmployee::where('user_id', $ownerId)->findOrFail($id);
            $employee->update($data);
        } else {
            $employee = HrisEmployee::create($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data karyawan berhasil disimpan!',
            'employee' => $employee,
        ]);
    }

    /**
     * API: Delete Employee
     */
    public function deleteEmployee(Request $request, $id)
    {
        $ownerId = $this->getOwnerId();
        $employee = HrisEmployee::where('user_id', $ownerId)->findOrFail($id);
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Karyawan berhasil dihapus.',
        ]);
    }

    /**
     * API: Save Settings
     */
    public function saveSettings(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $settings = HrisSetting::where('user_id', $ownerId)->first();

        $data = [
            'user_id' => $ownerId,
            'studio_name' => $request->input('studio_name', 'Penapict Photography Studio'),
            'studio_address' => $request->input('studio_address'),
            'studio_lat' => floatval($request->input('studio_lat')),
            'studio_lng' => floatval($request->input('studio_lng')),
            'radius_meters' => intval($request->input('radius_meters', 100)),
            'work_start_time' => $request->input('work_start_time', '08:30'),
            'work_end_time' => $request->input('work_end_time', '17:30'),
            'late_tolerance_minutes' => intval($request->input('late_tolerance_minutes', 15)),
            'overtime_rate_per_hour' => floatval($request->input('overtime_rate_per_hour', 25000)),
            'late_penalty_per_hour' => floatval($request->input('late_penalty_per_hour', 20000)),
        ];

        if ($settings) {
            $settings->update($data);
        } else {
            $settings = HrisSetting::create($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan studio & geofence berhasil disimpan!',
            'settings' => $settings,
        ]);
    }

    /**
     * API: Generate Monthly Payrolls
     */
    public function generateMonthlyPayroll(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $settings = HrisSetting::where('user_id', $ownerId)->first();
        $penaltyRate = $settings ? $settings->late_penalty_per_hour : 20000;

        $employees = HrisEmployee::where('user_id', $ownerId)->where('status', 'active')->get();
        $count = 0;

        foreach ($employees as $emp) {
            // Count attendance metrics
            $attendances = HrisAttendance::where('user_id', $ownerId)
                ->where('employee_id', $emp->id)
                ->where('date', 'like', "{$month}%")
                ->get();

            $totalPresent = $attendances->count();
            $totalLate = $attendances->where('status', 'terlambat')->count();
            $totalLateMinutes = $attendances->sum('late_minutes');

            // Approved leaves
            $totalLeaves = HrisLeave::where('user_id', $ownerId)
                ->where('employee_id', $emp->id)
                ->where('status', 'approved')
                ->where('start_date', 'like', "{$month}%")
                ->sum('total_days');

            // Approved overtimes
            $approvedOvertimes = HrisOvertime::where('user_id', $ownerId)
                ->where('employee_id', $emp->id)
                ->where('status', 'approved')
                ->where('date', 'like', "{$month}%")
                ->get();

            $totalOvertimeHours = $approvedOvertimes->sum('duration_hours');
            $totalOvertimePay = $approvedOvertimes->sum('estimated_pay');

            // Deductions for lateness
            $lateDeduction = round(($totalLateMinutes / 60) * $penaltyRate, 0);

            $baseSalary = $emp->base_salary;
            $allowance = $emp->allowance;
            $netSalary = max(0, $baseSalary + $allowance + $totalOvertimePay - $lateDeduction);

            HrisPayroll::updateOrCreate(
                [
                    'user_id' => $ownerId,
                    'employee_id' => $emp->id,
                    'period_month' => $month,
                ],
                [
                    'base_salary' => $baseSalary,
                    'allowance' => $allowance,
                    'overtime_pay' => $totalOvertimePay,
                    'bonus' => 0,
                    'deductions' => 0,
                    'late_deductions' => $lateDeduction,
                    'net_salary' => $netSalary,
                    'total_present' => $totalPresent,
                    'total_late' => $totalLate,
                    'total_leave' => $totalLeaves,
                    'total_overtime_hours' => $totalOvertimeHours,
                    'status' => 'draft',
                    'notes' => "Slip Gaji Otomatis Periode {$month}",
                ]
            );
            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil memproses slip gaji untuk {$count} karyawan pada periode {$month}.",
        ]);
    }

    /**
     * API: Save Announcement
     */
    public function saveAnnouncement(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $id = $request->input('id');

        $data = [
            'user_id' => $ownerId,
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'category' => $request->input('category', 'Umum'),
            'is_pinned' => $request->boolean('is_pinned'),
        ];

        if ($id) {
            $ann = HrisAnnouncement::where('user_id', $ownerId)->findOrFail($id);
            $ann->update($data);
        } else {
            $ann = HrisAnnouncement::create($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman studio berhasil disimpan!',
            'announcement' => $ann,
        ]);
    }

    /**
     * API: Delete Announcement
     */
    public function deleteAnnouncement($id)
    {
        $ownerId = $this->getOwnerId();
        $ann = HrisAnnouncement::where('user_id', $ownerId)->findOrFail($id);
        $ann->delete();

        return response()->json(['success' => true, 'message' => 'Pengumuman dihapus.']);
    }

    /**
     * View: Printable Monthly Recap
     */
    public function printRecap(Request $request)
    {
        $ownerId = $this->getOwnerId();
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $settings = HrisSetting::where('user_id', $ownerId)->first();
        $employees = HrisEmployee::where('user_id', $ownerId)->where('status', 'active')->orderBy('division')->get();
        $payrolls = HrisPayroll::where('user_id', $ownerId)->where('period_month', $month)->get()->keyBy('employee_id');

        return view('hris.recap_print', compact('month', 'settings', 'employees', 'payrolls'));
    }
}

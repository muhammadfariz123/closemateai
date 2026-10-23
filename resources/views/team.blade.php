<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Tim - CloseMateAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #1e1e2d;
            --sidebar-text: #a1a5b7;
            --sidebar-active-bg: #6c5ce7;
            --sidebar-active-text: #ffffff;
            --primary: #6c5ce7;
            --primary-hover: #5a4ad1;
            --bg-light: #f8f9fa;
            --card-bg: #ffffff;
            --border-color: #f1f1f4;
            --text-dark: #181c32;
            --text-muted: #64748b;
            --success: #50cd89;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-light); display: flex; min-height: 100vh; color: var(--text-dark); }

        /* Sidebar */
        .sidebar { width: 260px; background-color: var(--sidebar-bg); display: flex; flex-direction: column; height: 100vh; position: fixed; left: 0; top: 0; transition: width 0.3s ease; overflow-x: hidden; z-index: 100; }
        .sidebar-header { padding: 24px; display: flex; align-items: center; gap: 12px; transition: 0.3s ease; }
        .sidebar-logo-icon { min-width: 40px; width: 40px; height: 40px; background-color: rgba(108, 92, 231, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #a29bfe; font-size: 18px; }
        .sidebar-title { white-space: nowrap; }
        .sidebar-title h2 { color: white; font-size: 16px; font-weight: 600; margin-bottom: 2px; }
        .sidebar-title p { color: var(--sidebar-text); font-size: 12px; }
        
        .nav-menu { flex: 1; overflow-y: auto; padding: 12px 16px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 10px 14px; color: var(--sidebar-text); text-decoration: none; border-radius: 8px; font-size: 13px; font-weight: 500; margin-bottom: 4px; transition: 0.2s; white-space: nowrap; cursor: pointer; }
        .nav-item:hover { color: white; background-color: rgba(255,255,255,0.05); }
        .nav-item.active { background-color: var(--sidebar-active-bg); color: var(--sidebar-active-text) !important; font-weight: 600; }
        .nav-item.active i { color: white !important; }
        .nav-item i { font-size: 15px; width: 20px; text-align: center; color: #727586; }

        /* Sidebar Collapsed State */
        body.sidebar-collapsed .sidebar { width: 80px; }
        body.sidebar-collapsed .main-content { margin-left: 80px; }
        body.sidebar-collapsed .sidebar-title { display: none; }
        body.sidebar-collapsed .sidebar-header { justify-content: center; padding: 24px 0; }
        body.sidebar-collapsed .nav-item span { display: none; }
        body.sidebar-collapsed .nav-item { justify-content: center; padding-left: 0; padding-right: 0; }
        body.sidebar-collapsed #collapse-icon { transform: rotate(180deg); }

        /* Main Content */
        .main-content { margin-left: 260px; flex: 1; padding: 30px; display: flex; flex-direction: column; transition: margin-left 0.3s ease; }
        
        /* Topbar */
        .topbar { display: flex; justify-content: space-between; align-items: center; margin: -30px -30px 24px -30px; padding: 16px 30px; background-color: white; border-bottom: 1px solid #edf2f7; }
        .page-title h1 { font-size: 18px; font-weight: 700; color: #181c32; margin-bottom: 4px; }
        .page-title p { font-size: 13px; color: #64748b; }
        
        .top-actions { display: flex; align-items: center; gap: 16px; }
        .status-pill { display: flex; align-items: center; gap: 8px; background-color: #f0fdf4; color: #16a34a; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid #bbf7d0; }
        .status-dot { width: 7px; height: 7px; background-color: #16a34a; border-radius: 50%; }
        
        .profile-wrapper { position: relative; cursor: pointer; }
        .user-profile { display: flex; align-items: center; gap: 10px; padding: 4px 10px; border-radius: 30px; transition: background-color 0.2s; }
        .user-profile:hover { background-color: rgba(108, 92, 231, 0.06); }
        .avatar { width: 34px; height: 34px; background-color: #6c5ce7; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 13px; }
        .user-info h4 { font-size: 13px; font-weight: 600; color: #181c32; display: flex; align-items: center; gap: 4px; }
        .user-info p { font-size: 11px; color: #94a3b8; }

        /* Capacity Card */
        .capacity-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .capacity-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }
        .capacity-badge-title {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 8px;
        }
        .capacity-badge-title i {
            color: #8c7ae6;
            font-size: 15px;
        }
        .capacity-headline {
            font-size: 24px;
            font-weight: 700;
            color: #181c32;
            margin-bottom: 4px;
        }
        .capacity-subtitle {
            font-size: 13px;
            color: #64748b;
        }
        .btn-invite {
            background-color: #6c5ce7;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.15s;
        }
        .btn-invite:hover {
            background-color: #5a4ad1;
        }
        .progress-track {
            height: 8px;
            background: #ede9fe;
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 8px;
        }
        .progress-fill {
            height: 100%;
            background: #6c5ce7;
            border-radius: 999px;
            transition: width 0.3s ease;
        }
        .capacity-remaining {
            font-size: 12px;
            color: #64748b;
        }

        /* Filter Tabs */
        .tabs-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }
        .tab-btn {
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            background: transparent;
            color: #64748b;
            transition: 0.15s;
        }
        .tab-btn.active {
            background: white;
            border: 1px solid #e2e8f0;
            color: #181c32;
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }

        /* Members List Card */
        .members-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .member-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 24px;
            border-bottom: 1px solid #f1f1f4;
            transition: background 0.15s;
        }
        .member-row:last-child {
            border-bottom: none;
        }
        .member-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .member-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: white;
            flex-shrink: 0;
        }
        .avatar-owner {
            background: #6c5ce7;
        }
        .avatar-admin {
            background: #3b82f6;
        }
        .avatar-pending {
            background: #f59e0b;
        }
        .member-text h3 {
            font-size: 14px;
            font-weight: 600;
            color: #181c32;
            margin-bottom: 2px;
        }
        .member-text p {
            font-size: 13px;
            color: #64748b;
        }
        .role-badge {
            background: #f8f9fa;
            border: 1px solid #e2e8f0;
            color: #64748b;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .role-badge.badge-pending {
            background: #fffbeb;
            border-color: #fde68a;
            color: #d97706;
        }

        .empty-state {
            padding: 48px 24px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }

        /* Notice Banner */
        .notice-banner {
            background: #f8faff;
            border: 1px solid #e0e7ff;
            border-radius: 14px;
            padding: 18px 22px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 24px;
        }
        .notice-banner i {
            color: #6366f1;
            font-size: 18px;
            margin-top: 2px;
            flex-shrink: 0;
        }
        .notice-banner p {
            font-size: 13px;
            color: #475569;
            line-height: 1.6;
            margin: 0;
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 100000;
            align-items: center;
            justify-content: center;
        }
        .modal-box {
            background: white;
            width: 90%;
            max-width: 460px;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            position: relative;
        }
        .form-group {
            margin-bottom: 14px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #181c32;
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            height: 42px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0 12px;
            font-size: 13px;
            color: #181c32;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.15s;
        }
        .form-control:focus {
            border-color: #6c5ce7;
        }

        /* Toast */
        .toast-notif {
            display: none;
            position: fixed;
            top: 76px;
            right: 28px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 12px 20px;
            font-size: 13px;
            font-weight: 600;
            color: #181c32;
            z-index: 999999;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(-10px);
        }
    </style>
    <link rel="stylesheet" href="/css/responsive.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo-icon">
                <i class="fa-solid fa-heart"></i>
            </div>
            <div class="sidebar-title">
                <h2>{{ $ownerName }}</h2>
                <p>AI Bot Console</p>
            </div>
        </div>
        
        @include('components.sidebar-nav')
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <button class="mobile-menu-btn" style="display:none; background:none; border:none; font-size:20px; cursor:pointer; margin-right:16px; color:var(--text-dark);"><i class="fa-solid fa-bars"></i></button>
            <div class="page-title">
                <h1>Manajemen Tim</h1>
                <p>Undang Admin CS via email dan atur siapa yang bisa mengakses shared inbox.</p>
            </div>
            
            <div class="top-actions">
                @php
                    $isWaConnected = $user && $user->wa_status == 'connected';
                @endphp
                @if($isWaConnected)
                <div class="status-pill">
                    <div class="status-dot"></div>
                    WhatsApp Connected
                </div>
                @else
                <div class="status-pill" style="background-color: #f1f1f4; color: var(--text-muted); border-color: #e4e6ef;">
                    <div class="status-dot" style="background-color: var(--text-muted);"></div>
                    WhatsApp Disconnected
                </div>
                @endif
                
                <div class="profile-wrapper">
                    <div class="user-profile">
                        <div class="avatar">
                            {{ strtoupper(substr($ownerName, 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <h4>{{ $ownerName }} <i class="fa-solid fa-chevron-down" style="font-size: 10px; color: #a1a5b7; margin-left: 2px;"></i></h4>
                            <p>Unlimited</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kapasitas Tim Card -->
        <div class="capacity-card">
            <div class="capacity-header">
                <div>
                    <div class="capacity-badge-title">
                        <i class="fa-regular fa-id-badge"></i> Kapasitas Tim
                    </div>
                    <div class="capacity-headline" id="capacityHeadline">
                        {{ $seatsUsed }}/{{ $totalSeats }} User Seats
                    </div>
                    <div class="capacity-subtitle">
                        Paket Unlimited — 1 Owner + {{ $totalSeats - 1 }} Admin CS
                    </div>
                </div>
                <button type="button" class="btn-invite" onclick="openInviteModal()">
                    <i class="fa-solid fa-user-plus"></i> Undang Admin CS
                </button>
            </div>
            <div class="progress-track">
                @php
                    $percent = ($seatsUsed / $totalSeats) * 100;
                @endphp
                <div class="progress-fill" id="capacityProgressFill" style="width: {{ max(1.01, $percent) }}%;"></div>
            </div>
            <div class="capacity-remaining" id="capacityRemainingText">
                {{ $seatsRemaining }} seat tersisa.
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="tabs-row">
            <button type="button" class="tab-btn active" id="tabBtnActive" onclick="switchTeamTab('active')">
                Aktif (<span id="countActive">{{ $activeCount }}</span>)
            </button>
            <button type="button" class="tab-btn" id="tabBtnPending" onclick="switchTeamTab('pending')">
                Menunggu Konfirmasi (<span id="countPending">{{ $pendingCount }}</span>)
            </button>
        </div>

        <!-- Members Card -->
        <div class="members-card">
            <!-- Active Tab Content -->
            <div id="tabContentActive">
                <!-- Owner Row -->
                <div class="member-row">
                    <div class="member-info">
                        <div class="member-avatar avatar-owner">
                            <i class="fa-solid fa-crown" style="font-size: 15px;"></i>
                        </div>
                        <div class="member-text">
                            <h3>{{ $ownerName }}</h3>
                            <p>{{ $ownerEmail }}</p>
                        </div>
                    </div>
                    <div>
                        <span class="role-badge">Owner</span>
                    </div>
                </div>

                <!-- Active Admin CS Container -->
                <div id="activeAdminListContainer">
                    @if(count($activeMembers) > 0)
                        @foreach($activeMembers as $m)
                        <div class="member-row" id="member-row-{{ $m->id }}">
                            <div class="member-info">
                                <div class="member-avatar avatar-admin">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div class="member-text">
                                    <h3>{{ $m->name }}</h3>
                                    <p>{{ $m->email }} @if($m->phone) &bull; {{ $m->phone }} @endif</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="role-badge">Admin CS</span>
                                <button type="button" onclick="deleteTeamMember({{ $m->id }})" title="Hapus Admin CS" style="background: none; border: none; cursor: pointer; color: #a1a5b7; padding: 6px; font-size: 14px;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#a1a5b7'">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-state" id="emptyStateActive">
                            Belum ada Admin CS aktif. Undang mereka lewat email.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pending Tab Content -->
            <div id="tabContentPending" style="display: none;">
                <div id="pendingListContainer">
                    @if(count($pendingMembers) > 0)
                        @foreach($pendingMembers as $p)
                        <div class="member-row" id="pending-row-{{ $p->id }}" style="display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; border-bottom: 1px solid #f1f1f4;">
                            <div class="member-info" style="display: flex; align-items: center; gap: 14px;">
                                <div style="width: 42px; height: 42px; border-radius: 50%; background: #fef3c7; display: flex; align-items: center; justify-content: center; color: #d97706; font-size: 16px; flex-shrink: 0;">
                                    <i class="fa-regular fa-clock"></i>
                                </div>
                                <div class="member-text">
                                    <h3 style="font-size: 14px; font-weight: 700; color: #181c32; margin-bottom: 2px;">{{ $p->name ?: 'Admin CS' }}</h3>
                                    <p style="font-size: 13px; color: #64748b; margin: 0;">{{ $p->email }}</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                <span style="background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                                    <span>⏳</span> Menunggu Konfirmasi
                                </span>
                                <button type="button" onclick="resendInvite({{ $p->id }})" style="background: white; border: 1px solid #e2e8f0; color: #1e293b; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: 0.15s;" onmouseover="this.style.borderColor='#6c5ce7'; this.style.color='#6c5ce7'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#1e293b'">
                                    <i class="fa-regular fa-paper-plane" style="font-size: 12px;"></i> Kirim Ulang Email
                                </button>
                                <button type="button" onclick="sendResetPassword({{ $p->id }})" style="background: white; border: 1px solid #e2e8f0; color: #1e293b; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: 0.15s;" onmouseover="this.style.borderColor='#6c5ce7'; this.style.color='#6c5ce7'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#1e293b'">
                                    <i class="fa-solid fa-key" style="font-size: 12px;"></i> Kirim Reset Password
                                </button>
                                <button type="button" onclick="deleteTeamMember({{ $p->id }})" style="background: none; border: none; color: #ef4444; font-size: 13px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; padding: 6px 8px;">
                                    <i class="fa-regular fa-trash-can" style="font-size: 13px;"></i> Hapus Undangan
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-state" id="emptyStatePending">
                            Belum ada undangan yang menunggu konfirmasi.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notice Banner -->
        <div class="notice-banner">
            <i class="fa-solid fa-shield-halved"></i>
            <p>
                Admin CS yang diundang akan menerima email berisi link aktivasi. Setelah membuat password, akun mereka otomatis terhubung ke vendor ini dan langsung diarahkan ke Live Chat Inbox. Anggota aktif menempati seat, undangan pending dan anggota nonaktif tidak.
            </p>
        </div>
    </div>

    <!-- Modal Undang Admin CS (Image 1) -->
    <div class="modal-overlay" id="inviteModal" onclick="if(event.target === this) closeInviteModal()">
        <div class="modal-box" style="background: white; width: 90%; max-width: 440px; border-radius: 16px; padding: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #181c32;">Undang Admin CS</h3>
                <i class="fa-solid fa-xmark" style="cursor: pointer; color: #a1a5b7; font-size: 16px; padding: 4px;" onclick="closeInviteModal()"></i>
            </div>
            <p style="font-size: 13px; color: #64748b; margin-bottom: 20px; line-height: 1.5;">
                Kami akan mengirim email undangan berisi link untuk membuat password.
            </p>

            <form id="inviteForm" onsubmit="submitInvite(event)">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #181c32; margin-bottom: 6px;">Nama Admin</label>
                    <input type="text" id="inputInviteName" class="form-control" placeholder="Admin Maya" style="width: 100%; height: 42px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0 14px; font-size: 13px; color: #181c32; outline: none; box-sizing: border-box;">
                </div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #181c32; margin-bottom: 6px;">Email Admin</label>
                    <input type="email" id="inputInviteEmail" class="form-control" placeholder="maya@vendor.com" required style="width: 100%; height: 42px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0 14px; font-size: 13px; color: #181c32; outline: none; box-sizing: border-box;">
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeInviteModal()" style="height: 38px; padding: 0 20px; border: 1px solid #e2e8f0; background: white; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; color: #181c32;">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitInvite" style="height: 38px; padding: 0 20px; background: #8c7ae6; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; color: white;">
                        Kirim Undangan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notification (Image 2) -->
    <div id="teamToast" class="toast-notif">
        <span style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; background: #181c32; color: #fff; border-radius: 50%; font-size: 11px; flex-shrink: 0;">
            <i class="fa-solid fa-check"></i>
        </span>
        <span id="teamToastText">Undangan terkirim. Admin CS akan menerima email aktivasi akun.</span>
    </div>

    @include('components.whatsapp-widget')

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // Sidebar collapse
        const btnCollapse = document.getElementById('btn-collapse');
        if (btnCollapse) {
            btnCollapse.addEventListener('click', () => {
                document.body.classList.toggle('sidebar-collapsed');
            });
        }

        // Tabs switching
        let currentTab = 'active';
        function switchTeamTab(tab) {
            currentTab = tab;
            const btnActive = document.getElementById('tabBtnActive');
            const btnPending = document.getElementById('tabBtnPending');
            const contentActive = document.getElementById('tabContentActive');
            const contentPending = document.getElementById('tabContentPending');

            if (tab === 'active') {
                btnActive.classList.add('active');
                btnPending.classList.remove('active');
                contentActive.style.display = 'block';
                contentPending.style.display = 'none';
            } else {
                btnPending.classList.add('active');
                btnActive.classList.remove('active');
                contentActive.style.display = 'none';
                contentPending.style.display = 'block';
            }
        }

        // Modal controls
        function openInviteModal() {
            document.getElementById('inputInviteName').value = '';
            document.getElementById('inputInviteEmail').value = '';
            document.getElementById('inviteModal').style.display = 'flex';
        }

        function closeInviteModal() {
            document.getElementById('inviteModal').style.display = 'none';
        }

        // Submit invitation
        function submitInvite(e) {
            e.preventDefault();
            const name = document.getElementById('inputInviteName').value.trim();
            const email = document.getElementById('inputInviteEmail').value.trim();

            if (!email) {
                alert("Harap masukkan alamat email Admin CS");
                return;
            }

            const btn = document.getElementById('btnSubmitInvite');
            btn.disabled = true;
            btn.innerText = "Mengirim...";

            fetch('/api/team/invite', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ name, email })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerText = "Kirim Undangan";
                if (data.success) {
                    closeInviteModal();
                    showTeamToast(data.message || "Undangan berhasil dikirim");
                    refreshTeamData();
                } else {
                    alert(data.error || "Gagal mengirim undangan");
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerText = "Kirim Undangan";
                alert("Terjadi kesalahan sistem");
            });
        }

        // Delete member or invitation
        function deleteTeamMember(id) {
            if (!confirm("Yakin ingin menghapus anggota / undangan ini?")) return;

            fetch(`/api/team/members/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showTeamToast(data.message || "Berhasil dihapus");
                    refreshTeamData();
                } else {
                    alert(data.error || "Gagal menghapus");
                }
            })
            .catch(() => {
                alert("Terjadi kesalahan sistem");
            });
        }

        // Resend invitation email
        function resendInvite(id, btnEl) {
            const btn = btnEl || event?.currentTarget;
            const origHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="font-size: 12px;"></i> Mengirim...';
            }

            fetch(`/api/team/members/${id}/resend`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                }
                if (data.success) {
                    showTeamToast(data.message || "Undangan berhasil dikirim ulang.");
                } else {
                    alert(data.error || "Gagal mengirim ulang undangan");
                }
            })
            .catch(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                }
                alert("Terjadi kesalahan sistem");
            });
        }

        // Send reset password email
        function sendResetPassword(id, btnEl) {
            const btn = btnEl || event?.currentTarget;
            const origHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="font-size: 12px;"></i> Mengirim...';
            }

            fetch(`/api/team/members/${id}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                }
                if (data.success) {
                    showTeamToast(data.message || "Email reset password berhasil dikirim.");
                } else {
                    alert(data.error || "Gagal mengirim email reset password");
                }
            })
            .catch(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                }
                alert("Terjadi kesalahan sistem");
            });
        }

        // Activate team member (toggle status)
        function activateTeamMember(id) {
            fetch(`/api/team/members/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showTeamToast(data.message || "Status berhasil diperbarui");
                    refreshTeamData();
                } else {
                    alert(data.error || "Gagal memperbarui status");
                }
            })
            .catch(() => {
                alert("Terjadi kesalahan sistem");
            });
        }

        // Refresh team members list dynamically
        function refreshTeamData() {
            fetch('/api/team/members')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const stats = data.stats;
                    // Update stats
                    document.getElementById('capacityHeadline').innerText = `${stats.seats_used}/${stats.total_seats} User Seats`;
                    document.getElementById('capacityRemainingText').innerText = `${stats.seats_remaining} seat tersisa.`;
                    const percent = Math.max(1.01, (stats.seats_used / stats.total_seats) * 100);
                    document.getElementById('capacityProgressFill').style.width = `${percent}%`;

                    document.getElementById('countActive').innerText = stats.active_count;
                    document.getElementById('countPending').innerText = stats.pending_count;

                    // Update Active Admin CS list
                    const activeContainer = document.getElementById('activeAdminListContainer');
                    if (data.active_members && data.active_members.length > 0) {
                        let html = '';
                        data.active_members.forEach(m => {
                            html += `
                            <div class="member-row" id="member-row-${m.id}">
                                <div class="member-info">
                                    <div class="member-avatar avatar-admin">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <div class="member-text">
                                        <h3>${escapeHtml(m.name || 'Admin CS')}</h3>
                                        <p>${escapeHtml(m.email)} ${m.phone ? `&bull; ${escapeHtml(m.phone)}` : ''}</p>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span class="role-badge">Admin CS</span>
                                    <button type="button" onclick="deleteTeamMember(${m.id})" title="Hapus Admin CS" style="background: none; border: none; cursor: pointer; color: #a1a5b7; padding: 6px; font-size: 14px;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#a1a5b7'">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>`;
                        });
                        activeContainer.innerHTML = html;
                    } else {
                        activeContainer.innerHTML = `<div class="empty-state" id="emptyStateActive">Belum ada Admin CS aktif. Undang mereka lewat email.</div>`;
                    }

                    // Update Pending list matching Image 3
                    const pendingContainer = document.getElementById('pendingListContainer');
                    if (data.pending_members && data.pending_members.length > 0) {
                        let phtml = '';
                        data.pending_members.forEach(p => {
                            phtml += `
                            <div class="member-row" id="pending-row-${p.id}" style="display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; border-bottom: 1px solid #f1f1f4;">
                                <div class="member-info" style="display: flex; align-items: center; gap: 14px;">
                                    <div style="width: 42px; height: 42px; border-radius: 50%; background: #fef3c7; display: flex; align-items: center; justify-content: center; color: #d97706; font-size: 16px; flex-shrink: 0;">
                                        <i class="fa-regular fa-clock"></i>
                                    </div>
                                    <div class="member-text">
                                        <h3 style="font-size: 14px; font-weight: 700; color: #181c32; margin-bottom: 2px;">${escapeHtml(p.name || 'Admin CS')}</h3>
                                        <p style="font-size: 13px; color: #64748b; margin: 0;">${escapeHtml(p.email)}</p>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                    <span style="background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                                        <span>⏳</span> Menunggu Konfirmasi
                                    </span>
                                    <button type="button" onclick="resendInvite(${p.id}, this)" style="background: white; border: 1px solid #e2e8f0; color: #1e293b; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: 0.15s;" onmouseover="this.style.borderColor='#6c5ce7'; this.style.color='#6c5ce7'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#1e293b'">
                                        <i class="fa-regular fa-paper-plane" style="font-size: 12px;"></i> Kirim Ulang Email
                                    </button>
                                    <button type="button" onclick="sendResetPassword(${p.id}, this)" style="background: white; border: 1px solid #e2e8f0; color: #1e293b; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: 0.15s;" onmouseover="this.style.borderColor='#6c5ce7'; this.style.color='#6c5ce7'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#1e293b'">
                                        <i class="fa-solid fa-key" style="font-size: 12px;"></i> Kirim Reset Password
                                    </button>
                                    <button type="button" onclick="deleteTeamMember(${p.id})" style="background: none; border: none; color: #ef4444; font-size: 13px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; padding: 6px 8px;">
                                        <i class="fa-regular fa-trash-can" style="font-size: 13px;"></i> Hapus Undangan
                                    </button>
                                </div>
                            </div>`;
                        });
                        pendingContainer.innerHTML = phtml;
                    } else {
                        pendingContainer.innerHTML = `<div class="empty-state" id="emptyStatePending">Belum ada undangan yang menunggu konfirmasi.</div>`;
                    }
                }
            })
            .catch(() => {});
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }

        // Toast Notification
        function showTeamToast(message) {
            const toast = document.getElementById('teamToast');
            const textEl = document.getElementById('teamToastText');
            if (toast && textEl) {
                textEl.innerText = message;
                toast.style.display = 'flex';
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
                
                if (window.toastTimeout) clearTimeout(window.toastTimeout);
                window.toastTimeout = setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-10px)';
                    setTimeout(() => { toast.style.display = 'none'; }, 300);
                }, 3000);
            }
        }
    </script>
    <script src="/js/responsive.js"></script>
</body>
</html>

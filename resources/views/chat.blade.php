<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Chat Inbox - CloseMateAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #1e1e2d;
            --sidebar-text: #a1a5b7;
            --sidebar-active-bg: rgba(107, 92, 216, 0.2);
            --sidebar-active-text: #6b5cd8;
            --primary: #6b5cd8;
            --bg-light: #f5f8fa;
            --card-bg: #ffffff;
            --border-color: #eff2f5;
            --text-dark: #181c32;
            --text-muted: #a1a5b7;
            --success: #50cd89;
            --danger: #f1416c;
            --danger-bg: #fff5f8;
            --warning: #ffc700;
            --warning-bg: #fff8dd;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--card-bg); display: flex; min-height: 100vh; color: var(--text-dark); }

        /* Sidebar */
        .sidebar { width: 260px; background-color: var(--sidebar-bg); display: flex; flex-direction: column; height: 100vh; position: fixed; left: 0; top: 0; transition: width 0.3s ease; overflow-x: hidden; z-index: 200; }
        .sidebar-header { padding: 24px; display: flex; align-items: center; gap: 12px; transition: 0.3s ease; }
        .sidebar-logo-icon { min-width: 40px; width: 40px; height: 40px; background-color: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; }
        .sidebar-title { white-space: nowrap; }
        .sidebar-title h2 { color: white; font-size: 16px; font-weight: 600; margin-bottom: 4px; }
        .sidebar-title p { color: var(--sidebar-text); font-size: 12px; }
        
        .nav-menu { flex: 1; overflow-y: auto; padding: 12px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: var(--sidebar-text); text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 500; margin-bottom: 4px; transition: 0.2s; white-space: nowrap; cursor: pointer; }
        .nav-item:hover { color: white; background-color: rgba(255,255,255,0.05); }
        .nav-item.active { background-color: var(--sidebar-active-bg); color: var(--sidebar-active-text); }
        .nav-item i { font-size: 16px; width: 20px; text-align: center; }

        /* Main Content */
        .main-content { margin-left: 260px; flex: 1; display: flex; flex-direction: column; transition: margin-left 0.3s ease; height: 100vh; }
        
        .topbar { display: flex; justify-content: space-between; align-items: center; padding: 16px 30px; background-color: white; border-bottom: 1px solid var(--border-color); }
        .page-title h1 { font-size: 20px; font-weight: 600; margin-bottom: 6px; }
        .page-title p { font-size: 14px; color: var(--text-muted); }
        
        .top-actions { display: flex; align-items: center; gap: 20px; }
        .status-pill { display: flex; align-items: center; gap: 8px; background-color: rgba(80, 205, 137, 0.1); color: var(--success); padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid rgba(80, 205, 137, 0.2); }
        .status-dot { width: 8px; height: 8px; background-color: var(--success); border-radius: 50%; }
        
        .profile-wrapper { position: relative; cursor: pointer; }
        .user-profile { display: flex; align-items: center; gap: 12px; padding: 6px 16px 6px 6px; border-radius: 30px; transition: background-color 0.2s; }
        .user-profile:hover { background-color: rgba(107, 92, 216, 0.08); }
        .avatar { width: 36px; height: 36px; background-color: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; }
        .user-info h4 { font-size: 14px; font-weight: 600; }
        .user-info p { font-size: 12px; color: var(--text-muted); }

        /* Chat Layout */
        .chat-container { display: flex; flex: 1; overflow: hidden; }
        
        /* Sidebar Chat */
        .chat-sidebar { width: 280px; border-right: 1px solid var(--border-color); display: flex; flex-direction: column; background: white; flex-shrink: 0; }
        .chat-search { padding: 16px; display: flex; gap: 8px; align-items: center; }
        .search-box { position: relative; flex: 1; }
        .search-box input { width: 100%; padding: 10px 10px 10px 36px; border: 1px solid var(--border-color); border-radius: 20px; font-size: 13px; outline: none; background: var(--bg-light); transition: 0.2s; }
        .search-box input:focus { border-color: var(--primary); background: white; }
        .search-box i { position: absolute; left: 14px; top: 12px; color: var(--text-muted); font-size: 13px; }
        .btn-icon { width: 36px; height: 36px; border: 1px solid var(--border-color); background: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--text-dark); cursor: pointer; flex-shrink: 0; transition: 0.2s; }
        .btn-icon:hover { background: var(--bg-light); }
        
        .chat-tabs-wrapper { background: var(--bg-light); padding: 4px; border-radius: 12px; display: flex; margin: 0 16px 16px 16px; }
        .chat-tab { flex: 1; text-align: center; font-size: 12px; font-weight: 600; padding: 8px; color: var(--text-muted); cursor: pointer; border-radius: 8px; }
        .chat-tab.active { background: white; color: var(--text-dark); box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        
        .chat-filters { padding: 0 16px 16px 16px; display: flex; gap: 8px; border-bottom: 1px solid var(--border-color); flex-wrap: wrap; }
        .filter-pill { padding: 6px 12px; border: 1px solid var(--border-color); border-radius: 20px; font-size: 11px; color: var(--text-muted); cursor: pointer; font-weight: 500; background: white; }
        .filter-pill.active { background: var(--primary); color: white; border-color: var(--primary); }
        
        .chat-list { flex: 1; overflow-y: auto; }
        .chat-item { padding: 16px; border-bottom: 1px solid var(--border-color); cursor: pointer; transition: 0.2s; }
        .chat-item:hover { background: var(--bg-light); }
        .chat-item.active { background: rgba(107, 92, 216, 0.05); border-left: 3px solid var(--primary); }
        .chat-item-header { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .chat-item-title { font-weight: 600; font-size: 14px; color: var(--text-dark); }
        .chat-item-time { font-size: 11px; color: var(--text-muted); }
        .chat-item-subtitle { font-size: 11px; color: var(--text-muted); margin-bottom: 6px; }
        .chat-item-msg { font-size: 12px; color: var(--text-muted); margin-bottom: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .chat-item-tags { display: flex; gap: 6px; }
        .tag { padding: 4px 8px; border-radius: 12px; font-size: 10px; font-weight: 600; }
        .tag-danger { background: var(--danger-bg); color: var(--danger); }
        .tag-primary { background: rgba(107, 92, 216, 0.1); color: var(--primary); }
        .tag-light { background: var(--bg-light); color: var(--text-muted); }

        /* Main Chat */
        .chat-main { flex: 1; display: flex; flex-direction: column; background: #fcfcfc; min-width: 350px; }
        .chat-header { padding: 12px 16px; background: white; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .chat-header-user { display: flex; align-items: center; gap: 10px; }
        .chat-header-avatar { width: 36px; height: 36px; background: #e4e6ef; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; color: var(--text-muted); }
        .chat-header-info h3 { font-size: 14px; font-weight: 600; margin-bottom: 2px; }
        .chat-header-info p { font-size: 11px; color: var(--text-muted); }
        .chat-header-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .btn-action { padding: 6px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px; border: none; white-space: nowrap; }
        .btn-outline { background: white; border: 1px solid var(--border-color); color: var(--text-dark); }
        .btn-outline:hover { background: var(--bg-light); }
        
        .takeover-toggle-container { display: flex; align-items: center; gap: 8px; margin-left: 8px; }
        .takeover-label { font-size: 12px; font-weight: 600; color: var(--primary); }
        
        /* Toggle Switch */
        .switch { position: relative; display: inline-block; width: 36px; height: 20px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 20px; }
        .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 2px; bottom: 2px; background-color: white; transition: .4s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        input:checked + .slider { background-color: var(--primary); }
        input:checked + .slider:before { transform: translateX(16px); }
        
        .chat-banner { background: var(--danger-bg); padding: 10px 24px; color: var(--danger); font-size: 12px; font-weight: 500; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid rgba(241, 65, 108, 0.1); }
        
        .chat-messages { flex: 1; overflow-y: auto; padding: 24px; display: flex; flex-direction: column; gap: 12px; }
        .bubble-wrapper { display: flex; flex-direction: column; max-width: 70%; }
        .bubble-wrapper.right { align-self: flex-end; align-items: flex-end; }
        .bubble-wrapper.left { align-self: flex-start; align-items: flex-start; }
        
        .bubble { padding: 12px 16px; border-radius: 12px; font-size: 13px; line-height: 1.5; position: relative; }
        .bubble-right { background: var(--primary); color: white; border-bottom-right-radius: 4px; }
        .bubble-left { background: white; color: var(--text-dark); border: 1px solid var(--border-color); border-bottom-left-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        
        .bubble-time { font-size: 10px; color: var(--text-muted); margin-top: 4px; }
        .bubble-time.right { text-align: right; }
        
        .ai-badge { position: absolute; top: -10px; right: -8px; background: white; color: var(--primary); font-size: 9px; font-weight: 700; padding: 2px 6px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }

        .chat-input-area { padding: 16px 24px; background: white; border-top: 1px solid var(--border-color); }
        .chat-input-box { display: flex; align-items: flex-end; gap: 12px; background: white; border: 1px solid var(--border-color); border-radius: 12px; padding: 12px 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
        .chat-input-box input { flex: 1; border: none; outline: none; font-size: 13px; padding: 8px 0; background: transparent; }
        .chat-input-actions { display: flex; gap: 12px; color: var(--text-muted); align-items: center; padding-bottom: 8px; }
        .chat-input-actions i { cursor: pointer; transition: 0.2s; font-size: 16px; }
        .chat-input-actions i:hover { color: var(--primary); }
        .btn-send { background: var(--primary); color: white; border: none; border-radius: 8px; width: 80px; height: 36px; font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; }
        .btn-send:hover { background: #5a4bce; }

        /* Details Sidebar */
        .chat-details { width: 250px; border-left: 1px solid var(--border-color); background: white; padding: 16px; flex-shrink: 0; overflow-y: auto; }
        .details-header { margin-bottom: 20px; }
        .details-header h2 { font-size: 16px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px; }
        .details-header p { font-size: 12px; color: var(--text-muted); }
        
        .client-card { border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
        .client-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px; }
        .client-name h3 { font-size: 15px; font-weight: 600; }
        .client-name p { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
        
        .info-row { display: flex; gap: 12px; margin-bottom: 12px; }
        .info-icon { width: 20px; color: var(--text-muted); font-size: 13px; text-align: center; margin-top: 2px; }
        .info-content h4 { font-size: 11px; color: var(--text-muted); font-weight: 500; margin-bottom: 2px; }
        .info-content p { font-size: 13px; font-weight: 500; color: var(--text-dark); }
        
        .form-group { margin-bottom: 16px; }
        .form-label { font-size: 12px; font-weight: 600; color: var(--text-dark); margin-bottom: 8px; display: block; }
        .form-select { width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; outline: none; appearance: none; background: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e") no-repeat right 12px center; background-size: 14px; }
        
        .tag-box { padding: 6px 12px; background: var(--danger-bg); color: var(--danger); font-size: 12px; font-weight: 600; border-radius: 6px; display: inline-block; margin-bottom: 20px; }
        
        .quick-notes textarea { width: 100%; height: 100px; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; outline: none; resize: none; }
        .note-hint { font-size: 10px; color: var(--text-muted); margin-top: 6px; }
        
        /* Toast Notification */
        .toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 1000; }
        .toast { background: var(--text-dark); color: white; padding: 12px 24px; border-radius: 8px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); margin-top: 10px; animation: slideIn 0.3s forwards; }
        .toast i { color: var(--success); font-size: 16px; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
        @keyframes fadeOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }

        /* Responsive Breakpoints */
        @media (max-width: 1400px) {
            .sidebar { width: 200px; }
            .main-content { margin-left: 200px; }
            .chat-sidebar { width: 250px; }
            .chat-details { width: 220px; }
        }
        @media (max-width: 1200px) {
            .sidebar { width: 70px; }
            .main-content { margin-left: 70px; }
            .sidebar-title { display: none; }
            .nav-item span { display: none; }
            .nav-item { justify-content: center; padding: 12px; }
            .nav-item i { font-size: 18px; margin: 0; }
            #btn-collapse { display: none; }
            .chat-header { flex-direction: column; align-items: flex-start; }
            .chat-header-actions { width: 100%; padding-top: 8px; border-top: 1px solid var(--border-color); margin-top: 4px; }
        }
        @media (max-width: 992px) {
            .chat-details { display: none; } /* Hide details on small screens */
        }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo-icon">
                <i class="fa-solid fa-heart"></i>
            </div>
            <div class="sidebar-title">
                <h2>{{ auth()->check() ? auth()->user()->business_name : 'CloseMateAI' }}</h2>
                <p>AI Bot Console</p>
            </div>
        </div>
        
        <div class="nav-menu">
            <a class="nav-item"><i class="fa-solid fa-circle-play"></i> <span>Video Tutorial</span></a>
            <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}"><i class="fa-solid fa-border-all"></i> <span>Dashboard & WA Status</span></a>
            <a href="/chat" class="nav-item {{ request()->is('chat') ? 'active' : '' }}"><i class="fa-solid fa-message"></i> <span>Live Chat Inbox</span></a>
            <a href="/knowledge" class="nav-item {{ request()->is('knowledge') ? 'active' : '' }}"><i class="fa-solid fa-book"></i> <span>Knowledge Base</span></a>
            <a href="/leads" class="nav-item {{ request()->is('leads') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> <span>Leads CRM</span></a>
            <a href="/followup" class="nav-item {{ request()->is('followup') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> <span>Follow-Up Otomatis</span></a>
            <a href="/booking" class="nav-item {{ request()->is('booking') ? 'active' : '' }}"><i class="fa-regular fa-calendar-check"></i> <span>Booking & Operasional</span></a>
            <a href="/invoice" class="nav-item {{ request()->is('invoice') ? 'active' : '' }}"><i class="fa-solid fa-file-invoice"></i> <span>Invoice Generator</span></a>
            <a href="/quotations" class="nav-item {{ request()->is('quotations') ? 'active' : '' }}"><i class="fa-solid fa-file-contract"></i> <span>Quotation Generator</span></a>
            <a href="/finance" class="nav-item {{ request()->is('finance') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i> <span>Laporan Keuangan</span></a>
            <a href="/production" class="nav-item {{ request()->is('production') ? 'active' : '' }}"><i class="fa-solid fa-list-check"></i> <span>Progres Produksi</span></a>
            <a class="nav-item"><i class="fa-solid fa-user-group"></i> <span>Manajemen Tim</span></a>
            <a href="/activity" class="nav-item {{ request()->is('activity') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Aktivitas</span></a>
            <a class="nav-item"><i class="fa-solid fa-credit-card"></i> <span>Langganan & Pembayaran</span></a>
            
            <div style="margin-top: 30px;"></div>
            
            <a href="/settings" class="nav-item {{ request()->is('settings') ? 'active' : '' }}"><i class="fa-solid fa-gear"></i> <span>Settings & Profile</span></a>
            <a class="nav-item" id="btn-collapse" style="margin-top: auto; opacity: 0.7;">
                <i class="fa-solid fa-angles-left" id="collapse-icon" style="transition: transform 0.3s;"></i> <span>Collapse</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <div class="page-title">
                <h1>Live Chat Inbox</h1>
                <p>Percakapan WhatsApp real-time</p>
            </div>
            <div class="top-actions">
                <div class="status-pill">
                    <div class="status-dot"></div>
                    WhatsApp Connected
                </div>
                <div class="profile-wrapper">
                    <div class="user-profile">
                        <div class="avatar">{{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'W' }}</div>
                        <div class="user-info">
                            <h4>{{ auth()->check() ? auth()->user()->name : 'weddingvidgram' }} <i class="fa-solid fa-chevron-down"></i></h4>
                            <p>Enterprise</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Container -->
        <div class="chat-container">
            <!-- Left Panel: Chat List -->
            <div class="chat-sidebar">
                <div class="chat-search">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Cari nama atau pesan...">
                    </div>
                    <div class="btn-icon">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </div>
                </div>
                
                <div class="chat-tabs-wrapper">
                    <div class="chat-tab">Semua Chat</div>
                    <div class="chat-tab">Chat Saya</div>
                    <div class="chat-tab active">Belum<br>Dihandle</div>
                </div>
                
                <div class="chat-filters">
                    <div class="filter-pill active">All</div>
                    <div class="filter-pill">AI Active</div>
                    <div class="filter-pill">Human Takeover</div>
                    <div class="filter-pill">Unread</div>
                </div>
                
                <div class="chat-list" id="chatListContainer">
                    <div style="height: 100%; display: flex; align-items: center; justify-content: center; color: #a1a5b7; font-size: 13px; font-weight: 500;">
                        Belum ada percakapan masuk
                    </div>
                </div>
            </div>

            <!-- Middle Panel: Chat View -->
            <div class="chat-main">
                <div class="chat-header" id="chatHeader" style="width: 100%; display: none;">
                    <div class="chat-header-user">
                        <div class="chat-header-avatar">Be</div>
                        <div class="chat-header-info">
                            <h3>Bekti</h3>
                            <p style="color: #6b5cd8; font-weight: 500; background: rgba(107, 92, 216, 0.1); display: inline-block; padding: 2px 8px; border-radius: 12px; margin-top: 2px;">Belum Dihandle</p>
                        </div>
                    </div>
                    <div class="chat-header-actions">
                        <button class="btn-action btn-outline"><i class="fa-regular fa-user"></i> Ambil Chat Ini</button>
                        <button class="btn-action btn-outline" style="color: #dc3545; border-color: rgba(220, 53, 69, 0.2);" onclick="clearChatHistory()"><i class="fa-solid fa-trash"></i> Reset & Hapus Chat</button>
                        <button class="btn-action btn-outline"><i class="fa-solid fa-share-nodes"></i> Pindahkan Handler</button>
                        
                        <div class="takeover-toggle-container">
                            <span class="takeover-label" id="takeoverText">Human Takeover</span>
                            <label class="switch">
                                <input type="checkbox" id="humanTakeoverToggle" checked onchange="toggleHumanTakeover()">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="chat-banner" id="chatBanner" style="display: none;">
                    <i class="fa-solid fa-microphone-lines-slash"></i> AI di-PAUSE untuk chat ini – matikan toggle untuk mengaktifkan AI kembali
                </div>
                
                <div class="chat-messages" id="chatMessagesBox" style="display: flex; align-items: center; justify-content: center;">
                    <div style="color: #a1a5b7; font-size: 14px; font-weight: 500;">Belum ada percakapan masuk</div>
                </div>
                
                <div class="chat-input-area" id="chatInputArea" style="width: 100%; display: none;">
                    <div class="chat-input-box">
                        <div class="chat-input-actions" style="gap: 16px;">
                            <i class="fa-solid fa-paperclip"></i>
                            <i class="fa-solid fa-microphone"></i>
                            <span style="font-size: 12px; font-weight: 600; cursor: pointer;"><i class="fa-solid fa-bolt"></i> Quick Reply</span>
                        </div>
                        <input type="text" id="chatInputMessage" placeholder="Tulis pesan untuk klien... (Enter untuk kirim, Shift+Enter baris baru)">
                        <button class="btn-send"><i class="fa-regular fa-paper-plane" style="margin-right: 6px;"></i> Send Message</button>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Lead Details -->
            <div class="chat-details">
                <div class="details-header">
                    <h2>Lead Details</h2>
                    <p>Informasi klien & catatan internal</p>
                </div>
                
                <div class="client-card">
                    <div class="client-card-header">
                        <div class="client-name">
                            <h3>Bekti</h3>
                            <p>WA Name: Bekti | 62895367938408</p>
                        </div>
                        <i class="fa-solid fa-pen" style="color: var(--text-muted); cursor: pointer; font-size: 14px;"></i>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-icon"><i class="fa-regular fa-calendar"></i></div>
                        <div class="info-content">
                            <h4>Event Date</h4>
                            <p>—</p>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                        <div class="info-content">
                            <h4>Phone</h4>
                            <p>62895367938408</p>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon"><i class="fa-solid fa-wallet"></i></div>
                        <div class="info-content">
                            <h4>Estimated Budget</h4>
                            <p>—</p>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div class="info-content">
                            <h4>Location</h4>
                            <p>—</p>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Lead Status</label>
                    <select class="form-select">
                        <option>Hot Lead</option>
                        <option>Warm Lead</option>
                        <option>Cold Lead</option>
                        <option>Closed</option>
                    </select>
                </div>
                
                <div class="tag-box">Hot Lead</div>
                
                <div class="form-group quick-notes">
                    <label class="form-label">Quick Notes</label>
                    <textarea placeholder=""></textarea>
                    <div class="note-hint">Tersimpan saat kamu klik di luar kotak catatan</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="toast-container" id="toast-container"></div>

    
    <script>
        let activeChatId = null;
        let chatsData = [];

        async function fetchChats() {
            try {
                let res = await fetch('/api/chats');
                let chats = await res.json();
                chatsData = chats;
                renderChatList(chats);
                if(activeChatId) {
                    // Update active chat silently if new messages arrived
                    let stillExists = chats.find(c => c.id === activeChatId);
                    if(stillExists && stillExists.messages.length > 0) {
                        // We will just re-fetch the active chat
                        fetchActiveChatMessages(true);
                    }
                }
            } catch (e) { console.error(e); }
        }

        function renderChatList(chats) {
            const container = document.getElementById('chatListContainer');
            if (chats.length === 0) {
                container.innerHTML = '<div style="height: 100%; display: flex; align-items: center; justify-content: center; color: #a1a5b7; font-size: 13px; font-weight: 500;">Belum ada percakapan masuk</div>';
                
                const msgBox = document.getElementById('chatMessagesBox');
                if (msgBox) {
                    msgBox.innerHTML = '<div style="height: 100%; display: flex; align-items: center; justify-content: center; color: #a1a5b7; font-size: 14px; font-weight: 500;">Belum ada percakapan masuk</div>';
                }
                return;
            }
            
            let html = '';
            chats.forEach(chat => {
                let lastMsg = chat.messages && chat.messages.length > 0 ? chat.messages[0].message : 'Tidak ada pesan';
                let time = chat.updated_at ? new Date(chat.updated_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';
                let isActive = chat.id === activeChatId ? 'active' : '';
                let tagColor = chat.is_human_takeover ? 'tag-danger' : 'tag-primary';
                let tagText = chat.is_human_takeover ? 'HUMAN TAKEOVER' : 'AI Active';
                let name = chat.client_name || chat.client_wa_number;

                html += `
                    <div class="chat-item ${isActive}" onclick="openChat(${chat.id})">
                        <div class="chat-item-header">
                            <div class="chat-item-title">${name}</div>
                            <div class="chat-item-time">${time}</div>
                        </div>
                        <div class="chat-item-subtitle">${chat.client_wa_number}</div>
                        <div class="chat-item-msg">${lastMsg.substring(0, 50)}${lastMsg.length > 50 ? '...' : ''}</div>
                        <div class="chat-item-tags">
                            <div class="tag ${tagColor}">${tagText}</div>
                            <div class="tag tag-light">${chat.status}</div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        async function openChat(id) {
            activeChatId = id;
            renderChatList(chatsData); // update active class
            await fetchActiveChatMessages(false);
        }

        async function fetchActiveChatMessages(silent = false) {
            if(!activeChatId) return;
            try {
                let res = await fetch(`/api/chats/${activeChatId}`);
                let data = await res.json();
                
                // Show UI elements
                document.getElementById('chatHeader').style.display = 'flex';
                document.getElementById('chatInputArea').style.display = 'block';
                document.getElementById('chatMessagesBox').style.display = 'block';

                // Update Header
                let name = data.chat.client_name || data.chat.client_wa_number;
                document.querySelector('.chat-header-info h3').innerText = name;
                document.querySelector('.chat-header-avatar').innerText = name.substring(0, 2).toUpperCase();
                
                // Update details panel
                document.querySelector('.client-name h3').innerText = name;
                document.querySelector('.client-name p').innerText = `WA Name: ${name} | ${data.chat.client_wa_number}`;
                document.querySelectorAll('.info-row .info-content p')[1].innerText = data.chat.client_wa_number;
                
                // Update Takeover UI
                const toggle = document.getElementById('humanTakeoverToggle');
                if(toggle && !silent) {
                    toggle.checked = data.chat.is_human_takeover == 1;
                    updateTakeoverUI(data.chat.is_human_takeover == 1, false);
                }

                // Render Messages
                const msgBox = document.getElementById('chatMessagesBox');
                let html = '';
                data.messages.forEach(msg => {
                    let isRight = msg.sender === 'admin' || msg.sender === 'ai';
                    let time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    let badge = msg.sender === 'ai' ? '<div class="ai-badge">AI</div>' : '';
                    let bgColor = msg.sender === 'admin' ? '#4a3da8' : 'var(--primary)'; // slightly different for human
                    
                    if (isRight) {
                        html += `
                            <div class="bubble-wrapper right">
                                <div class="bubble bubble-right" style="background: ${bgColor};">
                                    ${badge}
                                    ${msg.message.replace(/\\n/g, '<br>')}
                                </div>
                                <div class="bubble-time right">${time}</div>
                            </div>
                        `;
                    } else {
                        html += `
                            <div class="bubble-wrapper left">
                                <div class="bubble bubble-left">
                                    ${msg.message.replace(/\\n/g, '<br>')}
                                </div>
                                <div class="bubble-time">${time}</div>
                            </div>
                        `;
                    }
                });
                msgBox.innerHTML = html;
                
                if(!silent) {
                    msgBox.scrollTop = msgBox.scrollHeight;
                } else {
                    // Only scroll down if already near bottom
                    if (msgBox.scrollHeight - msgBox.scrollTop - msgBox.clientHeight < 100) {
                        msgBox.scrollTop = msgBox.scrollHeight;
                    }
                }
            } catch(e) { console.error(e); }
        }

        async function toggleHumanTakeover() {
            if(!activeChatId) return;
            const toggle = document.getElementById('humanTakeoverToggle');
            const isTakeover = toggle.checked;
            
            try {
                let res = await fetch(`/api/chats/${activeChatId}/takeover`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ is_takeover: isTakeover })
                });
                let data = await res.json();
                if(data.success) {
                    updateTakeoverUI(isTakeover, true);
                    fetchChats(); // refresh list
                }
            } catch(e) { console.error(e); }
        }

        async function clearChatHistory() {
            if(!activeChatId) return;
            if(!confirm('Yakin ingin mereset dan menghapus seluruh riwayat chat ini? (Gunakan hanya untuk testing/simulasi)')) return;
            
            try {
                let res = await fetch(`/api/chats/${activeChatId}/clear-history`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                let data = await res.json();
                if(data.success) {
                    showToast(data.message, 'fa-check-circle');
                    fetchActiveChatMessages(false);
                    fetchChats(); // refresh list
                }
            } catch(e) { console.error(e); }
        }

        async function sendAdminMessage() {
            if(!activeChatId) return;
            const input = document.getElementById('chatInputMessage');
            const text = input.value.trim();
            if(!text) return;
            
            input.value = '';
            input.disabled = true;
            
            // Optimistic UI
            const msgBox = document.getElementById('chatMessagesBox');
            let time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            msgBox.innerHTML += `
                <div class="bubble-wrapper right" style="opacity: 0.7;">
                    <div class="bubble bubble-right" style="background: #4a3da8;">
                        ${text.replace(/\\n/g, '<br>')}
                    </div>
                    <div class="bubble-time right">${time} (Sending...)</div>
                </div>
            `;
            msgBox.scrollTop = msgBox.scrollHeight;
            
            try {
                let res = await fetch(`/api/chats/${activeChatId}/send`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ message: text })
                });
                await fetchActiveChatMessages(false);
                fetchChats();
            } catch(e) { 
                console.error(e); 
                showToast('Gagal mengirim pesan', 'fa-times-circle');
            } finally {
                input.disabled = false;
                input.focus();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchChats();
            setInterval(() => {
                fetchChats();
            }, 5000); // refresh every 5s
            
            const btnSend = document.querySelector('.btn-send');
            const input = document.getElementById('chatInputMessage');
            if(btnSend && input) {
                btnSend.onclick = sendAdminMessage;
                input.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        sendAdminMessage();
                    }
                });
            }
        });
        function updateTakeoverUI(isTakeover, showToastAlert = false) {
            const banner = document.getElementById('chatBanner');
            const tag = document.getElementById('tagTakeoverStatus');
            const label = document.getElementById('takeoverText');
            if(!banner || !tag || !label) return;
            if(isTakeover) {
                banner.style.display = 'flex';
                tag.className = 'tag tag-danger';
                tag.innerText = 'HUMAN TAKEOVER';
                label.style.color = 'var(--primary)';
                if(showToastAlert) showToast('Mode Human Takeover Aktif. AI dihentikan.', 'fa-user-shield');
            } else {
                banner.style.display = 'none';
                tag.className = 'tag tag-primary';
                tag.innerText = 'AI Active';
                label.style.color = 'var(--text-muted)';
                if(showToastAlert) showToast('AI kembali aktif', 'fa-robot');
            }
        }
        function showToast(message, iconClass = 'fa-check-circle') {
            const container = document.getElementById('toast-container');
            if(!container) return;
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `<i class="fa-solid ${iconClass}"></i> ${message}`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = 'fadeOut 0.3s forwards';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>

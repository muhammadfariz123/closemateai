<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Generator - CloseMateAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #1e1e2d;
            --sidebar-text: #cbd5e1;
            --sidebar-active-bg: rgba(107, 92, 216, 0.2);
            --sidebar-active-text: #6b5cd8;
            --primary: #6b5cd8;
            --bg-light: #f8f9fa;
            --card-bg: #ffffff;
            --border-color: #e1e1e4;
            --text-dark: #181c32;
            --text-muted: #a1a5b7;
            --success: #50cd89;
            --danger: #f1416c;
            --warning: #ffc700;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-light); display: flex; min-height: 100vh; color: var(--text-dark); }

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
        .main-content { margin-left: 260px; flex: 1; padding: 30px; display: flex; flex-direction: column; }
        
        .topbar { display: flex; justify-content: space-between; align-items: center; margin: -30px -30px 30px -30px; padding: 16px 30px; background-color: white; border-bottom: 1px solid var(--border-color); }
        .page-title h1 { font-size: 20px; font-weight: 600; margin-bottom: 6px; }
        .page-title p { font-size: 14px; color: var(--text-muted); }
        
        .top-actions { display: flex; align-items: center; gap: 20px; }
        .status-pill { display: flex; align-items: center; gap: 8px; background-color: rgba(80, 205, 137, 0.1); color: var(--success); padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid rgba(80, 205, 137, 0.2); }
        .status-dot { width: 8px; height: 8px; background-color: var(--success); border-radius: 50%; }
        
        .profile-wrapper { display: flex; align-items: center; gap: 12px; padding: 6px 16px 6px 6px; border-radius: 30px; cursor: pointer; }
        .avatar { width: 36px; height: 36px; background-color: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; }
        .user-info h4 { font-size: 14px; font-weight: 600; }
        .user-info p { font-size: 12px; color: var(--text-muted); }

        /* Actions Row */
        .actions-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .btn { padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; border: 1px solid transparent; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-secondary { background: white; border-color: var(--border-color); color: var(--text-dark); }
        .btn-outline-primary { border: 1px dashed var(--primary); color: var(--primary); background: transparent; width: 100%; padding: 12px; }
        .btn-group { display: flex; background: white; border: 1px solid var(--border-color); border-radius: 20px; padding: 2px; }
        .btn-group .btn { padding: 6px 16px; border-radius: 18px; border: none; font-size: 12px; font-weight: 600; color: var(--text-muted); }
        .btn-group .btn.active { background: var(--primary); color: white; }

        /* Invoice Grid */
        .invoice-grid { display: grid; grid-template-columns: 1.1fr 1fr; gap: 24px; }
        
        /* Accordion Panel */
        .acc-panel { background: white; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 16px; overflow: hidden; }
        .acc-header { padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; font-weight: 600; font-size: 14px; color: var(--text-dark); }
        .acc-header i { color: var(--text-muted); transition: 0.3s; font-size: 12px; }
        .acc-body { padding: 0 20px 20px 20px; }
        
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 12px; color: var(--text-muted); margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; outline: none; font-family: inherit; }
        .form-control:focus { border-color: var(--primary); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        
        .upload-box { border: 1px dashed var(--border-color); border-radius: 8px; padding: 12px; text-align: center; color: var(--text-dark); font-size: 13px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .info-text { font-size: 11px; color: var(--text-muted); margin-bottom: 8px; }
        .info-text.bg { background: var(--bg-light); padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-color); margin-top: 12px; }
        
        .add-on-box { border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; margin-bottom: 16px; position: relative; }
        .btn-trash { position: absolute; right: 16px; top: 16px; color: var(--text-muted); cursor: pointer; border: none; background: transparent; }
        .subtotal-text { text-align: right; font-size: 12px; color: var(--text-muted); margin-top: 8px; }
        .subtotal-text strong { color: var(--text-dark); }

        /* Draft WhatsApp Area */
        .draft-area { background: white; border-radius: 12px; border: 1px solid var(--border-color); padding: 20px; margin-top: 24px; }
        .draft-title { font-weight: 600; font-size: 14px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .tone-badges { display: flex; gap: 8px; margin-bottom: 12px; }
        .tone-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; cursor: pointer; border: 1px solid var(--border-color); color: var(--text-muted); }
        .tone-badge.active { background: var(--primary); color: white; border-color: var(--primary); }
        .draft-textarea { width: 100%; min-height: 200px; background: #f8f9fa; border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; font-size: 13px; color: var(--text-dark); resize: vertical; outline: none; margin-bottom: 12px; }
        
        /* Right Panel */
        .section-title { font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 12px; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .design-options { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 24px; }
        .design-card { background: white; border: 1px solid var(--border-color); border-radius: 8px; padding: 12px 16px; font-size: 12px; font-weight: 600; color: var(--text-dark); cursor: pointer; flex: 1; text-align: center; min-width: 140px; }
        .design-card.active { border: 2px solid var(--primary); }
        
        /* Invoice Paper */
        .invoice-paper { background: white; border-radius: 8px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 24px; font-family: 'Inter', sans-serif; color: #333; }
        .inv-header { display: flex; justify-content: space-between; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        .inv-logo-text { font-size: 20px; font-weight: 700; letter-spacing: 1px; }
        .inv-meta { text-align: right; }
        .inv-meta h2 { font-size: 24px; font-weight: 300; letter-spacing: 2px; color: #666; margin-bottom: 10px; }
        .inv-meta p { font-size: 11px; color: #888; margin-bottom: 4px; }
        
        .inv-bill-to { margin-bottom: 30px; }
        .inv-bill-to p { font-size: 10px; color: #888; text-transform: uppercase; margin-bottom: 4px; }
        .inv-bill-to h4 { font-size: 14px; font-weight: 600; }
        
        .inv-desc-row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 16px; }
        
        .inv-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .inv-table th { text-align: left; padding: 10px 0; border-bottom: 1px solid #ddd; font-size: 10px; color: #888; text-transform: uppercase; }
        .inv-table td { padding: 12px 0; border-bottom: 1px solid #eee; font-size: 12px; }
        .inv-table td.text-right, .inv-table th.text-right { text-align: right; }
        
        .inv-totals { display: flex; justify-content: flex-end; margin-bottom: 30px; }
        .inv-totals-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 30px; font-size: 12px; text-align: right; }
        .inv-totals-grid .bold { font-weight: 700; font-size: 13px; }
        
        .inv-payment { background: #f9f9f9; padding: 16px; border-radius: 6px; font-size: 11px; margin-bottom: 30px; }
        .inv-payment p { color: #888; margin-bottom: 4px; }
        .inv-payment h5 { font-size: 13px; font-weight: 600; margin-bottom: 4px; }
        
        .inv-footer { display: flex; justify-content: space-between; font-size: 9px; color: #888; }
        .inv-signature { text-align: center; width: 120px; }
        .inv-signature p { margin-top: 40px; border-top: 1px solid #ccc; padding-top: 8px; }
        
        .checkbox-row { display: flex; gap: 16px; font-size: 12px; color: var(--text-muted); margin-top: 12px; }
        .checkbox-row label { display: flex; align-items: center; gap: 6px; cursor: pointer; }

        /* Invoice Themes */
        .invoice-paper.theme-minimalist { background: white; color: #333; }
        .invoice-paper.theme-minimalist .inv-header { border-bottom: none; }
        .invoice-paper.theme-minimalist .inv-table th { border-top: 1px solid #eee; border-bottom: 1px solid #eee; padding: 12px 0; }
        .invoice-paper.theme-minimalist .inv-table td { padding: 12px 0; border-bottom: 1px dashed #eee; }
        .invoice-paper.theme-minimalist .highlight-bg { background: #fafafa; padding: 8px 12px; margin: -8px -12px; border-radius: 4px; display: inline-block; width: calc(100% + 24px); box-sizing: border-box; }
        .invoice-paper.theme-minimalist .inv-payment { background: transparent; padding: 0; }
        
        /* Creative & Aesthetic */
        .invoice-paper.theme-creative { background: #fff5f7; color: #5a3c4a; }
        .invoice-paper.theme-creative .inv-header { flex-direction: column; align-items: center; text-align: center; border-bottom: none; }
        .invoice-paper.theme-creative .inv-logo-text { color: #d67a9a; font-size: 24px; margin-bottom: 16px; }
        .invoice-paper.theme-creative .inv-meta { text-align: center; }
        .invoice-paper.theme-creative .inv-meta h2 { color: #d67a9a; font-weight: 300; letter-spacing: 4px; font-size: 28px; }
        .invoice-paper.theme-creative .inv-meta p { color: #c47692; }
        .invoice-paper.theme-creative .inv-table th { color: #d67a9a; border-bottom: none; background: #fce8ee; padding: 12px 16px; }
        .invoice-paper.theme-creative .inv-table td { padding: 12px 16px; border-bottom: 1px solid #f9e1e8; }
        .invoice-paper.theme-creative .highlight-bg { background: #fce8ee; padding: 8px 12px; margin: -8px -12px; border-radius: 4px; display: inline-block; width: calc(100% + 24px); box-sizing: border-box; color: #d67a9a; }
        .invoice-paper.theme-creative .inv-payment { background: transparent; padding: 0; }
        .invoice-paper.theme-creative .bold { color: #a34c6e; }
        
        /* Corporate & Professional */
        .invoice-paper.theme-corporate { background: white; color: #333; padding: 0; overflow: hidden; }
        .invoice-paper.theme-corporate .inv-header { background: #284168; color: white; padding: 40px; border-bottom: none; margin-bottom: 0; }
        .invoice-paper.theme-corporate .inv-header .inv-logo-text { color: white; }
        .invoice-paper.theme-corporate .inv-meta h2 { color: white; font-weight: 600; letter-spacing: 1px; }
        .invoice-paper.theme-corporate .inv-meta p { color: rgba(255,255,255,0.7); }
        .invoice-paper.theme-corporate .inv-desc-row { padding: 30px 40px 10px; }
        .invoice-paper.theme-corporate .inv-bill-to p { color: #888; }
        .invoice-paper.theme-corporate .inv-table { margin: 0 40px 30px; width: calc(100% - 80px); }
        .invoice-paper.theme-corporate .inv-table th { color: #284168; border-top: 2px solid #284168; border-bottom: 2px solid #284168; background: transparent; padding: 12px 0; }
        .invoice-paper.theme-corporate .inv-table td { padding: 12px 0; border-bottom: 1px solid #eee; }
        .invoice-paper.theme-corporate .inv-totals { padding: 0 40px 30px; }
        .invoice-paper.theme-corporate .highlight-bg { background: #ebf1f9; padding: 8px 12px; margin: -8px -12px; border-radius: 4px; display: inline-block; width: calc(100% + 24px); box-sizing: border-box; }
        .invoice-paper.theme-corporate .highlight-bg.left { color: #284168; }
        .invoice-paper.theme-corporate .highlight-bg.right { color: #284168; }
        .invoice-paper.theme-corporate .inv-payment { margin: 0 40px 30px; background: transparent; padding: 0; }
        .invoice-paper.theme-corporate .inv-footer { padding: 0 40px 40px; }

        /* Modern & Bold */
        .invoice-paper.theme-modern { background: white; color: #111; font-family: 'Inter', sans-serif; }
        .invoice-paper.theme-modern .inv-header { border-bottom: 8px solid #111; padding-bottom: 30px; }
        .invoice-paper.theme-modern .inv-meta h2 { color: #111; font-weight: 800; font-size: 32px; letter-spacing: 0; }
        .invoice-paper.theme-modern .inv-table th { color: white; background: #111; border-bottom: none; padding: 12px 16px; }
        .invoice-paper.theme-modern .inv-table td { padding: 12px 16px; border-bottom: 1px solid #eee; }
        .invoice-paper.theme-modern .highlight-bg { background: #f5f5f5; padding: 8px 12px; margin: -8px -12px; border-radius: 4px; display: inline-block; width: calc(100% + 24px); box-sizing: border-box; }
        .invoice-paper.theme-modern .inv-payment { background: transparent; padding: 0; }
        
        /* Classic / Traditional */
        .invoice-paper.theme-classic { background: #fdfbf6; color: #4a3c31; font-family: 'Times New Roman', serif; }
        .invoice-paper.theme-classic .inv-header { flex-direction: column; align-items: center; text-align: center; border-bottom: none; }
        .invoice-paper.theme-classic .inv-logo-text { color: #876839; font-size: 24px; margin-bottom: 16px; }
        .invoice-paper.theme-classic .inv-meta { text-align: center; }
        .invoice-paper.theme-classic .inv-meta h2 { color: #876839; font-weight: 400; letter-spacing: 4px; font-size: 28px; }
        .invoice-paper.theme-classic .inv-table th { color: #876839; border-bottom: none; background: #f0e3cc; padding: 12px 16px; }
        .invoice-paper.theme-classic .inv-table td { padding: 12px 16px; border-bottom: 1px solid #f0e3cc; }
        .invoice-paper.theme-classic .highlight-bg { background: #f0e3cc; padding: 8px 12px; margin: -8px -12px; border-radius: 4px; display: inline-block; width: calc(100% + 24px); box-sizing: border-box; color: #876839; }
        .invoice-paper.theme-classic .inv-payment { background: transparent; padding: 0; }

        /* Responsive Mobile Layout for Invoice Generator */
        @media (max-width: 992px) {
            .invoice-grid { grid-template-columns: 1fr !important; gap: 20px !important; }
            .top-actions .status-pill { display: none !important; }
            .actions-row { flex-wrap: wrap; gap: 12px; }
            .design-options { flex-wrap: nowrap; overflow-x: auto; padding-bottom: 8px; -webkit-overflow-scrolling: touch; }
            .design-card { min-width: 140px; flex-shrink: 0; }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0 !important;
                padding: 135px 16px 24px 16px !important;
                width: 100% !important;
                min-height: 100vh !important;
                height: auto !important;
            }
            .page-title h1 { font-size: 18px; margin-bottom: 2px; }
            .page-title p { font-size: 12px; }
            .actions-row {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                margin-bottom: 20px !important;
                gap: 10px !important;
            }
            .form-row:not(.form-row-2col) {
                grid-template-columns: 1fr !important;
                gap: 16px !important;
            }
            .form-row.form-row-2col {
                grid-template-columns: 1fr 1fr !important;
                gap: 12px !important;
            }
            .invoice-paper {
                padding: 24px 16px !important;
                border-radius: 12px !important;
                box-shadow: 0 2px 12px rgba(0,0,0,0.06) !important;
            }
            .inv-header {
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: flex-start !important;
                gap: 12px !important;
            }
            .inv-desc-row {
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: flex-start !important;
            }
            .inv-totals {
                padding-right: 0 !important;
                justify-content: flex-end !important;
            }
            .inv-totals > div {
                width: 100% !important;
                max-width: 320px !important;
            }
            .inv-action-btns {
                flex-wrap: wrap !important;
                gap: 10px !important;
            }
            .inv-action-btns .btn {
                flex: 1 1 calc(50% - 10px) !important;
            }
            .inv-action-btns .btn-full {
                flex: 1 1 100% !important;
            }
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
                <h2>{{ auth()->check() ? auth()->user()->business_name : 'CloseMateAI' }}</h2>
                <p>AI Bot Console</p>
            </div>
        </div>
        
        <div class="nav-menu">
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
            <button class="mobile-menu-btn" style="display:none; background:none; border:none; font-size:20px; cursor:pointer; margin-right:16px; color:var(--text-dark);"><i class="fa-solid fa-bars"></i></button>
            <div class="page-title">
                <h1>Invoice Generator</h1>
                <p>Buat invoice profesional, simpan riwayatnya, dan kirim ke klien</p>
            </div>
            <div class="top-actions">
                                @php
                    $currentUser = auth()->user() ?? \App\Models\User::first();
                    $isWaConnected = $currentUser && $currentUser->wa_status == 'connected' ? true : false;
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
                    <div class="avatar">{{ auth()->check() ? auth()->user()->name[0] ?? 'P' : 'P' }}</div>
                    <div class="user-info">
                        <h4>{{ auth()->check() ? auth()->user()->name : 'Penapict' }} <i class="fa-solid fa-chevron-down"></i></h4>
                        <p>Starter</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="actions-row">
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Invoice Baru</button>
                <button class="btn btn-secondary"><i class="fa-regular fa-file-lines"></i> Invoice Tersimpan (1)</button>
            </div>
            
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-language" style="color: #64748b; font-size: 16px;"></i>
                <div class="btn-group">
                    <button class="btn active">ID</button>
                    <button class="btn">EN</button>
                </div>
            </div>
        </div>
        
        <div class="invoice-grid">
            
            <!-- LEFT COLUMN: Forms -->
            <div>
                <!-- Panel 1: Informasi Bisnis -->
                <div class="acc-panel">
                    <div class="acc-header">
                        Informasi Bisnis
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <label class="form-label">Logo Vendor (JPG/PNG, maks 5MB)</label>
                        <div id="logoUploadContainer" style="display: flex; gap: 12px; align-items: center; margin-bottom: 16px;">
                            <div id="logoPreviewThumbnail" style="width: 44px; height: 44px; min-width: 44px; border: 1px solid var(--border-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; overflow: hidden;">
                                <img id="logoImgThumbnail" src="" style="display: none; max-width: 100%; max-height: 100%; object-fit: contain;">
                                <i class="fa-regular fa-image" id="logoIconPlaceholder" style="color: #a1a5b7; font-size: 20px;"></i>
                            </div>
                            <div class="btn btn-secondary upload-btn-box" id="btnUploadBox" style="flex: 1; height: 44px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-dark); font-size: 13px; font-weight: 500; background: white; gap: 8px; cursor: pointer;" onclick="document.getElementById('logoInput').click()">
                                <i class="fa-solid fa-arrow-up-from-bracket"></i> Unggah Logo
                            </div>
                            <span id="btnHapusLogo" style="display: none; color: var(--text-muted); cursor: pointer; font-size: 13px; margin-left: 4px;" onclick="hapusLogo()">Hapus</span>
                            <input type="file" id="logoInput" style="display: none;" accept="image/png, image/jpeg" onchange="previewLogoFile(this)">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Nama Vendor</label>
                            <input type="text" class="form-control" value="Fariz hidayat" id="inputNamaVendor" oninput="syncPreview('inputNamaVendor', 'previewNamaVendor'); syncPreview('inputNamaVendor', 'previewTtdNama')">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Alamat Perusahaan</label>
                            <input type="text" class="form-control" value="Kombas purwokerto" id="inputAlamatVendor" oninput="syncPreview('inputAlamatVendor', 'previewAlamatVendor')">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 8px;">
                            <label class="form-label">Nomor Invoice (Kustom)</label>
                            <input type="text" class="form-control" value="INV-202610-002" id="inputNoInvoice" oninput="syncPreview('inputNoInvoice', 'previewNoInvoice')">
                        </div>
                        
                        <div class="info-text" style="margin-bottom: 16px;">Kosongkan untuk memakai nomor otomatis berurutan: <strong id="previewAutoInvoice">INV-202610-002</strong></div>
                        
                        <div class="form-row form-group" style="align-items: flex-end;">
                            <div>
                                <select class="form-control" id="inputFormatInvoice">
                                    <option value="INV-{YYYY}{MM}-{SEQ3}">INV-{YYYY}{MM}-{SEQ3}</option>
                                </select>
                            </div>
                            <div>
                                <button class="btn btn-secondary" style="width: 100%; border: 1px solid var(--primary); color: var(--primary);" id="btnSimpanFormat" onclick="simpanFormat()"><i class="fa-solid fa-check" id="iconFormat" style="display:none; margin-right:4px;"></i> <span id="textFormat">Simpan Format</span></button>
                            </div>
                        </div>
                        <div class="info-text" style="font-size: 10px; color: #aaa; margin-top: -8px; margin-bottom: 16px;">YY/YYYY, MM, DD, SEQ3/SEQ4/SEQ5</div>
                        
                        <button class="btn btn-secondary" style="width: 100%; border: 1px solid var(--border-color); font-weight: 500;" id="btnSimpanBisnis" onclick="simpanBisnis()"><i class="fa-regular fa-floppy-disk" id="iconBisnis"></i> <span id="textBisnis">Simpan Informasi Bisnis</span></button>
                    </div>
                </div>

                <!-- Panel 2: Informasi Klien -->
                <div class="acc-panel">
                    <div class="acc-header">
                        Informasi Klien
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <div id="bookingConnectedBox" style="display: none; padding: 12px; background: rgba(80, 205, 137, 0.05); border: 1px solid rgba(80, 205, 137, 0.2); border-radius: 8px; margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-weight: 600; font-size: 13px; color: var(--text-dark);" id="bookingConnectedText">Terhubung ke Booking: -</span>
                                <span style="color: #f1416c; font-size: 12px; cursor: pointer;" onclick="putuskanBooking()">Putuskan Hubungan</span>
                            </div>
                            <div style="font-size: 11px; color: var(--text-muted);">Pembayaran yang dicatat di invoice ini otomatis memperbarui status pembayaran booking.</div>
                        </div>
                        
                        <button class="btn btn-primary" style="border-radius: 8px; width: fit-content; padding: 10px 16px; margin-bottom: 6px; font-size: 13px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;" id="btnAmbilBooking" onclick="openBookingModal()"><i class="fa-regular fa-calendar-check"></i> Ambil dari Booking</button>
                        <div class="info-text" style="margin-bottom: 16px;" id="infoAmbilBooking">Isi otomatis nama, WhatsApp, alamat, tanggal acara, paket, diskon, dan add-on dari data Booking & Operasional.</div>
                        
                        <div class="form-group">
                            <label class="form-label">Nama Klien (Pengantin)</label>
                            <input type="text" class="form-control" value="fariz" id="inputNamaKlien" oninput="syncPreviewClientWa()">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">No WhatsApp Klien</label>
                            <input type="text" class="form-control" value="085878067644" id="inputNoWaKlien" oninput="syncPreviewClientWa()">
                            <div id="helperWaKlien" style="font-size: 11px; color: #a1a5b7; margin-top: 4px;">+6285878067644</div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Alamat Klien (opsional)</label>
                            <input type="text" class="form-control" placeholder="Alamat lengkap klien" id="inputAlamatKlien" oninput="syncPreviewClientWa()">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Tanggal Acara</label>
                            <div style="position: relative;">
                                <input type="text" class="form-control" placeholder="mm/dd/yyyy" id="inputTglAcara" value="10/10/2026" oninput="syncPreviewDate()">
                                <i class="fa-regular fa-calendar" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--text-dark); pointer-events: none;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 3: Rincian Paket & Biaya -->
                <div class="acc-panel">
                    <div class="acc-header">
                        Rincian Paket & Biaya
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <div class="form-group" style="position: relative;">
                            <label class="form-label">Nama Paket Pernikahan</label>
                            <div style="position: relative;">
                                <input type="text" class="form-control" value="Penawaran QT-20261010-236" id="inputPaketUtama" oninput="calculateTotals()" style="padding-right: 40px;">
                                <div id="btnTogglePaketDropdown" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--text-dark); cursor: pointer; padding: 4px;" onclick="togglePaketDropdown(event)">
                                    <i class="fa-solid fa-chevron-down" style="font-size: 13px;"></i>
                                </div>
                            </div>
                            <!-- Floating Dropdown Menu Paket Tersimpan -->
                            <div id="paketDropdownMenu" style="display: none; position: absolute; left: 0; right: 0; top: calc(100% + 4px); background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.12); z-index: 1000; overflow: hidden; padding: 6px;"></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Harga Paket Utama</label>
                            <input type="text" class="form-control" value="Rp 9.000" id="inputHargaUtama" oninput="handleCurrencyInput(this); calculateTotals();">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Jumlah Paket</label>
                            <input type="number" class="form-control" value="2" min="1" id="inputJumlahPaket" oninput="calculateTotals();">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Diskon / Potongan</label>
                            <input type="text" class="form-control" value="Rp 50.000" id="inputDiskon" oninput="handleCurrencyInput(this); calculateTotals();">
                        </div>

                        <div id="paketTambahanContainer">
                            <!-- Paket tambahan row jika ada -->
                        </div>

                        <button class="btn btn-secondary" style="border: 1px solid var(--border-color); font-weight: 500; padding: 10px 16px; margin-bottom: 8px; display: inline-flex; align-items: center; gap: 8px;" id="btnSimpanBiaya" onclick="simpanBiaya()"><i class="fa-regular fa-floppy-disk" id="iconBiaya"></i> <span id="textBiaya">Simpan Paket & Harga</span></button>
                        <div class="info-text" style="margin-bottom: 16px;">Tersimpan di perangkat ini, bisa dipilih lagi lewat tombol panah di kolom nama paket.</div>
                        
                        <button class="btn btn-outline-primary form-group" onclick="addPaketTambahan()"><i class="fa-solid fa-plus"></i> Tambah Paket</button>
                        
                        <div class="info-text bg form-group" style="display: flex; justify-content: space-between; font-weight: 500; align-items: center; padding: 12px 16px; border-radius: 8px;">
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span style="font-size: 11px; color: var(--text-muted);">Subtotal Paket</span>
                                <span id="calcSubtotal" style="font-weight: 600;">Rp 18.000</span>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span style="font-size: 11px; color: var(--text-muted);">Total Add-on</span>
                                <span id="calcAddon" style="font-weight: 600;">Rp 0</span>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 4px; text-align: right;">
                                <span style="font-size: 11px; color: var(--text-muted);">GRAND TOTAL</span>
                                <span id="calcGrandTotal" style="color: var(--primary); font-size: 16px; font-weight: 700;">-Rp 32.000</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 4: Add-on Tambahan -->
                <div class="acc-panel">
                    <div class="acc-header">
                        Add-on Tambahan
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <div id="addonContainer"></div>
                        
                        <button class="btn btn-outline-primary" style="width: 100%; border: 1px dashed var(--primary); color: var(--primary); background: transparent; padding: 12px; border-radius: 8px; font-weight: 500;" onclick="addAddonItem()"><i class="fa-solid fa-plus"></i> Tambah Item Add-on</button>
                    </div>
                </div>
                
                <!-- Panel 5: Termin & Pembayaran -->
                <div class="acc-panel">
                    <div class="acc-header">
                        Termin & Pembayaran
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <div class="form-group">
                            <label class="form-label">Termin Saat Ini</label>
                            <select class="form-control" id="selectTermin" onchange="syncTerminPreview()">
                                <option value="Uang Muka / DP 1" selected>Uang Muka / DP 1</option>
                                <option value="Termin 2 / Pelunasan">Termin 2 / Pelunasan</option>
                                <option value="Pelunasan Penuh">Pelunasan Penuh</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Nominal DP 1</label>
                            <input type="text" class="form-control" value="Rp 3.000.000" id="inputDP1" oninput="handleCurrencyInput(this); calculateTotals();">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Nominal DP 2</label>
                            <input type="text" class="form-control" value="Rp 0" id="inputDP2" oninput="handleCurrencyInput(this); calculateTotals();">
                        </div>
                        
                        <div class="termin-summary-card" style="background: #f8f9fa; border: 1px solid var(--border-color); border-radius: 8px; padding: 16px 20px; margin: 16px 0;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; font-size: 13px;">
                                <span style="color: var(--text-dark); font-weight: 500;" id="labelTerminSummary">Total DP 1</span>
                                <span style="color: var(--text-dark); font-weight: 700;" id="valTerminSummary">Rp 3.000.000</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                                <span style="color: var(--text-dark); font-weight: 500;">Sisa Tagihan</span>
                                <span style="color: var(--text-dark); font-weight: 700;" id="valSisaSummary">Rp 0</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Tanggal Jatuh Tempo Pelunasan</label>
                            <div style="position: relative;">
                                <input type="text" class="form-control" placeholder="mm/dd/yyyy" id="inputTglJatuhTempo">
                                <i class="fa-regular fa-calendar" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--text-dark); pointer-events: none;"></i>
                            </div>
                        </div>
                        
                        <div class="form-row form-group">
                            <div>
                                <label class="form-label">Nama Bank</label>
                                <input type="text" class="form-control" value="BNI" id="inputNamaBank" oninput="syncBankPreview()">
                            </div>
                            <div>
                                <label class="form-label">No Rekening</label>
                                <input type="text" class="form-control" value="0431063867" id="inputNoRekening" oninput="syncBankPreview()">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Atas Nama</label>
                            <input type="text" class="form-control" value="Umi Salamah" id="inputAtasNama" oninput="syncBankPreview()">
                        </div>
                        
                        <button class="btn btn-secondary" style="width: 100%; border: 1px solid var(--border-color); font-weight: 500;" id="btnSimpanBank" onclick="simpanBank()"><i class="fa-regular fa-floppy-disk"></i> Simpan Data Bank</button>
                    </div>
                </div>

                <!-- Panel 6 -->
                <div class="acc-panel">
                    <div class="acc-header">
                        Syarat & Ketentuan (Kustom)
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <div class="info-text form-group">Atur poin-poin S&K yang muncul di invoice. Perubahan tersimpan otomatis di perangkat ini.</div>
                        <div class="form-row">
                            <button class="btn btn-outline-primary"><i class="fa-solid fa-plus"></i> Tambah Poin</button>
                            <button class="btn btn-secondary" style="border: 1px dashed var(--border-color);">Reset ke Default</button>
                        </div>
                    </div>
                </div>
                
                <!-- Draf Pesan WA -->
                <div class="draft-area">
                    <div class="draft-title">
                        <i class="fa-regular fa-message"></i> Draf Pesan WhatsApp
                    </div>
                    
                    <div class="tone-badges">
                        <div class="tone-badge active" data-tone="ramah">Ramah</div>
                        <div class="tone-badge" data-tone="formal">Formal</div>
                        <div class="tone-badge" data-tone="ringkas">Ringkas</div>
                        <div class="tone-badge" data-tone="reminder">Reminder</div>
                    </div>
                    
                    <textarea class="draft-textarea" id="wa-draft-textarea">Halo Kak , semoga persiapannya lancar ya. ✨ 
Kami dari *PENAPICT * ingin mengirimkan invoice untuk *Uang Muka / DP 1*. 

Total tagihan untuk termin ini sebesar *Rp 3.000.000* 

Dapat ditransfer ke:
 *BNI* 0431063867 a.n Umi Salamah. 

Detail invoice (termasuk rincian paket, diskon, add-on, serta Syarat & Ketentuan) terlampir ya Kak. 
Terima kasih banyak! 🤍</textarea>
                    
                    <div class="info-text form-group">Pesan bisa diedit langsung sebelum dikirim ke klien.</div>
                    
                    <button class="btn btn-primary" style="width: 100%; padding: 14px; font-weight: 600;"><i class="fa-regular fa-paper-plane"></i> Kirim ke WhatsApp Klien</button>
                </div>
                
            </div>
            
            <!-- RIGHT COLUMN: Preview -->
            <div>
                <div class="section-title"><i class="fa-solid fa-palette"></i> Gaya Desain Invoice</div>
                <div class="design-options">
                    <div class="design-card active" data-theme="theme-minimalist">
                        Minimalis & Clean
                        <div style="height: 4px; background: #666; border-radius: 2px; margin-top: 12px; width: 100%;"></div>
                    </div>
                    <div class="design-card" data-theme="theme-creative">
                        Creative & Aesthetic
                        <div style="height: 4px; background: #d67a9a; border-radius: 2px; margin-top: 12px; width: 100%;"></div>
                    </div>
                    <div class="design-card" data-theme="theme-corporate">
                        Corporate & Professional
                        <div style="height: 4px; background: #1b365d; border-radius: 2px; margin-top: 12px; width: 100%;"></div>
                    </div>
                    <div class="design-card" data-theme="theme-modern">
                        Modern & Bold
                        <div style="height: 4px; background: #111; border-radius: 2px; margin-top: 12px; width: 100%;"></div>
                    </div>
                    <div class="design-card" data-theme="theme-classic">
                        Classic / Traditional
                        <div style="height: 4px; background: #876839; border-radius: 2px; margin-top: 12px; width: 100%;"></div>
                    </div>
                </div>
                
                <div class="section-title"><i class="fa-regular fa-eye"></i> LIVE PREVIEW INVOICE</div>
                
                <!-- Paper Invoice -->
                <div class="invoice-paper theme-minimalist" id="invoice-preview" style="background: white; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border-radius: 12px; margin-top: 20px;">
                    <!-- HEADER -->
                    <div class="inv-header" style="border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-start;">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div id="previewLogoContainer" style="width: 56px; height: 56px; border-radius: 50%; background: #f1f1f4; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; border: 1px solid #e4e6ef;">
                                <img id="previewLogoImg" src="" style="max-height: 100%; max-width: 100%; display: none;">
                                <i class="fa-regular fa-image" id="previewLogoPlaceholder" style="color: #a1a5b7; font-size: 20px;"></i>
                            </div>
                            <div>
                                <div class="inv-logo-text" id="previewNamaVendor" style="margin-bottom: 4px; font-size: 20px; font-weight: 600; color: #222;">Fariz hidayat</div>
                                <div style="font-size: 10px; color: #888;" id="previewAlamatVendor">kombas purwokerto</div>
                            </div>
                        </div>
                        <div class="inv-meta" style="text-align: right;">
                            <h2 style="font-size: 24px; font-weight: 300; letter-spacing: 2px; color: #555; margin-bottom: 8px;">INVOICE</h2>
                            <p style="font-size: 10px; color: #888; margin-bottom: 2px;">No. <span id="previewNoInvoice">INV-202610-002</span></p>
                            <p style="font-size: 10px; color: #888;" id="previewInvoiceDate">11 Oktober 2026</p>
                        </div>
                    </div>
                    
                    <!-- KEPADA / TANGGAL ACARA -->
                    <div class="inv-desc-row" style="margin-bottom: 24px; display: flex; justify-content: space-between;">
                        <div class="inv-bill-to">
                            <p style="font-size: 9px; font-weight: 600; color: #999; margin-bottom: 8px; text-transform: uppercase;">KEPADA</p>
                            <h4 id="previewNamaKlien" style="font-size: 15px; font-weight: 500; color: #333; margin-bottom: 4px;">fariz</h4>
                            <p style="font-size: 11px; color: #888; margin-bottom: 2px;" id="previewWaKlien">+6285878067644</p>
                            <p style="font-size: 11px; color: #888; display: none;" id="previewAlamatKlien"></p>
                        </div>
                        <div class="inv-bill-to" style="text-align: right;">
                            <p style="font-size: 9px; font-weight: 600; color: #999; margin-bottom: 8px; text-transform: uppercase;">TANGGAL ACARA</p>
                            <h4 style="font-size: 15px; font-weight: 500; color: #333; margin-bottom: 4px;" id="previewTglAcara">Sabtu, 10 Oktober 2026</h4>
                            <p style="font-size: 11px; color: #888;" id="previewTerminLabel">Uang Muka / DP 1</p>
                        </div>
                    </div>
                    
                    <!-- TABEL -->
                    <table class="inv-table" style="margin-bottom: 16px; width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th style="text-align: left; padding: 12px 16px; font-size: 10px; font-weight: 600; color: #666; text-transform: uppercase;">DESKRIPSI</th>
                                <th style="text-align: right; padding: 12px 16px; font-size: 10px; font-weight: 600; color: #666; text-transform: uppercase;">HARGA</th>
                            </tr>
                        </thead>
                        <tbody id="previewTableBody">
                            <tr>
                                <td style="padding: 16px; border-bottom: 1px solid #f1f1f4;">
                                    <div style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 4px;">Premium Wedding Documentation</div>
                                    <div style="font-size: 10px; color: #888;">Paket Utama</div>
                                </td>
                                <td style="text-align: right; padding: 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; color: #333;">Rp 10.000.000</td>
                            </tr>
                            <tr>
                                <td style="padding: 16px; border-bottom: 1px solid #f1f1f4;">
                                    <div style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 4px;">Diskon / Potongan Harga</div>
                                    <div style="font-size: 10px; color: #888;">Penawaran khusus</div>
                                </td>
                                <td style="text-align: right; padding: 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; color: #f1416c;">- Rp 500.000</td>
                            </tr>
                            <tr>
                                <td style="padding: 16px; border-bottom: 1px solid #f1f1f4;">
                                    <div style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 4px;">Live Streaming</div>
                                    <div style="font-size: 10px; color: #888;">Add-on &bull; 1 pcs</div>
                                </td>
                                <td style="text-align: right; padding: 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; color: #333;">Rp 1.500.000</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!-- TOTALS -->
                    <div class="inv-totals" style="display: flex; justify-content: flex-end; margin-bottom: 24px; padding-right: 16px;">
                        <div style="width: 280px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 11px;">
                                <div style="color: #666;">Subtotal Paket</div>
                                <div id="previewSubtotal" style="color: #333;">Rp 10.000.000</div>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 11px;">
                                <div style="color: #666;">Total Add-on</div>
                                <div id="previewAddon" style="color: #333;">Rp 1.500.000</div>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 11px;">
                                <div style="color: #666;">Diskon</div>
                                <div id="previewDiskon" style="color: #f1416c;">- Rp 500.000</div>
                            </div>
                            <!-- Grand Total -->
                            <div style="display: flex; justify-content: space-between; padding-top: 12px; border-top: 1px solid #eee; font-size: 12px; font-weight: 700;">
                                <div style="color: #333;">GRAND TOTAL</div>
                                <div id="previewGrandTotal" style="color: #333;">Rp 11.000.000</div>
                            </div>
                            
                            <!-- DP Info Box -->
                            <div style="background: #f8f9fa; border-radius: 8px; padding: 12px; margin-top: 16px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 11px;">
                                    <div style="color: #666;" id="previewDPLabel">Uang Muka / DP 1</div>
                                    <div id="previewDP1" style="color: #333;">Rp 3.000.000</div>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 600;">
                                    <div style="color: #666;">Sisa Tagihan</div>
                                    <div id="previewSisaTagihan" style="color: #333; font-size: 13px;">Rp 8.000.000</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- METODE PEMBAYARAN -->
                    <div class="inv-payment" style="border: 1px solid #f1f1f4; border-radius: 8px; padding: 16px; margin-bottom: 32px;" id="previewPaymentBox">
                        <p style="text-transform: uppercase; font-size: 9px; font-weight: 600; color: #999; margin-bottom: 8px;">METODE PEMBAYARAN</p>
                        <h5 style="font-size: 12px; font-weight: 400; color: #555; margin: 0;" id="previewPaymentText"><strong style="color: #333;" id="previewBankName">BNI</strong> - <span id="previewNoRek">0431063867</span> - a.n <span id="previewAtasNama">Umi Salamah</span></h5>
                    </div>
                    
                    <!-- SYARAT KETENTUAN -->
                    <div class="inv-footer" style="display: block;">
                        <div style="margin-bottom: 24px;">
                            <p style="font-weight: 700; font-size: 10px; margin-bottom: 12px; color: #555;">SYARAT & KETENTUAN</p>
                            <!-- Line divider and signature -->
                            <div style="border-top: 1px solid #eee; padding-top: 24px; display: flex; justify-content: flex-end;">
                                <div class="inv-signature" style="text-align: center; width: 150px;">
                                    <div style="margin-bottom: 40px; font-weight: 400; font-style: italic; font-size: 14px; color: #333;" id="previewTtdNama">Fariz hidayat</div>
                                    <div style="border-top: 1px solid #ccc; padding-top: 8px;">
                                        <p style="font-size: 9px; color: #888; margin: 0;">Tanda Tangan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="section-title"><i class="fa-solid fa-pen-nib"></i> Tanda Tangan (PNG/JPG, maks 2MB)</div>
                <div class="upload-box form-group">
                    <i class="fa-solid fa-signature" style="color: var(--text-muted);"></i> Unggah TTD
                </div>
                <div class="checkbox-row" style="margin-bottom: 24px;">
                    <label><input type="checkbox"> Belum ada logo</label>
                    <label><input type="checkbox"> Belum ada tanda tangan</label>
                </div>
                
                <div class="inv-action-btns" style="display: flex; gap: 12px; margin-bottom: 40px;">
                    <button class="btn btn-secondary"><i class="fa-regular fa-image"></i> Unduh PNG</button>
                    <button class="btn btn-secondary" style="background: var(--text-dark); color: white; border-color: var(--text-dark);"><i class="fa-regular fa-file-pdf"></i> Unduh PDF</button>
                    <button class="btn btn-primary btn-full" style="flex: 1;"><i class="fa-regular fa-folder-open"></i> Simpan ke Folder</button>
                </div>
                
            </div>
            
        </div>
    </div>

    <script>
        // Fitur Sidebar Collapse
        const btnCollapse = document.getElementById('btn-collapse');
        btnCollapse.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-collapsed');
        });

        // Accordion Toggle
        document.querySelectorAll('.acc-header').forEach(header => {
            header.addEventListener('click', () => {
                const body = header.nextElementSibling;
                const icon = header.querySelector('i');
                if (body.style.display === 'none') {
                    body.style.display = 'block';
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    body.style.display = 'none';
                    icon.style.transform = 'rotate(180deg)';
                }
            });
        });

        // Invoice Theme Switcher
        const designCards = document.querySelectorAll('.design-card');
        const invoicePaper = document.getElementById('invoice-preview');
        
        designCards.forEach(card => {
            card.addEventListener('click', () => {
                designCards.forEach(c => c.classList.remove('active'));
                card.classList.add('active');
                
                // Remove all theme classes
                invoicePaper.className = 'invoice-paper';
                
                // Add selected theme
                const theme = card.getAttribute('data-theme');
                if (theme) {
                    invoicePaper.classList.add(theme);
                }
            });
        });

        // WhatsApp Draft Templates
        const waTemplates = {
            ramah: `Halo Kak , semoga persiapannya lancar ya. ✨ 
Kami dari *PENAPICT * ingin mengirimkan invoice untuk *Uang Muka / DP 1*. 

Total tagihan untuk termin ini sebesar *Rp 3.000.000* 

Dapat ditransfer ke:
 *BNI* 0431063867 a.n Umi Salamah. 

Detail invoice (termasuk rincian paket, diskon, add-on, serta Syarat & Ketentuan) terlampir ya Kak. 
Terima kasih banyak! 🤍`,
            formal: `Kepada Yth. Bapak/Ibu ,

Berikut kami sampaikan tagihan resmi dari *PENAPICT * untuk *Uang Muka / DP 1*.

• No. Invoice: INV-202610-006
• Jumlah Tagihan: *Rp 3.000.000*
• Rekening Pembayaran: *BNI* 0431063867 a.n Umi Salamah

Dokumen invoice beserta rincian paket dan Syarat & Ketentuan terlampir.
Atas perhatian dan kerja samanya, kami ucapkan terima kasih.`,
            ringkas: `*INVOICE — PENAPICT *
Klien: 
Tagihan: Uang Muka / DP 1 — *Rp 3.000.000*
Transfer ke: *BNI* 0431063867 a.n Umi Salamah

Invoice digital terlampir. Mohon konfirmasi bukti transfer jika sudah dibayar ya.`,
            reminder: `Halo Kak , salam dari *PENAPICT *. 🙏

Sekadar mengingatkan untuk invoice *Uang Muka / DP 1* sebesar *Rp 3.000.000*.

Pembayaran dapat ditransfer ke:
*BNI* 0431063867 a.n Umi Salamah

Jika pembayaran sudah dilakukan, mohon abaikan pesan ini atau kirimkan buktinya ya Kak. Terima kasih banyak! 🤍`
        };

        const toneBadges = document.querySelectorAll('.tone-badge');
        const waTextarea = document.getElementById('wa-draft-textarea');
        
        toneBadges.forEach(badge => {
            badge.addEventListener('click', () => {
                toneBadges.forEach(b => b.classList.remove('active'));
                badge.classList.add('active');
                
                const tone = badge.getAttribute('data-tone');
                if (tone && waTemplates[tone]) {
                    waTextarea.value = waTemplates[tone];
                }
            });
        });

        // Live Preview functions
        function syncPreview(inputId, previewId) {
            const inputVal = document.getElementById(inputId).value;
            const previewEl = document.getElementById(previewId);
            
            if (previewEl) {
                previewEl.innerText = inputVal;
            }
            
            // Khusus untuk input Nomor Invoice Kustom
            if(inputId === 'inputNoInvoice') {
                const autoInvoiceEl = document.getElementById('previewAutoInvoice');
                if (autoInvoiceEl) {
                    autoInvoiceEl.innerText = inputVal || "INV-202610-001";
                }
                if (!inputVal && previewEl) {
                    previewEl.innerText = "INV-202610-001"; // fallback ke otomatis jika kosong
                }
            }
        }

        function previewLogoFile(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logoPreviewThumbnail').style.display = 'flex';
                    document.getElementById('logoImgThumbnail').src = e.target.result;
                    document.getElementById('btnUploadBox').style.display = 'none';
                    document.getElementById('btnHapusLogo').style.display = 'inline';
                    
                    document.getElementById('previewLogoPlaceholder').style.display = 'none';
                    document.getElementById('previewLogoImg').style.display = 'block';
                    document.getElementById('previewLogoImg').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function hapusLogo() {
            document.getElementById('logoInput').value = '';
            document.getElementById('logoPreviewThumbnail').style.display = 'none';
            document.getElementById('logoImgThumbnail').src = '';
            document.getElementById('btnUploadBox').style.display = 'flex';
            document.getElementById('btnHapusLogo').style.display = 'none';
            
            document.getElementById('previewLogoPlaceholder').style.display = 'block';
            document.getElementById('previewLogoImg').style.display = 'none';
            document.getElementById('previewLogoImg').src = '';
        }

        function simpanFormat() {
            const btn = document.getElementById('btnSimpanFormat');
            const icon = document.getElementById('iconFormat');
            const text = document.getElementById('textFormat');
            
            btn.style.background = 'rgba(80, 205, 137, 0.1)';
            btn.style.borderColor = 'var(--success)';
            btn.style.color = 'var(--success)';
            icon.style.display = 'inline-block';
            text.innerText = 'Tersimpan';
            setTimeout(() => {
                btn.style.background = 'transparent';
                btn.style.borderColor = 'var(--primary)';
                btn.style.color = 'var(--primary)';
                icon.style.display = 'none';
                text.innerText = 'Simpan Format';
            }, 2000);
        }

        function simpanBisnis() {
            const btn = document.getElementById('btnSimpanBisnis');
            const icon = document.getElementById('iconBisnis');
            const text = document.getElementById('textBisnis');
            
            btn.style.background = 'rgba(80, 205, 137, 0.1)';
            btn.style.borderColor = 'var(--success)';
            btn.style.color = 'var(--success)';
            icon.className = 'fa-solid fa-check';
            text.innerText = 'Tersimpan';
            setTimeout(() => {
                btn.style.background = 'transparent';
                btn.style.borderColor = 'var(--border-color)';
                btn.style.color = 'var(--text-dark)';
                icon.className = 'fa-regular fa-floppy-disk';
                text.innerText = 'Simpan Informasi Bisnis';
            }, 2000);
        }
        
        // --- Modal Booking ---
        function openBookingModal() {
            if(!document.getElementById('bookingModal')) {
                const modalHtml = `
                <div id="bookingModal" style="display: flex; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
                    <div style="background: white; width: 90%; max-width: 500px; border-radius: 12px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <h3 style="margin: 0; font-size: 18px; font-weight: 600;">Pilih Booking</h3>
                            <i class="fa-solid fa-xmark" style="cursor: pointer; color: var(--text-muted); font-size: 18px;" onclick="closeBookingModal()"></i>
                        </div>
                        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Data klien pada form invoice akan ditimpa oleh data booking yang dipilih.</p>
                        
                        <div style="margin-bottom: 16px; position: relative;">
                            <input type="text" class="form-control" placeholder="Cari nama klien atau paket..." style="border-radius: 20px; padding-left: 16px; border-color: var(--primary);">
                        </div>
                        
                        <div style="max-height: 250px; overflow-y: auto;">
                            <div style="padding: 12px 16px; border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; margin-bottom: 8px; transition: 0.2s;" onclick="pilihBooking('fariz', '085878067644', '', '10/10/2026', 'Penawaran QT-20261010-236', 9000, 50000, 2)" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
                                <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px;">fariz</div>
                                <div style="font-size: 12px; color: var(--text-muted);">2026-10-10 &bull; Penawaran QT-20261010-236 &bull; Rp 9.000</div>
                            </div>
                        </div>
                    </div>
                </div>`;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
            } else {
                document.getElementById('bookingModal').style.display = 'flex';
            }
        }
        
        function closeBookingModal() {
            document.getElementById('bookingModal').style.display = 'none';
        }
        
        function pilihBooking(nama, wa, alamat, tgl, paketUtama, hargaUtama, diskonUtama, qtyUtama) {
            closeBookingModal();
            
            // Tampilkan state connected
            document.getElementById('btnAmbilBooking').style.display = 'none';
            document.getElementById('infoAmbilBooking').style.display = 'none';
            document.getElementById('bookingConnectedBox').style.display = 'block';
            document.getElementById('bookingConnectedText').innerText = `Terhubung ke Booking: ${nama} — 2026-10-10`;
            
            // Set values di UI Klien
            document.getElementById('inputNamaKlien').value = nama;
            document.getElementById('inputNoWaKlien').value = wa;
            document.getElementById('inputAlamatKlien').value = alamat || '';
            document.getElementById('inputTglAcara').value = tgl;
            
            // Set values di UI Biaya
            document.getElementById('inputPaketUtama').value = paketUtama;
            document.getElementById('inputHargaUtama').value = formatRupiah(hargaUtama);
            document.getElementById('inputJumlahPaket').value = qtyUtama || 1;
            document.getElementById('inputDiskon').value = formatRupiah(diskonUtama || 0);
            
            // Clear paket tambahan & addons
            document.getElementById('paketTambahanContainer').innerHTML = '';
            document.getElementById('addonContainer').innerHTML = '';
            
            // Render Live Preview
            syncPreviewClientWa();
            syncPreviewDate();
            calculateTotals();
            
            // Toast notification sesuai Gambar 1
            showToastNotification("Data klien diambil dari booking");
        }
        
        function putuskanBooking() {
            document.getElementById('btnAmbilBooking').style.display = 'inline-flex';
            document.getElementById('infoAmbilBooking').style.display = 'block';
            document.getElementById('bookingConnectedBox').style.display = 'none';
            document.getElementById('inputNamaKlien').value = '';
            document.getElementById('inputNoWaKlien').value = '';
            document.getElementById('inputAlamatKlien').value = '';
            document.getElementById('inputTglAcara').value = '';
            syncPreviewClientWa();
            syncPreviewDate();
        }
        
        // --- Helper & Currency Formatting ---
        function parseCurrency(str) {
            if (!str) return 0;
            if (typeof str === 'number') return str;
            return parseInt(String(str).replace(/[^0-9]/g, '')) || 0;
        }

        function formatRupiah(angka) {
            const num = parseInt(angka) || 0;
            return 'Rp ' + num.toLocaleString('id-ID');
        }

        function handleCurrencyInput(el) {
            const raw = el.value.replace(/[^0-9]/g, '');
            if (raw) {
                el.value = formatRupiah(raw);
            } else {
                el.value = '';
            }
        }

        function formatWaNumber(wa) {
            if (!wa) return '';
            let clean = wa.trim();
            if (clean.startsWith('0')) {
                return '+62' + clean.slice(1);
            } else if (clean.startsWith('62')) {
                return '+' + clean;
            } else if (clean.startsWith('+')) {
                return clean;
            }
            return clean;
        }

        function syncPreviewClientWa() {
            const nama = (document.getElementById('inputNamaKlien').value || '').trim();
            const wa = (document.getElementById('inputNoWaKlien').value || '').trim();
            const alamat = (document.getElementById('inputAlamatKlien').value || '').trim();

            const formattedWa = formatWaNumber(wa);
            const helperWa = document.getElementById('helperWaKlien');
            if (helperWa) {
                helperWa.innerText = formattedWa;
                helperWa.style.display = formattedWa ? 'block' : 'none';
            }

            const prevNama = document.getElementById('previewNamaKlien');
            if (prevNama) {
                prevNama.innerText = nama || 'Nama Klien';
            }

            const prevWa = document.getElementById('previewWaKlien');
            if (prevWa) {
                prevWa.innerText = formattedWa;
                prevWa.style.display = formattedWa ? 'block' : 'none';
            }

            const prevAlamat = document.getElementById('previewAlamatKlien');
            if (prevAlamat) {
                prevAlamat.innerText = alamat;
                prevAlamat.style.display = alamat ? 'block' : 'none';
            }
        }

        function formatTanggalIndonesia(str) {
            if (!str) return '';
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            let d, m, y;
            if (str.includes('/')) {
                const parts = str.split('/');
                if (parts.length === 3) {
                    d = parseInt(parts[0], 10);
                    m = parseInt(parts[1], 10);
                    y = parseInt(parts[2], 10);
                }
            } else if (str.includes('-')) {
                const parts = str.split('-');
                if (parts.length === 3) {
                    if (parts[0].length === 4) {
                        y = parseInt(parts[0], 10);
                        m = parseInt(parts[1], 10);
                        d = parseInt(parts[2], 10);
                    } else {
                        d = parseInt(parts[0], 10);
                        m = parseInt(parts[1], 10);
                        y = parseInt(parts[2], 10);
                    }
                }
            }

            if (d && m && y) {
                const dateObj = new Date(y, m - 1, d);
                if (!isNaN(dateObj.getTime())) {
                    const dayName = days[dateObj.getDay()];
                    const monthName = months[m - 1] || '';
                    return `${dayName}, ${d} ${monthName} ${y}`;
                }
            }
            return str;
        }

        function syncPreviewDate() {
            const rawVal = (document.getElementById('inputTglAcara').value || '').trim();
            const prevDate = document.getElementById('previewTglAcara');
            if (prevDate) {
                prevDate.innerText = formatTanggalIndonesia(rawVal) || rawVal;
            }
        }

        function syncTerminPreview() {
            const terminVal = document.getElementById('selectTermin').value;
            const previewTermin = document.getElementById('previewTerminLabel');
            const previewDPLabel = document.getElementById('previewDPLabel');
            const labelTerminSummary = document.getElementById('labelTerminSummary');
            
            if (previewTermin) previewTermin.innerText = terminVal;
            if (previewDPLabel) previewDPLabel.innerText = terminVal;
            if (labelTerminSummary) labelTerminSummary.innerText = 'Total ' + terminVal.replace('Uang Muka / ', '');
        }

        function syncBankPreview() {
            const bank = document.getElementById('inputNamaBank').value || 'BNI';
            const rek = document.getElementById('inputNoRekening').value || '0431063867';
            const an = document.getElementById('inputAtasNama').value || 'Umi Salamah';
            
            const bankEl = document.getElementById('previewBankName');
            const rekEl = document.getElementById('previewNoRek');
            const anEl = document.getElementById('previewAtasNama');
            
            if (bankEl) bankEl.innerText = bank;
            if (rekEl) rekEl.innerText = rek;
            if (anEl) anEl.innerText = an;
        }

        function simpanBank() {
            const btn = document.getElementById('btnSimpanBank');
            const icon = document.getElementById('iconBank');
            const text = document.getElementById('textBank');
            
            btn.style.background = 'rgba(80, 205, 137, 0.1)';
            btn.style.borderColor = 'var(--success)';
            btn.style.color = 'var(--success)';
            icon.className = 'fa-solid fa-check';
            text.innerText = 'Tersimpan';
            setTimeout(() => {
                btn.style.background = 'transparent';
                btn.style.borderColor = 'var(--border-color)';
                btn.style.color = 'var(--text-dark)';
                icon.className = 'fa-regular fa-floppy-disk';
                text.innerText = 'Simpan Data Bank';
            }, 2000);
        }

        // --- Saved Packages Dropdown Management ---
        const defaultSavedPackages = [
            { name: 'Penawaran QT-20261010-236', price: 9000 },
            { name: 'Silver Package', price: 15000000 }
        ];

        function getSavedPackages() {
            try {
                const stored = localStorage.getItem('invoice_saved_packages');
                if (stored) {
                    const parsed = JSON.parse(stored);
                    if (Array.isArray(parsed) && parsed.length > 0) return parsed;
                }
            } catch(e) {}
            return defaultSavedPackages;
        }

        function savePackagesToStorage(pkgs) {
            try {
                localStorage.setItem('invoice_saved_packages', JSON.stringify(pkgs));
            } catch(e) {}
        }

        function renderSavedPackages() {
            const list = getSavedPackages();
            const container = document.getElementById('paketDropdownMenu');
            if (!container) return;

            if (list.length === 0) {
                container.innerHTML = `<div style="padding: 12px; font-size: 12px; color: var(--text-muted); text-align: center;">Tidak ada paket tersimpan</div>`;
                return;
            }

            const currentName = (document.getElementById('inputPaketUtama').value || '').trim();

            let html = '';
            list.forEach((pkg, idx) => {
                const isSelected = pkg.name === currentName;
                const bgStyle = isSelected ? 'background: rgba(107, 92, 216, 0.12);' : 'background: transparent;';

                html += `
                <div class="saved-pkg-item" onclick="selectSavedPackage(${idx})" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; border-radius: 8px; cursor: pointer; transition: 0.15s; ${bgStyle}" onmouseover="if(!${isSelected}) this.style.background='#f8f9fa'" onmouseout="if(!${isSelected}) this.style.background='transparent'">
                    <div>
                        <div style="font-size: 13px; font-weight: 600; color: var(--text-dark);">${pkg.name}</div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">${formatRupiah(pkg.price)}</div>
                    </div>
                    <button type="button" onclick="deleteSavedPackage(event, ${idx})" title="Hapus paket tersimpan" style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 6px; font-size: 14px; transition: 0.2s;" onmouseover="this.style.color='#f1416c'" onmouseout="this.style.color='var(--text-muted)'">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </div>`;
            });

            container.innerHTML = html;
        }

        function togglePaketDropdown(e) {
            if (e) e.stopPropagation();
            const dropdown = document.getElementById('paketDropdownMenu');
            if (dropdown) {
                if (dropdown.style.display === 'none' || !dropdown.style.display) {
                    renderSavedPackages();
                    dropdown.style.display = 'block';
                } else {
                    dropdown.style.display = 'none';
                }
            }
        }

        function selectSavedPackage(idx) {
            const list = getSavedPackages();
            const pkg = list[idx];
            if (pkg) {
                document.getElementById('inputPaketUtama').value = pkg.name;
                document.getElementById('inputHargaUtama').value = formatRupiah(pkg.price);
                document.getElementById('paketDropdownMenu').style.display = 'none';
                calculateTotals();
            }
        }

        function deleteSavedPackage(e, idx) {
            e.stopPropagation();
            let list = getSavedPackages();
            list.splice(idx, 1);
            savePackagesToStorage(list);
            renderSavedPackages();
        }

        function simpanBiaya() {
            const name = (document.getElementById('inputPaketUtama').value || '').trim();
            const price = parseCurrency(document.getElementById('inputHargaUtama').value);

            if (name) {
                let list = getSavedPackages();
                const existingIdx = list.findIndex(p => p.name.toLowerCase() === name.toLowerCase());
                if (existingIdx >= 0) {
                    list[existingIdx].price = price;
                } else {
                    list.push({ name, price });
                }
                savePackagesToStorage(list);
            }

            const btn = document.getElementById('btnSimpanBiaya');
            const icon = document.getElementById('iconBiaya');
            const text = document.getElementById('textBiaya');

            btn.style.background = 'rgba(80, 205, 137, 0.1)';
            btn.style.borderColor = 'var(--success)';
            btn.style.color = 'var(--success)';
            icon.className = 'fa-solid fa-check';
            text.innerText = 'Tersimpan';
            setTimeout(() => {
                btn.style.background = 'transparent';
                btn.style.borderColor = 'var(--border-color)';
                btn.style.color = 'var(--text-dark)';
                icon.className = 'fa-regular fa-floppy-disk';
                text.innerText = 'Simpan Paket & Harga';
            }, 2000);

            showToastNotification("Paket disimpan");
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const dropdown = document.getElementById('paketDropdownMenu');
            const btnToggle = document.getElementById('btnTogglePaketDropdown');
            if (dropdown && dropdown.style.display === 'block') {
                if (!dropdown.contains(e.target) && (!btnToggle || !btnToggle.contains(e.target))) {
                    dropdown.style.display = 'none';
                }
            }
        });

        // --- Addon Tambahan Management ---
        function addAddonItem() {
            const html = `
            <div class="addon-item-box" style="border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                <div style="position: relative; margin-bottom: 12px;">
                    <input type="text" class="form-control addon-name" value="Add-on Baru" placeholder="Nama Add-on" oninput="calculateTotals()" style="padding-right: 36px;">
                    <button type="button" class="btn-trash" onclick="removeAddonItem(this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 15px;">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </div>
                <div class="form-row form-row-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 8px;">
                    <div>
                        <label class="form-label">Jumlah (pcs)</label>
                        <input type="number" class="form-control addon-qty" value="1" min="1" oninput="calculateTotals()">
                    </div>
                    <div>
                        <label class="form-label">Harga Satuan</label>
                        <input type="text" class="form-control addon-price" value="Rp 500.000" oninput="handleCurrencyInput(this); calculateTotals();">
                    </div>
                </div>
                <div class="subtotal-text" style="text-align: right; font-size: 12px; color: var(--text-muted); margin-top: 8px;">
                    <span class="addon-calc-formula">1 x Rp 500.000 = </span><strong class="addon-calc-result" style="color: var(--text-dark);">Rp 500.000</strong>
                </div>
            </div>`;
            document.getElementById('addonContainer').insertAdjacentHTML('beforeend', html);
            calculateTotals();
        }

        function removeAddonItem(btn) {
            btn.closest('.addon-item-box').remove();
            calculateTotals();
        }

        // --- Paket Tambahan Management ---
        function addPaketTambahan() {
            const html = `
            <div class="paket-tambahan-box" style="border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; margin-bottom: 16px; position: relative; background: #ffffff;">
                <button type="button" class="btn-trash" onclick="removePaketTambahan(this)" title="Hapus paket tambahan" style="position: absolute; right: 12px; top: 12px; background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 15px;">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label">Nama Paket Tambahan</label>
                    <input type="text" class="form-control pkt-extra-name" value="layanan 2" placeholder="Nama Paket Tambahan" oninput="calculateTotals()">
                </div>
                <div class="form-row form-row-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label class="form-label">Harga Paket</label>
                        <input type="text" class="form-control pkt-extra-price" value="Rp 5.000.000" oninput="handleCurrencyInput(this); calculateTotals();">
                    </div>
                    <div>
                        <label class="form-label">Diskon</label>
                        <input type="text" class="form-control pkt-extra-disc" value="Rp 10.000" oninput="handleCurrencyInput(this); calculateTotals();">
                    </div>
                </div>
            </div>`;
            document.getElementById('paketTambahanContainer').insertAdjacentHTML('beforeend', html);
            calculateTotals();
        }

        function removePaketTambahan(btn) {
            btn.closest('.paket-tambahan-box').remove();
            calculateTotals();
        }

        // --- Calculate Totals & Sync Preview ---
        function calculateTotals() {
            // 1. Paket Utama
            const pktUtama = (document.getElementById('inputPaketUtama').value || '').trim() || 'Paket Utama';
            const hargaUtama = parseCurrency(document.getElementById('inputHargaUtama').value);
            const qtyUtama = parseInt(document.getElementById('inputJumlahPaket').value) || 1;
            const diskonUtama = parseCurrency(document.getElementById('inputDiskon').value);

            const subtotalUtama = hargaUtama * qtyUtama;
            let subtotalPaket = subtotalUtama;
            let totalDiskon = diskonUtama;

            let tbHtml = '';

            // Paket Utama row
            const qtyText = qtyUtama > 1 ? ` &times; ${qtyUtama}` : '';
            const subDescText = qtyUtama > 1 ? `Paket Utama &bull; @ ${formatRupiah(hargaUtama)}` : 'Paket Utama';

            tbHtml += `
            <tr>
                <td style="padding: 16px; border-bottom: 1px solid #f1f1f4;">
                    <div style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 4px;">${pktUtama}${qtyText}</div>
                    <div style="font-size: 10px; color: #888;">${subDescText}</div>
                </td>
                <td style="text-align: right; padding: 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; color: #333;">${formatRupiah(subtotalUtama)}</td>
            </tr>`;

            // 2. Paket Tambahan
            const extraPakets = document.querySelectorAll('.paket-tambahan-box');
            extraPakets.forEach(box => {
                const name = (box.querySelector('.pkt-extra-name').value || '').trim() || 'Paket Tambahan';
                const price = parseCurrency(box.querySelector('.pkt-extra-price').value);
                const disc = parseCurrency(box.querySelector('.pkt-extra-disc').value);
                subtotalPaket += price;
                totalDiskon += disc;

                tbHtml += `
                <tr>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f1f4;">
                        <div style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 4px;">${name}</div>
                        <div style="font-size: 10px; color: #888;">Paket Tambahan</div>
                    </td>
                    <td style="text-align: right; padding: 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; color: #333;">${formatRupiah(price)}</td>
                </tr>`;
            });

            // 3. Diskon row (if totalDiskon > 0)
            if (totalDiskon > 0) {
                tbHtml += `
                <tr>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f1f4;">
                        <div style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 4px;">Diskon / Potongan Harga</div>
                        <div style="font-size: 10px; color: #888;">Penawaran khusus</div>
                    </td>
                    <td style="text-align: right; padding: 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; color: #f1416c;">- ${formatRupiah(totalDiskon)}</td>
                </tr>`;
            }

            // 4. Add-ons
            let totalAddon = 0;
            const addonItems = document.querySelectorAll('.addon-item-box');
            addonItems.forEach(item => {
                const name = (item.querySelector('.addon-name').value || '').trim() || 'Add-on';
                const qty = parseInt(item.querySelector('.addon-qty').value) || 1;
                const price = parseCurrency(item.querySelector('.addon-price').value);
                const itemTotal = qty * price;
                totalAddon += itemTotal;

                const formulaEl = item.querySelector('.addon-calc-formula');
                const resultEl = item.querySelector('.addon-calc-result');
                if (formulaEl) formulaEl.innerText = `${qty} x ${formatRupiah(price)} = `;
                if (resultEl) resultEl.innerText = formatRupiah(itemTotal);

                tbHtml += `
                <tr>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f1f4;">
                        <div style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 4px;">${name}</div>
                        <div style="font-size: 10px; color: #888;">Add-on &bull; ${qty} pcs</div>
                    </td>
                    <td style="text-align: right; padding: 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; color: #333;">${formatRupiah(itemTotal)}</td>
                </tr>`;
            });

            // 5. Grand Total Calculation
            const grandTotal = subtotalPaket + totalAddon - totalDiskon;

            // 6. DP & Sisa Tagihan Calculation
            const dp1 = parseCurrency(document.getElementById('inputDP1').value);
            const dp2 = parseCurrency(document.getElementById('inputDP2').value);
            const totalDP = dp1 + dp2;
            let sisa = grandTotal - totalDP;
            if (grandTotal <= 0 || sisa < 0) {
                sisa = 0;
            }

            const grandTotalText = grandTotal < 0 ? `-Rp ${Math.abs(grandTotal).toLocaleString('id-ID')}` : formatRupiah(grandTotal);

            // Update Left Summary Cards
            document.getElementById('calcSubtotal').innerText = formatRupiah(subtotalPaket);
            document.getElementById('calcAddon').innerText = formatRupiah(totalAddon);
            document.getElementById('calcGrandTotal').innerText = grandTotalText;

            // Update Termin Summary Card
            document.getElementById('valTerminSummary').innerText = formatRupiah(dp1);
            document.getElementById('valSisaSummary').innerText = formatRupiah(sisa);

            // Update Preview Table & Totals
            document.getElementById('previewTableBody').innerHTML = tbHtml;
            document.getElementById('previewSubtotal').innerText = formatRupiah(subtotalPaket);
            document.getElementById('previewAddon').innerText = formatRupiah(totalAddon);

            const prevDiskonEl = document.getElementById('previewDiskon');
            if (prevDiskonEl) {
                if (totalDiskon > 0) {
                    prevDiskonEl.parentElement.style.display = 'flex';
                    prevDiskonEl.innerText = '- ' + formatRupiah(totalDiskon);
                } else {
                    prevDiskonEl.parentElement.style.display = 'none';
                }
            }

            document.getElementById('previewGrandTotal').innerText = grandTotalText;
            document.getElementById('previewDP1').innerText = formatRupiah(dp1);
            document.getElementById('previewSisaTagihan').innerText = formatRupiah(sisa);
        }

        // --- Toast Notification ---
        function showToastNotification(msg) {
            let toast = document.getElementById('invoiceToast');
            const toastText = document.getElementById('invoiceToastText');
            if (toast && toastText) {
                toastText.innerText = msg;
                toast.style.display = 'flex';
                void toast.offsetWidth;
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

        // Initialize once on load
        document.addEventListener('DOMContentLoaded', () => {
            calculateTotals();
            syncPreviewClientWa();
            syncPreviewDate();
            syncBankPreview();
            syncTerminPreview();
        });
    </script>
    
    <!-- Toast Notification Element -->
    <div id="invoiceToast" style="display: none; position: fixed; top: 76px; right: 28px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 12px 20px; font-size: 13px; font-weight: 600; color: #181c32; z-index: 99999; align-items: center; gap: 10px; transition: all 0.3s ease; opacity: 0; transform: translateY(-10px);">
        <span style="display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; background: #181c32; color: #fff; border-radius: 50%; font-size: 11px;">
            <i class="fa-solid fa-check"></i>
        </span>
        <span id="invoiceToastText">Data klien diambil dari booking</span>
    </div>
    <script src="/js/responsive.js"></script>
</body>
</html>

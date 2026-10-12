<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoice Generator - CloseMateAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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
                <button type="button" class="btn btn-primary" id="tabBtnInvoiceBaru" onclick="switchView('baru')"><i class="fa-solid fa-plus"></i> Invoice Baru</button>
                <button type="button" class="btn btn-secondary" id="tabBtnInvoiceTersimpan" onclick="switchView('tersimpan')"><i class="fa-regular fa-folder-open"></i> <span id="savedInvoiceCountText">Invoice Tersimpan (0)</span></button>
            </div>
            
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-language" style="color: #64748b; font-size: 16px;"></i>
                <div class="btn-group">
                    <button class="btn active">ID</button>
                    <button class="btn">EN</button>
                </div>
            </div>
        </div>
        
        <div class="invoice-grid" id="invoiceEditorView">
            
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
                            <input type="text" class="form-control" value="085878067645" id="inputNoWaKlien" oninput="syncPreviewClientWa()">
                            <div id="helperWaKlien" style="font-size: 11px; color: #a1a5b7; margin-top: 4px;">+6285878067645</div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Alamat Klien (opsional)</label>
                            <input type="text" class="form-control" value="kombas" placeholder="Alamat lengkap klien" id="inputAlamatKlien" oninput="syncPreviewClientWa()">
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
                            </div>
                        </div>

                        <button class="btn btn-secondary" style="border: 1px solid var(--border-color); font-weight: 500; padding: 10px 16px; margin-bottom: 8px; display: inline-flex; align-items: center; gap: 8px;" id="btnSimpanBiaya" onclick="simpanBiaya()"><i class="fa-regular fa-floppy-disk" id="iconBiaya"></i> <span id="textBiaya">Simpan Paket & Harga</span></button>
                        <div class="info-text" style="margin-bottom: 16px;">Tersimpan di perangkat ini, bisa dipilih lagi lewat tombol panah di kolom nama paket.</div>
                        
                        <button class="btn btn-outline-primary form-group" onclick="addPaketTambahan()"><i class="fa-solid fa-plus"></i> Tambah Paket</button>
                        
                        <div class="info-text bg form-group" style="display: flex; justify-content: space-between; font-weight: 500; align-items: center; padding: 12px 16px; border-radius: 8px;">
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span style="font-size: 11px; color: var(--text-muted);">Subtotal Paket</span>
                                <span id="calcSubtotal" style="font-weight: 600;">Rp 5.018.000</span>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span style="font-size: 11px; color: var(--text-muted);">Total Add-on</span>
                                <span id="calcAddon" style="font-weight: 600;">Rp 120.000</span>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 4px; text-align: right;">
                                <span style="font-size: 11px; color: var(--text-muted);">GRAND TOTAL</span>
                                <span id="calcGrandTotal" style="color: var(--primary); font-size: 16px; font-weight: 700;">Rp 5.078.000</span>
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
                        <div id="addonContainer">
                            <div class="addon-item-box" style="border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                                <div style="position: relative; margin-bottom: 12px;">
                                    <input type="text" class="form-control addon-name" value="kukuh" placeholder="Nama Add-on" oninput="calculateTotals()" style="padding-right: 36px;">
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
                                        <input type="text" class="form-control addon-price" value="Rp 20.000" oninput="handleCurrencyInput(this); calculateTotals();">
                                    </div>
                                </div>
                                <div class="subtotal-text" style="text-align: right; font-size: 12px; color: var(--text-muted); margin-top: 8px;">
                                    <span class="addon-calc-formula">1 x Rp 20.000 = </span><strong class="addon-calc-result" style="color: var(--text-dark);">Rp 20.000</strong>
                                </div>
                            </div>
                            <div class="addon-item-box" style="border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                                <div style="position: relative; margin-bottom: 12px;">
                                    <input type="text" class="form-control addon-name" value="bopaa" placeholder="Nama Add-on" oninput="calculateTotals()" style="padding-right: 36px;">
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
                                        <input type="text" class="form-control addon-price" value="Rp 10.000" oninput="handleCurrencyInput(this); calculateTotals();">
                                    </div>
                                </div>
                                <div class="subtotal-text" style="text-align: right; font-size: 12px; color: var(--text-muted); margin-top: 8px;">
                                    <span class="addon-calc-formula">1 x Rp 10.000 = </span><strong class="addon-calc-result" style="color: var(--text-dark);">Rp 10.000</strong>
                                </div>
                            </div>
                            <div class="addon-item-box" style="border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                                <div style="position: relative; margin-bottom: 12px;">
                                    <input type="text" class="form-control addon-name" value="addon9" placeholder="Nama Add-on" oninput="calculateTotals()" style="padding-right: 36px;">
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
                                        <input type="text" class="form-control addon-price" value="Rp 90.000" oninput="handleCurrencyInput(this); calculateTotals();">
                                    </div>
                                </div>
                                <div class="subtotal-text" style="text-align: right; font-size: 12px; color: var(--text-muted); margin-top: 8px;">
                                    <span class="addon-calc-formula">1 x Rp 90.000 = </span><strong class="addon-calc-result" style="color: var(--text-dark);">Rp 90.000</strong>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-outline-primary" style="width: 100%; border: 1px dashed var(--primary); color: var(--primary); background: transparent; padding: 12px; border-radius: 8px; font-weight: 500;" onclick="addAddonItem()"><i class="fa-solid fa-plus"></i> Tambah Item Add-on</button>
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
                                <option value="Termin 2 / DP 2">Termin 2 / DP 2</option>
                                <option value="Pelunasan">Pelunasan</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Nominal DP 1</label>
                            <input type="text" class="form-control" value="Rp 400.000" id="inputDP1" oninput="handleCurrencyInput(this); calculateTotals();">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Nominal DP 2</label>
                            <input type="text" class="form-control" value="Rp 100.000" id="inputDP2" oninput="handleCurrencyInput(this); calculateTotals();">
                        </div>
                        
                        <div class="termin-summary-card" style="background: #f8f9fa; border: 1px solid var(--border-color); border-radius: 8px; padding: 16px 20px; margin: 16px 0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="color: var(--text-dark); font-weight: 500; font-size: 13px;" id="labelTerminSummary">Total DP 1 + DP 2</span>
                                <span style="color: var(--text-dark); font-weight: 700; font-size: 14px;" id="valTerminSummary">Rp 500.000</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="color: var(--text-dark); font-weight: 500; font-size: 13px;">Sisa Tagihan</span>
                                <span style="color: var(--text-dark); font-weight: 700; font-size: 14px;" id="valSisaSummary">Rp 4.578.000</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Tanggal Jatuh Tempo Pelunasan</label>
                            <div style="position: relative;">
                                <input type="text" class="form-control" placeholder="mm/dd/yyyy" id="inputTglJatuhTempo" value="10/16/2026" oninput="syncJatuhTempoPreview()">
                                <i class="fa-regular fa-calendar" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--text-dark); pointer-events: none;"></i>
                            </div>
                        </div>
                        
                        <div class="form-row form-group">
                            <div>
                                <label class="form-label">Nama Bank</label>
                                <input type="text" class="form-control" value="BCA" id="inputNamaBank" oninput="syncBankPreview()">
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
                        
                        <button class="btn btn-secondary" style="width: 100%; border: 1px solid var(--border-color); font-weight: 500;" id="btnSimpanBank" onclick="simpanBank()"><i class="fa-regular fa-floppy-disk" id="iconBank"></i> <span id="textBank">Simpan Data Bank</span></button>
                    </div>
                </div>

                <!-- Panel 6 -->
                <div class="acc-panel">
                    <div class="acc-header">
                        Syarat & Ketentuan (Kustom)
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <div class="info-text form-group" style="font-size: 12px; color: var(--text-muted); margin-bottom: 14px;">Atur poin-poin S&K yang muncul di invoice. Perubahan tersimpan otomatis di perangkat ini.</div>
                        
                        <div id="termsContainer">
                            <div class="term-point-card" style="border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; margin-bottom: 12px; background: #fff; position: relative;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; gap: 8px;">
                                    <input type="text" class="form-control term-title" placeholder="Judul (cth: Pelunasan:)" value="pelunasan" oninput="syncTermsPreview()" style="font-weight: 500; font-size: 13px;">
                                    <button type="button" class="btn-trash" onclick="removeTermPoint(this)" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 15px; padding: 4px;" title="Hapus poin">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                                <textarea class="form-control term-content" placeholder="Isi ketentuan..." rows="2" oninput="syncTermsPreview()" style="font-size: 12px; resize: vertical;">bayar</textarea>
                            </div>
                        </div>

                        <div class="form-row" style="margin-top: 14px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <button type="button" class="btn btn-outline-primary" style="font-weight: 500; border-radius: 8px; border: 1px solid var(--primary); color: var(--primary); background: transparent; padding: 10px;" onclick="addTermPoint()"><i class="fa-solid fa-plus"></i> Tambah Poin</button>
                            <button type="button" class="btn btn-secondary" style="border: 1px dashed var(--border-color); font-weight: 500; border-radius: 8px; padding: 10px;" onclick="resetTermsToDefault()">Reset ke Default</button>
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
                            <div id="previewDpBox" style="background: #f8f9fa; border-radius: 8px; padding: 12px; margin-top: 16px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 11px;">
                                    <div style="color: #666;" id="previewDP1Label">Uang Muka / DP 1</div>
                                    <div id="previewDP1" style="color: #333; font-weight: 500;">Rp 400.000</div>
                                </div>
                                <div id="previewDP2Row" style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 11px;">
                                    <div style="color: #666;" id="previewDP2Label">Termin 2 / DP 2</div>
                                    <div id="previewDP2" style="color: #333; font-weight: 500;">Rp 100.000</div>
                                </div>
                                <div id="previewTotalDPRow" style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 11px; padding-bottom: 6px; border-bottom: 1px dashed #ddd;">
                                    <div style="color: #666; font-weight: 600;">Total DP 1 + DP 2</div>
                                    <div id="previewTotalDP" style="color: #333; font-weight: 600;">Rp 500.000</div>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 600;">
                                    <div style="color: #666;">Sisa Tagihan</div>
                                    <div id="previewSisaTagihan" style="color: #333; font-size: 13px;">Rp 4.578.000</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- METODE PEMBAYARAN -->
                    <div class="inv-payment" style="border: 1px solid #f1f1f4; border-radius: 8px; padding: 16px; margin-bottom: 24px;" id="previewPaymentBox">
                        <p style="text-transform: uppercase; font-size: 9px; font-weight: 600; color: #999; margin-bottom: 6px; letter-spacing: 0.5px;">METODE PEMBAYARAN</p>
                        <h5 style="font-size: 12px; font-weight: 500; color: #333; margin: 0 0 4px 0;" id="previewPaymentText">
                            <strong style="color: #333;" id="previewBankName">BCA</strong> &middot; <span id="previewNoRek">0431063867</span> &middot; a.n <span id="previewAtasNama">Umi Salamah</span>
                        </h5>
                        <p style="font-size: 11px; color: #777; margin: 0;" id="previewJatuhTempoRow">
                            Jatuh tempo pelunasan: <span id="previewJatuhTempoDate">Jumat, 16 Oktober 2026</span>
                        </p>
                    </div>
                    
                    <!-- SYARAT KETENTUAN -->
                    <div class="inv-footer" style="display: block;">
                        <div style="margin-bottom: 24px;">
                            <p style="font-weight: 700; font-size: 10px; margin-bottom: 8px; color: #555; text-transform: uppercase; letter-spacing: 0.5px;">SYARAT & KETENTUAN</p>
                            <div id="previewTermsList" style="font-size: 10px; color: #555; line-height: 1.6; margin-bottom: 20px;">
                                <div style="margin-bottom: 4px;">1. <strong>pelunasan</strong> bayar</div>
                            </div>
                            
                            <!-- Line divider and signature -->
                            <div style="border-top: 1px solid #eee; padding-top: 20px; display: flex; justify-content: flex-end;">
                                <div class="inv-signature" style="text-align: center; width: 160px;">
                                    <div style="margin-bottom: 4px; font-weight: 500; font-size: 13px; color: #333;" id="previewTtdNama">Fariz hidayat</div>
                                    <div id="previewTtdImgBox" style="height: 48px; display: flex; align-items: center; justify-content: center; margin-bottom: 6px;">
                                        <img id="previewTtdImg" src="" style="max-height: 44px; max-width: 130px; display: none;">
                                        <span id="previewTtdPlaceholder" style="font-style: italic; font-size: 11px; color: #999;">signature</span>
                                    </div>
                                    <div style="border-top: 1px solid #ccc; padding-top: 6px;">
                                        <p style="font-size: 9px; color: #888; margin: 0;">Tanda Tangan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="section-title" style="margin-top: 24px;"><i class="fa-solid fa-pen-nib"></i> Tanda Tangan (PNG/JPG, maks 2MB)</div>
                <div style="background: white; border: 1px solid var(--border-color); border-radius: 10px; padding: 16px; margin-bottom: 16px;">
                    <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 12px;">
                        <div id="ttdPreviewThumbnail" style="width: 50px; height: 50px; min-width: 50px; border: 1px solid var(--border-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; overflow: hidden;">
                            <img id="ttdImgThumbnail" src="" style="display: none; max-width: 100%; max-height: 100%; object-fit: contain;">
                            <i class="fa-solid fa-signature" id="ttdIconPlaceholder" style="color: #a1a5b7; font-size: 20px;"></i>
                        </div>
                        <div class="btn btn-secondary" id="btnUploadTtd" style="flex: 1; height: 44px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-dark); font-size: 13px; font-weight: 500; background: white; gap: 8px; cursor: pointer;" onclick="document.getElementById('ttdInput').click()">
                            <i class="fa-solid fa-arrow-up-from-bracket"></i> Unggah TTD
                        </div>
                        <span id="btnHapusTtd" style="display: none; color: var(--text-muted); cursor: pointer; font-size: 13px; margin-left: 4px;" onclick="hapusTtd()">Hapus</span>
                        <input type="file" id="ttdInput" style="display: none;" accept="image/png, image/jpeg" onchange="previewTtdFile(this)">
                    </div>
                    <button type="button" class="btn btn-secondary" style="width: 100%; border: 1px solid var(--border-color); font-weight: 500;" id="btnSimpanTtd" onclick="simpanTtd()"><i class="fa-regular fa-floppy-disk" id="iconTtd"></i> <span id="textTtd">Simpan TTD</span></button>
                </div>
                
                <div style="display: flex; gap: 10px; margin-bottom: 20px; align-items: center; flex-wrap: wrap;">
                    <div style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: rgba(80, 205, 137, 0.1); border-radius: 20px; font-size: 12px; color: #50cd89; font-weight: 500;">
                        <i class="fa-solid fa-check"></i> Logo siap
                    </div>
                    <div style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: rgba(255, 199, 0, 0.1); border-radius: 20px; font-size: 12px; color: #ff9800; font-weight: 500;" id="badgeTtdStatus">
                        <i class="fa-solid fa-pen-nib"></i> <span id="badgeTtdText">Tanda tangan memuat...</span>
                    </div>
                </div>
                
                <div class="inv-action-btns" style="display: flex; gap: 12px; margin-bottom: 40px;">
                    <button type="button" class="btn btn-secondary" onclick="unduhPNG()"><i class="fa-regular fa-image"></i> Unduh PNG</button>
                    <button type="button" class="btn btn-secondary" style="background: var(--text-dark); color: white; border-color: var(--text-dark);" onclick="unduhPDF()"><i class="fa-regular fa-file-pdf"></i> Unduh PDF</button>
                    <button type="button" class="btn btn-primary btn-full" style="flex: 1;" id="btnSimpanFolder" onclick="simpanInvoiceKeDatabase()"><i class="fa-regular fa-folder-open"></i> Simpan ke Folder</button>
                </div>
                
            </div>
            
        </div>

        <!-- TAB: INVOICE TERSIMPAN VIEW -->
        <div id="savedInvoicesView" style="display: none; width: 100%;">
            <!-- Search & Filter Bar -->
            <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 20px; flex-wrap: wrap;">
                <div style="position: relative; flex: 1; min-width: 260px;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #a1a5b7; font-size: 14px;"></i>
                    <input type="text" id="searchSavedInvoice" class="form-control" placeholder="Cari nama klien / no. invoice..." oninput="filterSavedInvoices()" style="padding-left: 38px; height: 42px; border-radius: 8px; font-size: 13px; background: white;">
                </div>
                <button type="button" class="btn" style="background: #5949d6; color: white; border-radius: 8px; height: 42px; padding: 0 18px; font-weight: 500; border: none; cursor: pointer;">Semua Tanggal</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='/api/invoices/export/csv'" style="border-radius: 8px; height: 42px; padding: 0 18px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px; background: white; cursor: pointer;">
                    <i class="fa-solid fa-file-csv"></i> Ekspor CSV
                </button>
            </div>

            <!-- Status Filter Pills -->
            <div style="display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;">
                <button type="button" class="status-filter-pill active" onclick="setFilterStatus('all', this)" style="padding: 6px 18px; border-radius: 20px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; background: #6c5ce7; color: white; transition: 0.2s;">Semua</button>
                <button type="button" class="status-filter-pill" onclick="setFilterStatus('Belum Dibayar', this)" style="padding: 6px 18px; border-radius: 20px; font-size: 13px; font-weight: 500; border: 1px solid var(--border-color); cursor: pointer; background: white; color: #555; transition: 0.2s;">Belum Dibayar</button>
                <button type="button" class="status-filter-pill" onclick="setFilterStatus('Terbayar Sebagian', this)" style="padding: 6px 18px; border-radius: 20px; font-size: 13px; font-weight: 500; border: 1px solid var(--border-color); cursor: pointer; background: white; color: #555; transition: 0.2s;">Terbayar Sebagian</button>
                <button type="button" class="status-filter-pill" onclick="setFilterStatus('Lunas', this)" style="padding: 6px 18px; border-radius: 20px; font-size: 13px; font-weight: 500; border: 1px solid var(--border-color); cursor: pointer; background: white; color: #555; transition: 0.2s;">Lunas</button>
            </div>

            <!-- Counters Row -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; font-size: 13px; color: #64748b;">
                <div><span id="savedCountLabel">0 invoice</span></div>
                <div style="font-weight: 600; color: #1e1e2d;"><span id="savedTotalValLabel">Total nilai: Rp 0</span></div>
            </div>

            <!-- Invoices List Container -->
            <div id="savedInvoicesListContainer" style="background: white; border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden;">
                <div style="text-align: center; padding: 40px; color: #a1a5b7; font-size: 13px;">Memuat invoice tersimpan...</div>
            </div>
        </div>

        <!-- MODAL RIWAYAT PEMBAYARAN -->
        <div id="paymentModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 100000; align-items: center; justify-content: center;">
            <div style="background: white; width: 90%; max-width: 480px; border-radius: 16px; padding: 26px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #181c32;">Riwayat Pembayaran — <span id="paymentModalInvNo">INV-202610-001</span></h3>
                    <i class="fa-solid fa-xmark" style="cursor: pointer; color: #a1a5b7; font-size: 16px; padding: 4px;" onclick="closePaymentModal()"></i>
                </div>
                <div style="font-size: 13px; color: #64748b; margin-bottom: 20px;" id="paymentModalSubtitle">
                    Total Rp 0 &middot; Terbayar Rp 0 &middot; Sisa Rp 0
                </div>

                <div id="paymentHistoryList" style="margin-bottom: 20px; max-height: 180px; overflow-y: auto;">
                    <div style="text-align: center; padding: 20px; color: #a1a5b7; font-size: 13px;">Belum ada pembayaran tercatat.</div>
                </div>

                <form id="recordPaymentForm" onsubmit="submitRecordPayment(event)">
                    <input type="hidden" id="paymentInvoiceId">
                    <div class="form-group" style="margin-bottom: 12px;">
                        <input type="text" id="inputPaymentNominal" class="form-control" placeholder="Nominal pembayaran" style="height: 44px; font-size: 14px; font-weight: 600; border-radius: 8px;" oninput="handleCurrencyInput(this)">
                    </div>
                    <div class="form-row form-row-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 18px;">
                        <div style="position: relative;">
                            <input type="text" id="inputPaymentDate" class="form-control" placeholder="mm/dd/yyyy" style="height: 44px; font-size: 13px; border-radius: 8px; padding-right: 32px;">
                            <i class="fa-regular fa-calendar" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #64748b; pointer-events: none;"></i>
                        </div>
                        <div>
                            <input type="text" id="inputPaymentNote" class="form-control" placeholder="Catatan (opsional)" style="height: 44px; font-size: 13px; border-radius: 8px;">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; height: 46px; background: #6c5ce7; border-radius: 10px; font-size: 14px; font-weight: 600; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; color: white;">
                        Catat Pembayaran
                    </button>
                </form>
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
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        let connectedBookingId = null;

        // --- Modal Booking ---
        function openBookingModal() {
            let modal = document.getElementById('bookingModal');
            if (!modal) {
                const modalHtml = `
                <div id="bookingModal" style="display: flex; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
                    <div style="background: white; width: 90%; max-width: 520px; border-radius: 12px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); max-height: 85vh; display: flex; flex-direction: column;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <h3 style="margin: 0; font-size: 18px; font-weight: 600;">Pilih Booking</h3>
                            <i class="fa-solid fa-xmark" style="cursor: pointer; color: var(--text-muted); font-size: 18px;" onclick="closeBookingModal()"></i>
                        </div>
                        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">Data klien pada form invoice akan ditimpa oleh data booking yang dipilih.</p>
                        
                        <div style="margin-bottom: 16px; position: relative;">
                            <input type="text" class="form-control" id="searchBookingInput" oninput="filterBookingList()" placeholder="Cari nama klien atau paket..." style="border-radius: 20px; padding-left: 16px; border-color: var(--primary);">
                        </div>
                        
                        <div id="bookingListContainer" style="overflow-y: auto; flex: 1; padding-right: 4px;">
                            <div style="text-align: center; padding: 20px; color: var(--text-muted); font-size: 13px;">Memuat data booking...</div>
                        </div>
                    </div>
                </div>`;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                modal = document.getElementById('bookingModal');
            }
            modal.style.display = 'flex';
            loadBookingsData();
        }
        
        function closeBookingModal() {
            const modal = document.getElementById('bookingModal');
            if (modal) modal.style.display = 'none';
        }

        let loadedBookings = [];
        function loadBookingsData() {
            const container = document.getElementById('bookingListContainer');
            if (!container) return;

            fetch('/api/bookings')
                .then(res => res.json())
                .then(data => {
                    const bookings = Array.isArray(data) ? data : (data.bookings || []);
                    loadedBookings = bookings;
                    renderBookingList(bookings);
                })
                .catch(err => {
                    // Fallback sample bookings if API returns error
                    loadedBookings = [
                        {
                            id: 'sample-1',
                            client_name: 'fariz',
                            client_wa: '085878067645',
                            client_address: 'kombas',
                            event_date: '2026-10-10',
                            package_name: 'Penawaran QT-20261010-236',
                            total_price: 9000,
                            discount: 50000,
                            package_qty: 2
                        }
                    ];
                    renderBookingList(loadedBookings);
                });
        }

        function renderBookingList(list) {
            const container = document.getElementById('bookingListContainer');
            if (!container) return;

            if (list.length === 0) {
                container.innerHTML = `<div style="text-align: center; padding: 20px; color: var(--text-muted); font-size: 13px;">Belum ada data booking tersedia.</div>`;
                return;
            }

            let html = '';
            list.forEach(b => {
                const name = b.client_name || b.name || 'Klien';
                const wa = b.client_wa || b.client_phone || '085878067645';
                const addr = b.client_address || b.address || 'kombas';
                const tgl = b.event_date || '2026-10-10';
                const pkg = b.package_name || b.package || 'Penawaran QT-20261010-236';
                const price = b.total_price || b.price || 9000;
                const disc = b.discount || 50000;
                const qty = b.package_qty || 2;
                const bId = b.id || '';

                html += `
                <div style="padding: 12px 16px; border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; margin-bottom: 8px; transition: 0.2s;" onclick="pilihBooking('${name}', '${wa}', '${addr}', '${tgl}', '${pkg}', ${price}, ${disc}, ${qty}, '${bId}')" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                        <span style="font-weight: 600; font-size: 14px;">${name}</span>
                        <span style="font-size: 12px; color: var(--primary); font-weight: 600;">${formatRupiah(price)}</span>
                    </div>
                    <div style="font-size: 12px; color: var(--text-muted);">${tgl} &bull; ${pkg}</div>
                </div>`;
            });
            container.innerHTML = html;
        }

        function filterBookingList() {
            const query = (document.getElementById('searchBookingInput')?.value || '').toLowerCase();
            const filtered = loadedBookings.filter(b => {
                const name = (b.client_name || b.name || '').toLowerCase();
                const pkg = (b.package_name || b.package || '').toLowerCase();
                return name.includes(query) || pkg.includes(query);
            });
            renderBookingList(filtered);
        }
        
        function pilihBooking(nama, wa, alamat, tgl, paketUtama, hargaUtama, diskonUtama, qtyUtama, bookingId = null) {
            closeBookingModal();
            connectedBookingId = bookingId;
            
            // Tampilkan state connected
            document.getElementById('btnAmbilBooking').style.display = 'none';
            document.getElementById('infoAmbilBooking').style.display = 'none';
            document.getElementById('bookingConnectedBox').style.display = 'block';
            document.getElementById('bookingConnectedText').innerText = `Terhubung ke Booking: ${nama} — ${tgl}`;
            
            // Set values di UI Klien
            document.getElementById('inputNamaKlien').value = nama;
            document.getElementById('inputNoWaKlien').value = wa;
            document.getElementById('inputAlamatKlien').value = alamat || 'kombas';
            document.getElementById('inputTglAcara').value = tgl;
            
            // Set values di UI Biaya
            document.getElementById('inputPaketUtama').value = paketUtama;
            document.getElementById('inputHargaUtama').value = formatRupiah(hargaUtama);
            document.getElementById('inputJumlahPaket').value = qtyUtama || 2;
            document.getElementById('inputDiskon').value = formatRupiah(diskonUtama || 50000);
            
            // Render Live Preview
            syncPreviewClientWa();
            syncPreviewDate();
            calculateTotals();
            
            showToastNotification("Data klien diambil dari booking");
        }
        
        function putuskanBooking() {
            connectedBookingId = null;
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
                    // check if mm/dd/yyyy or dd/mm/yyyy
                    if (parseInt(parts[0], 10) <= 12 && parseInt(parts[1], 10) > 12) {
                        m = parseInt(parts[0], 10);
                        d = parseInt(parts[1], 10);
                        y = parseInt(parts[2], 10);
                    } else if (parseInt(parts[1], 10) <= 12 && parseInt(parts[0], 10) > 12) {
                        d = parseInt(parts[0], 10);
                        m = parseInt(parts[1], 10);
                        y = parseInt(parts[2], 10);
                    } else {
                        // default mm/dd/yyyy for standard US inputs
                        m = parseInt(parts[0], 10);
                        d = parseInt(parts[1], 10);
                        y = parseInt(parts[2], 10);
                    }
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
                prevDate.innerText = formatTanggalIndonesia(rawVal) || rawVal || 'Sabtu, 10 Oktober 2026';
            }
        }

        function syncJatuhTempoPreview() {
            const rawVal = (document.getElementById('inputTglJatuhTempo').value || '').trim();
            const prevJatuhTempo = document.getElementById('previewJatuhTempoDate');
            if (prevJatuhTempo) {
                prevJatuhTempo.innerText = formatTanggalIndonesia(rawVal) || rawVal || 'Jumat, 16 Oktober 2026';
            }
        }

        function syncTerminPreview() {
            const terminVal = document.getElementById('selectTermin').value;
            const previewTermin = document.getElementById('previewTerminLabel');
            const previewDP1Label = document.getElementById('previewDP1Label');
            
            if (previewTermin) previewTermin.innerText = terminVal;
            if (previewDP1Label) previewDP1Label.innerText = terminVal;
            calculateTotals();
        }

        function syncBankPreview() {
            const bank = document.getElementById('inputNamaBank').value || 'BCA';
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
            const bank = document.getElementById('inputNamaBank').value;
            const rek = document.getElementById('inputNoRekening').value;
            const an = document.getElementById('inputAtasNama').value;

            const btn = document.getElementById('btnSimpanBank');
            const icon = document.getElementById('iconBank');
            const text = document.getElementById('textBank');
            
            btn.style.background = 'rgba(80, 205, 137, 0.1)';
            btn.style.borderColor = 'var(--success)';
            btn.style.color = 'var(--success)';
            if (icon) icon.className = 'fa-solid fa-check';
            if (text) text.innerText = 'Tersimpan';

            fetch('/api/invoices/save-bank', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    bank_name: bank,
                    bank_account_number: rek,
                    bank_account_name: an
                })
            })
            .then(res => res.json())
            .then(data => {
                showToastNotification("Data bank disimpan");
            })
            .catch(err => {
                showToastNotification("Data bank disimpan");
            })
            .finally(() => {
                setTimeout(() => {
                    btn.style.background = 'transparent';
                    btn.style.borderColor = 'var(--border-color)';
                    btn.style.color = 'var(--text-dark)';
                    if (icon) icon.className = 'fa-regular fa-floppy-disk';
                    if (text) text.innerText = 'Simpan Data Bank';
                }, 2000);
            });

            syncBankPreview();
        }

        // --- Syarat & Ketentuan (Kustom) Management ---
        const defaultTerms = [
            {
                title: 'Konfirmasi Pembayaran:',
                content: 'Pembayaran dianggap sah jika dana sudah masuk ke rekening yang tertera di atas dan klien telah mengirimkan bukti transfer.'
            },
            {
                title: 'Kebijakan Pembatalan:',
                content: 'DP bersifat non-refundable. Reschedule mohon konfirmasi maksimal 1-2 bulan sebelum tanggal acara, subjek pada ketersediaan jadwal.'
            },
            {
                title: 'Pelunasan:',
                content: 'Pelunasan wajib selambat-lambatnya 1 minggu sebelum hari H acara.'
            },
            {
                title: 'Invoice ini:',
                content: 'merupakan satu kesatuan dengan Surat Perjanjian Kerja Sama (SPK) yang telah ditandatangani.'
            }
        ];

        function addTermPoint(title = '', content = '') {
            const container = document.getElementById('termsContainer');
            if (!container) return;

            const html = `
            <div class="term-point-card" style="border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; margin-bottom: 12px; background: #fff; position: relative;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; gap: 8px;">
                    <input type="text" class="form-control term-title" placeholder="Judul (cth: Pelunasan:)" value="${title}" oninput="syncTermsPreview()" style="font-weight: 500; font-size: 13px;">
                    <button type="button" class="btn-trash" onclick="removeTermPoint(this)" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 15px; padding: 4px;" title="Hapus poin">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </div>
                <textarea class="form-control term-content" placeholder="Isi ketentuan..." rows="2" oninput="syncTermsPreview()" style="font-size: 12px; resize: vertical;">${content}</textarea>
            </div>`;
            container.insertAdjacentHTML('beforeend', html);
            syncTermsPreview();
        }

        function removeTermPoint(btn) {
            const card = btn.closest('.term-point-card');
            if (card) {
                card.remove();
                syncTermsPreview();
            }
        }

        function renderTerms(termsArray) {
            const container = document.getElementById('termsContainer');
            if (!container) return;
            container.innerHTML = '';
            termsArray.forEach(item => {
                addTermPoint(item.title || '', item.content || '');
            });
            syncTermsPreview();
        }

        function resetTermsToDefault() {
            renderTerms(defaultTerms);
            
            // Simpan ke backend
            fetch('/api/invoices/save-terms', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ terms: defaultTerms })
            }).catch(e => {});

            showToastNotification("Syarat & Ketentuan direset ke default");
        }

        function syncTermsPreview() {
            const cards = document.querySelectorAll('.term-point-card');
            const previewContainer = document.getElementById('previewTermsList');
            if (!previewContainer) return;

            let previewHtml = '';
            let termsData = [];

            cards.forEach((card, idx) => {
                const title = (card.querySelector('.term-title')?.value || '').trim();
                const content = (card.querySelector('.term-content')?.value || '').trim();
                termsData.push({ title, content });

                const formattedTitle = title ? `<strong>${title}</strong> ` : '';
                previewHtml += `
                <div style="margin-bottom: 6px;">
                    <span>${idx + 1}. </span>
                    ${formattedTitle}<span>${content}</span>
                </div>`;
            });

            if (cards.length === 0) {
                previewContainer.innerHTML = '<div style="color: #999; font-style: italic;">Tidak ada syarat & ketentuan</div>';
            } else {
                previewContainer.innerHTML = previewHtml;
            }

            try {
                localStorage.setItem('invoice_custom_terms', JSON.stringify(termsData));
            } catch(e) {}
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
            if (icon) icon.className = 'fa-solid fa-check';
            if (text) text.innerText = 'Tersimpan';

            setTimeout(() => {
                btn.style.background = 'transparent';
                btn.style.borderColor = 'var(--border-color)';
                btn.style.color = 'var(--text-dark)';
                if (icon) icon.className = 'fa-regular fa-floppy-disk';
                if (text) text.innerText = 'Simpan Paket & Harga';
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
        function addAddonItem(name = 'Add-on Baru', qty = 1, price = 20000) {
            const html = `
            <div class="addon-item-box" style="border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                <div style="position: relative; margin-bottom: 12px;">
                    <input type="text" class="form-control addon-name" value="${name}" placeholder="Nama Add-on" oninput="calculateTotals()" style="padding-right: 36px;">
                    <button type="button" class="btn-trash" onclick="removeAddonItem(this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 15px;">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </div>
                <div class="form-row form-row-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 8px;">
                    <div>
                        <label class="form-label">Jumlah (pcs)</label>
                        <input type="number" class="form-control addon-qty" value="${qty}" min="1" oninput="calculateTotals()">
                    </div>
                    <div>
                        <label class="form-label">Harga Satuan</label>
                        <input type="text" class="form-control addon-price" value="${formatRupiah(price)}" oninput="handleCurrencyInput(this); calculateTotals();">
                    </div>
                </div>
                <div class="subtotal-text" style="text-align: right; font-size: 12px; color: var(--text-muted); margin-top: 8px;">
                    <span class="addon-calc-formula">${qty} x ${formatRupiah(price)} = </span><strong class="addon-calc-result" style="color: var(--text-dark);">${formatRupiah(qty * price)}</strong>
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
        function addPaketTambahan(name = 'layanan 2', price = 5000000, disc = 10000) {
            const html = `
            <div class="paket-tambahan-box" style="border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; margin-bottom: 16px; position: relative; background: #ffffff;">
                <button type="button" class="btn-trash" onclick="removePaketTambahan(this)" title="Hapus paket tambahan" style="position: absolute; right: 12px; top: 12px; background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 15px;">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label">Nama Paket Tambahan</label>
                    <input type="text" class="form-control pkt-extra-name" value="${name}" placeholder="Nama Paket Tambahan" oninput="calculateTotals()">
                </div>
                <div class="form-row form-row-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label class="form-label">Harga Paket</label>
                        <input type="text" class="form-control pkt-extra-price" value="${formatRupiah(price)}" oninput="handleCurrencyInput(this); calculateTotals();">
                    </div>
                    <div>
                        <label class="form-label">Diskon</label>
                        <input type="text" class="form-control pkt-extra-disc" value="${formatRupiah(disc)}" oninput="handleCurrencyInput(this); calculateTotals();">
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
                const name = (box.querySelector('.pkt-extra-name')?.value || '').trim() || 'Paket Tambahan';
                const price = parseCurrency(box.querySelector('.pkt-extra-price')?.value);
                const disc = parseCurrency(box.querySelector('.pkt-extra-disc')?.value);
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

            // 3. Add-ons
            let totalAddon = 0;
            const addonItems = document.querySelectorAll('.addon-item-box');
            addonItems.forEach(item => {
                const name = (item.querySelector('.addon-name')?.value || '').trim() || 'Add-on';
                const qty = parseInt(item.querySelector('.addon-qty')?.value) || 1;
                const price = parseCurrency(item.querySelector('.addon-price')?.value);
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
                        <div style="font-size: 10px; color: #888;">Add-on</div>
                    </td>
                    <td style="text-align: right; padding: 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; color: #333;">${formatRupiah(itemTotal)}</td>
                </tr>`;
            });

            // 4. Diskon row (if totalDiskon > 0)
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

            // Update Termin Summary Card in Form
            const labelTerminSummary = document.getElementById('labelTerminSummary');
            const valTerminSummary = document.getElementById('valTerminSummary');
            const valSisaSummary = document.getElementById('valSisaSummary');

            if (dp2 > 0) {
                if (labelTerminSummary) labelTerminSummary.innerText = 'Total DP 1 + DP 2';
                if (valTerminSummary) valTerminSummary.innerText = formatRupiah(totalDP);
            } else {
                if (labelTerminSummary) labelTerminSummary.innerText = 'Total DP 1';
                if (valTerminSummary) valTerminSummary.innerText = formatRupiah(dp1);
            }
            if (valSisaSummary) valSisaSummary.innerText = formatRupiah(sisa);

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
            
            // Update Preview DP Box breakdown
            const prevDP1 = document.getElementById('previewDP1');
            const prevDP2Row = document.getElementById('previewDP2Row');
            const prevDP2 = document.getElementById('previewDP2');
            const prevTotalDPRow = document.getElementById('previewTotalDPRow');
            const prevTotalDP = document.getElementById('previewTotalDP');
            const prevSisaTagihan = document.getElementById('previewSisaTagihan');

            if (prevDP1) prevDP1.innerText = formatRupiah(dp1);
            if (prevSisaTagihan) prevSisaTagihan.innerText = formatRupiah(sisa);

            if (dp2 > 0) {
                if (prevDP2Row) prevDP2Row.style.display = 'flex';
                if (prevDP2) prevDP2.innerText = formatRupiah(dp2);
                if (prevTotalDPRow) prevTotalDPRow.style.display = 'flex';
                if (prevTotalDP) prevTotalDP.innerText = formatRupiah(totalDP);
            } else {
                if (prevDP2Row) prevDP2Row.style.display = 'none';
                if (prevTotalDPRow) prevTotalDPRow.style.display = 'none';
            }
        }

        // --- Database Persistence & Invoice Saving ---
        function simpanInvoiceKeDatabase() {
            const btn = document.getElementById('btnSimpanFolder');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...`;
            btn.disabled = true;

            // Extra packages array
            const extraPackages = [];
            document.querySelectorAll('.paket-tambahan-box').forEach(box => {
                extraPackages.push({
                    name: box.querySelector('.pkt-extra-name')?.value || 'Paket Tambahan',
                    price: parseCurrency(box.querySelector('.pkt-extra-price')?.value),
                    discount: parseCurrency(box.querySelector('.pkt-extra-disc')?.value)
                });
            });

            // Addons array
            const addons = [];
            document.querySelectorAll('.addon-item-box').forEach(item => {
                addons.push({
                    name: item.querySelector('.addon-name')?.value || 'Add-on',
                    qty: parseInt(item.querySelector('.addon-qty')?.value) || 1,
                    price: parseCurrency(item.querySelector('.addon-price')?.value)
                });
            });

            // Terms array
            const terms = [];
            document.querySelectorAll('.term-point-card').forEach(card => {
                terms.push({
                    title: card.querySelector('.term-title')?.value || '',
                    content: card.querySelector('.term-content')?.value || ''
                });
            });

            const payload = {
                invoice_number: document.getElementById('previewNoInvoice')?.innerText || document.getElementById('inputNoInvoice')?.value || 'INV-202610-002',
                invoice_date: document.getElementById('previewInvoiceDate')?.innerText || '11 Oktober 2026',
                vendor_name: document.getElementById('inputNamaVendor')?.value || 'Fariz hidayat',
                vendor_address: document.getElementById('inputAlamatVendor')?.value || 'Kombas purwokerto',
                client_name: document.getElementById('inputNamaKlien')?.value || 'fariz',
                client_wa: document.getElementById('inputNoWaKlien')?.value || '085878067645',
                client_address: document.getElementById('inputAlamatKlien')?.value || 'kombas',
                event_date: document.getElementById('inputTglAcara')?.value || '10/10/2026',
                booking_id: connectedBookingId,
                package_name: document.getElementById('inputPaketUtama')?.value || 'Penawaran QT-20261010-236',
                package_price: parseCurrency(document.getElementById('inputHargaUtama')?.value),
                package_qty: parseInt(document.getElementById('inputJumlahPaket')?.value) || 1,
                discount: parseCurrency(document.getElementById('inputDiskon')?.value),
                extra_packages: extraPackages,
                addons: addons,
                termin_label: document.getElementById('selectTermin')?.value || 'Uang Muka / DP 1',
                dp1_amount: parseCurrency(document.getElementById('inputDP1')?.value),
                dp2_amount: parseCurrency(document.getElementById('inputDP2')?.value),
                due_date: document.getElementById('inputTglJatuhTempo')?.value || '10/16/2026',
                bank_name: document.getElementById('inputNamaBank')?.value || 'BCA',
                bank_account_number: document.getElementById('inputNoRekening')?.value || '0431063867',
                bank_account_name: document.getElementById('inputAtasNama')?.value || 'Umi Salamah',
                terms_and_conditions: terms,
                theme: document.querySelector('.design-card.active')?.getAttribute('data-theme') || 'theme-minimalist'
            };

            fetch('/api/invoices', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                const noInv = (data.invoice && data.invoice.invoice_number) ? data.invoice.invoice_number : payload.invoice_number;
                showToastNotification(`Invoice ${noInv} tersimpan`);
                loadSavedInvoicesCount();
                switchView('tersimpan');
            })
            .catch(err => {
                showToastNotification(`Invoice ${payload.invoice_number} tersimpan`);
                loadSavedInvoicesCount();
                switchView('tersimpan');
            })
            .finally(() => {
                btn.innerHTML = `<i class="fa-solid fa-check"></i> Tersimpan`;
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }, 1500);
            });
        }

        // --- Unduh PNG & PDF Functions ---
        function unduhPNG() {
            const el = document.getElementById('invoice-preview');
            const noInv = document.getElementById('previewNoInvoice')?.innerText?.trim() || 'invoice';
            showToastNotification("Menyiapkan unduhan PNG...");

            const prevShadow = el.style.boxShadow;
            const prevMargin = el.style.margin;
            el.style.boxShadow = 'none';
            el.style.margin = '0';

            html2canvas(el, {
                scale: 2,
                useCORS: true,
                logging: false,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                el.style.boxShadow = prevShadow;
                el.style.margin = prevMargin;

                const link = document.createElement('a');
                link.download = `${noInv}.png`;
                link.href = canvas.toDataURL('image/png');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                showToastNotification(`Invoice ${noInv} berhasil diunduh sebagai PNG`);
            }).catch(err => {
                el.style.boxShadow = prevShadow;
                el.style.margin = prevMargin;
                console.error(err);
                showToastNotification("Gagal mengunduh invoice PNG");
            });
        }

        function unduhPDF() {
            const el = document.getElementById('invoice-preview');
            const noInv = document.getElementById('previewNoInvoice')?.innerText?.trim() || 'invoice';
            showToastNotification("Menyiapkan unduhan PDF 1 halaman...");

            const prevShadow = el.style.boxShadow;
            const prevMargin = el.style.margin;
            el.style.boxShadow = 'none';
            el.style.margin = '0';

            html2canvas(el, {
                scale: 2,
                useCORS: true,
                logging: false,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                el.style.boxShadow = prevShadow;
                el.style.margin = prevMargin;

                const jsPDFClass = (window.jspdf && window.jspdf.jsPDF) ? window.jspdf.jsPDF : (window.jsPDF || null);
                if (!jsPDFClass) {
                    throw new Error("Pustaka PDF tidak tersedia");
                }

                const pdf = new jsPDFClass({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4',
                    compress: true
                });

                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();

                const margin = 8;
                const maxW = pageWidth - (margin * 2);
                const maxH = pageHeight - (margin * 2);

                const cWidth = canvas.width;
                const cHeight = canvas.height;
                const ratio = cWidth / cHeight;

                let printW = maxW;
                let printH = printW / ratio;

                // Pastikan seluruh invoice pas dalam 1 halaman A4 tanpa terpotong
                if (printH > maxH) {
                    printH = maxH;
                    printW = printH * ratio;
                }

                // Posisi rata tengah horizontal
                const x = margin + (maxW - printW) / 2;
                const y = margin;

                const imgData = canvas.toDataURL('image/jpeg', 0.98);
                pdf.addImage(imgData, 'JPEG', x, y, printW, printH, undefined, 'FAST');
                pdf.save(`${noInv}.pdf`);

                showToastNotification(`Invoice ${noInv} berhasil diunduh sebagai PDF`);
            }).catch(err => {
                el.style.boxShadow = prevShadow;
                el.style.margin = prevMargin;
                console.error(err);
                showToastNotification("Gagal mengunduh invoice PDF");
            });
        }


        // --- Tab View & Saved Invoices Management ---
        let savedInvoicesData = [];
        let activeFilterStatus = 'all';

        function switchView(view) {
            const tabBtnBaru = document.getElementById('tabBtnInvoiceBaru');
            const tabBtnTersimpan = document.getElementById('tabBtnInvoiceTersimpan');
            const editorView = document.getElementById('invoiceEditorView');
            const savedView = document.getElementById('savedInvoicesView');

            if (view === 'baru') {
                tabBtnBaru.className = 'btn btn-primary';
                tabBtnBaru.style.background = 'var(--primary)';
                tabBtnBaru.style.color = 'white';
                tabBtnTersimpan.className = 'btn btn-secondary';
                tabBtnTersimpan.style.background = 'white';
                tabBtnTersimpan.style.color = 'var(--text-dark)';
                editorView.style.display = 'grid';
                savedView.style.display = 'none';
            } else {
                tabBtnBaru.className = 'btn btn-secondary';
                tabBtnBaru.style.background = 'white';
                tabBtnBaru.style.color = 'var(--text-dark)';
                tabBtnTersimpan.className = 'btn btn-primary';
                tabBtnTersimpan.style.background = '#6c5ce7';
                tabBtnTersimpan.style.color = 'white';
                editorView.style.display = 'none';
                savedView.style.display = 'block';
                loadSavedInvoicesTab();
            }
        }

        function loadSavedInvoicesCount() {
            fetch('/api/invoices')
                .then(res => res.json())
                .then(list => {
                    const count = Array.isArray(list) ? list.length : 0;
                    const textEl = document.getElementById('savedInvoiceCountText');
                    if (textEl) textEl.innerText = `Invoice Tersimpan (${count})`;
                })
                .catch(e => {});
        }

        function formatTglIndo(val) {
            if (!val) return '';
            try {
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const d = new Date(val);
                if (isNaN(d.getTime())) return val;
                return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
            } catch(e) {
                return val;
            }
        }

        function loadSavedInvoicesTab() {
            const container = document.getElementById('savedInvoicesListContainer');
            if (container) container.innerHTML = `<div style="text-align: center; padding: 40px; color: #a1a5b7; font-size: 13px;">Memuat riwayat invoice...</div>`;

            fetch('/api/invoices')
                .then(res => res.json())
                .then(list => {
                    savedInvoicesData = Array.isArray(list) ? list : [];
                    renderSavedInvoices(savedInvoicesData);
                    loadSavedInvoicesCount();
                })
                .catch(err => {
                    if (container) container.innerHTML = `<div style="text-align: center; padding: 40px; color: #f1416c; font-size: 13px;">Gagal memuat invoice.</div>`;
                });
        }

        function setFilterStatus(status, btn) {
            activeFilterStatus = status;
            document.querySelectorAll('.status-filter-pill').forEach(b => {
                b.style.background = 'white';
                b.style.color = '#555';
                b.style.fontWeight = '500';
                b.style.border = '1px solid var(--border-color)';
            });
            btn.style.background = '#6c5ce7';
            btn.style.color = 'white';
            btn.style.fontWeight = '600';
            btn.style.border = 'none';

            filterSavedInvoices();
        }

        function filterSavedInvoices() {
            const query = (document.getElementById('searchSavedInvoice')?.value || '').toLowerCase().trim();
            let filtered = savedInvoicesData.filter(inv => {
                const matchQuery = !query || 
                    (inv.client_name && inv.client_name.toLowerCase().includes(query)) ||
                    (inv.invoice_number && inv.invoice_number.toLowerCase().includes(query));

                const invStatus = inv.payment_status || (inv.remaining_balance <= 0 ? 'Lunas' : (inv.total_dp > 0 ? 'Terbayar Sebagian' : 'Belum Dibayar'));
                const matchStatus = activeFilterStatus === 'all' || invStatus.toLowerCase() === activeFilterStatus.toLowerCase();

                return matchQuery && matchStatus;
            });

            renderSavedInvoices(filtered, false);
        }

        function renderSavedInvoices(list, updateAllCounter = true) {
            const container = document.getElementById('savedInvoicesListContainer');
            const countLabel = document.getElementById('savedCountLabel');
            const totalValLabel = document.getElementById('savedTotalValLabel');

            if (!container) return;

            let totalVal = 0;
            list.forEach(inv => {
                totalVal += parseFloat(inv.grand_total || 0);
            });

            if (countLabel) countLabel.innerText = `${list.length} invoice`;
            if (totalValLabel) totalValLabel.innerText = `Total nilai: Rp ${formatRupiah(totalVal)}`;

            if (list.length === 0) {
                container.innerHTML = `<div style="text-align: center; padding: 48px; color: #a1a5b7; font-size: 13px;">Belum ada invoice yang sesuai kriteria.</div>`;
                return;
            }

            let html = '';
            list.forEach(inv => {
                const isLunas = inv.payment_status === 'Lunas' || (parseFloat(inv.remaining_balance || 0) <= 0 && parseFloat(inv.grand_total || 0) > 0);
                const isPartial = !isLunas && (inv.payment_status === 'Terbayar Sebagian' || parseFloat(inv.total_dp || 0) > 0);

                let badgeHtml = '';
                if (isLunas) {
                    badgeHtml = `<span style="background: #e8fdf0; color: #10b981; padding: 3px 12px; border-radius: 14px; font-size: 11px; font-weight: 600;">Lunas</span>`;
                } else if (isPartial) {
                    badgeHtml = `<span style="background: #eff6ff; color: #3b82f6; padding: 3px 12px; border-radius: 14px; font-size: 11px; font-weight: 600;">Terbayar Sebagian</span>`;
                } else {
                    badgeHtml = `<span style="background: #fff8e6; color: #f59e0b; padding: 3px 12px; border-radius: 14px; font-size: 11px; font-weight: 600;">Belum Dibayar</span>`;
                }

                const clientDisplay = inv.client_name ? `${inv.client_name} - ` : '-- ';
                const invNo = inv.invoice_number || 'INV';
                const totalDisplay = formatRupiah(inv.grand_total || 0);
                const dpDisplay = formatRupiah(inv.total_dp || 0);
                const tglDisplay = formatTglIndo(inv.invoice_date);
                const acaraDisplay = inv.event_date ? (formatTglIndo(inv.event_date) || inv.event_date) : '';

                html += `
                <div class="saved-inv-row" style="display: flex; justify-content: space-between; align-items: center; padding: 18px 22px; border-bottom: 1px solid #f1f1f4; background: white; transition: 0.15s;" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor='#ffffff'">
                    <div>
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                            <span style="font-weight: 600; font-size: 14px; color: #181c32;">${clientDisplay}${invNo}</span>
                            ${badgeHtml}
                        </div>
                        <div style="font-size: 12px; color: #888; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <span>Rp ${totalDisplay}</span>
                            ${(parseFloat(inv.total_dp || 0) > 0 || isLunas) ? `<span>&bull; Terbayar: Rp ${dpDisplay}</span>` : ''}
                            ${tglDisplay ? `<span>&bull; ${tglDisplay}</span>` : ''}
                            ${acaraDisplay ? `<span>&bull; Acara: ${acaraDisplay}</span>` : ''}
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        ${!isLunas ? `<button type="button" class="btn btn-tandai-lunas" onclick="tandaiLunas('${inv.id}')" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; font-weight: 500; font-size: 12px; padding: 6px 14px; border-radius: 8px; cursor: pointer;">Tandai Lunas</button>` : ''}
                        <button type="button" class="btn-icon" onclick="openPaymentHistoryModal('${inv.id}')" title="Riwayat Pembayaran" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); border-radius: 8px; background: white; color: #555; cursor: pointer;"><i class="fa-solid fa-wallet"></i></button>
                        <button type="button" class="btn-icon" onclick="salinLinkPublik('${inv.id}')" title="Salin Link Publik" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); border-radius: 8px; background: white; color: #555; cursor: pointer;"><i class="fa-solid fa-link"></i></button>
                        <button type="button" class="btn-icon" onclick="duplikatInvoice('${inv.id}')" title="Duplikat Invoice" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); border-radius: 8px; background: white; color: #555; cursor: pointer;"><i class="fa-regular fa-copy"></i></button>
                        <button type="button" class="btn btn-secondary" onclick="bukaInvoice('${inv.id}')" style="font-size: 12px; padding: 6px 14px; border-radius: 8px; border: 1px solid var(--border-color); background: white; cursor: pointer;">Buka</button>
                        <button type="button" class="btn-icon" onclick="hapusInvoice('${inv.id}')" title="Hapus Invoice" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); border-radius: 8px; background: white; color: #f1416c; cursor: pointer;"><i class="fa-regular fa-trash-can"></i></button>
                    </div>
                </div>`;
            });

            container.innerHTML = html;
        }

        function tandaiLunas(id) {
            fetch(`/api/invoices/${id}/mark-paid`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                showToastNotification("Invoice ditandai lunas");
                loadSavedInvoicesTab();
                loadSavedInvoicesCount();
            })
            .catch(() => {
                showToastNotification("Gagal menandai lunas");
            });
        }

        function salinLinkPublik(id) {
            const url = `${window.location.origin}/inv/${id}`;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(() => {
                    showToastNotification("Link publik invoice berhasil disalin ke clipboard");
                }).catch(() => {
                    showToastNotification("Link publik: " + url);
                });
            } else {
                const ta = document.createElement('textarea');
                ta.value = url;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                showToastNotification("Link publik invoice berhasil disalin ke clipboard");
            }
        }

        function bukaInvoice(id) {
            muatInvoice(id);
            switchView('baru');
        }

        function duplikatInvoice(id) {
            fetch(`/api/invoices/${id}`)
                .then(res => res.json())
                .then(inv => {
                    muatInvoice(id);
                    const nextSeq = Math.floor(Math.random() * 900) + 100;
                    const newNo = `INV-202610-${nextSeq}`;
                    document.getElementById('inputNoInvoice').value = newNo;
                    document.getElementById('previewNoInvoice').innerText = newNo;
                    switchView('baru');
                    showToastNotification("Invoice diduplikat dengan nomor baru: " + newNo);
                })
                .catch(() => {
                    showToastNotification("Gagal menduplikat invoice");
                });
        }

        // --- Riwayat Pembayaran Modal ---
        let currentModalInvoice = null;

        function openPaymentHistoryModal(id) {
            fetch(`/api/invoices/${id}`)
                .then(res => res.json())
                .then(inv => {
                    currentModalInvoice = inv;
                    renderPaymentModalDetails(inv);
                    document.getElementById('paymentModal').style.display = 'flex';
                })
                .catch(() => {
                    showToastNotification("Gagal memuat data pembayaran");
                });
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
            currentModalInvoice = null;
        }

        function renderPaymentModalDetails(inv) {
            document.getElementById('paymentInvoiceId').value = inv.id;
            document.getElementById('paymentModalInvNo').innerText = inv.invoice_number;

            const grandTotal = parseFloat(inv.grand_total || 0);
            const totalPaid = parseFloat(inv.total_dp || 0);
            const sisa = Math.max(0, grandTotal - totalPaid);

            document.getElementById('paymentModalSubtitle').innerHTML = 
                `Total Rp ${formatRupiah(grandTotal)} &middot; Terbayar Rp ${formatRupiah(totalPaid)} &middot; Sisa Rp ${formatRupiah(sisa)}`;

            const inputNominal = document.getElementById('inputPaymentNominal');
            if (sisa > 0) {
                inputNominal.value = formatRupiah(sisa).replace('Rp ', '');
            } else {
                inputNominal.value = '';
                inputNominal.placeholder = 'Nominal pembayaran';
            }

            const now = new Date();
            const pad = n => n < 10 ? '0' + n : n;
            document.getElementById('inputPaymentDate').value = `${pad(now.getMonth() + 1)}/${pad(now.getDate())}/${now.getFullYear()}`;
            document.getElementById('inputPaymentNote').value = '';

            const historyList = document.getElementById('paymentHistoryList');
            const payments = Array.isArray(inv.payments) ? inv.payments : [];

            if (payments.length === 0) {
                historyList.innerHTML = `<div style="text-align: center; padding: 20px; color: #a1a5b7; font-size: 13px;">Belum ada pembayaran tercatat.</div>`;
            } else {
                let html = '';
                payments.forEach(p => {
                    html += `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; border: 1px solid #edf2f7; border-radius: 8px; margin-bottom: 8px; background: #fff;">
                        <div>
                            <div style="font-weight: 700; font-size: 14px; color: #181c32;">Rp ${formatRupiah(p.amount || 0)}</div>
                            <div style="font-size: 11px; color: #a1a5b7; margin-top: 2px;">${p.date || ''} ${p.note ? '&bull; ' + p.note : ''}</div>
                        </div>
                        <button type="button" style="background: none; border: none; color: #a1a5b7; cursor: pointer; padding: 4px; font-size: 14px;" onclick="hapusPembayaran('${inv.id}', '${p.id}')">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>`;
                });
                historyList.innerHTML = html;
            }
        }

        function submitRecordPayment(e) {
            if (e) e.preventDefault();

            const invId = document.getElementById('paymentInvoiceId').value;
            const amount = parseCurrency(document.getElementById('inputPaymentNominal').value);
            const date = document.getElementById('inputPaymentDate').value;
            const note = document.getElementById('inputPaymentNote').value;

            if (amount <= 0) {
                alert("Masukkan nominal pembayaran!");
                return;
            }

            fetch(`/api/invoices/${invId}/payments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ amount, date, note })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    showToastNotification(res.message);
                    currentModalInvoice = res.invoice;
                    renderPaymentModalDetails(res.invoice);
                    loadSavedInvoicesTab();
                    loadSavedInvoicesCount();
                } else {
                    showToastNotification(res.error || "Gagal mencatat pembayaran");
                }
            })
            .catch(() => {
                showToastNotification("Gagal mencatat pembayaran");
            });
        }

        function hapusPembayaran(invoiceId, paymentId) {
            if (!confirm("Hapus catatan pembayaran ini?")) return;

            fetch(`/api/invoices/${invoiceId}/payments/${paymentId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(res => {
                showToastNotification("Pembayaran dihapus");
                if (res.invoice) {
                    currentModalInvoice = res.invoice;
                    renderPaymentModalDetails(res.invoice);
                }
                loadSavedInvoicesTab();
                loadSavedInvoicesCount();
            })
            .catch(() => {
                showToastNotification("Gagal menghapus pembayaran");
            });
        }

        function muatInvoice(id) {
            fetch(`/api/invoices/${id}`)
                .then(res => res.json())
                .then(inv => {
                    switchView('baru');
                    
                    if (inv.invoice_number) {
                        document.getElementById('inputNoInvoice').value = inv.invoice_number;
                        document.getElementById('previewNoInvoice').innerText = inv.invoice_number;
                    }
                    if (inv.client_name) document.getElementById('inputNamaKlien').value = inv.client_name;
                    if (inv.client_wa) document.getElementById('inputNoWaKlien').value = inv.client_wa;
                    if (inv.client_address) document.getElementById('inputAlamatKlien').value = inv.client_address;
                    if (inv.event_date) document.getElementById('inputTglAcara').value = inv.event_date;
                    
                    if (inv.package_name) document.getElementById('inputPaketUtama').value = inv.package_name;
                    if (inv.package_price) document.getElementById('inputHargaUtama').value = formatRupiah(inv.package_price);
                    if (inv.package_qty) document.getElementById('inputJumlahPaket').value = inv.package_qty;
                    if (inv.discount) document.getElementById('inputDiskon').value = formatRupiah(inv.discount);

                    // Load extra packages
                    const pktContainer = document.getElementById('paketTambahanContainer');
                    if (pktContainer) {
                        pktContainer.innerHTML = '';
                        const extraPkgs = Array.isArray(inv.extra_packages) ? inv.extra_packages : [];
                        extraPkgs.forEach(p => {
                            addPaketTambahan(p.name, p.price, p.discount);
                        });
                    }

                    // Load addons
                    const addonContainer = document.getElementById('addonContainer');
                    if (addonContainer) {
                        addonContainer.innerHTML = '';
                        const addons = Array.isArray(inv.addons) ? inv.addons : [];
                        addons.forEach(a => {
                            addAddonItem(a.name, a.qty, a.price);
                        });
                    }

                    // Termin & Bank
                    if (inv.dp1_amount !== undefined) document.getElementById('inputDP1').value = formatRupiah(inv.dp1_amount);
                    if (inv.dp2_amount !== undefined) document.getElementById('inputDP2').value = formatRupiah(inv.dp2_amount);
                    if (inv.bank_name) document.getElementById('inputNamaBank').value = inv.bank_name;
                    if (inv.bank_account_number) document.getElementById('inputNoRekening').value = inv.bank_account_number;
                    if (inv.bank_account_name) document.getElementById('inputAtasNama').value = inv.bank_account_name;
                    if (inv.due_date) document.getElementById('inputTglJatuhTempo').value = inv.due_date;

                    // Terms
                    if (Array.isArray(inv.terms_and_conditions) && inv.terms_and_conditions.length > 0) {
                        renderTerms(inv.terms_and_conditions);
                    }

                    syncPreviewClientWa();
                    syncPreviewDate();
                    syncJatuhTempoPreview();
                    syncBankPreview();
                    calculateTotals();

                    showToastNotification(`Invoice ${inv.invoice_number || ''} dimuat`);
                })
                .catch(err => {
                    showToastNotification("Gagal memuat detail invoice");
                });
        }

        function hapusInvoice(id) {
            if (!confirm("Yakin ingin menghapus invoice ini?")) return;

            fetch(`/api/invoices/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(() => {
                showToastNotification("Invoice berhasil dihapus");
                loadSavedInvoicesTab();
                loadSavedInvoicesCount();
            })
            .catch(() => {
                showToastNotification("Gagal menghapus invoice");
            });
        }

        function resetFormInvoiceBaru() {
            switchView('baru');
            const nextSeq = Math.floor(Math.random() * 900) + 100;
            const newNo = `INV-202610-${nextSeq}`;
            
            document.getElementById('inputNoInvoice').value = newNo;
            document.getElementById('previewNoInvoice').innerText = newNo;
            putuskanBooking();

            document.getElementById('inputNamaKlien').value = '';
            document.getElementById('inputNoWaKlien').value = '';
            document.getElementById('inputAlamatKlien').value = '';
            document.getElementById('inputTglAcara').value = '';

            syncPreviewClientWa();
            syncPreviewDate();
            calculateTotals();

        }

        // --- Signature & Logo Management ---
        function previewTtdFile(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const thumb = document.getElementById('ttdImgThumbnail');
                    const icon = document.getElementById('ttdIconPlaceholder');
                    const btnUpload = document.getElementById('btnUploadTtd');
                    const btnHapus = document.getElementById('btnHapusTtd');
                    const prevImg = document.getElementById('previewTtdImg');
                    const prevPlaceholder = document.getElementById('previewTtdPlaceholder');

                    if (thumb) { thumb.src = e.target.result; thumb.style.display = 'block'; }
                    if (icon) icon.style.display = 'none';
                    if (btnUpload) btnUpload.style.display = 'none';
                    if (btnHapus) btnHapus.style.display = 'inline';

                    if (prevImg) { prevImg.src = e.target.result; prevImg.style.display = 'block'; }
                    if (prevPlaceholder) prevPlaceholder.style.display = 'none';

                    const badge = document.getElementById('badgeTtdText');
                    if (badge) badge.innerText = 'Tanda tangan siap';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function hapusTtd() {
            document.getElementById('ttdInput').value = '';
            const thumb = document.getElementById('ttdImgThumbnail');
            const icon = document.getElementById('ttdIconPlaceholder');
            const btnUpload = document.getElementById('btnUploadTtd');
            const btnHapus = document.getElementById('btnHapusTtd');
            const prevImg = document.getElementById('previewTtdImg');
            const prevPlaceholder = document.getElementById('previewTtdPlaceholder');

            if (thumb) { thumb.src = ''; thumb.style.display = 'none'; }
            if (icon) icon.style.display = 'block';
            if (btnUpload) btnUpload.style.display = 'flex';
            if (btnHapus) btnHapus.style.display = 'none';

            if (prevImg) { prevImg.src = ''; prevImg.style.display = 'none'; }
            if (prevPlaceholder) prevPlaceholder.style.display = 'inline';

            const badge = document.getElementById('badgeTtdText');
            if (badge) badge.innerText = 'Belum ada tanda tangan';
        }

        function simpanTtd() {
            const btn = document.getElementById('btnSimpanTtd');
            const icon = document.getElementById('iconTtd');
            const text = document.getElementById('textTtd');

            btn.style.background = 'rgba(80, 205, 137, 0.1)';
            btn.style.borderColor = 'var(--success)';
            btn.style.color = 'var(--success)';
            if (icon) icon.className = 'fa-solid fa-check';
            if (text) text.innerText = 'Tersimpan';

            setTimeout(() => {
                btn.style.background = 'transparent';
                btn.style.borderColor = 'var(--border-color)';
                btn.style.color = 'var(--text-dark)';
                if (icon) icon.className = 'fa-regular fa-floppy-disk';
                if (text) text.innerText = 'Simpan TTD';
            }, 2000);

            showToastNotification("Tanda tangan disimpan");
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
            syncJatuhTempoPreview();
            syncBankPreview();
            syncTerminPreview();
            syncTermsPreview();
            loadSavedInvoicesCount();

            // Fetch vendor settings (bank, terms) from backend
            fetch('/api/invoices/settings')
                .then(res => res.json())
                .then(settings => {
                    if (settings.bank_name) {
                        document.getElementById('inputNamaBank').value = settings.bank_name;
                    }
                    if (settings.bank_account_number) {
                        document.getElementById('inputNoRekening').value = settings.bank_account_number;
                    }
                    if (settings.bank_account_name) {
                        document.getElementById('inputAtasNama').value = settings.bank_account_name;
                    }
                    syncBankPreview();
                })
                .catch(e => {});
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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $isProduction ? 'Dashboard Produksi & Stok' : 'Stock Opname & Inventaris' }} - CloseMateAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('components.theme-styles')
    <style>
        :root {
            --sidebar-bg: #1e1e2d;
            --sidebar-text: #cbd5e1;
            --sidebar-active-bg: rgba(68, 114, 196, 0.2);
            --sidebar-active-text: {{ auth()->check() ? auth()->user()->getThemeColor() : '#4472c4' }};
            --primary: {{ auth()->check() ? auth()->user()->getThemeColor() : '#4472c4' }};
            --primary-hover: #365b9e;
            --bg-light: #f8f9fa;
            --card-bg: #ffffff;
            --border-color: #f1f1f4;
            --text-dark: #181c32;
            --text-muted: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #0284c7;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-light); display: flex; min-height: 100vh; color: var(--text-dark); }

        /* Sidebar */
        .sidebar { width: 260px; background-color: var(--sidebar-bg); display: flex; flex-direction: column; height: 100vh; position: fixed; left: 0; top: 0; transition: width 0.3s ease; overflow-x: hidden; z-index: 100; }
        .sidebar-header { padding: 24px; display: flex; align-items: center; gap: 12px; transition: 0.3s ease; }
        .sidebar-logo-icon { min-width: 40px; width: 40px; height: 40px; background-color: var(--sidebar-active-bg); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 18px; }
        .sidebar-title { white-space: nowrap; }
        .sidebar-title h2 { color: white; font-size: 16px; font-weight: 600; margin-bottom: 2px; }
        .sidebar-title p { color: var(--sidebar-text); font-size: 12px; }
        
        .nav-menu { flex: 1; overflow-y: auto; padding: 12px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: var(--sidebar-text); text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 500; margin-bottom: 4px; transition: 0.2s; white-space: nowrap; cursor: pointer; }
        .nav-item:hover { color: white; background-color: rgba(255,255,255,0.05); }
        .nav-item.active { background-color: var(--sidebar-active-bg); color: var(--sidebar-active-text) !important; font-weight: 600; }
        .nav-item.active i { color: var(--sidebar-active-text) !important; }
        .nav-item i { font-size: 16px; width: 20px; text-align: center; color: #727586; }

        /* Sidebar Collapsed State */
        body.sidebar-collapsed .sidebar { width: 80px; }
        body.sidebar-collapsed .main-content { margin-left: 80px; }
        body.sidebar-collapsed .sidebar-title { display: none; }
        body.sidebar-collapsed .sidebar-header { justify-content: center; padding: 24px 0; }
        body.sidebar-collapsed .nav-item span { display: none; }
        body.sidebar-collapsed .nav-item { justify-content: center; padding-left: 0; padding-right: 0; }

        /* Main Content */
        .main-content { margin-left: 260px; flex: 1; padding: 30px; display: flex; flex-direction: column; transition: margin-left 0.3s ease; }
        
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .page-title h1 { font-size: 24px; font-weight: 700; color: var(--text-dark); }
        .page-title p { color: var(--text-muted); font-size: 13px; margin-top: 4px; }
        
        .top-actions { display: flex; align-items: center; gap: 14px; }
        .status-pill { display: flex; align-items: center; gap: 8px; background-color: #e8fff3; color: var(--success); padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid #b7f8d0; }
        .status-dot { width: 8px; height: 8px; background-color: var(--success); border-radius: 50%; }
        
        .balance-pill { display: flex; align-items: center; gap: 8px; background: #e0f2fe; color: #0284c7; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; border: 1px solid #bae6fd; }
        
        .user-profile { display: flex; align-items: center; gap: 10px; cursor: pointer; position: relative; }
        .avatar { width: 38px; height: 38px; background-color: var(--primary); border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; }
        .user-info h4 { font-size: 13px; font-weight: 600; }
        .user-info p { font-size: 11px; color: var(--text-muted); }

        /* Alert Banners */
        .alert-banner { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; font-weight: 500; }
        .alert-warning { background: #fffbeb; border: 1px solid #fef3c7; color: #b45309; }
        .alert-info { background: #eff6ff; border: 1px solid #dbeafe; color: #1d4ed8; }
        .alert-danger { background: #fef2f2; border: 1px solid #fee2e2; color: #b91c1c; }

        /* KPI Cards Grid */
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .kpi-card { background: white; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .kpi-title { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between; }
        .kpi-value { font-size: 26px; font-weight: 700; color: #181c32; }
        .kpi-subtitle { font-size: 12px; color: #94a3b8; margin-top: 4px; }

        /* Tabs Nav */
        .tabs-header { display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #e2e8f0; margin-bottom: 24px; overflow-x: auto; }
        .tab-btn { padding: 12px 20px; background: transparent; border: none; font-size: 13px; font-weight: 600; color: #64748b; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; white-space: nowrap; }
        .tab-btn:hover { color: var(--primary); }
        .tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); }
        .tab-badge { background: #fee2e2; color: #ef4444; font-size: 11px; padding: 2px 7px; border-radius: 10px; font-weight: 700; }

        /* Card Container */
        .content-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); margin-bottom: 24px; }
        .content-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 14px; }
        .content-card-title h3 { font-size: 16px; font-weight: 700; color: #181c32; }
        .content-card-title p { font-size: 13px; color: #64748b; margin-top: 2px; }

        /* Buttons */
        .btn-primary { background: var(--primary); color: white; border: none; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: 0.15s; }
        .btn-primary:hover { filter: brightness(0.92); }
        .btn-secondary { background: white; color: #181c32; border: 1px solid #e2e8f0; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: 0.15s; }
        .btn-secondary:hover { background: #f8fafc; }
        .btn-success { background: #10b981; color: white; border: none; padding: 8px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
        .btn-danger { background: #ef4444; color: white; border: none; padding: 8px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
        .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 8px; }

        /* Tables */
        .custom-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .custom-table th { background: #f8fafc; color: #475569; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; padding: 14px 16px; text-align: left; border-bottom: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0; }
        .custom-table th:first-child { border-top-left-radius: 10px; border-bottom-left-radius: 10px; }
        .custom-table th:last-child { border-top-right-radius: 10px; border-bottom-right-radius: 10px; text-align: right; }
        .custom-table td { padding: 14px 16px; font-size: 13px; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .custom-table td:last-child { text-align: right; }
        .custom-table tr:hover td { background-color: #fafbfc; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-available { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-warning { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-danger { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge-info { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

        /* Diff styling */
        .diff-tag { font-weight: 700; padding: 3px 8px; border-radius: 6px; font-size: 12px; display: inline-block; }
        .diff-match { background: #dcfce7; color: #166534; }
        .diff-negative { background: #fee2e2; color: #991b1b; }
        .diff-positive { background: #e0f2fe; color: #075985; }

        /* Forms */
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 6px; }
        .form-control { width: 100%; height: 42px; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0 14px; font-size: 13px; color: #181c32; outline: none; background: #fff; transition: 0.15s; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-focus-shadow); }
        textarea.form-control { height: auto; padding: 12px 14px; min-height: 80px; resize: vertical; }

        /* Modals */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; padding: 16px; }
        .modal-box { background: white; border-radius: 16px; padding: 24px; width: 100%; max-width: 560px; box-shadow: 0 20px 40px rgba(0,0,0,0.18); position: relative; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .modal-header h3 { font-size: 17px; font-weight: 700; color: #181c32; }
        .modal-close { cursor: pointer; font-size: 18px; color: #94a3b8; transition: 0.15s; }
        .modal-close:hover { color: #181c32; }

        /* Toast */
        .toast-notif { display: none; position: fixed; top: 28px; right: 28px; background: white; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.12); padding: 14px 20px; font-size: 13px; font-weight: 600; color: #181c32; z-index: 999999; align-items: center; gap: 12px; opacity: 0; transform: translateY(-10px); transition: all 0.3s ease; }

        /* Quick Chips */
        .chip-btn { padding: 6px 14px; border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 20px; font-size: 12px; font-weight: 600; color: #475569; cursor: pointer; transition: 0.15s; }
        .chip-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--sidebar-active-bg); }

        /* Interactive Simulation Box */
        .sim-box { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 16px; margin: 16px 0; }
        .sim-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; font-size: 13px; }
        .sim-row:last-child { margin-bottom: 0; padding-top: 8px; border-top: 1px solid #e2e8f0; }

    </style>
    <link rel="stylesheet" href="/css/responsive.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo-icon">
                <i class="fa-solid fa-camera-retro"></i>
            </div>
            <div class="sidebar-title">
                <h2>{{ $owner->business_name ?? 'CloseMateAI' }}</h2>
                <p>{{ $isProduction ? 'Divisi Produksi' : 'AI Bot Console' }}</p>
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
                <h1>{{ $isProduction ? 'Dashboard & Operasional Produksi' : 'Stock Opname & Inventaris' }}</h1>
                <p>
                    @if($isProduction)
                        Pemeriksaan fisik stok perlengkapan foto, ajukan pembelian bahan/item, dan pantau saldo operasional.
                    @else
                        Pantau ketersediaan perlengkapan fotografi, verifikasi pemeriksaan fisik (opname), pembelian produksi, dan saldo operasional.
                    @endif
                </p>
            </div>
            
            <div class="top-actions">
                <div class="balance-pill" title="Saldo Operasional Tersedia">
                    <i class="fa-solid fa-wallet"></i>
                    <span>Saldo: Rp {{ number_format($currentBalance, 0, ',', '.') }}</span>
                </div>

                @if(!$isProduction)
                <button type="button" class="btn-secondary btn-sm" onclick="exportStockCsv()">
                    <i class="fa-solid fa-file-excel" style="color: #10b981;"></i> Export Laporan CSV
                </button>
                @endif
                
                <div class="profile-wrapper">
                    <div class="user-profile">
                        <div class="avatar" style="{{ $isProduction ? 'background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);' : '' }}">
                            {{ strtoupper(substr($user->name ?: 'U', 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <h4>{{ $user->name }}</h4>
                            <p>{{ $isProduction ? 'Divisi Produksi' : 'Owner' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Alerts -->
        @if(!$isProduction)
            @if($pendingOpnamesCount > 0)
            <div class="alert-banner alert-warning">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-clipboard-question" style="font-size: 16px;"></i>
                    <span>Terdapat <strong>{{ $pendingOpnamesCount }} pengajuan Stock Opname</strong> dari Divisi Produksi yang menunggu verifikasi Anda.</span>
                </div>
                <button class="btn-primary btn-sm" onclick="switchStockTab('opname')">Periksa Sekarang</button>
            </div>
            @endif

            @if($pendingPurchasesCount > 0)
            <div class="alert-banner alert-info">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-receipt" style="font-size: 16px;"></i>
                    <span>Terdapat <strong>{{ $pendingPurchasesCount }} pengajuan pembelian barang</strong> dari Divisi Produksi yang memerlukan persetujuan Owner.</span>
                </div>
                <button class="btn-primary btn-sm" onclick="switchStockTab('purchases')">Verifikasi Pembelian</button>
            </div>
            @endif

            @if($problematicStockCount > 0)
            <div class="alert-banner alert-danger">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 16px;"></i>
                    <span>Perhatian: Ada <strong>{{ $problematicStockCount }} barang</strong> dengan stok menipis atau habis. Segera restock untuk kelancaran produksi foto!</span>
                </div>
                <button class="btn-secondary btn-sm" onclick="switchStockTab('stock')">Lihat Stok</button>
            </div>
            @endif
        @else
            @if($problematicStockCount > 0)
            <div class="alert-banner alert-warning">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 16px;"></i>
                    <span>Peringatan Produksi: <strong>{{ $problematicStockCount }} perlengkapan</strong> memiliki stok menipis/habis. Lakukan pemeriksaan fisik atau ajukan pembelian baru.</span>
                </div>
                <button class="btn-primary btn-sm" onclick="switchStockTab('purchases')">Ajukan Pembelian</button>
            </div>
            @endif
        @endif

        <!-- Summary KPI Cards -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-title">
                    <span>Total Jenis Barang</span>
                    <i class="fa-solid fa-boxes-stacked" style="color: var(--primary);"></i>
                </div>
                <div class="kpi-value">{{ $totalItems }}</div>
                <div class="kpi-subtitle">Item perlengkapan terdaftar</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-title">
                    <span>Perlu Pemeriksaan</span>
                    <i class="fa-solid fa-clock-rotate-left" style="color: #f59e0b;"></i>
                </div>
                <div class="kpi-value">{{ $needCheckCount }}</div>
                <div class="kpi-subtitle">Belum di-opname bulan ini</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-title">
                    <span>Menunggu Verifikasi</span>
                    <i class="fa-solid fa-hourglass-half" style="color: #0284c7;"></i>
                </div>
                <div class="kpi-value">{{ $pendingOpnamesCount + $pendingPurchasesCount }}</div>
                <div class="kpi-subtitle">{{ $pendingOpnamesCount }} Opname &bull; {{ $pendingPurchasesCount }} Pembelian</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-title">
                    <span>Stok Bermasalah</span>
                    <i class="fa-solid fa-triangle-exclamation" style="color: #ef4444;"></i>
                </div>
                <div class="kpi-value" style="color: {{ $problematicStockCount > 0 ? '#ef4444' : '#10b981' }};">
                    {{ $problematicStockCount }}
                </div>
                <div class="kpi-subtitle">Stok menipis atau habis</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-title">
                    <span>Saldo Operasional</span>
                    <i class="fa-solid fa-wallet" style="color: #10b981;"></i>
                </div>
                <div class="kpi-value" style="font-size: 20px; color: #10b981;">
                    Rp {{ number_format($currentBalance, 0, ',', '.') }}
                </div>
                <div class="kpi-subtitle">Dana belanja perlengkapan</div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="tabs-header">
            @if(!$isProduction)
                <button type="button" class="tab-btn {{ $activeTab === 'stock' ? 'active' : '' }}" onclick="switchStockTab('stock')">
                    <i class="fa-solid fa-boxes-stacked"></i> 1. Manajemen Stok
                </button>
                <button type="button" class="tab-btn {{ $activeTab === 'opname' ? 'active' : '' }}" onclick="switchStockTab('opname')">
                    <i class="fa-solid fa-clipboard-check"></i> 2. Stock Opname
                    @if($pendingOpnamesCount > 0)
                        <span class="tab-badge">{{ $pendingOpnamesCount }}</span>
                    @endif
                </button>
                <button type="button" class="tab-btn {{ $activeTab === 'purchases' ? 'active' : '' }}" onclick="switchStockTab('purchases')">
                    <i class="fa-solid fa-receipt"></i> 3. Transaksi Pembelian
                    @if($pendingPurchasesCount > 0)
                        <span class="tab-badge">{{ $pendingPurchasesCount }}</span>
                    @endif
                </button>
                <button type="button" class="tab-btn {{ $activeTab === 'balance' ? 'active' : '' }}" onclick="switchStockTab('balance')">
                    <i class="fa-solid fa-wallet"></i> 4. Saldo Divisi Produksi
                </button>
                <button type="button" class="tab-btn {{ $activeTab === 'reports' ? 'active' : '' }}" onclick="switchStockTab('reports')">
                    <i class="fa-solid fa-chart-pie"></i> 5. Laporan & Rekapitulasi
                </button>
            @else
                <button type="button" class="tab-btn {{ $activeTab === 'dashboard' ? 'active' : '' }}" onclick="switchStockTab('dashboard')">
                    <i class="fa-solid fa-chart-line"></i> Dashboard Produksi
                </button>
                <button type="button" class="tab-btn {{ $activeTab === 'items' ? 'active' : '' }}" onclick="switchStockTab('items')">
                    <i class="fa-solid fa-boxes-stacked"></i> Ketersediaan Barang
                </button>
                <button type="button" class="tab-btn {{ $activeTab === 'opname' ? 'active' : '' }}" onclick="switchStockTab('opname')">
                    <i class="fa-solid fa-clipboard-check"></i> Stock Opname (Fisik)
                </button>
                <button type="button" class="tab-btn {{ $activeTab === 'purchases' ? 'active' : '' }}" onclick="switchStockTab('purchases')">
                    <i class="fa-solid fa-receipt"></i> Transaksi Pembelian
                </button>
                <button type="button" class="tab-btn {{ $activeTab === 'balance' ? 'active' : '' }}" onclick="switchStockTab('balance')">
                    <i class="fa-solid fa-wallet"></i> Saldo Produksi
                </button>
            @endif
        </div>

        {{-- TAB: DASHBOARD PRODUKSI (Divisi Produksi Only) --}}
        @if($isProduction)
        <div id="tabContent-dashboard" class="tab-content" style="{{ $activeTab === 'dashboard' ? 'display:block;' : 'display:none;' }}">
            <div class="content-card">
                <div class="content-card-header">
                    <div class="content-card-title">
                        <h3>Halo, {{ $user->name }}! (Divisi Produksi)</h3>
                        <p>Berikut adalah ringkasan ketersediaan perlengkapan foto dan saldo operasional Anda saat ini.</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn-primary" onclick="switchStockTab('opname')">
                            <i class="fa-solid fa-clipboard-check"></i> Periksa Stok Fisik (Opname)
                        </button>
                        <button class="btn-secondary" onclick="switchStockTab('purchases')">
                            <i class="fa-solid fa-cart-plus"></i> Ajukan Pembelian Baru
                        </button>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                    <div>
                        <h4 style="font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 14px;">
                            <i class="fa-solid fa-triangle-exclamation" style="color: #f59e0b; margin-right: 6px;"></i> Barang yang Perlu Diperhatikan (Menipis / Habis)
                        </h4>
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Barang</th>
                                    <th>Stok Sistem</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $lowItems = $items->filter(function($i) { return $i->stock_system <= $i->min_stock; });
                                @endphp
                                @forelse($lowItems as $li)
                                <tr>
                                    <td><span style="font-family: monospace; font-weight: 600;">{{ $li->code ?: '-' }}</span></td>
                                    <td><strong>{{ $li->name }}</strong></td>
                                    <td>{{ $li->stock_system }} {{ $li->unit }}</td>
                                    <td>
                                        @if($li->stock_system <= 0)
                                            <span class="badge badge-danger">Habis</span>
                                        @else
                                            <span class="badge badge-warning">Menipis (&le; {{ $li->min_stock }})</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn-primary btn-sm" onclick="prepareOpnameForItem({{ $li->id }}, '{{ addslashes($li->name) }}', {{ $li->stock_system }}, '{{ $li->unit }}')">
                                            Cek Fisik
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #64748b; padding: 24px;">
                                        <i class="fa-solid fa-check-circle" style="color: #10b981; font-size: 20px; margin-bottom: 6px; display: block;"></i>
                                        Seluruh perlengkapan saat ini dalam jumlah aman (di atas batas minimum).
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <h4 style="font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 14px;">
                            <i class="fa-solid fa-wallet" style="color: #0284c7; margin-right: 6px;"></i> Saldo Operasional Belanja
                        </h4>
                        <div style="background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%); border-radius: 14px; padding: 20px; color: white; margin-bottom: 16px;">
                            <div style="font-size: 12px; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px;">Saldo Tersedia</div>
                            <div style="font-size: 26px; font-weight: 700; margin: 8px 0;">Rp {{ number_format($currentBalance, 0, ',', '.') }}</div>
                            <div style="font-size: 12px; opacity: 0.85;">Dapat digunakan untuk pembelian perlengkapan & cetak foto</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                            <div style="font-size: 13px; font-weight: 600; color: #181c32; margin-bottom: 8px;">Aktivitas Pembelian Terbaru:</div>
                            @if(count($purchases) > 0)
                                @foreach($purchases->take(3) as $rp)
                                <div style="display: flex; justify-content: space-between; font-size: 12px; padding: 6px 0; border-bottom: 1px dashed #e2e8f0;">
                                    <span>{{ $rp->item_name }} ({{ $rp->qty }})</span>
                                    <strong>Rp {{ number_format($rp->total_price, 0, ',', '.') }}</strong>
                                </div>
                                @endforeach
                            @else
                                <div style="font-size: 12px; color: #94a3b8;">Belum ada pembelian diajukan.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- TAB: MANAJEMEN STOK / KETERSEDIAAN BARANG --}}
        <div id="tabContent-{{ $isProduction ? 'items' : 'stock' }}" class="tab-content" style="{{ ($isProduction ? $activeTab === 'items' : $activeTab === 'stock') ? 'display:block;' : 'display:none;' }}">
            <div class="content-card">
                <div class="content-card-header">
                    <div class="content-card-title">
                        <h3>Daftar Persediaan & Ketersediaan Barang</h3>
                        <p>Katalog perlengkapan fotografi: album foto, flashdisk, gantungan kunci, packaging box kayu, dsb.</p>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="text" id="searchItemInput" class="form-control" placeholder="Cari nama / kode..." style="width: 200px; height: 38px;" onkeyup="filterStockItemsTable()">
                        <button class="btn-primary" onclick="openAddItemModal()">
                            <i class="fa-solid fa-plus"></i> Tambah Barang Baru
                        </button>
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table class="custom-table" id="stockItemsTable">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Satuan</th>
                                <th>Stok Sistem</th>
                                <th>Batas Min</th>
                                <th>Status</th>
                                <th>Harga Satuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr id="item-row-{{ $item->id }}" data-name="{{ strtolower($item->name) }}" data-code="{{ strtolower($item->code ?: '') }}">
                                <td><span style="font-family: monospace; font-weight: 600; color: #475569;">{{ $item->code ?: '-' }}</span></td>
                                <td>
                                    <strong>{{ $item->name }}</strong>
                                    @if($item->notes)
                                        <div style="font-size: 11px; color: #94a3b8;">{{ $item->notes }}</div>
                                    @endif
                                </td>
                                <td><span style="background: #f1f5f9; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 500;">{{ $item->category }}</span></td>
                                <td>{{ $item->unit }}</td>
                                <td>
                                    <strong style="font-size: 14px;">{{ $item->stock_system }}</strong> {{ $item->unit }}
                                </td>
                                <td>{{ $item->min_stock }} {{ $item->unit }}</td>
                                <td>
                                    @if($item->stock_system <= 0)
                                        <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> Habis</span>
                                    @elseif($item->stock_system <= $item->min_stock)
                                        <span class="badge badge-warning"><i class="fa-solid fa-triangle-exclamation"></i> Menipis</span>
                                    @else
                                        <span class="badge badge-available"><i class="fa-solid fa-check"></i> Tersedia</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td>
                                    <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                        <button class="btn-secondary btn-sm" onclick="openEditItemModal({{ json_encode($item) }})" title="Edit Barang / Harga">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </button>
                                        @if(!$isProduction)
                                        <button class="btn-danger btn-sm" onclick="confirmDeleteItem({{ $item->id }}, '{{ addslashes($item->name) }}')" title="Hapus Barang">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" style="text-align: center; color: #64748b; padding: 24px;">Belum ada data barang. Silakan tambah barang baru.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB: STOCK OPNAME (Pemeriksaan Fisik) --}}
        <div id="tabContent-opname" class="tab-content" style="{{ $activeTab === 'opname' ? 'display:block;' : 'display:none;' }}">
            <div style="display: grid; grid-template-columns: {{ $isProduction ? '1fr 1.5fr' : '1fr' }}; gap: 24px;">
                {{-- Form Input Opname (Divisi Produksi / Owner) --}}
                <div class="content-card">
                    <div class="content-card-header">
                        <div class="content-card-title">
                            <h3><i class="fa-solid fa-clipboard-check" style="color: var(--primary); margin-right: 6px;"></i> Form Stock Opname Fisik</h3>
                            <p>Cocokkan stok yang tercatat di sistem dengan jumlah aktual fisik barang.</p>
                        </div>
                    </div>

                    <form id="opnameForm" onsubmit="submitOpnameForm(event)">
                        <div class="form-group">
                            <label>Pilih Barang yang Diperiksa</label>
                            <select id="opnameItemId" class="form-control" required onchange="handleOpnameItemChange()">
                                <option value="">-- Pilih Barang --</option>
                                @foreach($items as $i)
                                    <option value="{{ $i->id }}" data-system-stock="{{ $i->stock_system }}" data-unit="{{ $i->unit }}" data-name="{{ $i->name }}">
                                        {{ $i->name }} (Stok Sistem: {{ $i->stock_system }} {{ $i->unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Stok dalam Sistem</label>
                                <input type="text" id="opnameSystemStockDisplay" class="form-control" readonly value="0 unit" style="background: #f8fafc; font-weight: 600;">
                            </div>
                            <div class="form-group">
                                <label>Jumlah Stok Fisik (Aktual)</label>
                                <input type="number" id="opnamePhysicalStockInput" class="form-control" min="0" required placeholder="Contoh: 8" oninput="calculateOpnameDiff()">
                            </div>
                        </div>

                        <!-- Live Interactive Simulation Box -->
                        <div class="sim-box" id="opnameSimBox">
                            <div class="sim-row">
                                <span style="color: #64748b;">Nama Barang:</span>
                                <strong id="simItemName">-</strong>
                            </div>
                            <div class="sim-row">
                                <span style="color: #64748b;">Stok Sistem Saat Ini:</span>
                                <span id="simSystemStock">0 unit</span>
                            </div>
                            <div class="sim-row">
                                <span style="color: #64748b;">Jumlah Dihitung (Fisik):</span>
                                <span id="simPhysicalStock">0 unit</span>
                            </div>
                            <div class="sim-row">
                                <strong style="color: #181c32;">Simulasi Selisih Stok:</strong>
                                <span id="simDiffTag" class="diff-tag diff-match">0 unit (Sesuai)</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Catatan Pemeriksaan</label>
                            <textarea id="opnameNotes" class="form-control" placeholder="Contoh: 2 album mengalami kerusakan cover fisik saat penyimpanan."></textarea>
                        </div>

                        <div style="display: flex; justify-content: flex-end;">
                            <button type="submit" id="btnSubmitOpname" class="btn-primary" style="width: 100%;">
                                <i class="fa-solid fa-paper-plane"></i>
                                {{ $isProduction ? 'Kirim Hasil Pemeriksaan (Menunggu Verifikasi)' : 'Simpan & Sesuaikan Stok Sistem' }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Tabel Riwayat & Hasil Pemeriksaan --}}
                <div class="content-card">
                    <div class="content-card-header">
                        <div class="content-card-title">
                            <h3>Hasil Pemeriksaan & Riwayat Stock Opname</h3>
                            <p>Daftar seluruh pemeriksaan stok fisik beserta status verifikasi Owner.</p>
                        </div>
                    </div>

                    <div style="overflow-x: auto;">
                        <table class="custom-table" id="opnamesTable">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Petugas</th>
                                    <th>Sistem</th>
                                    <th>Fisik</th>
                                    <th>Selisih</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($opnames as $op)
                                @php
                                    $diff = $op->difference;
                                    $diffClass = ($diff === 0) ? 'diff-match' : (($diff < 0) ? 'diff-negative' : 'diff-positive');
                                    $diffLabel = ($diff > 0 ? "+{$diff}" : $diff) . ' ' . ($op->item->unit ?? 'unit');
                                @endphp
                                <tr id="opname-row-{{ $op->id }}">
                                    <td>
                                        <strong>{{ $op->item->name ?? '-' }}</strong>
                                        @if($op->notes)
                                            <div style="font-size: 11px; color: #64748b; max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                "{{ $op->notes }}"
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="font-size: 12px; font-weight: 500;">{{ $op->inspector->name ?? 'Tim' }}</span>
                                    </td>
                                    <td>{{ $op->system_stock }}</td>
                                    <td><strong>{{ $op->physical_stock }}</strong></td>
                                    <td>
                                        <span class="diff-tag {{ $diffClass }}">{{ $diffLabel }}</span>
                                    </td>
                                    <td>
                                        @if($op->status === 'approved')
                                            <span class="badge badge-available"><i class="fa-solid fa-check"></i> Disetujui</span>
                                        @elseif($op->status === 'rejected')
                                            <span class="badge badge-danger" title="{{ $op->rejection_reason }}"><i class="fa-solid fa-xmark"></i> Ditolak</span>
                                        @else
                                            <span class="badge badge-warning"><i class="fa-regular fa-clock"></i> Menunggu Verifikasi</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 11px; color: #64748b;">
                                        {{ $op->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td>
                                        <button class="btn-secondary btn-sm" onclick="openOpnameDetailModal({{ json_encode($op) }})">
                                            <i class="fa-solid fa-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" style="text-align: center; color: #64748b; padding: 24px;">Belum ada riwayat stock opname.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB: TRANSAKSI PEMBELIAN --}}
        <div id="tabContent-purchases" class="tab-content" style="{{ $activeTab === 'purchases' ? 'display:block;' : 'display:none;' }}">
            <div style="display: grid; grid-template-columns: {{ $isProduction ? '1fr 1.6fr' : '1fr' }}; gap: 24px;">
                {{-- Form Pengajuan Pembelian --}}
                <div class="content-card">
                    <div class="content-card-header">
                        <div class="content-card-title">
                            <h3><i class="fa-solid fa-receipt" style="color: var(--primary); margin-right: 6px;"></i> Tambah Transaksi Pembelian</h3>
                            <p>Catat pembelian perlengkapan fotografi, input nota dan harga barang.</p>
                        </div>
                    </div>

                    <form id="purchaseForm" onsubmit="submitPurchaseForm(event)">
                        <div class="form-group">
                            <label>Pilih Barang dari Katalog ATAU Ketik Baru</label>
                            <select id="purchaseItemId" class="form-control" onchange="handlePurchaseItemChange()">
                                <option value="">-- Ketik Nama Barang Baru --</option>
                                @foreach($items as $i)
                                    <option value="{{ $i->id }}" data-price="{{ $i->price }}" data-name="{{ $i->name }}" data-unit="{{ $i->unit }}">
                                        {{ $i->name }} (Harga standar: Rp {{ number_format($i->price, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="purchaseItemNameGroup">
                            <label>Nama Barang / Perlengkapan</label>
                            <input type="text" id="purchaseItemName" class="form-control" placeholder="Contoh: Flashdisk Sandisk 16GB / Frame Kayu 20R">
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Jumlah (Qty)</label>
                                <input type="number" id="purchaseQty" class="form-control" min="1" value="1" required oninput="calculatePurchaseTotal()">
                            </div>
                            <div class="form-group">
                                <label>Harga Satuan (Rp)</label>
                                <input type="number" id="purchaseUnitPrice" class="form-control" min="0" placeholder="0" required oninput="calculatePurchaseTotal()">
                            </div>
                        </div>

                        <div class="sim-box">
                            <div class="sim-row">
                                <span style="color: #64748b;">Total Estimasi Biaya:</span>
                                <strong style="font-size: 16px; color: #10b981;" id="purchaseTotalDisplay">Rp 0</strong>
                            </div>
                            <div class="sim-row">
                                <span style="color: #64748b;">Saldo Operasional Saat Ini:</span>
                                <span>Rp {{ number_format($currentBalance, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Unggah Foto Struk / Nota Pembelian</label>
                            <input type="file" id="purchaseReceipt" class="form-control" accept="image/*,application/pdf" style="padding-top: 8px;">
                            <small style="color: #64748b; font-size: 11px;">Maksimal 10MB (JPG, PNG, WebP, PDF).</small>
                        </div>

                        <div class="form-group">
                            <label>Catatan / Keterangan Toko & Keperluan</label>
                            <textarea id="purchaseNotes" class="form-control" placeholder="Contoh: Beli di Toko Kamera Sejahtera untuk pesanan klien Wedding tanggal 20."></textarea>
                        </div>

                        <button type="submit" id="btnSubmitPurchase" class="btn-primary" style="width: 100%;">
                            <i class="fa-solid fa-paper-plane"></i>
                            {{ $isProduction ? 'Kirim Pengajuan Pembelian (Menunggu Verifikasi)' : 'Simpan Pembelian & Potong Saldo' }}
                        </button>
                    </form>
                </div>

                {{-- Tabel Transaksi Pembelian --}}
                <div class="content-card">
                    <div class="content-card-header">
                        <div class="content-card-title">
                            <h3>Riwayat Transaksi Pembelian Barang</h3>
                            <p>Seluruh riwayat pembelian yang diajukan divisi produksi dan diverifikasi owner.</p>
                        </div>
                    </div>

                    <div style="overflow-x: auto;">
                        <table class="custom-table" id="purchasesTable">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama Barang</th>
                                    <th>Qty</th>
                                    <th>Harga Satuan</th>
                                    <th>Total Biaya</th>
                                    <th>Pengaju</th>
                                    <th>Bukti Nota</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchases as $pc)
                                <tr id="purchase-row-{{ $pc->id }}">
                                    <td style="font-size: 12px; color: #64748b;">{{ $pc->created_at->format('d M Y') }}</td>
                                    <td>
                                        <strong>{{ $pc->item_name }}</strong>
                                        @if($pc->notes)
                                            <div style="font-size: 11px; color: #64748b;">{{ $pc->notes }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $pc->qty }}</td>
                                    <td>Rp {{ number_format($pc->unit_price, 0, ',', '.') }}</td>
                                    <td><strong style="color: #10b981;">Rp {{ number_format($pc->total_price, 0, ',', '.') }}</strong></td>
                                    <td><span style="font-size: 12px;">{{ $pc->creator->name ?? 'Tim' }}</span></td>
                                    <td>
                                        @if($pc->receipt_file)
                                            <a href="{{ $pc->receipt_file }}" target="_blank" style="color: var(--primary); font-weight: 600; text-decoration: none; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-regular fa-image"></i> Lihat Nota
                                            </a>
                                        @else
                                            <span style="color: #94a3b8; font-size: 11px;">Tidak ada</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pc->status === 'approved')
                                            <span class="badge badge-available"><i class="fa-solid fa-check"></i> Disetujui</span>
                                        @elseif($pc->status === 'rejected')
                                            <span class="badge badge-danger" title="{{ $pc->rejection_reason }}"><i class="fa-solid fa-xmark"></i> Ditolak</span>
                                        @else
                                            <span class="badge badge-warning"><i class="fa-regular fa-clock"></i> Menunggu Verifikasi</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn-secondary btn-sm" onclick="openPurchaseDetailModal({{ json_encode($pc) }})">
                                            <i class="fa-solid fa-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" style="text-align: center; color: #64748b; padding: 24px;">Belum ada data transaksi pembelian.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB: SALDO DIVISI PRODUKSI --}}
        <div id="tabContent-balance" class="tab-content" style="{{ $activeTab === 'balance' ? 'display:block;' : 'display:none;' }}">
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
                {{-- Balance Overview Card --}}
                <div class="content-card">
                    <div class="content-card-header">
                        <div class="content-card-title">
                            <h3>Saldo Operasional Produksi</h3>
                            <p>Saldo khusus untuk pengadaan barang & perlengkapan.</p>
                        </div>
                    </div>

                    <div style="background: linear-gradient(135deg, var(--primary) 0%, rgba(68, 114, 196, 0.75) 100%); border-radius: 16px; padding: 24px; color: white; margin-bottom: 20px;">
                        <div style="font-size: 13px; opacity: 0.9;">Saldo Tersedia Saat Ini</div>
                        <div style="font-size: 32px; font-weight: 700; margin: 8px 0;" id="mainBalanceDisplay">
                            Rp {{ number_format($currentBalance, 0, ',', '.') }}
                        </div>
                        <div style="font-size: 12px; opacity: 0.85;">Dikelola oleh Owner & digunakan oleh Divisi Produksi</div>
                    </div>

                    @if(!$isProduction)
                    <button class="btn-primary" style="width: 100%; justify-content: center; padding: 12px;" onclick="openTopupModal()">
                        <i class="fa-solid fa-circle-plus"></i> Tambah Saldo (Top Up)
                    </button>
                    @else
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; font-size: 12px; color: #64748b;">
                        <i class="fa-solid fa-circle-info" style="color: #0284c7; margin-right: 4px;"></i>
                        Jika saldo operasional habis atau kurang untuk pembelian, hubungi Owner untuk menambahkan saldo.
                    </div>
                    @endif
                </div>

                {{-- Balance Mutation History --}}
                <div class="content-card">
                    <div class="content-card-header">
                        <div class="content-card-title">
                            <h3>Riwayat Mutasi & Penggunaan Saldo</h3>
                            <p>Rekap penambahan dana oleh Owner dan pemotongan saat pembelian disetujui.</p>
                        </div>
                    </div>

                    <div style="overflow-x: auto;">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Nominal</th>
                                    <th>Saldo Akhir</th>
                                    <th>Keterangan</th>
                                    <th>Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($balanceLogs as $log)
                                <tr>
                                    <td style="font-size: 12px; color: #64748b;">{{ $log->created_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        @if($log->type === 'topup')
                                            <span class="badge badge-available"><i class="fa-solid fa-arrow-down"></i> Top Up Saldo</span>
                                        @else
                                            <span class="badge badge-info"><i class="fa-solid fa-arrow-up"></i> Pembelian</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->amount >= 0)
                                            <strong style="color: #10b981;">+Rp {{ number_format($log->amount, 0, ',', '.') }}</strong>
                                        @else
                                            <strong style="color: #ef4444;">-Rp {{ number_format(abs($log->amount), 0, ',', '.') }}</strong>
                                        @endif
                                    </td>
                                    <td><strong>Rp {{ number_format($log->balance_after, 0, ',', '.') }}</strong></td>
                                    <td>{{ $log->notes ?: '-' }}</td>
                                    <td style="font-size: 12px;">{{ $log->creator->name ?? 'System' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; color: #64748b; padding: 24px;">Belum ada riwayat mutasi saldo.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB: LAPORAN & REKAPITULASI (Owner Only) --}}
        @if(!$isProduction)
        <div id="tabContent-reports" class="tab-content" style="{{ $activeTab === 'reports' ? 'display:block;' : 'display:none;' }}">
            <div class="content-card">
                <div class="content-card-header">
                    <div class="content-card-title">
                        <h3>Laporan Rekapitulasi Persediaan & Nilai Aset Stok</h3>
                        <p>Ringkasan total persediaan barang fotografi dan estimasi total aset perlengkapan.</p>
                    </div>
                    <button class="btn-primary" onclick="exportStockCsv()">
                        <i class="fa-solid fa-file-excel"></i> Export Laporan CSV
                    </button>
                </div>

                @php
                    $totalAssetVal = 0;
                    foreach($items as $it) {
                        $totalAssetVal += ($it->stock_system * $it->price);
                    }
                @endphp

                <div class="kpi-grid" style="margin-bottom: 24px;">
                    <div class="kpi-card" style="background: var(--sidebar-active-bg); border-color: var(--primary);">
                        <div class="kpi-title" style="color: var(--primary);">Total Nilai Aset Stok</div>
                        <div class="kpi-value" style="color: var(--primary); font-size: 24px;">Rp {{ number_format($totalAssetVal, 0, ',', '.') }}</div>
                        <div class="kpi-subtitle">Total nilai stok sistem &times; harga standar</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-title">Total Jenis Perlengkapan</div>
                        <div class="kpi-value">{{ count($items) }} Item</div>
                        <div class="kpi-subtitle">Album, FD, Souvenir, Box Kayu</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-title">Saldo Operasional Tersedia</div>
                        <div class="kpi-value" style="color: #10b981;">Rp {{ number_format($currentBalance, 0, ',', '.') }}</div>
                        <div class="kpi-subtitle">Alokasi belanja produksi</div>
                    </div>
                </div>

                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Stok Sistem</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal Nilai Aset</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $idx => $it)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td><span style="font-family: monospace;">{{ $it->code ?: '-' }}</span></td>
                            <td><strong>{{ $it->name }}</strong></td>
                            <td>{{ $it->category }}</td>
                            <td><strong>{{ $it->stock_system }}</strong> {{ $it->unit }}</td>
                            <td>Rp {{ number_format($it->price, 0, ',', '.') }}</td>
                            <td><strong style="color: var(--primary);">Rp {{ number_format($it->stock_system * $it->price, 0, ',', '.') }}</strong></td>
                            <td>
                                @if($it->stock_system <= 0)
                                    <span class="badge badge-danger">Habis</span>
                                @elseif($it->stock_system <= $it->min_stock)
                                    <span class="badge badge-warning">Menipis</span>
                                @else
                                    <span class="badge badge-available">Tersedia</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- MODAL: Tambah / Edit Barang -->
    <div class="modal-overlay" id="itemModal" onclick="if(event.target === this) closeItemModal()">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="itemModalTitle">Tambah Barang Baru</h3>
                <i class="fa-solid fa-xmark modal-close" onclick="closeItemModal()"></i>
            </div>
            <form id="itemForm" onsubmit="saveItemForm(event)">
                <input type="hidden" id="itemEditId" value="">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Kode Barang (Opsional)</label>
                        <input type="text" id="itemCode" class="form-control" placeholder="Contoh: ALB-01">
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select id="itemCategory" class="form-control">
                            <option value="Perlengkapan">Perlengkapan</option>
                            <option value="Album">Album</option>
                            <option value="Flashdisk">Flashdisk</option>
                            <option value="Packaging">Packaging</option>
                            <option value="Aksesoris">Aksesoris</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nama Barang *</label>
                    <input type="text" id="itemName" class="form-control" required placeholder="Contoh: Album Foto Hardcover 20 Halaman">
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Satuan Unit *</label>
                        <input type="text" id="itemUnit" class="form-control" value="unit" required placeholder="unit, pcs, box, buah">
                    </div>
                    <div class="form-group">
                        <label>Harga Satuan / Beli (Rp)</label>
                        <input type="number" id="itemPrice" class="form-control" min="0" placeholder="0">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Batas Minimum Stok (Alert)</label>
                        <input type="number" id="itemMinStock" class="form-control" min="0" value="5">
                    </div>
                    @if(!$isProduction)
                    <div class="form-group">
                        <label>Stok Sistem Awal</label>
                        <input type="number" id="itemStockSystem" class="form-control" min="0" value="0">
                    </div>
                    @endif
                </div>

                <div class="form-group">
                    <label>Catatan Tambahan</label>
                    <textarea id="itemNotes" class="form-control" placeholder="Spesifikasi, vendor langganan, dsb."></textarea>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn-secondary" onclick="closeItemModal()">Batal</button>
                    <button type="submit" id="btnSaveItem" class="btn-primary">Simpan Barang</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Detail & Verifikasi Stock Opname -->
    <div class="modal-overlay" id="opnameDetailModal" onclick="if(event.target === this) closeOpnameDetailModal()">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Detail Pemeriksaan Stock Opname</h3>
                <i class="fa-solid fa-xmark modal-close" onclick="closeOpnameDetailModal()"></i>
            </div>
            <div id="opnameDetailBody" style="font-size: 13px; line-height: 1.6;">
                <!-- Filled via JS -->
            </div>
        </div>
    </div>

    <!-- MODAL: Detail & Verifikasi Pembelian -->
    <div class="modal-overlay" id="purchaseDetailModal" onclick="if(event.target === this) closePurchaseDetailModal()">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Detail Transaksi Pembelian</h3>
                <i class="fa-solid fa-xmark modal-close" onclick="closePurchaseDetailModal()"></i>
            </div>
            <div id="purchaseDetailBody" style="font-size: 13px; line-height: 1.6;">
                <!-- Filled via JS -->
            </div>
        </div>
    </div>

    <!-- MODAL: Top Up Saldo Produksi (Owner Only) -->
    @if(!$isProduction)
    <div class="modal-overlay" id="topupModal" onclick="if(event.target === this) closeTopupModal()">
        <div class="modal-box" style="max-width: 440px;">
            <div class="modal-header">
                <h3>Top Up Saldo Divisi Produksi</h3>
                <i class="fa-solid fa-xmark modal-close" onclick="closeTopupModal()"></i>
            </div>
            <form id="topupForm" onsubmit="submitTopupForm(event)">
                <div class="form-group">
                    <label>Nominal Tambah Saldo (Rp) *</label>
                    <input type="number" id="topupAmount" class="form-control" required min="1000" placeholder="Contoh: 1000000">
                </div>

                <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px;">
                    <button type="button" class="chip-btn" onclick="setTopupAmount(500000)">+500 Rb</button>
                    <button type="button" class="chip-btn" onclick="setTopupAmount(1000000)">+1 Juta</button>
                    <button type="button" class="chip-btn" onclick="setTopupAmount(2500000)">+2.5 Juta</button>
                    <button type="button" class="chip-btn" onclick="setTopupAmount(5000000)">+5 Juta</button>
                </div>

                <div class="form-group">
                    <label>Catatan Pengisian</label>
                    <textarea id="topupNotes" class="form-control" placeholder="Contoh: Alokasi cetak album wedding & pengadaan flashdisk minggu pertama."></textarea>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn-secondary" onclick="closeTopupModal()">Batal</button>
                    <button type="submit" id="btnSubmitTopup" class="btn-primary">Top Up Saldo</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Toast Notification -->
    <div id="stockToast" class="toast-notif">
        <span style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; background: #10b981; color: white; border-radius: 50%; font-size: 11px; flex-shrink: 0;">
            <i class="fa-solid fa-check"></i>
        </span>
        <span id="stockToastText">Berhasil!</span>
    </div>

    @include('components.whatsapp-widget')

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const isProductionUser = {{ $isProduction ? 'true' : 'false' }};

        // Toast helper
        function showToast(text, isError = false) {
            const toast = document.getElementById('stockToast');
            const toastText = document.getElementById('stockToastText');
            if (!toast || !toastText) return;
            toastText.innerText = text;
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
            }, 10);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px)';
                setTimeout(() => { toast.style.display = 'none'; }, 300);
            }, 3000);
        }

        // Tab Switching
        function switchStockTab(tabName) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');

            const content = document.getElementById(`tabContent-${tabName}`);
            if (content) {
                content.style.display = 'block';
            }

            // Highlight button
            const activeBtn = Array.from(document.querySelectorAll('.tab-btn')).find(b => b.getAttribute('onclick')?.includes(`'${tabName}'`));
            if (activeBtn) activeBtn.classList.add('active');

            // Update URL without reload
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.replaceState({}, '', url);
        }

        // Search Filter on stock items
        function filterStockItemsTable() {
            const q = document.getElementById('searchItemInput')?.value.toLowerCase().trim() || '';
            const rows = document.querySelectorAll('#stockItemsTable tbody tr[data-name]');
            rows.forEach(r => {
                const name = r.getAttribute('data-name') || '';
                const code = r.getAttribute('data-code') || '';
                if (!q || name.includes(q) || code.includes(q)) {
                    r.style.display = '';
                } else {
                    r.style.display = 'none';
                }
            });
        }

        // --- ITEM MODAL & LOGIC ---
        function openAddItemModal() {
            document.getElementById('itemModalTitle').innerText = 'Tambah Barang Baru';
            document.getElementById('itemEditId').value = '';
            document.getElementById('itemCode').value = '';
            document.getElementById('itemName').value = '';
            document.getElementById('itemCategory').value = 'Perlengkapan';
            document.getElementById('itemUnit').value = 'unit';
            document.getElementById('itemPrice').value = '';
            document.getElementById('itemMinStock').value = '5';
            if (document.getElementById('itemStockSystem')) {
                document.getElementById('itemStockSystem').value = '0';
                document.getElementById('itemStockSystem').removeAttribute('readonly');
            }
            document.getElementById('itemNotes').value = '';
            document.getElementById('itemModal').style.display = 'flex';
        }

        function openEditItemModal(item) {
            document.getElementById('itemModalTitle').innerText = 'Edit Barang: ' + item.name;
            document.getElementById('itemEditId').value = item.id;
            document.getElementById('itemCode').value = item.code || '';
            document.getElementById('itemName').value = item.name || '';
            document.getElementById('itemCategory').value = item.category || 'Perlengkapan';
            document.getElementById('itemUnit').value = item.unit || 'unit';
            document.getElementById('itemPrice').value = item.price || 0;
            document.getElementById('itemMinStock').value = item.min_stock || 5;
            if (document.getElementById('itemStockSystem')) {
                document.getElementById('itemStockSystem').value = item.stock_system || 0;
            }
            document.getElementById('itemNotes').value = item.notes || '';
            document.getElementById('itemModal').style.display = 'flex';
        }

        function closeItemModal() {
            document.getElementById('itemModal').style.display = 'none';
        }

        function saveItemForm(e) {
            e.preventDefault();
            const editId = document.getElementById('itemEditId').value;
            const payload = {
                code: document.getElementById('itemCode').value.trim(),
                name: document.getElementById('itemName').value.trim(),
                category: document.getElementById('itemCategory').value,
                unit: document.getElementById('itemUnit').value.trim() || 'unit',
                price: parseFloat(document.getElementById('itemPrice').value) || 0,
                min_stock: parseInt(document.getElementById('itemMinStock').value) || 0,
                notes: document.getElementById('itemNotes').value.trim(),
            };

            if (document.getElementById('itemStockSystem')) {
                payload.stock_system = parseInt(document.getElementById('itemStockSystem').value) || 0;
            }

            const btn = document.getElementById('btnSaveItem');
            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            const endpoint = editId ? `/api/stock/items/${editId}` : '/api/stock/items';

            fetch(endpoint, {
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
                btn.disabled = false;
                btn.innerText = 'Simpan Barang';
                if (data.success) {
                    showToast(data.message || 'Barang berhasil disimpan!');
                    closeItemModal();
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    alert(data.error || 'Gagal menyimpan barang.');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerText = 'Simpan Barang';
                alert('Terjadi kesalahan koneksi.');
            });
        }

        function confirmDeleteItem(id, name) {
            if (!confirm(`Apakah Anda yakin ingin menghapus barang "${name}" dari inventaris?`)) return;

            fetch(`/api/stock/items/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    const row = document.getElementById(`item-row-${id}`);
                    if (row) row.remove();
                } else {
                    alert(data.error || 'Gagal menghapus barang.');
                }
            })
            .catch(() => alert('Terjadi kesalahan jaringan.'));
        }

        // --- STOCK OPNAME INTERACTION & SIMULATION ---
        function handleOpnameItemChange() {
            const sel = document.getElementById('opnameItemId');
            const opt = sel.options[sel.selectedIndex];
            if (!opt || !opt.value) {
                document.getElementById('opnameSystemStockDisplay').value = '0 unit';
                document.getElementById('simItemName').innerText = '-';
                document.getElementById('simSystemStock').innerText = '0 unit';
                calculateOpnameDiff();
                return;
            }

            const sysStock = opt.getAttribute('data-system-stock') || 0;
            const unit = opt.getAttribute('data-unit') || 'unit';
            const name = opt.getAttribute('data-name') || '-';

            document.getElementById('opnameSystemStockDisplay').value = `${sysStock} ${unit}`;
            document.getElementById('simItemName').innerText = name;
            document.getElementById('simSystemStock').innerText = `${sysStock} ${unit}`;

            calculateOpnameDiff();
        }

        function calculateOpnameDiff() {
            const sel = document.getElementById('opnameItemId');
            const opt = sel ? sel.options[sel.selectedIndex] : null;
            const sysStock = opt && opt.value ? parseInt(opt.getAttribute('data-system-stock')) || 0 : 0;
            const unit = opt && opt.value ? opt.getAttribute('data-unit') || 'unit' : 'unit';

            const physicalVal = document.getElementById('opnamePhysicalStockInput')?.value;
            const physicalStock = (physicalVal !== '' && !isNaN(physicalVal)) ? parseInt(physicalVal) : sysStock;

            document.getElementById('simPhysicalStock').innerText = `${physicalStock} ${unit}`;

            const diff = physicalStock - sysStock;
            const tag = document.getElementById('simDiffTag');

            if (diff === 0) {
                tag.className = 'diff-tag diff-match';
                tag.innerText = `0 ${unit} (Sesuai)`;
            } else if (diff < 0) {
                tag.className = 'diff-tag diff-negative';
                tag.innerText = `${diff} ${unit} (Selisih Kurang)`;
            } else {
                tag.className = 'diff-tag diff-positive';
                tag.innerText = `+${diff} ${unit} (Selisih Lebih)`;
            }
        }

        function prepareOpnameForItem(id, name, currentStock, unit) {
            switchStockTab('opname');
            const sel = document.getElementById('opnameItemId');
            if (sel) {
                sel.value = id;
                handleOpnameItemChange();
                document.getElementById('opnamePhysicalStockInput')?.focus();
            }
        }

        function submitOpnameForm(e) {
            e.preventDefault();
            const itemId = document.getElementById('opnameItemId').value;
            const physicalStock = document.getElementById('opnamePhysicalStockInput').value;
            const notes = document.getElementById('opnameNotes').value.trim();

            if (!itemId) {
                alert('Silakan pilih barang yang diperiksa.');
                return;
            }

            const btn = document.getElementById('btnSubmitOpname');
            btn.disabled = true;
            btn.innerText = 'Mengirim...';

            fetch('/api/stock/opnames', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    stock_item_id: itemId,
                    physical_stock: physicalStock,
                    notes: notes
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerText = isProductionUser ? 'Kirim Hasil Pemeriksaan (Menunggu Verifikasi)' : 'Simpan & Sesuaikan Stok Sistem';
                if (data.success) {
                    showToast(data.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert(data.error || 'Gagal menyimpan hasil Stock Opname.');
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerText = 'Kirim Hasil Pemeriksaan';
                alert('Terjadi kesalahan koneksi.');
            });
        }

        function openOpnameDetailModal(op) {
            const isOwner = !isProductionUser;
            const diff = op.difference;
            const diffClass = (diff === 0) ? 'diff-match' : ((diff < 0) ? 'diff-negative' : 'diff-positive');
            const diffLabel = (diff > 0 ? `+${diff}` : diff) + ` ${op.item?.unit || 'unit'}`;

            let actionHtml = '';
            if (isOwner && op.status === 'pending') {
                actionHtml = `
                <div style="display: flex; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0; justify-content: flex-end;">
                    <button class="btn-danger" onclick="verifyOpnameAction(${op.id}, 'reject')">
                        <i class="fa-solid fa-xmark"></i> Tolak Pemeriksaan
                    </button>
                    <button class="btn-success" onclick="verifyOpnameAction(${op.id}, 'approve')">
                        <i class="fa-solid fa-check"></i> Setujui & Update Stok Sistem
                    </button>
                </div>`;
            }

            const bodyHtml = `
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 16px;">
                    <div style="font-size: 15px; font-weight: 700; color: #181c32; margin-bottom: 4px;">${escapeHtml(op.item?.name || '-')}</div>
                    <div style="color: #64748b; font-size: 12px;">Kategori: ${escapeHtml(op.item?.category || 'Perlengkapan')} &bull; Satuan: ${escapeHtml(op.item?.unit || 'unit')}</div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                        <span style="color: #64748b; font-size: 11px; text-transform: uppercase;">Stok Sebelum Opname</span>
                        <div style="font-size: 18px; font-weight: 700; color: #181c32; margin-top: 4px;">${op.system_stock} ${op.item?.unit || 'unit'}</div>
                    </div>
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                        <span style="color: #64748b; font-size: 11px; text-transform: uppercase;">Stok Fisik Dihitung</span>
                        <div style="font-size: 18px; font-weight: 700; color: #181c32; margin-top: 4px;">${op.physical_stock} ${op.item?.unit || 'unit'}</div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: #f8fafc; border-radius: 10px; margin-bottom: 16px;">
                    <span style="font-weight: 600;">Selisih Stok:</span>
                    <span class="diff-tag ${diffClass}" style="font-size: 13px;">${diffLabel}</span>
                </div>

                <div style="margin-bottom: 12px;">
                    <span style="color: #64748b; font-size: 12px; font-weight: 600;">Catatan dari Petugas Produksi:</span>
                    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; margin-top: 4px; font-size: 13px;">
                        ${escapeHtml(op.notes || 'Tidak ada catatan.')}
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px; color: #64748b;">
                    <div>Petugas Pemeriksa: <strong>${escapeHtml(op.inspector?.name || 'Tim')}</strong></div>
                    <div>Waktu: <strong>${op.created_at ? new Date(op.created_at).toLocaleString('id-ID') : '-'}</strong></div>
                    <div>Status Verifikasi: <strong>${op.status.toUpperCase()}</strong></div>
                    <div>Diverifikasi Oleh: <strong>${escapeHtml(op.verifier?.name || '-')}</strong></div>
                </div>

                ${op.rejection_reason ? `
                <div style="margin-top: 14px; background: #fee2e2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px; font-size: 12px; color: #991b1b;">
                    <strong>Alasan Penolakan:</strong> ${escapeHtml(op.rejection_reason)}
                </div>` : ''}

                ${actionHtml}
            `;

            document.getElementById('opnameDetailBody').innerHTML = bodyHtml;
            document.getElementById('opnameDetailModal').style.display = 'flex';
        }

        function closeOpnameDetailModal() {
            document.getElementById('opnameDetailModal').style.display = 'none';
        }

        function verifyOpnameAction(id, action) {
            let reason = '';
            if (action === 'reject') {
                reason = prompt('Masukkan alasan penolakan Stock Opname ini:');
                if (reason === null) return;
                if (!reason.trim()) {
                    alert('Harap isi alasan penolakan.');
                    return;
                }
            }

            fetch(`/api/stock/opnames/${id}/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ action, reason })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    closeOpnameDetailModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert(data.error || 'Gagal memverifikasi Stock Opname.');
                }
            })
            .catch(() => alert('Terjadi kesalahan jaringan.'));
        }

        // --- TRANSAKSI PEMBELIAN LOGIC ---
        function handlePurchaseItemChange() {
            const sel = document.getElementById('purchaseItemId');
            const opt = sel ? sel.options[sel.selectedIndex] : null;
            const nameGroup = document.getElementById('purchaseItemNameGroup');

            if (opt && opt.value) {
                document.getElementById('purchaseUnitPrice').value = opt.getAttribute('data-price') || 0;
                if (nameGroup) nameGroup.style.display = 'none';
                document.getElementById('purchaseItemName').value = opt.getAttribute('data-name') || '';
            } else {
                if (nameGroup) nameGroup.style.display = 'block';
                document.getElementById('purchaseItemName').value = '';
                document.getElementById('purchaseUnitPrice').value = '';
            }
            calculatePurchaseTotal();
        }

        function calculatePurchaseTotal() {
            const qty = parseInt(document.getElementById('purchaseQty')?.value) || 0;
            const price = parseFloat(document.getElementById('purchaseUnitPrice')?.value) || 0;
            const total = qty * price;
            const display = document.getElementById('purchaseTotalDisplay');
            if (display) {
                display.innerText = 'Rp ' + total.toLocaleString('id-ID');
            }
        }

        function submitPurchaseForm(e) {
            e.preventDefault();
            const itemId = document.getElementById('purchaseItemId').value;
            let itemName = document.getElementById('purchaseItemName').value.trim();
            const qty = document.getElementById('purchaseQty').value;
            const unitPrice = document.getElementById('purchaseUnitPrice').value;
            const notes = document.getElementById('purchaseNotes').value.trim();
            const receiptInput = document.getElementById('purchaseReceipt');

            if (!itemId && !itemName) {
                alert('Harap pilih atau masukkan nama barang yang dibeli.');
                return;
            }

            const formData = new FormData();
            if (itemId) formData.append('stock_item_id', itemId);
            if (itemName) formData.append('item_name', itemName);
            formData.append('qty', qty);
            formData.append('unit_price', unitPrice);
            if (notes) formData.append('notes', notes);
            if (receiptInput && receiptInput.files[0]) {
                formData.append('receipt', receiptInput.files[0]);
            }

            const btn = document.getElementById('btnSubmitPurchase');
            btn.disabled = true;
            btn.innerText = 'Mengirim...';

            fetch('/api/stock/purchases', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerText = isProductionUser ? 'Kirim Pengajuan Pembelian (Menunggu Verifikasi)' : 'Simpan Pembelian & Potong Saldo';
                if (data.success) {
                    showToast(data.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert(data.error || 'Gagal menyimpan transaksi pembelian.');
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerText = 'Kirim Pengajuan Pembelian';
                alert('Terjadi kesalahan jaringan.');
            });
        }

        function openPurchaseDetailModal(pc) {
            const isOwner = !isProductionUser;
            let actionHtml = '';
            if (isOwner && pc.status === 'pending') {
                actionHtml = `
                <div style="display: flex; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0; justify-content: flex-end;">
                    <button class="btn-danger" onclick="verifyPurchaseAction(${pc.id}, 'reject')">
                        <i class="fa-solid fa-xmark"></i> Tolak Pengajuan
                    </button>
                    <button class="btn-success" onclick="verifyPurchaseAction(${pc.id}, 'approve')">
                        <i class="fa-solid fa-check"></i> Setujui Pembelian (Potong Saldo & Tambah Stok)
                    </button>
                </div>`;
            }

            const receiptHtml = pc.receipt_file ? `
                <div style="margin: 16px 0;">
                    <span style="font-size: 12px; font-weight: 600; color: #475569; display: block; margin-bottom: 6px;">Bukti Foto Struk / Nota:</span>
                    <a href="${pc.receipt_file}" target="_blank">
                        <img src="${pc.receipt_file}" style="max-width: 100%; max-height: 240px; border-radius: 8px; border: 1px solid #e2e8f0; object-fit: contain;">
                    </a>
                </div>` : '<div style="margin: 10px 0; color: #94a3b8; font-size: 12px;">Tidak melampirkan foto nota.</div>';

            const bodyHtml = `
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 16px;">
                    <div style="font-size: 16px; font-weight: 700; color: #181c32;">${escapeHtml(pc.item_name)}</div>
                    <div style="font-size: 13px; color: #64748b; margin-top: 4px;">
                        Jumlah: <strong>${pc.qty} unit</strong> &bull; Harga Satuan: <strong>Rp ${(pc.unit_price || 0).toLocaleString('id-ID')}</strong>
                    </div>
                    <div style="font-size: 20px; font-weight: 700; color: #10b981; margin-top: 8px;">
                        Total: Rp ${(pc.total_price || 0).toLocaleString('id-ID')}
                    </div>
                </div>

                ${receiptHtml}

                <div style="margin-bottom: 14px;">
                    <span style="font-size: 12px; font-weight: 600; color: #475569;">Keperluan / Catatan:</span>
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-size: 13px; margin-top: 4px;">
                        ${escapeHtml(pc.notes || 'Tidak ada catatan.')}
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px; color: #64748b;">
                    <div>Diajukan oleh: <strong>${escapeHtml(pc.creator?.name || 'Tim')}</strong></div>
                    <div>Waktu: <strong>${pc.created_at ? new Date(pc.created_at).toLocaleString('id-ID') : '-'}</strong></div>
                    <div>Status: <strong>${pc.status.toUpperCase()}</strong></div>
                    <div>Diverifikasi: <strong>${escapeHtml(pc.verifier?.name || '-')}</strong></div>
                </div>

                ${pc.rejection_reason ? `
                <div style="margin-top: 14px; background: #fee2e2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px; font-size: 12px; color: #991b1b;">
                    <strong>Alasan Penolakan:</strong> ${escapeHtml(pc.rejection_reason)}
                </div>` : ''}

                ${actionHtml}
            `;

            document.getElementById('purchaseDetailBody').innerHTML = bodyHtml;
            document.getElementById('purchaseDetailModal').style.display = 'flex';
        }

        function closePurchaseDetailModal() {
            document.getElementById('purchaseDetailModal').style.display = 'none';
        }

        function verifyPurchaseAction(id, action) {
            let reason = '';
            if (action === 'reject') {
                reason = prompt('Masukkan alasan penolakan pembelian ini:');
                if (reason === null) return;
                if (!reason.trim()) {
                    alert('Harap masukkan alasan penolakan.');
                    return;
                }
            }

            fetch(`/api/stock/purchases/${id}/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ action, reason })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    closePurchaseDetailModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert(data.error || 'Gagal memverifikasi transaksi.');
                }
            })
            .catch(() => alert('Terjadi kesalahan jaringan.'));
        }

        // --- SALDO & TOP UP LOGIC ---
        function openTopupModal() {
            document.getElementById('topupModal').style.display = 'flex';
        }

        function closeTopupModal() {
            document.getElementById('topupModal').style.display = 'none';
        }

        function setTopupAmount(amt) {
            document.getElementById('topupAmount').value = amt;
        }

        function submitTopupForm(e) {
            e.preventDefault();
            const amount = document.getElementById('topupAmount').value;
            const notes = document.getElementById('topupNotes').value.trim();

            const btn = document.getElementById('btnSubmitTopup');
            btn.disabled = true;
            btn.innerText = 'Mengisi...';

            fetch('/api/stock/balance/topup', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ amount, notes })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerText = 'Top Up Saldo';
                if (data.success) {
                    showToast(data.message);
                    closeTopupModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert(data.error || 'Gagal top up saldo.');
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerText = 'Top Up Saldo';
                alert('Terjadi kesalahan koneksi.');
            });
        }

        function exportStockCsv() {
            window.location.href = '/api/stock/export/csv';
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Collapse Sidebar Toggle
        const btnCollapse = document.getElementById('btn-collapse');
        if (btnCollapse) {
            btnCollapse.addEventListener('click', () => {
                document.body.classList.toggle('sidebar-collapsed');
            });
        }
    </script>
</body>
</html>


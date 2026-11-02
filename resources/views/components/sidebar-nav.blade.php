@php
    $currentUser = auth()->user();
    $isAdminCs = $currentUser && $currentUser->role === 'admin_cs';
    $isProduction = $currentUser && ($currentUser->role === 'production' || $currentUser->role === 'divisi_produksi');
@endphp
<div class="nav-menu">
    @if($isProduction)
        <a href="/stock?tab=dashboard" class="nav-item {{ request()->is('stock*') && (request()->get('tab') === 'dashboard' || !request()->has('tab')) ? 'active' : '' }}"><i class="fa-solid fa-chart-line"></i> <span>Dashboard Produksi</span></a>
        <a href="/stock?tab=items" class="nav-item {{ request()->is('stock*') && request()->get('tab') === 'items' ? 'active' : '' }}"><i class="fa-solid fa-boxes-stacked"></i> <span>Ketersediaan Barang</span></a>
        <a href="/stock?tab=opname" class="nav-item {{ request()->is('stock*') && request()->get('tab') === 'opname' ? 'active' : '' }}"><i class="fa-solid fa-clipboard-check"></i> <span>Stock Opname</span></a>
        <a href="/stock?tab=purchases" class="nav-item {{ request()->is('stock*') && request()->get('tab') === 'purchases' ? 'active' : '' }}"><i class="fa-solid fa-receipt"></i> <span>Transaksi Pembelian</span></a>
        <a href="/stock?tab=balance" class="nav-item {{ request()->is('stock*') && request()->get('tab') === 'balance' ? 'active' : '' }}"><i class="fa-solid fa-wallet"></i> <span>Saldo Produksi</span></a>
        <a href="/production" class="nav-item {{ request()->is('production') ? 'active' : '' }}"><i class="fa-solid fa-list-check"></i> <span>Progres Produksi</span></a>
        <a href="/activity" class="nav-item {{ request()->is('activity') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Aktivitas</span></a>
    @elseif($isAdminCs)
        <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}"><i class="fa-solid fa-border-all"></i> <span>Dashboard & WA Status</span></a>
        <a href="/chat" class="nav-item {{ request()->is('chat') ? 'active' : '' }}"><i class="fa-solid fa-message"></i> <span>Live Chat Inbox</span></a>
        <a href="/leads" class="nav-item {{ request()->is('leads') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> <span>Leads CRM</span></a>
        <a href="/followup" class="nav-item {{ request()->is('followup') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> <span>Follow-Up Otomatis</span></a>
        <a href="/booking" class="nav-item {{ request()->is('booking') ? 'active' : '' }}"><i class="fa-regular fa-calendar-check"></i> <span>Booking & Operasional</span></a>
        <a href="/invoice" class="nav-item {{ request()->is('invoice*') || request()->is('invoices*') ? 'active' : '' }}"><i class="fa-solid fa-file-invoice"></i> <span>Invoice Generator</span></a>
        <a href="/quotations" class="nav-item {{ request()->is('quotation*') ? 'active' : '' }}"><i class="fa-solid fa-file-contract"></i> <span>Quotation Generator</span></a>
        <a href="/production" class="nav-item {{ request()->is('production') ? 'active' : '' }}"><i class="fa-solid fa-list-check"></i> <span>Progres Produksi</span></a>
        <a href="/activity" class="nav-item {{ request()->is('activity') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Aktivitas</span></a>
    @else
        <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}"><i class="fa-solid fa-border-all"></i> <span>Dashboard & WA Status</span></a>
        <a href="/chat" class="nav-item {{ request()->is('chat') ? 'active' : '' }}"><i class="fa-solid fa-message"></i> <span>Live Chat Inbox</span></a>
        <a href="/knowledge" class="nav-item {{ request()->is('knowledge') ? 'active' : '' }}"><i class="fa-solid fa-book"></i> <span>Knowledge Base</span></a>
        <a href="/leads" class="nav-item {{ request()->is('leads') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> <span>Leads CRM</span></a>
        <a href="/followup" class="nav-item {{ request()->is('followup') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> <span>Follow-Up Otomatis</span></a>
        <a href="/booking" class="nav-item {{ request()->is('booking') ? 'active' : '' }}"><i class="fa-regular fa-calendar-check"></i> <span>Booking & Operasional</span></a>
        <a href="/invoice" class="nav-item {{ request()->is('invoice*') || request()->is('invoices*') ? 'active' : '' }}"><i class="fa-solid fa-file-invoice"></i> <span>Invoice Generator</span></a>
        <a href="/quotations" class="nav-item {{ request()->is('quotation*') ? 'active' : '' }}"><i class="fa-solid fa-file-contract"></i> <span>Quotation Generator</span></a>
        <a href="/finance" class="nav-item {{ request()->is('finance') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i> <span>Laporan Keuangan</span></a>
        <a href="/production" class="nav-item {{ request()->is('production') ? 'active' : '' }}"><i class="fa-solid fa-list-check"></i> <span>Progres Produksi</span></a>
        <a href="/stock" class="nav-item {{ request()->is('stock*') ? 'active' : '' }}"><i class="fa-solid fa-clipboard-check"></i> <span>Stock Opname & Inventaris</span></a>
        <a href="/team" class="nav-item {{ request()->is('team*') ? 'active' : '' }}"><i class="fa-solid fa-user-group"></i> <span>Manajemen Tim</span></a>
        <a href="/activity" class="nav-item {{ request()->is('activity') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Aktivitas</span></a>
        
        <div style="margin-top: 30px;"></div>
        
        <a href="/settings" class="nav-item {{ request()->is('settings') ? 'active' : '' }}"><i class="fa-solid fa-gear"></i> <span>Settings & Profile</span></a>
    @endif
    <a class="nav-item" id="btn-collapse" style="margin-top: auto; opacity: 0.7;">
        <i class="fa-solid fa-angles-left" id="collapse-icon" style="transition: transform 0.3s;"></i> <span>Collapse</span>
    </a>
</div>

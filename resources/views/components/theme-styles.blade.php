@php
    $appThemeColor = auth()->check() ? auth()->user()->getThemeColor() : '#4472c4';
@endphp
<style>
    :root {
        --primary: {{ $appThemeColor }};
        --primary-color: {{ $appThemeColor }};
        --primary-hover: {{ $appThemeColor }};
        --sidebar-active-bg: {{ $appThemeColor }}26; /* ~15% opacity */
        --sidebar-active-text: {{ $appThemeColor }};
        --primary-15: {{ $appThemeColor }}26;
        --primary-10: {{ $appThemeColor }}1a;
        --primary-light: {{ $appThemeColor }}14;
        --primary-focus-shadow: {{ $appThemeColor }}33;
    }

    /* Universal Theme Overrides - Buttons */
    .btn-primary, .btn.btn-primary, button.btn-primary, a.btn-primary,
    .btn-purple, button.btn-purple,
    .btn-gradient-primary {
        background-color: var(--primary) !important;
        border-color: var(--primary) !important;
        color: #ffffff !important;
    }
    .btn-primary:hover, .btn.btn-primary:hover, button.btn-primary:hover, a.btn-primary:hover,
    .btn-purple:hover {
        filter: brightness(0.92) !important;
        color: #ffffff !important;
    }
    .btn-primary:active, .btn.btn-primary:active {
        filter: brightness(0.85) !important;
    }

    /* Sidebar Active & Highlights */
    .nav-item.active {
        background-color: var(--sidebar-active-bg) !important;
        color: var(--sidebar-active-text) !important;
        font-weight: 600 !important;
    }
    .nav-item.active i {
        color: var(--sidebar-active-text) !important;
    }
    .sidebar-logo-icon {
        background-color: var(--sidebar-active-bg) !important;
        color: var(--primary) !important;
    }
    .nav-item:hover i {
        color: var(--primary);
    }

    /* Topbar & Header icons */
    .header-icon {
        background-color: var(--sidebar-active-bg) !important;
        color: var(--primary) !important;
    }
    .avatar, .topbar .avatar {
        background-color: var(--primary) !important;
        color: #ffffff !important;
    }

    /* Form Inputs on Focus */
    .form-control:focus, input.form-control:focus, select.form-control:focus, textarea.form-control:focus,
    input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus,
    input[type="number"]:focus, input[type="date"]:focus, textarea:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px var(--primary-focus-shadow) !important;
        outline: none !important;
    }

    /* Active Tabs, Filters, Segmented Buttons */
    .filter-tab.active, .report-view-tab.active, .btn-group .btn.active,
    .badge-tab.active-purple, .tab-btn.active, .status-filter-pill.active,
    .tab.active, .segment-btn.active {
        background-color: var(--primary) !important;
        color: #ffffff !important;
        border-color: var(--primary) !important;
    }
    .filter-tab.active i, .report-view-tab.active i, .badge-tab.active-purple i, .tab-btn.active i {
        color: #ffffff !important;
    }

    /* Underline style active tabs */
    .tab-btn.tab-underline.active, .nav-tabs .nav-link.active {
        color: var(--primary) !important;
        border-bottom-color: var(--primary) !important;
    }

    /* Calendar */
    .calendar-day.today {
        border: 2px solid var(--primary) !important;
    }
    .calendar-day.today .calendar-day-num {
        color: var(--primary) !important;
        font-weight: 700 !important;
    }

    /* Badges, Tags, Tinted Boxes & Icons */
    .badge-primary, .tag-purple, .badge.bg-primary, .badge-lunas {
        background-color: var(--primary) !important;
        color: #ffffff !important;
    }
    .badge-light-primary, .badge-purple-light, .tag-primary, .faq-badge,
    .icon-purple, .fu-icon, .handler-badge, .status-terkirim {
        background-color: var(--sidebar-active-bg) !important;
        color: var(--primary) !important;
    }

    /* Links & Accents */
    a.text-primary, .text-primary {
        color: var(--primary) !important;
    }
    .action-icons i:hover, .action-btn:hover {
        color: var(--primary) !important;
    }
    input[type="checkbox"]:checked, input[type="radio"]:checked {
        accent-color: var(--primary) !important;
    }

    /* User Profile Hover & Header Accents */
    .user-profile:hover, .profile-wrapper:hover .user-profile {
        background-color: var(--primary-10) !important;
    }
    .chat-item.active {
        background: var(--primary-light) !important;
        border-left: 3px solid var(--primary) !important;
    }
    .theme-btn.active {
        border-color: var(--primary) !important;
        background: var(--primary-light) !important;
        color: var(--primary) !important;
    }

    /* Flatpickr Date Picker Ranges */
    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
        background: var(--primary) !important;
        border-color: var(--primary) !important;
        color: #ffffff !important;
    }
    .flatpickr-day.inRange {
        background: var(--primary-15) !important;
        box-shadow: -5px 0 0 var(--primary-15), 5px 0 0 var(--primary-15) !important;
        border-color: transparent !important;
    }

    /* Simulator & Floating elements */
    .btn-simulator {
        background: var(--primary) !important;
        color: #ffffff !important;
    }
    .btn-simulator:hover {
        box-shadow: 0 6px 20px var(--primary-focus-shadow) !important;
    }

    /* Global Horizontal Scroll Prevention */
    html, body {
        overflow-x: hidden !important;
        max-width: 100vw !important;
        width: 100% !important;
    }
    .main-content {
        min-width: 0 !important;
        max-width: calc(100vw - 260px) !important;
        box-sizing: border-box !important;
    }
    body.sidebar-collapsed .main-content {
        max-width: calc(100vw - 80px) !important;
    }
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0 !important;
            max-width: 100vw !important;
            width: 100% !important;
        }
    }
    .table-responsive {
        width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }
</style>

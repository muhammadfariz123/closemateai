<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} - {{ $invoice->client_name ?: 'Klien' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root {
            --primary: #6b5cd8;
            --bg-light: #f4f5f8;
            --text-dark: #181c32;
            --text-muted: #888888;
            --border-color: #e1e1e4;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-light); color: var(--text-dark); min-height: 100vh; display: flex; flex-direction: column; align-items: center; padding: 24px 16px 40px; }
        
        .top-bar {
            width: 100%;
            max-width: 820px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }
        .brand-badge i { color: var(--primary); }
        .action-btns {
            display: flex;
            gap: 8px;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid var(--border-color);
            background: white;
            color: var(--text-dark);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
            text-decoration: none;
        }
        .btn:hover { background: #f8f9fa; }
        .btn-primary { background: var(--primary); color: white; border-color: var(--primary); }
        .btn-primary:hover { background: #5a4bc7; }

        /* Status Banner */
        .status-banner {
            width: 100%;
            max-width: 820px;
            margin-bottom: 16px;
            padding: 12px 20px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }
        .status-banner.lunas { background: #e8fdf0; border: 1px solid #bbf7d0; color: #16a34a; }
        .status-banner.sebagian { background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb; }
        .status-banner.belum { background: #fff8e6; border: 1px solid #fde68a; color: #d97706; }

        /* Invoice Paper */
        .invoice-paper {
            width: 100%;
            max-width: 820px;
            background: white;
            border-radius: 12px;
            padding: 48px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            box-sizing: border-box;
            position: relative;
        }

        .inv-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #eee; padding-bottom: 24px; margin-bottom: 24px; }
        .inv-header-brand { display: flex; align-items: center; gap: 16px; }
        .inv-logo-box { width: 52px; height: 52px; border-radius: 50%; background: #f1f1f4; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; border: 1px solid #e4e6ef; }
        .inv-logo-text { font-size: 20px; font-weight: 600; color: #222; }
        .inv-addr-text { font-size: 11px; color: #888; margin-top: 2px; }
        .inv-meta { text-align: right; }
        .inv-meta h2 { font-size: 24px; font-weight: 300; letter-spacing: 2px; color: #555; margin-bottom: 8px; }
        .inv-meta p { font-size: 11px; color: #888; margin-bottom: 2px; }

        .inv-desc-row { display: flex; justify-content: space-between; margin-bottom: 28px; }
        .inv-bill-to p { font-size: 10px; font-weight: 600; color: #999; text-transform: uppercase; margin-bottom: 6px; }
        .inv-bill-to h4 { font-size: 16px; font-weight: 600; color: #222; margin-bottom: 4px; }

        .inv-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .inv-table th { text-align: left; padding: 12px 16px; font-size: 10px; font-weight: 600; color: #666; text-transform: uppercase; background: #f8f9fa; }
        .inv-table td { padding: 14px 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; }
        .inv-table td.text-right, .inv-table th.text-right { text-align: right; }

        .inv-totals { display: flex; justify-content: flex-end; margin-bottom: 28px; }
        .inv-totals-grid { width: 320px; }
        .inv-total-line { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 12px; color: #666; }
        .inv-total-line.grand { border-top: 1px solid #eee; padding-top: 12px; margin-top: 12px; font-size: 14px; font-weight: 700; color: #222; }

        .inv-dp-box { background: #f8f9fa; border-radius: 8px; padding: 14px; margin-top: 16px; border: 1px solid #edf2f7; }
        .inv-payment-box { border: 1px solid #f1f1f4; border-radius: 8px; padding: 18px; margin-bottom: 24px; background: #fafafa; }
        .inv-terms-box { margin-bottom: 28px; font-size: 11px; color: #555; line-height: 1.6; }
        .inv-signature-box { display: flex; justify-content: flex-end; border-top: 1px solid #eee; padding-top: 20px; text-align: center; }

        @media (max-width: 640px) {
            .invoice-paper { padding: 24px 16px; }
            .inv-header { flex-direction: column; gap: 16px; }
            .inv-meta { text-align: left; }
            .inv-totals-grid { width: 100%; }
        }

        @media print {
            body { background: white !important; padding: 0 !important; }
            .top-bar, .status-banner { display: none !important; }
            .invoice-paper { box-shadow: none !important; max-width: 100% !important; padding: 20px !important; border-radius: 0 !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    
        /* ----------------- INVOICE THEMES (MATCHING REFERENCE IMAGES 1-5) ----------------- */
        /* 1. Minimalis & Clean */
        .invoice-paper.theme-minimalist { background: #ffffff; color: #1e293b; }
        .invoice-paper.theme-minimalist .inv-header { border-bottom: 1px solid #e2e8f0; }
        .invoice-paper.theme-minimalist .inv-logo-text { color: #1e293b; font-size: 20px; font-weight: 700; }
        .invoice-paper.theme-minimalist .inv-meta h2 { color: #64748b; font-size: 24px; font-weight: 300; letter-spacing: 2px; }
        .invoice-paper.theme-minimalist .inv-table th { background: #f8fafc; color: #475569; }
        .invoice-paper.theme-minimalist .inv-dp-box { background: #f8fafc; border: none; }
        .invoice-paper.theme-minimalist .inv-payment-box { border: 1px solid #f1f5f9; background: #ffffff; }

        /* 2. Creative & Aesthetic */
        .invoice-paper.theme-creative { background: #ffffff; color: #374151; }
        .invoice-paper.theme-creative .inv-header { margin: -48px -48px 24px -48px; padding: 36px 48px 24px 48px; background: linear-gradient(180deg, #fcecee 0%, #fef5f7 75%, #ffffff 100%); flex-direction: column; align-items: center; text-align: center; border-bottom: none; }
        .invoice-paper.theme-creative .inv-header-brand { flex-direction: column; align-items: center; text-align: center; gap: 0; }
        .invoice-paper.theme-creative .inv-logo-box { margin: 0 auto 10px auto; width: 56px; height: 56px; border: 2px solid #f8ccd7 !important; }
        .invoice-paper.theme-creative .inv-logo-text { color: #d66d85; font-size: 20px; font-weight: 700; }
        .invoice-paper.theme-creative .inv-addr-text { color: #888888; font-size: 11px; margin-bottom: 12px; }
        .invoice-paper.theme-creative .inv-meta { text-align: center; }
        .invoice-paper.theme-creative .inv-meta h2 { color: #d66d85; font-size: 24px; font-weight: 400; letter-spacing: 8px; margin: 4px 0 6px 0; }
        .invoice-paper.theme-creative .inv-table th { background: #fdf0f2; color: #d66d85; border: none; }
        .invoice-paper.theme-creative .inv-table td { border-bottom: 1px solid #fde8ed; }
        .invoice-paper.theme-creative .inv-dp-box { background: #fdf2f4; border: none; }
        .invoice-paper.theme-creative .inv-payment-box { border: 1px solid #fce7eb; background: #ffffff; }

        /* 3. Corporate & Professional */
        .invoice-paper.theme-corporate { background: #ffffff; color: #1e293b; }
        .invoice-paper.theme-corporate .inv-header { margin: -48px -48px 24px -48px; padding: 32px 48px; background: #1e3a5f; color: #ffffff; border-radius: 12px 12px 0 0; border-bottom: none; }
        .invoice-paper.theme-corporate .inv-logo-box { border: 2px solid rgba(255,255,255,0.4) !important; }
        .invoice-paper.theme-corporate .inv-logo-text { color: #ffffff; text-transform: uppercase; font-size: 19px; font-weight: 700; letter-spacing: 0.5px; }
        .invoice-paper.theme-corporate .inv-addr-text { color: #cbd5e1; }
        .invoice-paper.theme-corporate .inv-meta h2 { color: #ffffff; letter-spacing: 3px; font-weight: 400; }
        .invoice-paper.theme-corporate .inv-meta p { color: #cbd5e1; }
        .invoice-paper.theme-corporate .inv-table th { background: #edf2f7; color: #1e3a5f; border: none; }
        .invoice-paper.theme-corporate .inv-table td { border-bottom: 1px solid #edf2f7; }
        .invoice-paper.theme-corporate .inv-dp-box { background: #edf2f7; border: none; }
        .invoice-paper.theme-corporate .inv-payment-box { border: 1px solid #e2e8f0; background: #ffffff; }

        /* 4. Modern & Bold */
        .invoice-paper.theme-modern { background: #ffffff; color: #111827; }
        .invoice-paper.theme-modern .inv-header { border-bottom: 3px solid #111827; padding-bottom: 16px; margin-bottom: 24px; }
        .invoice-paper.theme-modern .inv-logo-text { color: #111827; font-weight: 900; text-transform: uppercase; font-size: 19px; }
        .invoice-paper.theme-modern .inv-meta h2 { color: #111827; font-weight: 900; font-size: 28px; }
        .invoice-paper.theme-modern .inv-table th { background: #111827; color: #ffffff; border: none; }
        .invoice-paper.theme-modern .inv-dp-box { background: #f4f4f5; border: none; }
        .invoice-paper.theme-modern .inv-payment-box { border: 1px solid #e4e4e7; background: #ffffff; }

        /* 5. Classic / Traditional */
        .invoice-paper.theme-classic { background: #ffffff; color: #3e3223; font-family: 'Playfair Display', Georgia, serif; }
        .invoice-paper.theme-classic .inv-header { margin: -48px -48px 24px -48px; padding: 36px 48px 24px 48px; background: #f5ede0; border-radius: 12px 12px 0 0; flex-direction: column; align-items: center; text-align: center; border-bottom: none; }
        .invoice-paper.theme-classic .inv-header-brand { flex-direction: column; align-items: center; text-align: center; gap: 0; }
        .invoice-paper.theme-classic .inv-logo-box { margin: 0 auto 10px auto; width: 56px; height: 56px; border: 2px solid #e6d7be !important; }
        .invoice-paper.theme-classic .inv-logo-text { color: #6e4d25; font-size: 22px; font-weight: 600; font-family: 'Playfair Display', Georgia, serif; }
        .invoice-paper.theme-classic .inv-addr-text { color: #8c7860; font-family: 'Inter', sans-serif; }
        .invoice-paper.theme-classic .inv-meta { text-align: center; }
        .invoice-paper.theme-classic .inv-meta h2 { color: #6e4d25; letter-spacing: 8px; font-size: 22px; font-weight: 400; font-family: 'Playfair Display', Georgia, serif; margin: 4px 0 6px 0; }
        .invoice-paper.theme-classic .inv-meta p { color: #9c8a74; font-family: 'Inter', sans-serif; }
        .invoice-paper.theme-classic .inv-table th { background: #f3ebd9; color: #6e4d25; border: none; }
        .invoice-paper.theme-classic .inv-table td { border-bottom: 1px solid #f0e5d0; }
        .invoice-paper.theme-classic .inv-dp-box { background: #f5ede0; border: none; }
        .invoice-paper.theme-classic .inv-payment-box { border: 1px solid #eee4cd; background: #ffffff; }

    </style>
</head>
<body>

    <div class="top-bar">
        <div class="brand-badge">
            <i class="fa-solid fa-file-invoice"></i>
            <span>{{ $invoice->vendor_name ?: 'Invoice Online' }}</span>
        </div>
        <div class="action-btns">
            <button type="button" class="btn" onclick="unduhPNG()"><i class="fa-regular fa-image"></i> Unduh PNG</button>
            <button type="button" class="btn btn-primary" onclick="unduhPDF()"><i class="fa-regular fa-file-pdf"></i> Unduh PDF</button>
        </div>
    </div>

    @php
        $status = $invoice->payment_status ?: ($invoice->remaining_balance <= 0 ? 'Lunas' : ($invoice->total_dp > 0 ? 'Terbayar Sebagian' : 'Belum Dibayar'));
        $statusClass = $status === 'Lunas' ? 'lunas' : ($status === 'Terbayar Sebagian' ? 'sebagian' : 'belum');
    @endphp

    <div class="status-banner {{ $statusClass }}">
        <div>
            <strong>Status: {{ $status }}</strong> &bull; Total: Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}
        </div>
        <div>
            Sisa Tagihan: <strong>Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}</strong>
        </div>
    </div>

    <div class="invoice-paper {{ $invoice->theme ?? 'theme-minimalist' }}" id="invoice-paper">
        <!-- HEADER -->
        <div class="inv-header">
            <div class="inv-header-brand">
                <div class="inv-logo-box">
                    @if(!empty($invoice->user->avatar) || !empty($invoice->user->logo) || !empty($user->avatar) || !empty($user->logo))
                        <img src="{{ asset($invoice->user->avatar ?? $invoice->user->logo ?? $user->avatar ?? $user->logo) }}" style="max-height: 100%; max-width: 100%;">
                    @else
                        <i class="fa-regular fa-image" style="color: #a1a5b7; font-size: 20px;"></i>
                    @endif
                </div>
                <div>
                    <div class="inv-logo-text">{{ $invoice->vendor_name ?: ($user->business_name ?? 'Vendor') }}</div>
                    <div class="inv-addr-text">{{ $invoice->vendor_address ?: ($user->business_address ?? '') }}</div>
                </div>
            </div>
            <div class="inv-meta">
                <h2>INVOICE</h2>
                <p>No. <strong>{{ $invoice->invoice_number }}</strong></p>
                <p>{{ $invoice->invoice_date ? (is_string($invoice->invoice_date) ? $invoice->invoice_date : $invoice->invoice_date->format('d F Y')) : date('d F Y') }}</p>
            </div>
        </div>

        <!-- KEPADA & TANGGAL ACARA -->
        <div class="inv-desc-row">
            <div class="inv-bill-to">
                <p>KEPADA</p>
                <h4>{{ $invoice->client_name ?: '-' }}</h4>
                @if($invoice->client_wa)
                <div style="font-size: 12px; color: #888; margin-bottom: 2px;">{{ $invoice->client_wa }}</div>
                @endif
                @if($invoice->client_address)
                <div style="font-size: 12px; color: #888;">{{ $invoice->client_address }}</div>
                @endif
            </div>
            <div class="inv-bill-to" style="text-align: right;">
                <p>TANGGAL ACARA</p>
                <h4>{{ $invoice->event_date ? (is_string($invoice->event_date) ? $invoice->event_date : $invoice->event_date->format('l, d F Y')) : '-' }}</h4>
                <div style="font-size: 12px; color: #888;">{{ $invoice->termin_label ?: 'Uang Muka / DP 1' }}</div>
            </div>
        </div>

        <!-- TABEL RINCIAN -->
        <table class="inv-table">
            <thead>
                <tr>
                    <th>DESKRIPSI</th>
                    <th class="text-right">HARGA</th>
                </tr>
            </thead>
            <tbody>
                <!-- Paket Utama -->
                @if($invoice->package_name)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #222;">{{ $invoice->package_name }}</div>
                        <div style="font-size: 11px; color: #888;">Paket Utama &bull; {{ $invoice->package_qty ?? 1 }} pcs</div>
                    </td>
                    <td class="text-right" style="font-weight: 500;">
                        Rp {{ number_format(($invoice->package_price * ($invoice->package_qty ?? 1)), 0, ',', '.') }}
                    </td>
                </tr>
                @endif

                <!-- Paket Tambahan -->
                @if(!empty($invoice->extra_packages))
                    @foreach($invoice->extra_packages as $extra)
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: #222;">{{ $extra['name'] ?? 'Paket Tambahan' }}</div>
                            <div style="font-size: 11px; color: #888;">Paket Tambahan</div>
                        </td>
                        <td class="text-right" style="font-weight: 500;">
                            Rp {{ number_format((float) ($extra['price'] ?? 0), 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                @endif

                <!-- Addons -->
                @if(!empty($invoice->addons))
                    @foreach($invoice->addons as $addon)
                    @php $sub = (int)($addon['qty'] ?? 1) * (float)($addon['price'] ?? 0); @endphp
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: #222;">{{ $addon['name'] ?? 'Add-on' }}</div>
                            <div style="font-size: 11px; color: #888;">Add-on &bull; {{ $addon['qty'] ?? 1 }} pcs</div>
                        </td>
                        <td class="text-right" style="font-weight: 500;">
                            Rp {{ number_format($sub, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                @endif

                <!-- Diskon -->
                @if($invoice->total_discount > 0)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #222;">Diskon / Potongan Harga</div>
                        <div style="font-size: 11px; color: #888;">Penawaran khusus</div>
                    </td>
                    <td class="text-right" style="color: #f1416c; font-weight: 500;">
                        - Rp {{ number_format($invoice->total_discount, 0, ',', '.') }}
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- TOTALS -->
        <div class="inv-totals">
            <div class="inv-totals-grid">
                <div class="inv-total-line">
                    <div>Subtotal Paket</div>
                    <div>Rp {{ number_format($invoice->subtotal_package, 0, ',', '.') }}</div>
                </div>
                @if($invoice->total_addon > 0)
                <div class="inv-total-line">
                    <div>Total Add-on</div>
                    <div>Rp {{ number_format($invoice->total_addon, 0, ',', '.') }}</div>
                </div>
                @endif
                @if($invoice->total_discount > 0)
                <div class="inv-total-line" style="color: #f1416c;">
                    <div>Diskon</div>
                    <div>- Rp {{ number_format($invoice->total_discount, 0, ',', '.') }}</div>
                </div>
                @endif
                <div class="inv-total-line grand">
                    <div>GRAND TOTAL</div>
                    <div>Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</div>
                </div>

                <!-- DP Info -->
                <div class="inv-dp-box">
                    @if(str_contains($invoice->termin_label ?? '', 'Pelunasan'))
                        @if($invoice->dp1_amount > 0)
                        <div class="inv-total-line" style="font-size: 11px; margin-bottom: 4px;">
                            <div>Uang Muka / DP 1</div>
                            <div style="font-weight: 500; color: #555;">- Rp {{ number_format($invoice->dp1_amount, 0, ',', '.') }}</div>
                        </div>
                        @endif
                        @if($invoice->dp2_amount > 0)
                        <div class="inv-total-line" style="font-size: 11px; margin-bottom: 4px;">
                            <div>Termin 2 / DP 2</div>
                            <div style="font-weight: 500; color: #555;">- Rp {{ number_format($invoice->dp2_amount, 0, ',', '.') }}</div>
                        </div>
                        @endif
                        <div class="inv-total-line" style="font-size: 11px; margin-bottom: 6px; padding-bottom: 6px; border-bottom: 1px dashed #ddd;">
                            <div>Total DP</div>
                            <div style="font-weight: 600; color: #555;">- Rp {{ number_format($invoice->total_dp, 0, ',', '.') }}</div>
                        </div>
                        <div class="inv-total-line" style="margin-bottom: 0; font-size: 13px; font-weight: 700; color: #222;">
                            <div>Sisa yang harus dibayar</div>
                            <div>Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}</div>
                        </div>
                    @else
                        <div class="inv-total-line">
                            <div>Total Terbayar</div>
                            <div style="font-weight: 600; color: #16a34a;">Rp {{ number_format($invoice->total_dp, 0, ',', '.') }}</div>
                        </div>
                        <div class="inv-total-line" style="margin-bottom: 0; font-size: 13px; font-weight: 700; color: #222;">
                            <div>Sisa Tagihan</div>
                            <div>Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- METODE PEMBAYARAN -->
        <div class="inv-payment-box">
            <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #999; letter-spacing: 0.5px; margin-bottom: 6px;">METODE PEMBAYARAN</div>
            <div style="font-size: 13px; font-weight: 600; color: #222; margin-bottom: 4px;">
                {{ $invoice->bank_name ?: 'BCA' }} &bull; {{ $invoice->bank_account_number ?: '-' }} &bull; a.n {{ $invoice->bank_account_name ?: '-' }}
            </div>
            @if($invoice->due_date)
            <div style="font-size: 12px; color: #666;">
                Jatuh tempo pelunasan: {{ is_string($invoice->due_date) ? $invoice->due_date : $invoice->due_date->format('l, d F Y') }}
            </div>
            @endif
        </div>

        <!-- SYARAT & KETENTUAN -->
        @if(!empty($invoice->terms_and_conditions))
        <div class="inv-terms-box">
            <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #666; letter-spacing: 0.5px; margin-bottom: 8px;">SYARAT & KETENTUAN</div>
            <ol style="padding-left: 18px;">
                @foreach($invoice->terms_and_conditions as $term)
                <li style="margin-bottom: 4px;">
                    <strong>{{ $term['title'] ?? '' }}</strong> {{ $term['content'] ?? '' }}
                </li>
                @endforeach
            </ol>
        </div>
        @endif

        <!-- TANDA TANGAN -->
        <div class="inv-signature-box">
            <div style="width: 160px; text-align: center;">
                <div style="font-size: 13px; font-weight: 600; margin-bottom: 28px;">{{ $invoice->vendor_name ?: 'Vendor' }}</div>
                <div style="border-top: 1px solid #ccc; padding-top: 6px; font-size: 10px; color: #888;">Tanda Tangan</div>
            </div>
        </div>
    </div>

    <script>
        function unduhPNG() {
            const el = document.getElementById('invoice-paper');
            html2canvas(el, { scale: 2, useCORS: true }).then(canvas => {
                const link = document.createElement('a');
                link.download = '{{ $invoice->invoice_number }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }

        function unduhPDF() {
            const el = document.getElementById('invoice-paper');
            const noInv = '{{ $invoice->invoice_number }}';

            html2canvas(el, {
                scale: 2,
                useCORS: true,
                logging: false,
                backgroundColor: '#ffffff'
            }).then(canvas => {
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

                if (printH > maxH) {
                    printH = maxH;
                    printW = printH * ratio;
                }

                const x = margin + (maxW - printW) / 2;
                const y = margin;

                const imgData = canvas.toDataURL('image/jpeg', 0.98);
                pdf.addImage(imgData, 'JPEG', x, y, printW, printH, undefined, 'FAST');
                pdf.save(`${noInv}.pdf`);
            }).catch(err => {
                console.error(err);
                alert("Gagal mengunduh invoice PDF");
            });
        }
    </script>
</body>
</html>

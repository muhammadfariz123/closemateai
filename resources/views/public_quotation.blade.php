<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penawaran - {{ $quotation->client }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; padding: 40px 20px; background: #f1f1f4; font-family: 'Inter', sans-serif; display: flex; flex-direction: column; align-items: center; min-height: 100vh; }
        
        /* Document Themes */
        .theme-wedding { --doc-primary: #b76e79; --doc-secondary: #f9eaec; --doc-border: #ecd3d7; --doc-accent-text: #7d3f48; --doc-text: #333; --doc-font: 'Cormorant Garamond', Georgia, serif; --doc-vendor-style: italic; --doc-title-weight: 600; }
        .theme-navy   { --doc-primary: #1b365d; --doc-secondary: #eef3f9; --doc-border: #d3deec; --doc-accent-text: #1b365d; --doc-text: #333; --doc-font: 'Inter', sans-serif; --doc-vendor-style: normal; --doc-title-weight: 700; }
        .theme-minimal{ --doc-primary: #222222; --doc-secondary: #f5f5f5; --doc-border: #dddddd; --doc-accent-text: #222222; --doc-text: #333; --doc-font: 'Inter', sans-serif; --doc-vendor-style: normal; --doc-title-weight: 700; }
        
        .document-page { 
            box-sizing: border-box; 
            width: 794px; 
            min-height: 1123px;
            background: #fff; 
            padding: 52px 48px 64px 48px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            position: relative; 
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            margin: 0 auto;
            flex-shrink: 0;
            overflow: hidden;
            zoom: 1; /* For desktop, normal scale or CSS scale */
        }
        
        .doc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; gap: 20px; }
        .doc-vendor h1 { font-family: var(--doc-font); color: var(--doc-accent-text); font-size: 30px; font-style: var(--doc-vendor-style); font-weight: 600; line-height: 1.1; margin-bottom: 10px; margin-top: 0; }
        .doc-status { background: var(--doc-secondary); color: var(--doc-primary); padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 600; display: inline-block; }
        
        .doc-meta { text-align: right; }
        .doc-meta h2 { font-family: var(--doc-font); font-weight: var(--doc-title-weight); letter-spacing: 3px; color: var(--doc-accent-text); font-size: 22px; line-height: 1.2; margin-bottom: 6px; margin-top: 0; text-transform: uppercase; white-space: nowrap; }
        .doc-meta p { font-size: 11px; color: #777; margin-bottom: 2px; line-height: 1.4; margin-top: 0; }
        .doc-meta p.doc-no-text { font-weight: 700; color: #222; font-size: 12px; margin-top: 8px; margin-bottom: 4px; }
        
        .doc-info-box { background: var(--doc-secondary); padding: 18px 24px; border-radius: 12px; margin-bottom: 24px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .doc-info-item { min-width: 0; }
        .doc-info-item h4 { font-size: 10px; color: #777; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 4px; font-weight: 600; margin-top: 0; }
        .doc-info-item p { font-size: 13px; color: var(--doc-text); font-weight: 600; word-break: break-word; margin: 0; }
        .doc-info-item.full-width { grid-column: span 3; }
        
        .doc-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; table-layout: fixed; min-width: 0; word-break: break-word; }
        .doc-table th, .doc-table td { box-sizing: border-box; }
        .doc-table th { background: var(--doc-primary); color: white; padding: 10px 12px; font-size: 10px; letter-spacing: 1px; text-transform: uppercase; font-weight: 700; white-space: normal; border: none; text-align: left; }
        .doc-table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 13px; color: var(--doc-text); white-space: normal; vertical-align: top; }
        .doc-section-title { font-weight: 700; color: var(--doc-accent-text); font-size: 11px; letter-spacing: 2px; text-transform: uppercase; padding-top: 16px !important; padding-bottom: 8px !important; }
        
        .doc-totals { width: 100%; max-width: 340px; margin-left: auto; margin-bottom: 28px; }
        .doc-total-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 12px; font-size: 13px; }
        .doc-grand-total { background: var(--doc-primary); color: white; font-weight: 700; font-size: 14px; border-radius: 10px; margin-top: 8px; padding: 14px 16px; }
        .doc-grand-total span:last-child { font-size: 16px; }
        
        .doc-termins-title { font-size: 11px; color: var(--doc-accent-text); font-weight: 700; letter-spacing: 2px; margin-bottom: 12px; text-transform: uppercase; }
        .doc-termins-grid { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 24px; }
        .doc-termin-card { border: 1px solid var(--doc-border); border-top: 3px solid var(--doc-primary); padding: 12px 14px; border-radius: 10px; flex: 1 1 0; min-width: 120px; }
        .doc-termin-card h5 { font-size: 10px; color: var(--doc-accent-text); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 4px; font-weight: 700; margin-top: 0; }
        .doc-termin-card p { font-size: 12px; font-weight: 600; color: var(--doc-text); margin-bottom: 4px; margin-top: 0; }
        .doc-termin-card .termin-val { font-size: 14px; color: #222; font-weight: 700; }
        
        .doc-tnc { font-size: 11px; color: #444; line-height: 1.7; margin-bottom: auto; }
        .doc-tnc h4 { font-size: 11px; color: var(--doc-accent-text); font-weight: 700; letter-spacing: 2px; margin-bottom: 8px; text-transform: uppercase; margin-top: 0; }
        
        .doc-signatures { display: flex; justify-content: space-between; gap: 24px; margin-top: 32px; padding-bottom: 8px; }
        .doc-sig-box { text-align: center; flex: 1 1 0; max-width: 345px; }
        .doc-sig-box p { font-size: 11px; color: #666; margin-bottom: 56px; }
        .doc-sig-box .sig-line { border-bottom: 1px solid #222; margin-bottom: 6px; }
        .doc-sig-box .sig-name { font-weight: 700; font-size: 13px; color: #222; }

        .action-panel {
            width: 100%;
            max-width: 794px;
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-top: 24px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-approve {
            background: var(--primary, #6b5cd8);
            color: white;
            padding: 16px;
            border-radius: 10px;
            border: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .btn-approve:hover { filter: brightness(1.1); }
        .btn-approve:disabled { background: #b1b1b1; cursor: not-allowed; }

        .btn-approve.success {
            background: #a9a9a9;
            color: white;
            cursor: default;
        }

        .btn-wa {
            background: white;
            color: #333;
            padding: 14px;
            border-radius: 10px;
            border: 1px solid #ddd;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .btn-wa:hover { background: #f9f9f9; }

        .status-msg {
            text-align: center;
            color: #444;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        @media (max-width: 900px) {
            body { padding: 20px 10px; }
            .document-page { zoom: 0.8; }
        }
        @media (max-width: 600px) {
            .document-page { zoom: 0.6; }
        }
        @media (max-width: 400px) {
            .document-page { zoom: 0.45; }
        }
    </style>
</head>
<body>
    
    <div class="document-page theme-minimal" id="doc">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 8px; background: var(--doc-primary);"></div>
        
        <div class="doc-header">
            <div class="doc-vendor">
                <h1>Penapict</h1>
                <div class="doc-status">{{ $quotation->status }}</div>
            </div>
            <div class="doc-meta">
                <h2>PENAWARAN HARGA</h2>
                <p style="letter-spacing: 2px;">QUOTATION</p>
                <p class="doc-no-text">No. {{ $quotation->q_no }}</p>
                <p>Terbit: {{ \Carbon\Carbon::parse($quotation->created_at)->format('d F Y') }} &middot; Berlaku s/d {{ $quotation->valid_until ? \Carbon\Carbon::parse($quotation->valid_until)->format('d F Y') : '-' }}</p>
            </div>
        </div>
        
        <div class="doc-info-box">
            <div class="doc-info-item">
                <h4>NAMA KLIEN</h4>
                <p>{{ $quotation->client }}</p>
            </div>
            <div class="doc-info-item">
                <h4>WHATSAPP</h4>
                <p>{{ $quotation->phone ?? '-' }}</p>
            </div>
            <div class="doc-info-item">
                <h4>TANGGAL ACARA</h4>
                <p>{{ $quotation->event_date ? \Carbon\Carbon::parse($quotation->event_date)->format('d F Y') : '-' }}</p>
            </div>
            <div class="doc-info-item full-width">
                <h4>LOKASI / VENUE</h4>
                <p>{{ $quotation->venue ?? '-' }}</p>
            </div>
        </div>
        
        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 45%;">ITEM / LAYANAN</th>
                    <th style="text-align: center; width: 10%;">QTY</th>
                    <th style="text-align: right; width: 20%;">HARGA SATUAN</th>
                    <th style="text-align: right; width: 25%;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $items = $quotation->items ?? [];
                    $sections = [];
                    foreach($items as $item) {
                        $sec = $item['section'] ?? 'LAIN-LAIN';
                        if(!isset($sections[$sec])) $sections[$sec] = [];
                        $sections[$sec][] = $item;
                    }
                @endphp
                @foreach($sections as $secName => $secItems)
                    <tr><td colspan="4" class="doc-section-title">{{ $secName }}</td></tr>
                    @foreach($secItems as $item)
                        @php $sub = ($item['qty'] ?? 0) * ($item['harga'] ?? 0); @endphp
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $item['name'] ?? '' }}</div>
                                <div style="font-size: 11px; color: #666; margin-top: 4px;">{!! nl2br(e($item['desc'] ?? '')) !!}</div>
                            </td>
                            <td style="text-align: center;">{{ $item['qty'] ?? '' }} {{ $item['satuan'] ?? '' }}</td>
                            <td style="text-align: right;">Rp {{ number_format($item['harga'] ?? 0, 0, ',', '.') }}</td>
                            <td style="text-align: right; font-weight: 600;">Rp {{ number_format($sub, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
        
        <div class="doc-totals">
            @php
                $subtotal = 0;
                foreach($items as $i) $subtotal += ($i['qty'] ?? 0) * ($i['harga'] ?? 0);
            @endphp
            <div class="doc-total-row">
                <span style="color: #666;">Subtotal</span>
                <span style="font-weight: 600;">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="doc-total-row">
                <span style="color: #666;">Diskon</span>
                <span style="font-weight: 600;">- Rp {{ number_format($quotation->discount ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="doc-total-row doc-grand-total">
                <span>GRAND TOTAL</span>
                <span>Rp {{ number_format($quotation->grandTotal ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
        
        <div class="doc-termins-title">JADWAL PEMBAYARAN</div>
        <div class="doc-termins-grid">
            @foreach($quotation->termins ?? [] as $t)
                @php
                    $val = ($t['type'] ?? '') === 'Persentase (%)' ? ($quotation->grandTotal * (($t['value'] ?? 0) / 100)) : ($t['value'] ?? 0);
                    $suffix = ($t['type'] ?? '') === 'Persentase (%)' ? " ({$t['value']}%)" : '';
                @endphp
                <div class="doc-termin-card">
                    <h5>TAHAP</h5>
                    <p>{{ $t['name'] ?? '' }}{{ $suffix }}</p>
                    <div class="termin-val">Rp {{ number_format($val, 0, ',', '.') }}</div>
                </div>
            @endforeach
        </div>
        
        <div class="doc-tnc">
            <h4>SYARAT & KETENTUAN</h4>
            <div style="white-space: pre-wrap;">{{ $quotation->tnc ?? '-' }}</div>
        </div>
        
        <div class="doc-signatures">
            <div class="doc-sig-box">
                <p>Hormat kami,</p>
                <div class="sig-line"></div>
                <div class="sig-name">Penapict</div>
            </div>
            <div class="doc-sig-box">
                <p>Disetujui oleh,</p>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $quotation->client }}</div>
            </div>
        </div>
        
        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 8px; background: var(--doc-primary);"></div>
    </div>

    <div class="action-panel">
        @if($quotation->status === 'Disetujui')
            <div class="status-msg">Terima kasih! Penawaran berhasil disetujui.</div>
            <button class="btn-approve success" disabled><i class="fa-solid fa-check-circle"></i> Penawaran Sudah Disetujui</button>
        @else
            <button class="btn-approve" id="btnApprove" onclick="approve()"><i class="fa-solid fa-signature"></i> Setujui Penawaran Ini</button>
        @endif
        
        @php
            $waMsg = "Halo Penapict, saya ingin konsultasi soal penawaran {$quotation->q_no}.";
            $waLink = "https://wa.me/6281234567890?text=" . urlencode($waMsg);
        @endphp
        <a href="{{ $waLink }}" target="_blank" style="text-decoration: none;">
            <button class="btn-wa"><i class="fa-brands fa-whatsapp" style="color: #25D366; font-size: 16px;"></i> Konsultasi via WhatsApp</button>
        </a>
    </div>

    <script>
        // Apply theme from URL
        const params = new URLSearchParams(window.location.search);
        const theme = params.get('t') || 'minimal';
        const doc = document.getElementById('doc');
        
        if (theme === 'elegance' || theme === 'wedding') {
            doc.className = 'document-page theme-wedding';
        } else if (theme === 'navy') {
            doc.className = 'document-page theme-navy';
        } else {
            doc.className = 'document-page theme-minimal';
        }

        async function approve() {
            if(!confirm('Apakah Anda yakin ingin menyetujui penawaran ini?')) return;
            
            const btn = document.getElementById('btnApprove');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
            btn.disabled = true;

            try {
                const res = await fetch(`/q/{{ $quotation->id }}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                if(res.ok) {
                    window.location.reload();
                } else {
                    alert('Gagal menyetujui penawaran.');
                    btn.innerHTML = '<i class="fa-solid fa-signature"></i> Setujui Penawaran Ini';
                    btn.disabled = false;
                }
            } catch(e) {
                alert('Gagal menyetujui penawaran.');
                btn.innerHTML = '<i class="fa-solid fa-signature"></i> Setujui Penawaran Ini';
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>

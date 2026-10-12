<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} - {{ $invoice->client_name ?: 'Klien' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
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
        }

        .inv-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #eee; padding-bottom: 24px; margin-bottom: 24px; }
        .inv-logo-text { font-size: 22px; font-weight: 700; color: #222; }
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
            <div>
                <div class="inv-logo-text">{{ $invoice->vendor_name ?: ($user->business_name ?? 'Vendor') }}</div>
                <div style="font-size: 12px; color: #888; margin-top: 4px;">{{ $invoice->vendor_address ?: ($user->business_address ?? '') }}</div>
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
                    <div class="inv-total-line">
                        <div>Total Terbayar</div>
                        <div style="font-weight: 600; color: #16a34a;">Rp {{ number_format($invoice->total_dp, 0, ',', '.') }}</div>
                    </div>
                    <div class="inv-total-line" style="margin-bottom: 0; font-size: 13px; font-weight: 700; color: #222;">
                        <div>Sisa Tagihan</div>
                        <div>Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}</div>
                    </div>
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
            const opt = {
                margin: [0.2, 0.2, 0.2, 0.2],
                filename: '{{ $invoice->invoice_number }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(el).save();
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $payroll->employee->name ?? 'Karyawan' }} - {{ $payroll->period_month }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #f1f5f9; padding: 30px 15px; color: #1e293b; }
        .slip-container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            padding: 35px 30px;
            border: 1px solid #e2e8f0;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .brand-title { font-size: 20px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; }
        .brand-sub { font-size: 12px; color: #64748b; margin-top: 3px; }
        .doc-badge {
            text-align: right;
        }
        .badge-title { font-size: 16px; font-weight: 800; color: #4338ca; text-transform: uppercase; letter-spacing: 1px; }
        .badge-period { font-size: 13px; color: #475569; font-weight: 600; margin-top: 2px; }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            background: #f8fafc;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 22px;
            font-size: 13px;
        }
        .meta-item { display: flex; flex-direction: column; }
        .meta-label { color: #64748b; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .meta-val { color: #0f172a; font-weight: 700; margin-top: 2px; }
        .section-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .table-salary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 13px;
        }
        .table-salary th {
            background: #f1f5f9;
            text-align: left;
            padding: 8px 12px;
            font-weight: 700;
            color: #334155;
            font-size: 12px;
        }
        .table-salary td {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }
        .table-salary tr:last-child td { border-bottom: none; }
        .text-right { text-align: right; }
        .font-semibold { font-weight: 600; }
        .net-pay-box {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            color: #ffffff;
            border-radius: 8px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 15px;
            margin-bottom: 25px;
        }
        .net-label { font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; }
        .net-val { font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
        .footer-signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 35px;
            text-align: center;
            font-size: 12px;
        }
        .sig-space { height: 60px; }
        .sig-name { font-weight: 700; color: #0f172a; text-decoration: underline; }
        .sig-role { color: #64748b; margin-top: 2px; }
        .btn-print {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .slip-container { border: none; box-shadow: none; padding: 10px; max-width: 100%; }
            .btn-print { display: none; }
        }
    </style>
</head>
<body>

<div class="slip-container">
    <div class="header">
        <div>
            <div class="brand-title">{{ $settings->studio_name ?? 'Penapict Photography Studio' }}</div>
            <div class="brand-sub">{{ $settings->studio_address ?? 'Studio Jasa Fotografi & Videografi Profesional' }}</div>
        </div>
        <div class="doc-badge">
            <div class="badge-title">SLIP GAJI</div>
            <div class="badge-period">Periode: {{ date('F Y', strtotime($payroll->period_month . '-01')) }}</div>
        </div>
    </div>

    <div class="meta-grid">
        <div class="meta-item">
            <span class="meta-label">ID Karyawan / NIK</span>
            <span class="meta-val">{{ $payroll->employee->employee_code ?? '-' }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Nama Lengkap</span>
            <span class="meta-val">{{ $payroll->employee->name ?? '-' }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Divisi</span>
            <span class="meta-val">{{ $payroll->employee->division ?? '-' }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Posisi / Jabatan</span>
            <span class="meta-val">{{ $payroll->employee->position ?? '-' }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Kehadiran / Masuk</span>
            <span class="meta-val">{{ $payroll->total_present }} Hari (Terlambat: {{ $payroll->total_late }})</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Total Lembur Disetujui</span>
            <span class="meta-val">{{ number_format($payroll->total_overtime_hours, 1) }} Jam</span>
        </div>
    </div>

    <!-- PENDAPATAN -->
    <div class="section-title">Penerimaan / Pendapatan</div>
    <table class="table-salary">
        <thead>
            <tr>
                <th>Komponen</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td class="text-right font-semibold">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tunjangan Kehadiran & Operasional</td>
                <td class="text-right font-semibold">Rp {{ number_format($payroll->allowance, 0, ',', '.') }}</td>
            </tr>
            @if($payroll->overtime_pay > 0)
            <tr>
                <td>Upah Lembur Project ({{ number_format($payroll->total_overtime_hours, 1) }} Jam)</td>
                <td class="text-right font-semibold" style="color: #16a34a;">+ Rp {{ number_format($payroll->overtime_pay, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($payroll->bonus > 0)
            <tr>
                <td>Bonus Event / Kinerja</td>
                <td class="text-right font-semibold" style="color: #16a34a;">+ Rp {{ number_format($payroll->bonus, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- POTONGAN -->
    <div class="section-title">Potongan</div>
    <table class="table-salary">
        <thead>
            <tr>
                <th>Keterangan Potongan</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Potongan Keterlambatan Absensi</td>
                <td class="text-right font-semibold" style="color: #dc2626;">- Rp {{ number_format($payroll->late_deductions, 0, ',', '.') }}</td>
            </tr>
            @if($payroll->deductions > 0)
            <tr>
                <td>Potongan Lainnya / Kasbon</td>
                <td class="text-right font-semibold" style="color: #dc2626;">- Rp {{ number_format($payroll->deductions, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- NET SALARY -->
    <div class="net-pay-box">
        <div>
            <div class="net-label">TOTAL GAJI BERSIH (TAKE HOME PAY)</div>
            <div style="font-size: 11px; opacity: 0.8; margin-top: 3px;">Status: {{ strtoupper($payroll->status) }}</div>
        </div>
        <div class="net-val">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</div>
    </div>

    <div class="footer-signatures">
        <div>
            <div>Penerima,</div>
            <div class="sig-space"></div>
            <div class="sig-name">{{ $payroll->employee->name ?? 'Karyawan' }}</div>
            <div class="sig-role">{{ $payroll->employee->position ?? 'Staff' }}</div>
        </div>
        <div>
            <div>Disetujui Oleh,</div>
            <div class="sig-space"></div>
            <div class="sig-name">{{ $settings->studio_name ?? 'Penapict Studio' }}</div>
            <div class="sig-role">HR & Finance Dept</div>
        </div>
    </div>
</div>

<button class="btn-print" onclick="window.print()">
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
    Cetak / Simpan PDF
</button>

</body>
</html>


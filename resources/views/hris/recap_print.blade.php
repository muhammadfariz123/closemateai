<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Kehadiran & Penggajian - {{ $month }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #f8fafc; padding: 25px; color: #0f172a; }
        .recap-card { max-width: 900px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #0f172a; padding-bottom: 15px; margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: 800; color: #0f172a; }
        .sub { font-size: 13px; color: #64748b; margin-top: 3px; }
        .meta { font-size: 13px; text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 12px; }
        th { background: #0f172a; color: #fff; padding: 10px 8px; text-align: left; }
        td { padding: 9px 8px; border-bottom: 1px solid #e2e8f0; }
        tr:nth-child(even) td { background: #f8fafc; }
        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; }
        .badge-div { background: #e0e7ff; color: #3730a3; }
        .total-row td { font-weight: 800; background: #f1f5f9; border-top: 2px solid #0f172a; }
        .btn-print {
            position: fixed; bottom: 25px; right: 25px; background: #4f46e5; color: #fff; border: none;
            padding: 12px 24px; border-radius: 50px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 15px rgba(79,70,229,0.4);
        }
        @media print {
            body { background: #fff; padding: 0; }
            .recap-card { box-shadow: none; border: none; padding: 10px; max-width: 100%; }
            .btn-print { display: none; }
        }
    </style>
</head>
<body>

<div class="recap-card">
    <div class="header">
        <div>
            <div class="title">{{ $settings->studio_name ?? 'Penapict Photography Studio' }}</div>
            <div class="sub">Laporan Rekapitulasi Presensi & Penggajian Karyawan</div>
        </div>
        <div class="meta">
            <div style="font-weight: 700;">Periode: {{ date('F Y', strtotime($month . '-01')) }}</div>
            <div style="color: #64748b; font-size: 11px;">Dicetak: {{ date('d M Y H:i') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama Karyawan</th>
                <th>Divisi</th>
                <th class="text-right">Hadir</th>
                <th class="text-right">Terlambat</th>
                <th class="text-right">Lembur (Jam)</th>
                <th class="text-right">Gaji Pokok</th>
                <th class="text-right">Total Diterima</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalSalary = 0; 
                $totalHours = 0;
            @endphp
            @foreach($employees as $index => $emp)
                @php
                    $pay = $payrolls->get($emp->id);
                    $net = $pay ? $pay->net_salary : ($emp->base_salary + $emp->allowance);
                    $present = $pay ? $pay->total_present : 0;
                    $late = $pay ? $pay->total_late : 0;
                    $otHours = $pay ? $pay->total_overtime_hours : 0;
                    $totalSalary += $net;
                    $totalHours += $otHours;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><b>{{ $emp->employee_code }}</b></td>
                    <td>{{ $emp->name }}</td>
                    <td><span class="badge badge-div">{{ $emp->division }}</span></td>
                    <td class="text-right">{{ $present }}</td>
                    <td class="text-right">{{ $late }}</td>
                    <td class="text-right">{{ number_format($otHours, 1) }}</td>
                    <td class="text-right">Rp {{ number_format($emp->base_salary, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: 700;">Rp {{ number_format($net, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6">TOTAL REKAPITULASI ({{ $employees->count() }} Karyawan)</td>
                <td class="text-right">{{ number_format($totalHours, 1) }} Jam</td>
                <td class="text-right">-</td>
                <td class="text-right" style="color: #4338ca;">Rp {{ number_format($totalSalary, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</div>

<button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>

</body>
</html>

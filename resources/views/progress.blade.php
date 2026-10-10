<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progres Produksi - {{ $booking->client_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #7C3AED;
            --primary-light: #DDD6FE;
            --bg-color: #f3f4f6;
            --text-dark: #111827;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --white: #ffffff;
            --danger: #ef4444;
            --warning: #f59e0b;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-color); color: var(--text-dark); -webkit-font-smoothing: antialiased; }
        
        .container { max-width: 640px; margin: 40px auto; padding: 0 20px; }
        
        .card { background: var(--white); border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px; margin-bottom: 16px; }
        
        .header-label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .project-title { font-size: 24px; font-weight: 700; margin-bottom: 16px; }
        .project-meta { font-size: 13px; color: var(--text-muted); margin-bottom: 12px; line-height: 1.5; }
        .project-meta strong { font-weight: 500; color: var(--text-dark); }
        
        .badges { display: flex; gap: 8px; align-items: center; }
        .badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 100px; font-size: 12px; font-weight: 500; }
        .badge-outline { border: 1px solid var(--border-color); color: var(--text-dark); background: #f9fafb; }
        .badge-gray { background: #f3f4f6; color: var(--text-dark); }
        
        .track-card { background: var(--white); border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px; margin-bottom: 16px; }
        .track-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
        .track-title-wrap { display: flex; align-items: center; gap: 8px; }
        .track-title { font-weight: 600; font-size: 15px; }
        .track-estimasi { font-size: 13px; color: var(--text-muted); }
        
        /* Progress Bar */
        .stepper-container { position: relative; margin-top: 16px; }
        .stepper-track { position: absolute; top: 6px; left: 0; right: 0; height: 4px; background: var(--border-color); border-radius: 4px; z-index: 1; }
        .stepper-fill { position: absolute; top: 6px; left: 0; height: 4px; background: var(--primary); border-radius: 4px; z-index: 2; transition: width 0.3s ease; }
        
        .stepper-steps { display: flex; justify-content: space-between; position: relative; z-index: 3; }
        .step { display: flex; flex-direction: column; align-items: center; gap: 8px; flex: 1; position: relative; }
        .step-dot { width: 16px; height: 16px; border-radius: 50%; background: var(--white); border: 2px solid var(--border-color); transition: 0.3s; }
        .step.active .step-dot { border-color: var(--primary); background: var(--primary); }
        .step.completed .step-dot { border-color: var(--primary); background: var(--primary); }
        .step-label { font-size: 11px; color: var(--text-muted); font-weight: 500; text-align: center; }
        .step.active .step-label { color: var(--text-dark); font-weight: 600; }
        .step.completed .step-label { color: var(--primary); }

        .wa-float { position: fixed; bottom: 24px; right: 24px; background: #25D366; color: white; width: 56px; height: 56px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 30px; box-shadow: 0 4px 12px rgba(37,211,102,0.3); text-decoration: none; z-index: 100; transition: transform 0.2s; }
        .wa-float:hover { transform: scale(1.05); }
        .wa-tooltip { position: fixed; bottom: 84px; right: 24px; background: white; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid var(--border-color); color: var(--text-dark); display: flex; align-items: center; gap: 8px; }
        .wa-tooltip .close { cursor: pointer; color: var(--text-muted); }
    </style>
</head>
<body>

    @php
        $user = \App\Models\User::find($booking->user_id);
        $waNum = $user ? $user->wa_number : '';
        if($waNum && str_starts_with($waNum, '0')) {
            $waNum = '62' . substr($waNum, 1);
        }

        // Calculate diff days
        $targetStr = $booking->production_deadline ? \Carbon\Carbon::parse($booking->production_deadline)->translatedFormat('d M Y') : '-';
        $deadlineWarning = '';
        if ($booking->production_deadline) {
            $diffDays = \Carbon\Carbon::parse($booking->production_deadline)->startOfDay()->diffInDays(\Carbon\Carbon::now()->startOfDay(), false);
            // $diffDays will be negative if future, positive if past. Wait: now() - deadline()
            $diffDays = \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($booking->production_deadline)->startOfDay(), false);
            // $diffDays is negative if overdue.
            if ($diffDays < 0) {
                $deadlineWarning = ' (Terlambat ' . abs($diffDays) . ' hari)';
            } else if ($diffDays == 0) {
                $deadlineWarning = ' (Deadline hari ini)';
            }
        }
        
        $tracks = $booking->production_tracks ?? [];
        $revisionCount = 0;
        foreach($tracks as $t) {
            if(!empty($t['revision_notes']) && trim($t['revision_notes']) !== '') {
                $revisionCount++;
            }
        }
        
        $stages = ['Sortir', 'Coloring', 'Retouch', 'QC', 'Delivery'];
    @endphp

    <div class="container">
        <div class="card">
            <div class="header-label">PROGRES PRODUKSI</div>
            <div class="project-title">{{ $booking->client_name ?: 'Tanpa Nama' }}</div>
            
            <div class="project-meta">
                Tanggal acara: {{ $booking->event_date ? \Carbon\Carbon::parse($booking->event_date)->translatedFormat('d M Y') : '-' }} &middot; 
                Target selesai: {{ $targetStr }} <span style="color: {{ str_contains($deadlineWarning, 'Terlambat') ? 'var(--danger)' : 'var(--warning)' }}">{{ $deadlineWarning }}</span>
            </div>
            
            <div class="badges">
                <div class="badge badge-outline">Status: {{ $booking->production_status ?: 'In Production' }}</div>
                <div class="badge badge-outline">Revisi: {{ $revisionCount }}x</div>
            </div>
        </div>

        @if(count($tracks) == 0)
            <div class="card" style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                Belum ada track produksi untuk project ini.
            </div>
        @else
            @foreach($tracks as $track)
                @php
                    $currentStage = $track['stage'] ?? '';
                    $currentIndex = array_search($currentStage, $stages);
                    if($currentIndex === false) $currentIndex = 0;
                    
                    // calculate fill width
                    $fillWidth = 0;
                    if(count($stages) > 1) {
                        $fillWidth = ($currentIndex / (count($stages) - 1)) * 100;
                    }
                @endphp
                <div class="track-card">
                    <div class="track-header">
                        <div class="track-title-wrap">
                            <div class="track-title">{{ $track['kategori'] ?? 'Lainnya' }}</div>
                            <div class="badge badge-gray">{{ $currentStage }}</div>
                        </div>
                        <div class="track-estimasi">
                            Estimasi: {{ !empty($track['deadline_task']) ? \Carbon\Carbon::parse($track['deadline_task'])->translatedFormat('d M Y') : 'Belum diatur' }}
                        </div>
                    </div>
                    
                    <div class="stepper-container">
                        <div class="stepper-track"></div>
                        <div class="stepper-fill" style="width: {{ $fillWidth }}%;"></div>
                        <div class="stepper-steps">
                            @foreach($stages as $index => $stage)
                                @php
                                    $statusClass = '';
                                    if($index < $currentIndex) $statusClass = 'completed';
                                    if($index == $currentIndex) $statusClass = 'active';
                                @endphp
                                <div class="step {{ $statusClass }}">
                                    <div class="step-dot"></div>
                                    <div class="step-label">{{ $stage }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

    </div>

    @if($waNum)
    <div class="wa-tooltip" id="wa-tooltip">
        Chat with us <i class="fa-solid fa-xmark close" onclick="document.getElementById('wa-tooltip').style.display='none'"></i>
    </div>
    <a href="https://wa.me/{{ $waNum }}" target="_blank" class="wa-float">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
    @endif

</body>
</html>

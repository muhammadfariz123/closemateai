import re

file_path = r'd:\FREELANCE\closemateai\resources\views\settings.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace <head> with <head> and CSRF token
content = content.replace('<head>', '<head>\n    <meta name="csrf-token" content="{{ csrf_token() }}">')

# Add jQuery for AJAX (at the end of body)
jquery_script = '''
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Fonnte Logic
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#btn-save-token').click(function() {
                let token = $('#fonnte_token_input').val();
                if (!token) return alert('Silakan masukkan token!');
                
                $(this).text('Menyimpan...').prop('disabled', true);
                
                $.post('/settings/token', { fonnte_token: token }, function(res) {
                    if (res.success) {
                        $('#toast-message').html('<i class="fa-solid fa-check-circle" style="color:var(--success);"></i> Token tersimpan - silakan scan QR');
                        $('#toast-box').show();
                        setTimeout(() => location.reload(), 1500);
                    }
                }).fail(function() {
                    alert('Gagal menyimpan token');
                    $('#btn-save-token').text('Hubungkan WhatsApp').prop('disabled', false);
                });
            });

            let statusInterval = null;

            $('#btn-show-qr').click(function() {
                loadQR();
            });

            $('#btn-check-status').click(function() {
                checkStatus();
            });

            $('#btn-disconnect').click(function() {
                if(!confirm('Yakin ingin memutuskan koneksi WhatsApp?')) return;
                $(this).text('Memutuskan...').prop('disabled', true);
                
                $.post('/settings/disconnect', function(res) {
                    if(res.success) {
                        location.reload();
                    }
                });
            });

            function loadQR() {
                $('#qr-section').show();
                $('#qr-placeholder').html('<div class="spinner"></div><span>Menyiapkan QR...</span>');
                
                $.get('/settings/device', function(res) {
                    if (res.success && res.data.device_status === 'disconnect' && res.data.url) {
                        $('#qr-placeholder').html('<img src="' + res.data.url + '" style="width:100%; height:100%; border-radius:12px;">');
                        // Fonnte sometimes returns 'url' or 'qr' (base64)
                    } else if (res.success && res.data.device_status === 'disconnect' && res.data.qr) {
                        $('#qr-placeholder').html('<img src="data:image/png;base64,' + res.data.qr + '" style="width:100%; height:100%; border-radius:12px; object-fit:contain;">');
                    } else if (res.success && res.data.device_status === 'connect') {
                        location.reload();
                    } else {
                        $('#qr-placeholder').html('<span style="color:red">Gagal memuat QR. Coba lagi.</span>');
                    }
                });

                if(!statusInterval) {
                    statusInterval = setInterval(checkStatus, 3000);
                }
            }

            function checkStatus() {
                $.get('/settings/device', function(res) {
                    if (res.success && res.data.device_status === 'connect') {
                        clearInterval(statusInterval);
                        location.reload();
                    }
                });
            }
        });
    </script>
'''
content = content.replace('</body>', jquery_script + '\n</body>')

# Add @php block for user logic at the top of main content
php_block = '''
        @php
            $user = auth()->user();
            $hasToken = $user && $user->fonnte_token ? true : false;
            $isConnected = $user && $user->wa_status == 'connected' ? true : false;
        @endphp
'''
content = content.replace('<div class="settings-grid">', php_block + '\n        <div class="settings-grid">')

# Replace Onboarding WhatsApp Card entirely
onboarding_html = '''
                <!-- Onboarding WhatsApp Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-left">
                            <div class="header-icon"><i class="fa-solid fa-qrcode"></i></div>
                            <div class="header-title">
                                <h3>Onboarding WhatsApp</h3>
                                <p>Tiga langkah singkat lewat Fonnte. Setelah terhubung, pesan masuk otomatis dibalas AI.</p>
                            </div>
                        </div>
                        @if(!$hasToken)
                            <div class="badge badge-warning"><i class="fa-solid fa-circle" style="font-size: 8px;"></i> Belum diatur</div>
                        @elseif(!$isConnected)
                            <div class="badge badge-warning" style="background:#fff4de; color:#d97706; border-color:#fde68a;"><i class="fa-solid fa-circle" style="font-size: 8px;"></i> Menunggu scan</div>
                        @else
                            <div class="badge" style="background:rgba(80, 205, 137, 0.1); color:var(--success); border:1px solid rgba(80, 205, 137, 0.2);"><i class="fa-solid fa-circle" style="font-size: 8px;"></i> Connected</div>
                        @endif
                    </div>

                    <div class="progress-steps">
                        <div class="progress-line"></div>
                        <div class="progress-line-active" style="width: {{ $isConnected ? '100%' : ($hasToken ? '50%' : '33%') }};"></div>
                        
                        <div class="step {{ $hasToken ? 'completed' : 'active' }}">
                            <div class="step-icon"><i class="fa-solid fa-key"></i></div>
                            <div class="step-text">Langkah 1<br><span style="font-weight: 400; color: var(--text-muted);">Isi token Fonnte</span></div>
                        </div>
                        <div class="step {{ $isConnected ? 'completed' : ($hasToken ? 'active' : '') }}">
                            <div class="step-icon"><i class="fa-solid fa-mobile-screen"></i></div>
                            <div class="step-text">Langkah 2<br><span style="font-weight: 400; color: var(--text-muted);">Scan QR dari HP</span></div>
                        </div>
                        <div class="step {{ $isConnected ? 'active' : '' }}">
                            <div class="step-icon"><i class="fa-regular fa-circle-check"></i></div>
                            <div class="step-text">Langkah 3<br><span style="font-weight: 400; color: var(--text-muted);">Status Connected</span></div>
                        </div>
                    </div>

                    @if(!$isConnected)
                        <div class="alert-warning" style="background: {{ $hasToken ? '#f1f1f4' : '#fff8dd' }}; color: {{ $hasToken ? 'var(--text-dark)' : '#b45309' }}; border-color: {{ $hasToken ? '#e4e6ef' : '#fde68a' }};">
                            @if(!$hasToken)
                                <i class="fa-solid fa-triangle-exclamation" style="font-size: 18px;"></i>
                                Belum terhubung — masukkan token perangkat Fonnte untuk menyiapkan sesi.
                            @else
                                <i class="fa-solid fa-triangle-exclamation" style="font-size: 18px;"></i>
                                Perangkat siap — klik "Tampilkan QR" lalu scan dari WhatsApp di HP.
                            @endif
                        </div>

                        <div class="banner-box">
                            <div class="banner-text">
                                <h4>Belum punya akun Fonnte?</h4>
                                <p>Daftar dulu secara gratis, tambahkan perangkat, lalu salin token-nya ke sini.</p>
                            </div>
                            <a href="https://md.fonnte.com/new/register.php" target="_blank" class="btn btn-primary" style="white-space: nowrap; text-decoration: none;"><i class="fa-solid fa-arrow-up-right-from-square"></i> Daftar di Fonnte</a>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Langkah 1 - Token Perangkat Fonnte</label>
                            <div class="input-group">
                                <input type="text" id="fonnte_token_input" class="form-control" value="{{ $user ? $user->fonnte_token : '' }}" placeholder="Tempel token perangkat dari dashboard Fonnte">
                                <button id="btn-save-token" class="btn btn-light" style="white-space: nowrap; background: #dcd9f6; color: var(--primary);">{{ $hasToken ? 'Perbarui Token' : 'Hubungkan WhatsApp' }}</button>
                            </div>
                            <div class="form-text">Ambil token di dashboard Fonnte -> Device -> Token. Token disimpan aman di server.</div>
                        </div>

                        @if($hasToken)
                            <div class="form-group" style="margin-top: 24px;">
                                <label class="form-label">Langkah 2 - Scan QR</label>
                                <div style="display: flex; gap: 12px;">
                                    <button id="btn-show-qr" class="btn btn-outline"><i class="fa-solid fa-qrcode"></i> Tampilkan QR</button>
                                    <button id="btn-check-status" class="btn btn-outline"><i class="fa-solid fa-rotate-right"></i> Cek Status</button>
                                </div>
                                
                                <div class="qr-section" id="qr-section" style="display: none;">
                                    <div class="qr-placeholder" id="qr-placeholder">
                                        <div class="spinner"></div>
                                        <span>Menyiapkan QR...</span>
                                    </div>
                                    
                                    <div class="instruction-list">
                                        1. Buka WhatsApp di HP &rarr; Setelan &rarr; Perangkat Tertaut.<br>
                                        2. Pilih "Tautkan Perangkat" lalu arahkan kamera ke QR di atas.<br>
                                        3. Status dicek otomatis tiap 3 detik.
                                    </div>
                                    
                                    <div class="loading-text">
                                        <i class="fa-solid fa-rotate" style="animation: spin 2s linear infinite;"></i> Memantau status koneksi...
                                    </div>
                                </div>
                            </div>
                        @endif

                    @else
                        <!-- Connected State -->
                        <div class="alert-warning" style="background: rgba(80, 205, 137, 0.1); color: var(--success); border-color: rgba(80, 205, 137, 0.2); margin-bottom: 24px;">
                            <i class="fa-regular fa-circle-check" style="font-size: 18px;"></i>
                            Terhubung ke {{ $user->wa_number ?? 'WhatsApp' }}
                        </div>

                        <div class="form-group">
                            <label class="form-label">Webhook URL</label>
                            <div class="input-group">
                                <input type="text" class="form-control" readonly value="{{ url('/api/public/wa/' . ($user->webhook_secret ?? '')) }}" style="background: #fafafa; color: var(--text-dark);">
                                <button class="btn btn-outline" onclick="navigator.clipboard.writeText('{{ url('/api/public/wa/' . ($user->webhook_secret ?? '')) }}'); alert('Disalin!');" style="padding: 10px 14px;"><i class="fa-regular fa-copy"></i></button>
                            </div>
                            <div class="form-text">Tempel URL ini di dashboard Fonnte -> Device -> Webhook.</div>
                        </div>

                        <div style="margin-top:24px; text-align:right;">
                            <button id="btn-disconnect" class="btn btn-outline" style="color: var(--danger); border-color: rgba(241, 65, 108, 0.2); background: rgba(241, 65, 108, 0.05);"><i class="fa-solid fa-link-slash"></i> Putuskan WhatsApp</button>
                        </div>
                    @endif

                    <div class="accordion">
'''

# Find the start and end of Onboarding WhatsApp Card to replace it
start_idx = content.find('<!-- Onboarding WhatsApp Card -->')
end_idx = content.find('<!-- Business Profile Card -->')

if start_idx != -1 and end_idx != -1:
    content = content[:start_idx] + onboarding_html + content[content.find('<div class="accordion">', start_idx)+23:end_idx]

# Update the Toast dynamically
toast_html = '''
                <div class="toast" id="toast-box" style="display: {{ !$hasToken ? 'flex' : 'none' }};">
                    <span id="toast-message">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        Token Fonnte belum diatur
                    </span>
                </div>
'''
content = re.sub(r'<div class="toast">.*?</div>', toast_html, content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated settings.blade.php")

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Form Booking Sesi Foto - {{ $owner->business_name ?? 'Studio Foto' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: {{ $owner->theme_color ?? '#4472c4' }};
            --primary-dark: #2a4c8a;
            --bg: #f8fafc;
            --surface: #ffffff;
            --border: #e2e8f0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #0f172a; color: #1e293b; min-height: 100vh; padding: 20px 14px; display: flex; justify-content: center; align-items: flex-start; }
        .booking-card { width: 100%; max-width: 540px; background: #ffffff; border-radius: 28px; padding: 26px 20px 35px; box-shadow: 0 20px 45px rgba(0,0,0,0.3); }
        .header { text-align: center; margin-bottom: 24px; }
        .logo-circle { width: 62px; height: 62px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 12px; box-shadow: 0 6px 16px rgba(68,114,196,0.35); }
        .title { font-size: 20px; font-weight: 800; color: #0f172a; }
        .sub { font-size: 13px; color: #64748b; margin-top: 4px; line-height: 1.4; }
        
        .section-label { font-size: 13px; font-weight: 800; color: #0f172a; margin: 18px 0 10px; display: flex; align-items: center; gap: 6px; }
        
        /* Package Cards */
        .pkg-grid { display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px; }
        .pkg-card { border: 2px solid var(--border); border-radius: 16px; padding: 14px; cursor: pointer; transition: all 0.2s; position: relative; background: #f8fafc; }
        .pkg-card.selected { border-color: var(--primary); background: #eff6ff; }
        .pkg-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
        .pkg-name { font-size: 14px; font-weight: 800; color: #0f172a; }
        .pkg-price { font-size: 14px; font-weight: 800; color: var(--primary); }
        .pkg-desc { font-size: 11px; color: #64748b; line-height: 1.4; }

        /* Form elements */
        .form-group { margin-bottom: 12px; }
        .form-label { display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 11px 14px; border-radius: 12px; border: 1px solid var(--border); font-size: 13px; outline: none; background: #f8fafc; }
        .form-control:focus { border-color: var(--primary); background: #fff; }

        /* Add-ons checkboxes */
        .addon-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: #f8fafc; border: 1px solid var(--border); border-radius: 12px; margin-bottom: 8px; font-size: 12px; cursor: pointer; }
        .addon-item input { accent-color: var(--primary); width: 16px; height: 16px; margin-right: 8px; }

        /* Total estimation */
        .total-box { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff; border-radius: 16px; padding: 16px 18px; margin: 20px 0 16px; display: flex; justify-content: space-between; align-items: center; }
        .total-label { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.8; }
        .total-amount { font-size: 20px; font-weight: 800; color: #60a5fa; }

        .btn-submit { width: 100%; background: var(--primary); color: #fff; border: none; padding: 15px; border-radius: 16px; font-size: 15px; font-weight: 800; cursor: pointer; box-shadow: 0 6px 20px rgba(68,114,196,0.35); transition: all 0.2s; }
        .btn-submit:active { transform: scale(0.98); }

        .success-box { display: none; text-align: center; padding: 20px 10px; }
        .btn-wa-confirm { display: inline-flex; align-items: center; gap: 8px; background: #25d366; color: #fff; text-decoration: none; padding: 14px 24px; border-radius: 50px; font-weight: 800; font-size: 14px; margin-top: 18px; box-shadow: 0 6px 18px rgba(37,211,102,0.35); }
    </style>
</head>
<body>

<div class="booking-card">
    <div id="bookingFormContainer">
        <div class="header">
            <div class="logo-circle"><i class="fa-solid fa-camera-retro"></i></div>
            <h1 class="title">{{ $owner->business_name ?? 'Studio Foto' }}</h1>
            <p class="sub">Formulir Booking Resmi Jadwal Sesi Pemotretan & Event</p>
        </div>

        <form id="publicBookingForm" onsubmit="submitClientBooking(event)">
            <!-- 1. Pilih Paket -->
            <div class="section-label"><i class="fa-solid fa-box-open" style="color: var(--primary);"></i> 1. Pilih Paket Layanan</div>
            <div class="pkg-grid">
                @foreach($packages as $idx => $pkg)
                <div class="pkg-card {{ $idx === 0 ? 'selected' : '' }}" onclick="selectPackage(this, '{{ $pkg['name'] }}', {{ $pkg['price'] }})">
                    <div class="pkg-header">
                        <span class="pkg-name">{{ $pkg['name'] }}</span>
                        <span class="pkg-price">Rp {{ number_format($pkg['price'], 0, ',', '.') }}</span>
                    </div>
                    <div class="pkg-desc">{{ $pkg['desc'] }}</div>
                </div>
                @endforeach
            </div>
            <input type="hidden" id="selectedPkgName" value="{{ $packages[0]['name'] }}">
            <input type="hidden" id="selectedPkgPrice" value="{{ $packages[0]['price'] }}">

            <!-- 2. Tanggal & Jam -->
            <div class="section-label"><i class="fa-solid fa-calendar-day" style="color: var(--primary);"></i> 2. Tanggal & Waktu Acara</div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label class="form-label">Tanggal Pelaksanaan</label>
                    <input type="date" class="form-control" id="bookingDate" required min="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Perkiraan Jam</label>
                    <input type="time" class="form-control" id="bookingTime" value="10:00" required>
                </div>
            </div>

            <!-- 3. Add-on Tambahan -->
            <div class="section-label"><i class="fa-solid fa-plus-circle" style="color: var(--primary);"></i> 3. Tambahan Item (Opsional)</div>
            @foreach($addons as $addon)
            <label class="addon-item">
                <div style="display: flex; align-items: center;">
                    <input type="checkbox" class="addon-check" value="{{ $addon['name'] }}" data-price="{{ $addon['price'] }}" onchange="updateTotal()">
                    <span>{{ $addon['name'] }}</span>
                </div>
                <b style="color: var(--primary);">+ Rp {{ number_format($addon['price'], 0, ',', '.') }}</b>
            </label>
            @endforeach

            <!-- 4. Data Klien -->
            <div class="section-label"><i class="fa-solid fa-user-check" style="color: var(--primary);"></i> 4. Informasi Kontak Klien</div>
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="clientName" placeholder="Contoh: Sarah Angelina" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label class="form-label">Nomor WhatsApp Aktif</label>
                    <input type="tel" class="form-control" id="clientPhone" placeholder="08xxxxxxxxxx" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email (Opsional)</label>
                    <input type="email" class="form-control" id="clientEmail" placeholder="nama@email.com">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Lokasi Sesi / Alamat Acara</label>
                <input type="text" class="form-control" id="clientLocation" placeholder="Studio Utama / Hotel / Gedung / Rumah...">
            </div>
            <div class="form-group">
                <label class="form-label">Catatan Tambahan (Opsional)</label>
                <textarea class="form-control" id="clientNotes" rows="2" placeholder="Konsep foto yang diinginkan, tema warna, dll..."></textarea>
            </div>

            <!-- Total Estimation Box -->
            <div class="total-box">
                <div>
                    <div class="total-label">Estimasi Total Biaya:</div>
                    <div style="font-size: 11px; opacity: 0.8; margin-top: 2px;">Paket + Add-on terpilih</div>
                </div>
                <div class="total-amount" id="totalAmountDisplay">Rp 3.500.000</div>
            </div>

            <button type="submit" class="btn-submit" id="btnSubmitBooking">
                Kirim Formulir Booking
            </button>
        </form>
    </div>

    <!-- Confirmation Screen -->
    <div class="success-box" id="bookingSuccessBox">
        <div style="font-size: 60px; color: #10b981; margin-bottom: 12px;"><i class="fa-solid fa-circle-check"></i></div>
        <h2 style="font-size: 22px; font-weight: 800; color: #0f172a;">Booking Berhasil Dikirim!</h2>
        <p style="font-size: 13px; color: #64748b; margin-top: 8px; line-height: 1.5;">
            Terima kasih <b id="confirmClientName">Kak</b>, jadwal booking Anda telah masuk ke sistem operasional kami. Silakan klik tombol di bawah untuk konfirmasi langsung ke Admin via WhatsApp.
        </p>

        <a id="btnWaConfirmation" href="#" target="_blank" class="btn-wa-confirm">
            <i class="fa-brands fa-whatsapp" style="font-size: 20px;"></i>
            Konfirmasi ke WhatsApp Studio
        </a>
    </div>
</div>

<script>
    let basePrice = {{ $packages[0]['price'] ?? 3500000 }};

    function selectPackage(el, name, price) {
        document.querySelectorAll('.pkg-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('selectedPkgName').value = name;
        document.getElementById('selectedPkgPrice').value = price;
        basePrice = price;
        updateTotal();
    }

    function updateTotal() {
        let addonSum = 0;
        document.querySelectorAll('.addon-check:checked').forEach(cb => {
            addonSum += parseFloat(cb.getAttribute('data-price') || 0);
        });
        const total = basePrice + addonSum;
        document.getElementById('totalAmountDisplay').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    async function submitClientBooking(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitBooking');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan Booking...';

        const selectedAddons = [];
        document.querySelectorAll('.addon-check:checked').forEach(cb => {
            selectedAddons.push({
                name: cb.value,
                price: parseFloat(cb.getAttribute('data-price') || 0)
            });
        });

        const payload = {
            client_name: document.getElementById('clientName').value,
            client_phone: document.getElementById('clientPhone').value,
            client_email: document.getElementById('clientEmail').value,
            location: document.getElementById('clientLocation').value,
            package_name: document.getElementById('selectedPkgName').value,
            package_price: parseFloat(document.getElementById('selectedPkgPrice').value),
            event_date: document.getElementById('bookingDate').value,
            event_time: document.getElementById('bookingTime').value,
            addons: selectedAddons,
            notes: document.getElementById('clientNotes').value
        };

        try {
            const token = '{{ $token }}';
            const endpoint = token ? `/book/${token}` : '/book';
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const json = await res.json();
            if (json.success) {
                document.getElementById('bookingFormContainer').style.display = 'none';
                document.getElementById('bookingSuccessBox').style.display = 'block';
                document.getElementById('confirmClientName').innerText = payload.client_name;
                if (json.wa_url) {
                    document.getElementById('btnWaConfirmation').href = json.wa_url;
                }
            } else {
                alert(`Gagal: ${json.message}`);
                btn.disabled = false;
                btn.innerText = 'Kirim Formulir Booking';
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan saat mengirim formulir booking.');
            btn.disabled = false;
            btn.innerText = 'Kirim Formulir Booking';
        }
    }
</script>

</body>
</html>

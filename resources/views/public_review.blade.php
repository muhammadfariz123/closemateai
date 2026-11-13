<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Penilaian Layanan - {{ $owner->business_name ?? 'Studio Foto' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @include('components.theme-styles')
    <style>
        :root {
            --primary: {{ $owner->theme_color ?? '#4472c4' }};
            --gold: #f59e0b;
            --bg: #f8fafc;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #0f172a; color: #1e293b; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 16px; }
        .review-card { width: 100%; max-width: 480px; background: #ffffff; border-radius: 28px; padding: 28px 22px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); text-align: center; }
        .header-logo { width: 64px; height: 64px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 26px; margin: 0 auto 12px; box-shadow: 0 6px 16px rgba(68,114,196,0.3); }
        .studio-title { font-size: 20px; font-weight: 800; color: #0f172a; }
        .sub-title { font-size: 13px; color: #64748b; margin-top: 4px; }
        .event-badge { display: inline-block; background: #eff6ff; color: var(--primary); font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; margin: 12px 0 18px; border: 1px solid #bfdbfe; }
        
        /* Star Rating Ala Grab */
        .stars-container { display: flex; justify-content: center; gap: 8px; margin: 16px 0 6px; }
        .star-btn { background: none; border: none; font-size: 38px; color: #cbd5e1; cursor: pointer; transition: transform 0.2s, color 0.2s; }
        .star-btn.active { color: var(--gold); }
        .star-btn:hover { transform: scale(1.15); }
        .rating-feedback-label { font-size: 14px; font-weight: 800; color: var(--gold); margin-bottom: 16px; }

        /* Rating Rows */
        .rating-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px; text-align: left; }
        .mini-stars { display: flex; gap: 4px; }
        .mini-star { font-size: 18px; color: #cbd5e1; cursor: pointer; }
        .mini-star.active { color: var(--gold); }

        /* Quick Tags */
        .tags-grid { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin: 16px 0; }
        .tag-pill { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 20px; padding: 6px 14px; font-size: 12px; font-weight: 600; color: #475569; cursor: pointer; transition: all 0.2s; }
        .tag-pill.selected { background: #eff6ff; border-color: var(--primary); color: var(--primary); font-weight: 700; }

        /* Comment box */
        .comment-box { width: 100%; border: 1px solid #e2e8f0; border-radius: 16px; padding: 12px 14px; font-size: 13px; outline: none; margin-bottom: 18px; background: #f8fafc; resize: none; }
        .comment-box:focus { border-color: var(--primary); background: #fff; }

        .btn-submit { width: 100%; background: var(--primary); color: #fff; border: none; padding: 14px; border-radius: 16px; font-size: 15px; font-weight: 800; cursor: pointer; box-shadow: 0 6px 20px rgba(68,114,196,0.35); transition: all 0.2s; }
        .btn-submit:active { transform: scale(0.98); }

        .success-box { display: none; text-align: center; padding: 30px 10px; }
        .success-icon { font-size: 60px; color: #10b981; margin-bottom: 14px; }
    </style>
</head>
<body>

<div class="review-card">
    <div id="reviewFormContainer">
        <div class="header-logo"><i class="fa-solid fa-camera"></i></div>
        <h2 class="studio-title">{{ $owner->business_name ?? 'Penapict Studio' }}</h2>
        <p class="sub-title">Bagaimana pengalaman sesi foto Anda bersama tim kami?</p>

        <div class="event-badge">
            <i class="fa-regular fa-calendar-check"></i> {{ $booking->package_name ?? 'Sesi Foto' }} • Klien: <b>{{ $booking->client_name }}</b>
        </div>

        <div style="font-size: 12px; font-weight: 700; color: #475569;">Kru Lapangan: {{ $crewNames }}</div>

        <!-- Bintang Utama Keseluruhan -->
        <div class="stars-container" id="mainStarsBox">
            <button class="star-btn active" onclick="setMainRating(1)"><i class="fa-solid fa-star"></i></button>
            <button class="star-btn active" onclick="setMainRating(2)"><i class="fa-solid fa-star"></i></button>
            <button class="star-btn active" onclick="setMainRating(3)"><i class="fa-solid fa-star"></i></button>
            <button class="star-btn active" onclick="setMainRating(4)"><i class="fa-solid fa-star"></i></button>
            <button class="star-btn active" onclick="setMainRating(5)"><i class="fa-solid fa-star"></i></button>
        </div>
        <div class="rating-feedback-label" id="ratingLabel">Sangat Memuaskan! ⭐⭐⭐⭐⭐</div>

        <!-- Detail Kriteria Penilaian -->
        <div style="background: #f8fafc; padding: 12px 14px; border-radius: 16px; margin-bottom: 16px;">
            <div class="rating-row">
                <span>Pelayanan & Keramahan Kru:</span>
                <div class="mini-stars" id="starsHospitality">
                    <!-- 5 stars -->
                </div>
            </div>
            <div class="rating-row">
                <span>Ketepatan Waktu (Punctuality):</span>
                <div class="mini-stars" id="starsPunctuality">
                    <!-- 5 stars -->
                </div>
            </div>
            <div class="rating-row" style="border-bottom: none;">
                <span>Keahlian & Pengarahan Pose:</span>
                <div class="mini-stars" id="starsCrewSkill">
                    <!-- 5 stars -->
                </div>
            </div>
        </div>

        <!-- Quick Tags -->
        <div style="font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Apa yang paling Anda sukai?</div>
        <div class="tags-grid">
            <span class="tag-pill selected" onclick="toggleTag(this)">🌟 Sangat Ramah & Asik</span>
            <span class="tag-pill selected" onclick="toggleTag(this)">⏰ Tepat Waktu</span>
            <span class="tag-pill" onclick="toggleTag(this)">📸 Pose Diarahkan Jelas</span>
            <span class="tag-pill" onclick="toggleTag(this)">⚡ Kerja Cepat & Gesit</span>
            <span class="tag-pill" onclick="toggleTag(this)">😍 Hasil Keren Banget</span>
        </div>

        <!-- Review Comment -->
        <textarea class="comment-box" id="reviewComment" rows="3" placeholder="Tuliskan kesan atau masukan untuk tim foto kami (opsional)..."></textarea>

        <button class="btn-submit" id="btnSubmitReview" onclick="submitReview()">
            Kirim Penilaian Layanan
        </button>
    </div>

    <div class="success-box" id="successBox">
        <div class="success-icon"><i class="fa-solid fa-circle-check"></i></div>
        <h2 style="font-size: 20px; font-weight: 800; color: #0f172a;">Terima Kasih Banyak!</h2>
        <p style="font-size: 13px; color: #64748b; margin-top: 8px; line-height: 1.5;">
            Penilaian bintang dan ulasan Anda sangat berarti bagi seluruh tim <b>{{ $owner->business_name ?? 'Studio Foto' }}</b> untuk terus memberikan pelayanan terbaik.
        </p>
    </div>
</div>

<script>
    let ratingOverall = 5;
    let ratingHospitality = 5;
    let ratingPunctuality = 5;
    let ratingCrewSkill = 5;

    const ratingDescriptions = {
        5: "Sangat Memuaskan! ⭐⭐⭐⭐⭐",
        4: "Bagus & Memuaskan! ⭐⭐⭐⭐",
        3: "Cukup Baik ⭐⭐⭐",
        2: "Kurang Memuaskan ⭐⭐",
        1: "Perlu Banyak Perbaikan ⭐"
    };

    function setMainRating(n) {
        ratingOverall = n;
        const btns = document.querySelectorAll('#mainStarsBox .star-btn');
        btns.forEach((btn, idx) => {
            btn.classList.toggle('active', idx < n);
        });
        document.getElementById('ratingLabel').innerText = ratingDescriptions[n] || "";
    }

    function initMiniStars(containerId, initial, setter) {
        const box = document.getElementById(containerId);
        let html = '';
        for (let i = 1; i <= 5; i++) {
            html += `<span class="mini-star ${i <= initial ? 'active' : ''}" onclick="${setter}(${i})"><i class="fa-solid fa-star"></i></span>`;
        }
        box.innerHTML = html;
    }

    function setHospitality(n) {
        ratingHospitality = n;
        initMiniStars('starsHospitality', n, 'setHospitality');
    }
    function setPunctuality(n) {
        ratingPunctuality = n;
        initMiniStars('starsPunctuality', n, 'setPunctuality');
    }
    function setCrewSkill(n) {
        ratingCrewSkill = n;
        initMiniStars('starsCrewSkill', n, 'setCrewSkill');
    }

    initMiniStars('starsHospitality', 5, 'setHospitality');
    initMiniStars('starsPunctuality', 5, 'setPunctuality');
    initMiniStars('starsCrewSkill', 5, 'setCrewSkill');

    function toggleTag(el) {
        el.classList.toggle('selected');
    }

    async function submitReview() {
        const selectedTags = Array.from(document.querySelectorAll('.tag-pill.selected')).map(el => el.innerText.trim());
        const comment = document.getElementById('reviewComment').value;
        const btn = document.getElementById('btnSubmitReview');

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengirim...';

        try {
            const res = await fetch('/review/{{ $booking->uuid ?? $booking->id }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    rating_overall: ratingOverall,
                    rating_hospitality: ratingHospitality,
                    rating_punctuality: ratingPunctuality,
                    rating_field_crew: ratingCrewSkill,
                    positive_tags: selectedTags,
                    review_comment: comment,
                    field_crew_names: '{{ $crewNames }}'
                })
            });

            const json = await res.json();
            if (json.success) {
                document.getElementById('reviewFormContainer').style.display = 'none';
                document.getElementById('successBox').style.display = 'block';
            } else {
                alert(`Gagal: ${json.message}`);
                btn.disabled = false;
                btn.innerText = 'Kirim Penilaian Layanan';
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan saat mengirim ulasan.');
            btn.disabled = false;
            btn.innerText = 'Kirim Penilaian Layanan';
        }
    }
</script>

</body>
</html>


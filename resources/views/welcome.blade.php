<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CloseMateAI - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #6b5cd8;
            --primary-hover: #5849c4;
            --bg-color: #f8f9fc;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --white: #ffffff;
            --whatsapp-green: #25d366;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 24px 16px;
        }

        .header {
            text-align: center;
            margin-bottom: 16px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background-color: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin: 0 auto 12px;
            box-shadow: 0 4px 14px rgba(107, 92, 216, 0.3);
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-dark);
        }

        .header p {
            color: var(--text-muted);
            font-size: 14px;
        }

        .login-card {
            background: var(--white);
            width: 100%;
            max-width: 440px;
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--shadow);
        }

        .toggle-container {
            display: flex;
            background-color: #f1f5f9;
            border-radius: 8px;
            padding: 4px;
            margin-bottom: 20px;
        }

        .toggle-btn {
            flex: 1;
            padding: 8px;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-muted);
            border-radius: 6px;
            cursor: pointer;
            transition: var(--transition);
        }

        .toggle-btn.active {
            background-color: var(--white);
            color: var(--text-dark);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 4px;
            color: var(--text-dark);
        }

        .form-group label a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 400;
            font-size: 12px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            transition: var(--transition);
            outline: none;
            background-color: var(--white);
        }

        .select-wrapper {
            position: relative;
        }

        .select-wrapper select {
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            color: var(--text-dark);
        }
        
        .select-wrapper select:invalid {
            color: #94a3b8;
        }

        .select-wrapper select option {
            color: #000000; /* Pastikan pilihan tulisan berwarna hitam */
        }

        .select-wrapper select option[value=""] {
            color: #94a3b8; /* Placeholder tetap abu-abu */
        }

        .select-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
            font-size: 12px;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(107, 92, 216, 0.1);
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .btn {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-outline {
            background-color: white;
            border: 1px solid var(--border-color);
            color: var(--text-dark);
        }

        .btn-outline:hover {
            background-color: #f8f9fc;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 16px 0;
            color: var(--text-muted);
            font-size: 12px;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--border-color);
        }

        .divider:not(:empty)::before {
            margin-right: 16px;
        }

        .divider:not(:empty)::after {
            margin-left: 16px;
        }

        .google-icon {
            width: 18px;
            height: 18px;
        }

        .footer-text {
            text-align: center;
            margin-top: 16px;
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Floating WhatsApp */
        .whatsapp-widget {
            position: fixed;
            bottom: 24px;
            right: 24px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 12px;
            z-index: 100;
        }

        .chat-bubble {
            background-color: white;
            padding: 10px 16px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            border: 1px solid var(--border-color);
            position: relative;
        }
        
        .chat-bubble::after {
            content: '';
            position: absolute;
            bottom: -6px;
            right: 20px;
            width: 12px;
            height: 12px;
            background-color: white;
            border-bottom: 1px solid var(--border-color);
            border-right: 1px solid var(--border-color);
            transform: rotate(45deg);
        }

        .whatsapp-btn {
            width: 56px;
            height: 56px;
            background-color: var(--whatsapp-green);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            box-shadow: 0 4px 14px rgba(37, 211, 102, 0.4);
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }

        .whatsapp-btn:hover {
            transform: scale(1.05);
        }
        
        .close-widget {
            position: absolute;
            top: -5px;
            right: -5px;
            background: white;
            color: var(--text-muted);
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            cursor: pointer;
            border: 1px solid var(--border-color);
        }
        
        /* Subtle Entrance Animation */
        .login-card {
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        @keyframes slideUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            top: 24px;
            right: 24px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1000;
            transform: translateX(120%);
            transition: transform 0.3s ease;
            border: 1px solid var(--border-color);
        }
        .toast-notification.show {
            transform: translateX(0);
        }
        .toast-icon {
            width: 20px;
            height: 20px;
            background-color: var(--text-dark);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }
        .toast-text {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
        }

        /* Loading spinner */
        @keyframes spin { 100% { transform: rotate(360deg); } }
        .spinner {
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
            display: none;
        }
        .btn.loading .spinner { display: inline-block; }
        .btn.loading { opacity: 0.8; pointer-events: none; }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-icon">
            <i class="fa-regular fa-heart"></i>
        </div>
        <h1>CloseMateAI</h1>
        <p>Console WhatsApp AI chatbot & leads untuk vendor pernikahan</p>
    </div>

    <div class="login-card">
        <div class="toggle-container">
            <div class="toggle-btn active" id="btn-login">Masuk</div>
            <div class="toggle-btn" id="btn-register">Daftar Vendor</div>
        </div>

        <!-- Login Form -->
        <form id="login-form" action="/login" method="POST">
            @csrf
            
            @if ($errors->any())
                <div style="background-color: var(--danger-bg); color: var(--danger); padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; border: 1px solid rgba(241, 65, 108, 0.2);">
                    <i class="fa-solid fa-circle-exclamation" style="margin-right: 6px;"></i> {{ $errors->first() }}
                </div>
            @endif

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="vendor@studio.com" required>
            </div>

            <div class="form-group">
                <label for="password">
                    Password
                    <a href="#">Lupa password?</a>
                </label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary">Masuk</button>
        </form>

        <!-- Register Form -->
        <form id="register-form" action="#" method="POST" style="display: none;">
            @csrf
            <div class="form-group">
                <label for="reg-bisnis">Nama Bisnis</label>
                <input type="text" id="reg-bisnis" name="bisnis" class="form-control" placeholder="Aura Wedding Photography" required>
            </div>

            <div class="form-group">
                <label for="reg-email">Email</label>
                <input type="email" id="reg-email" name="email" class="form-control" placeholder="vendor@studio.com" required>
            </div>

            <div class="form-group">
                <label for="reg-password">Password</label>
                <input type="password" id="reg-password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
            </div>

            <div class="form-group">
                <label for="reg-kategori">Kategori Bisnis</label>
                <div class="select-wrapper">
                    <select id="reg-kategori" name="kategori" class="form-control" required>
                        <option value="" disabled selected hidden>Pilih bidang usaha</option>
                        <option value="wedding_organizer">Wedding Organizer</option>
                        <option value="dokumentasi">Dokumentasi Foto & Video</option>
                        <option value="mua">Makeup Artist (MUA)</option>
                        <option value="dekorasi">Dekorasi</option>
                        <option value="venue">Venue</option>
                        <option value="catering">Catering</option>
                        <option value="bridal">Bridal / Busana</option>
                        <option value="entertainment">Entertainment / Musik</option>
                        <option value="mc">MC</option>
                        <option value="undangan">Undangan & Suvenir</option>
                        <option value="rental">Rental Perlengkapan</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                    <i class="fa-solid fa-chevron-down select-icon"></i>
                </div>
            </div>

            <button type="submit" id="btn-submit-reg" class="btn btn-primary">
                <span class="spinner"></span>
                <span class="btn-text">Buat Akun Vendor</span>
            </button>
        </form>

        <div class="divider">atau</div>

        <a href="{{ route('google.redirect') }}" style="text-decoration: none;">
            <button type="button" class="btn btn-outline">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="google-icon">
                Lanjut dengan Google
            </button>
        </a>
    </div>
    
    <div class="footer-text">
        Setiap vendor punya workspace & data leads yang terpisah.
    </div>

    <div class="whatsapp-widget">
        <div class="chat-bubble">Chat with us</div>
        <div class="whatsapp-btn">
            <i class="fa-brands fa-whatsapp"></i>
            <div class="close-widget"><i class="fa-solid fa-xmark"></i></div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-notification" id="toast">
        <div class="toast-icon"><i class="fa-solid fa-check"></i></div>
        <div class="toast-text">Cek email kamu untuk konfirmasi akun.</div>
    </div>

    <script>
        // Toggle logic
        const loginBtn = document.getElementById('btn-login');
        const registerBtn = document.getElementById('btn-register');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');

        loginBtn.addEventListener('click', () => {
            loginBtn.classList.add('active');
            registerBtn.classList.remove('active');
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
        });

        registerBtn.addEventListener('click', () => {
            registerBtn.classList.add('active');
            loginBtn.classList.remove('active');
            registerForm.style.display = 'block';
            loginForm.style.display = 'none';
        });
        
        // Registration Flow
        const regForm = document.getElementById('register-form');
        const btnReg = document.getElementById('btn-submit-reg');
        const toast = document.getElementById('toast');
        const btnText = btnReg.querySelector('.btn-text');

        regForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            btnReg.classList.add('loading');
            btnText.textContent = 'Memproses...';
            
            try {
                const fd = new FormData(regForm);
                const res = await fetch('/register', {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await res.json();
                
                if (res.ok && data.success) {
                    btnReg.classList.remove('loading');
                    btnText.textContent = 'Buat Akun Vendor';
                    toast.classList.add('show');
                    
                    // Hide toast after 4s
                    setTimeout(() => {
                        toast.classList.remove('show');
                    }, 4000);
                } else {
                    alert(data.message || 'Terjadi kesalahan saat mendaftar.');
                    btnReg.classList.remove('loading');
                    btnText.textContent = 'Buat Akun Vendor';
                }
            } catch (error) {
                console.error(error);
                alert('Gagal menyambung ke server.');
                btnReg.classList.remove('loading');
                btnText.textContent = 'Buat Akun Vendor';
            }
        });
        
        const closeBtn = document.querySelector('.close-widget');
        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                document.querySelector('.whatsapp-widget').style.display = 'none';
            });
        }
    </script>
</body>
</html>

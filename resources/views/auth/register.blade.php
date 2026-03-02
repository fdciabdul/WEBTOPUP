<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar - {{ config('app.name') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #0033AA;
            --primary-dark: #002288;
            --success: #25D366;
            --danger: #EF4444;
            --warning: #F59E0B;
            --bg-body: #F1F5F9;
            --surface: #FFFFFF;
            --text-main: #1E293B;
            --text-sub: #64748B;
            --border: #E2E8F0;
            --card-width: 480px;
            --input-height: 52px;
            --radius: 24px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            background: linear-gradient(-45deg, #F1F5F9, #E2E8F0, #F8FAFC, #FFFFFF);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--text-main);
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .card {
            background: var(--surface);
            width: 100%;
            max-width: var(--card-width);
            border-radius: var(--radius);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08);
            padding: 40px 35px;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            animation: slideUpFade 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @media (max-width: 480px) {
            .card {
                padding: 30px 20px;
                border-radius: 20px;
            }
            .logo-modern { font-size: 60px; }
        }

        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header-section {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
            text-decoration: none;
        }

        .logo-modern {
            font-size: 80px;
            background: linear-gradient(135deg, var(--primary), #3366CC);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 4px 6px rgba(0, 51, 170, 0.2));
            animation: pulseLogo 4s ease-in-out infinite;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .logo-modern:hover { transform: scale(1.05); }

        @keyframes pulseLogo {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 4px 6px rgba(0, 51, 170, 0.2)); }
            50% { transform: scale(1.05); filter: drop-shadow(0 8px 12px rgba(0, 51, 170, 0.3)); }
        }

        /* Nav Tabs */
        .nav-tabs {
            display: flex;
            background: #F8FAFC;
            padding: 5px;
            border-radius: 14px;
            margin-bottom: 24px;
            border: 1px solid var(--border);
        }

        .nav-item {
            flex: 1;
            text-align: center;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-sub);
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .nav-item.active {
            background: var(--surface);
            color: var(--primary);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            font-weight: 700;
            transform: scale(1.02);
        }

        .alert {
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-size: 12px;
            font-weight: 500;
        }

        .alert-danger {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }

        .alert ul { margin: 0; padding-left: 16px; }

        .input-group { margin-bottom: 16px; position: relative; }

        .input-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--text-main);
            transition: color 0.2s;
        }

        .input-wrapper { position: relative; }

        .form-input {
            width: 100%;
            height: var(--input-height);
            padding: 0 15px 0 46px;
            border: 2px solid #F1F5F9;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-main);
            background: #F8FAFC;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            background: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(0, 51, 170, 0.1);
        }

        .input-group:focus-within .input-label { color: var(--primary); }
        .input-group:focus-within .icon-left { color: var(--primary); }

        .icon-left {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 16px;
            transition: 0.3s;
        }

        .toggle-pass {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            cursor: pointer;
            transition: 0.2s;
        }
        .toggle-pass:hover { color: var(--text-main); }

        .note {
            font-size: 11px;
            color: var(--text-sub);
            margin-top: 4px;
            margin-left: 2px;
            opacity: 0.8;
        }

        .error-text {
            color: var(--danger);
            font-size: 11px;
            margin-top: 4px;
            display: block;
            font-weight: 500;
        }

        /* Password Strength Meter */
        .strength-container {
            margin-top: 8px;
            display: none;
        }
        .strength-bar-bg {
            width: 100%;
            height: 4px;
            background: #E2E8F0;
            border-radius: 2px;
            overflow: hidden;
        }
        .strength-bar-fill {
            height: 100%;
            width: 0%;
            background: var(--danger);
            transition: width 0.3s ease, background 0.3s ease;
        }
        .strength-text {
            font-size: 11px;
            font-weight: 600;
            margin-top: 4px;
            text-align: right;
            color: var(--text-sub);
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 20px;
            margin-top: 10px;
            padding: 0 2px;
        }

        .checkbox-group input[type="checkbox"] {
            margin-top: 3px;
            min-width: 16px;
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: var(--primary);
            border-radius: 4px;
        }

        .checkbox-group label {
            font-size: 12px;
            color: var(--text-sub);
            line-height: 1.5;
            cursor: pointer;
            font-weight: 500;
            user-select: none;
        }

        .checkbox-group label:hover { color: var(--text-main); }

        .btn {
            width: 100%;
            height: var(--input-height);
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }

        .btn:active { transform: scale(0.98); }

        .btn-primary {
            background: var(--primary);
            color: white;
            margin-top: 5px;
            box-shadow: 0 4px 12px rgba(0, 51, 170, 0.25);
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 51, 170, 0.35);
        }

        .static-footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px dashed var(--border);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        .footer-text {
            font-size: 13px;
            color: var(--text-sub);
        }

        .footer-text a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
        }

        .footer-text a:hover { text-decoration: underline; }

        .btn-wa {
            background: var(--success);
            color: white;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.2);
            width: 100%;
        }
        .btn-wa:hover {
            background: #1ebc57;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 211, 102, 0.3);
        }

        .ip-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }
        .monitor-header {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748B;
            font-weight: 600;
            font-size: 13px;
        }
        .monitor-header i { color: var(--primary); animation: pulse 2s infinite; }
        @keyframes pulse { 0% { opacity: 0.6; } 50% { opacity: 1; } 100% { opacity: 0.6; } }
        .ip-row { display: flex; align-items: center; gap: 8px; }
        .ip-label-text { color: #64748B; font-size: 13px; font-weight: 500; }
        .ip-pill-box {
            background-color: #E2E8F0;
            padding: 6px 14px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .flag-img { width: 20px; height: auto; border-radius: 2px; }
        .ip-number { font-family: 'Courier New', monospace; font-weight: 700; font-size: 14px; color: #1E293B; }
        .monitor-disclaimer { font-size: 11px; color: #94A3B8; margin-top: 2px; }

        .back-home {
            display: flex;
            justify-content: center;
            margin-top: 12px;
        }
        .back-home a {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-sub);
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-home a:hover { color: var(--primary); }
    </style>
</head>
<body>

    <div class="card">
        <a href="{{ route('home') }}" class="header-section">
            <i class="fas fa-fingerprint logo-modern"></i>
        </a>

        <!-- Nav Tabs -->
        <div class="nav-tabs">
            <a href="{{ route('login') }}" class="nav-item">Masuk</a>
            <a href="{{ route('register') }}" class="nav-item active">Daftar</a>
            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="nav-item">Pemulihan</a>
            @endif
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="input-group">
                <label class="input-label">Nama Lengkap</label>
                <div class="input-wrapper">
                    <i class="fas fa-user icon-left"></i>
                    <input type="text" name="name" class="form-input" value="{{ old('name') }}" placeholder="Nama Lengkap" maxlength="25" required autofocus>
                </div>
                @error('name')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="input-group">
                <label class="input-label">Email Aktif</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope icon-left"></i>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="username@email.com" maxlength="40" required>
                </div>
                @error('email')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="input-group">
                <label class="input-label">No WhatsApp</label>
                <div class="input-wrapper">
                    <i class="fab fa-whatsapp icon-left"></i>
                    <input type="tel" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="08xxxxxxxx" maxlength="15" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                </div>
                @error('phone')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="input-group">
                <label class="input-label">Password Baru</label>
                <div class="input-wrapper">
                    <i class="fas fa-key icon-left"></i>
                    <input type="password" name="password" id="password" class="form-input" placeholder="Kombinasi Kuat" maxlength="20" oninput="checkStrength(this.value)" required>
                    <i class="fas fa-eye toggle-pass" onclick="togglePassword('password', this)"></i>
                </div>
                <div class="strength-container" id="strength-box">
                    <div class="strength-bar-bg">
                        <div class="strength-bar-fill" id="strength-fill"></div>
                    </div>
                    <div class="strength-text" id="strength-text"></div>
                </div>
                <div class="note">*8-20 Karakter (Huruf + Angka + Simbol)</div>
                @error('password')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <input type="hidden" name="password_confirmation" id="password_confirmation_hidden">

            <div class="checkbox-group">
                <input type="checkbox" id="reg-terms" name="terms" required>
                <label for="reg-terms">Dengan mendaftar saya telah setuju dengan segala <a href="#" style="color: var(--primary); text-decoration: none; font-weight: 700;">syarat dan ketentuan</a> yang berlaku di website ini.</label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus"></i>
                Daftar Sekarang
            </button>
        </form>

        <!-- Footer -->
        <div class="static-footer">
            <a href="https://wa.me/6282210109289" target="_blank" class="btn btn-wa">
                <i class="fab fa-whatsapp" style="font-size: 18px;"></i> Hubungi Admin
            </a>

            <div class="ip-container">
                <div class="monitor-header">
                    <i class="fas fa-shield-alt"></i> Secured System Monitor
                </div>
                <div class="ip-row">
                    <span class="ip-label-text">Your IP:</span>
                    <div class="ip-pill-box">
                        <img id="flag-icon" class="flag-img" src="" alt="" style="display:none;">
                        <span class="ip-number" id="user-ip">Scanning...</span>
                    </div>
                </div>
                <div class="monitor-disclaimer">All activities are logged for security purposes.</div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Auto-sync password confirmation
        document.getElementById('password').addEventListener('input', function() {
            document.getElementById('password_confirmation_hidden').value = this.value;
        });

        function checkStrength(password) {
            const meter = document.getElementById('strength-box');
            const fill = document.getElementById('strength-fill');
            const text = document.getElementById('strength-text');

            if (password.length === 0) {
                meter.style.display = 'none';
                return;
            }

            meter.style.display = 'block';

            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[\W_]+/)) strength++;

            if (password.length < 8) {
                fill.style.width = '30%';
                fill.style.background = '#EF4444';
                text.innerText = 'Rendah';
                text.style.color = '#EF4444';
            } else if (password.length >= 8 && strength < 4) {
                fill.style.width = '60%';
                fill.style.background = '#F59E0B';
                text.innerText = 'Sedang';
                text.style.color = '#F59E0B';
            } else {
                fill.style.width = '100%';
                fill.style.background = '#10B981';
                text.innerText = 'Kuat';
                text.style.color = '#10B981';
            }
        }

        // IP Detection
        fetch('https://ipwho.is/')
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('user-ip').innerText = data.ip;
                    if (data.country_code) {
                        const flag = document.getElementById('flag-icon');
                        flag.src = 'https://flagcdn.com/w40/' + data.country_code.toLowerCase() + '.png';
                        flag.alt = data.country;
                        flag.style.display = 'block';
                    }
                } else {
                    document.getElementById('user-ip').innerText = 'Unavailable';
                }
            })
            .catch(() => document.getElementById('user-ip').innerText = 'Error');
    </script>
</body>
</html>

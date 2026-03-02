<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemulihan Akun - {{ config('app.name') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #0033AA;
            --primary-dark: #002288;
            --success: #25D366;
            --danger: #EF4444;
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
            .card { padding: 30px 20px; border-radius: 20px; }
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
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

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
        .alert-danger { background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; }
        .alert-success { background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; }
        .alert ul { margin: 0; padding-left: 16px; }

        .method-label {
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .method-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
        }

        .method-tab {
            flex: 1;
            padding: 12px;
            border-radius: 12px;
            border: 2px solid var(--border);
            background: #F8FAFC;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-sub);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .method-tab.active {
            border-color: var(--primary);
            background: rgba(0, 51, 170, 0.05);
            color: var(--primary);
            box-shadow: 0 2px 8px rgba(0, 51, 170, 0.15);
        }

        .method-tab:hover:not(.active) {
            border-color: #CBD5E1;
            background: #FFFFFF;
        }

        .input-group { margin-bottom: 16px; position: relative; }

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

        .icon-left {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 16px;
            transition: 0.3s;
        }

        .input-group:focus-within .icon-left { color: var(--primary); }

        .error-text { color: var(--danger); font-size: 11px; margin-top: 4px; display: block; font-weight: 500; }

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

        .method-form { display: none; }
        .method-form.active { display: block; }
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
            <a href="{{ route('register') }}" class="nav-item">Daftar</a>
            <a href="{{ route('password.request') }}" class="nav-item active">Pemulihan</a>
        </div>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="method-label">Metode Pemulihan:</div>

        <!-- Method Tabs -->
        <div class="method-tabs">
            <div class="method-tab active" onclick="switchMethod('email')">
                <i class="fas fa-envelope"></i> Email
            </div>
            <div class="method-tab" onclick="switchMethod('whatsapp')">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </div>
        </div>

        <!-- Email Method -->
        <div class="method-form active" id="form-email">
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="input-group">
                    <div class="input-wrapper">
                        <i class="fas fa-envelope icon-left"></i>
                        <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="username@email.com" maxlength="40" required autofocus>
                    </div>
                    @error('email')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    Kirim Kode Akses
                </button>
            </form>
        </div>

        <!-- WhatsApp Method -->
        <div class="method-form" id="form-whatsapp">
            <div class="input-group">
                <div class="input-wrapper">
                    <i class="fab fa-whatsapp icon-left" style="color: #25D366;"></i>
                    <input type="tel" id="wa-number" class="form-input" placeholder="08xxxxxxxxxx" maxlength="15" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                </div>
            </div>

            <button type="button" class="btn btn-primary" onclick="sendWhatsApp()">
                <i class="fas fa-paper-plane"></i>
                Kirim Kode Akses
            </button>
        </div>

        <!-- Footer -->
        <div class="static-footer">
            <a href="https://wa.me/6282210109289" target="_blank" class="btn btn-wa">
                <i class="fab fa-whatsapp" style="font-size: 18px;"></i> Hubungi Admin
            </a>

            <div class="monitor-header">
                <i class="fas fa-shield-alt"></i> Secured System Monitor
            </div>
        </div>
    </div>

    <script>
        function switchMethod(method) {
            document.querySelectorAll('.method-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.method-form').forEach(f => f.classList.remove('active'));

            if (method === 'email') {
                document.querySelectorAll('.method-tab')[0].classList.add('active');
                document.getElementById('form-email').classList.add('active');
            } else {
                document.querySelectorAll('.method-tab')[1].classList.add('active');
                document.getElementById('form-whatsapp').classList.add('active');
            }
        }

        function sendWhatsApp() {
            const phone = document.getElementById('wa-number').value.trim();
            if (!phone || phone.length < 10) {
                alert('Masukkan nomor WhatsApp yang valid');
                return;
            }
            // Redirect to WhatsApp admin with recovery request
            const message = encodeURIComponent('Halo Admin, saya ingin melakukan pemulihan akun. Nomor WA saya: ' + phone);
            window.open('https://wa.me/6282210109289?text=' + message, '_blank');
        }
    </script>
</body>
</html>

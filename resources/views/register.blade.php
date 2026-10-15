<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Admin | 3GFood</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    @vite(['resources/css/app.css'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: radial-gradient(circle at center, #780d20 0%, #46040f 100%);
            color: #f8fafc; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            padding: 80px 16px 30px;
        }
        @media(max-width: 480px) {
            .auth-container { padding: 28px 20px; border-radius: 20px; }
            .auth-top-bar { top: 14px; left: 14px; right: 14px; }
        }
        /* Decorative blur orbs */
        .orb-1 { position: absolute; top: -10%; left: -10%; width: 50vw; height: 50vw; background: rgba(229, 0, 39, 0.22); filter: blur(100px); border-radius: 50%; z-index: 0; }
        .orb-2 { position: absolute; bottom: -10%; right: -10%; width: 40vw; height: 40vw; background: rgba(120, 13, 32, 0.4); filter: blur(100px); border-radius: 50%; z-index: 0; }

        .auth-top-bar {
            position: absolute;
            top: 24px;
            left: 24px;
            right: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 20;
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
            padding: 40px;
            background: rgba(30, 4, 8, 0.72);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            z-index: 10;
        }
        .brand { text-align: center; margin-bottom: 28px; }
        .brand-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #e50027, #b3001a);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            color: #fff;
            font-size: 20px;
            box-shadow: 0 8px 20px rgba(229, 0, 39, 0.45);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        .brand h1 { font-family: 'Outfit', sans-serif; font-size: 34px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
        .brand h1 span { 
            background: linear-gradient(135deg, #ff4d6d, #e50027);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .brand p { color: #fecdd3; font-size: 13px; margin-top: 4px; font-weight: 500; }

        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 12px; font-weight: 700; color: #f1f5f9; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
        .form-control {
            width: 100%;
            padding: 14px 16px;
            background: rgba(15, 2, 4, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            color: #fff;
            font-family: inherit;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus { outline: none; border-color: #e50027; box-shadow: 0 0 0 4px rgba(229, 0, 39, 0.25); background: rgba(15, 2, 4, 0.9); }
        .form-control::placeholder { color: #64748b; }
        
        .btn-submit {
            width: 100%;
            margin-top: 12px;
            padding: 14px;
            background: linear-gradient(135deg, #e50027, #b3001a);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -3px rgba(229, 0, 39, 0.45);
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 15px 25px -3px rgba(229, 0, 39, 0.6); background: linear-gradient(135deg, #f0052d, #c20020); }

        .auth-footer { text-align: center; margin-top: 24px; font-size: 13px; color: #cbd5e1; }
        .auth-footer a { color: #ff4d6d; text-decoration: none; font-weight: 700; transition: color 0.2s; }
        .auth-footer a:hover { color: #ff758f; text-decoration: underline; }

        .error-message { background: rgba(229, 0, 39, 0.15); border: 1px solid rgba(229, 0, 39, 0.35); color: #fecdd3; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 22px; text-align: center; font-weight: 500; }

        @media (max-width: 480px) {
            body { padding: 12px; }
            .auth-container { border-radius: 24px; padding: 28px 20px; }
            .orb-1, .orb-2 { filter: blur(70px); }
        }
    </style>
</head>
<body>
    <div class="orb-1"></div>
    <div class="orb-2"></div>

    <div class="auth-top-bar">
        <a href="{{ url('/') }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: #fff;">
            <img src="{{ asset('images/logo.jpg') }}" alt="3GFood" style="width: 34px; height: 34px; border-radius: 10px; object-fit: contain; background: #faf6f0; border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
            <span style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 16px;">3G<span style="color: #ff4d6d;">Food</span></span>
        </a>
        <a href="{{ route('customer.login') }}" style="display: inline-flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.9); background: rgba(255,255,255,0.1); padding: 7px 16px; border-radius: 99px; backdrop-filter: blur(8px); text-decoration: none; border: 1px solid rgba(255,255,255,0.15);">
            <i class="fas fa-user text-xs text-red-300"></i> Portal Pembeli
        </a>
    </div>
    
    <div class="auth-container">
        <div class="brand">
            <div style="width: 72px; height: 72px; margin: 0 auto 14px; border-radius: 20px; overflow: hidden; border: 2px solid rgba(255,255,255,0.25); box-shadow: 0 10px 25px rgba(229,0,39,0.45); background: #faf6f0; padding: 4px;">
                <img src="{{ asset('images/logo.jpg') }}" alt="3GFood Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 16px;">
            </div>
            <h1>3G<span>Food</span></h1>
            <p>Saveur & Tradition • Pendaftaran Admin Baru</p>
        </div>

        @if($errors->any())
        <div class="error-message">
            <i class="fas fa-circle-exclamation mr-1"></i> {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.register.submit') }}">
            @csrf
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" placeholder="Nama Admin" required autofocus>
            </div>

            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" placeholder="admin@3gfood.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <input id="password" name="password" type="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-submit">Daftarkan Admin Baru</button>
        </form>

        <div class="auth-footer">
            Sudah punya akun admin? <a href="{{ route('login') }}">Masuk di sini</a>
        </div>
    </div>
</body>
</html>

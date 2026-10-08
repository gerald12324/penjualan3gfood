<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Admin | 3GFood</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: radial-gradient(circle at top right, #1e1b4b, #0f172a 40%, #020617 100%);
            color: #e2e8f0; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        /* Decorative blur orbs */
        .orb-1 { position: absolute; top: -10%; left: -10%; width: 50vw; height: 50vw; background: rgba(139, 92, 246, 0.15); filter: blur(100px); border-radius: 50%; z-index: 0; }
        .orb-2 { position: absolute; bottom: -10%; right: -10%; width: 40vw; height: 40vw; background: rgba(16, 185, 129, 0.1); filter: blur(100px); border-radius: 50%; z-index: 0; }

        .auth-container {
            width: 100%;
            max-width: 440px;
            padding: 40px;
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            z-index: 10;
        }
        .brand { text-align: center; margin-bottom: 32px; }
        .brand h1 { font-family: 'Outfit', sans-serif; font-size: 36px; font-weight: 800; color: #fff; letter-spacing: -1px; }
        .brand h1 span { 
            background: linear-gradient(135deg, #34d399, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .brand p { color: #94a3b8; font-size: 14px; margin-top: 6px; }

        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 8px; }
        .form-control {
            width: 100%;
            padding: 14px 16px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #fff;
            font-family: inherit;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15); background: rgba(15, 23, 42, 0.8); }
        .form-control::placeholder { color: #475569; }
        
        .btn-submit {
            width: 100%;
            margin-top: 12px;
            padding: 14px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 15px 20px -3px rgba(16, 185, 129, 0.4); }

        .auth-footer { text-align: center; margin-top: 24px; font-size: 14px; color: #94a3b8; }
        .auth-footer a { color: #34d399; text-decoration: none; font-weight: 600; transition: color 0.2s; }
        .auth-footer a:hover { color: #6ee7b7; text-decoration: underline; }

        .error-message { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #fca5a5; padding: 12px; border-radius: 10px; font-size: 13px; margin-bottom: 24px; text-align: center; }

        @media (max-width: 480px) {
            .auth-container { border-radius: 0; min-height: 100vh; display: flex; flex-direction: column; justify-content: center; border: none; background: transparent; box-shadow: none; padding: 24px; }
            .orb-1, .orb-2 { filter: blur(70px); }
        }
    </style>
</head>
<body>
    <div class="orb-1"></div>
    <div class="orb-2"></div>
    
    <div class="auth-container">
        <div class="brand">
            <h1>3G<span>Food</span></h1>
            <p>Admin Registration</p>
        </div>

        @if($errors->any())
        <div class="error-message">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.register.submit') }}">
            @csrf
            <div class="form-group">
                <label for="name">Full Name</label>
                <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" placeholder="John Doe" required autofocus>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" placeholder="admin@3gfood.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-submit">Register Account</button>
        </form>

        <div class="auth-footer">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
        </div>
    </div>
</body>
</html>

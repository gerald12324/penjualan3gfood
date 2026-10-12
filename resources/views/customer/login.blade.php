<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akun Pembeli | 3GFood</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at center, #780d20 0%, #46040f 100%);
            min-height: 100vh;
        }
        .font-heading {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between items-center p-4 md:p-8">

    <!-- Top Brand Header -->
    <div class="w-full max-w-4xl flex items-center justify-between py-2 text-white/90">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 bg-[#e50027] rounded-full flex items-center justify-center text-white text-xs shadow-md">
                <i class="fas fa-utensils"></i>
            </div>
            <span class="font-black font-heading tracking-wide text-white text-base">3G<span class="text-red-400">Food</span></span>
        </div>
        <div class="inline-flex items-center gap-2 text-xs text-white/80 bg-white/10 px-3.5 py-1.5 rounded-full backdrop-blur-sm">
            <i class="fas fa-user-lock text-red-300"></i>
            <span>Portal Masuk Pembeli</span>
        </div>
    </div>

    <!-- Main Dual-Panel Auth Card -->
    <main class="w-full max-w-4xl my-auto">
        <div class="relative bg-white rounded-[32px] shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[540px] border border-white/10">
            
            <!-- ================= LEFT PANEL (Welcome Area) ================= -->
            <div class="w-full md:w-[42%] bg-gradient-to-br from-[#e50027] via-[#d60024] to-[#b3001a] p-8 md:p-12 flex flex-col items-center justify-center text-center text-white relative select-none">
                <!-- Subtle Decorative Shapes -->
                <div class="absolute -top-12 -left-12 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-12 -right-12 w-40 h-40 bg-black/10 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10">
                    <h1 class="text-3xl md:text-4xl font-black font-heading tracking-wide text-white mb-3">
                        WELCOME!
                    </h1>
                    <p class="text-xs md:text-sm text-white/90 font-medium leading-relaxed max-w-[240px] mx-auto">
                        LOGIN and SIGN IN to securely access your account.
                    </p>
                    <div class="mt-6 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-black/15 text-white/80 text-[11px] font-medium backdrop-blur-sm">
                        <i class="fas fa-shield-alt text-red-300"></i> Akun Pelanggan 3GFood
                    </div>
                </div>

                <!-- Floating Switch Pod for Desktop (centered on the dividing line) -->
                <div class="hidden md:flex absolute -right-[46px] top-1/2 -translate-y-1/2 z-30">
                    <div class="bg-white p-1.5 rounded-2xl shadow-xl border border-gray-100 flex flex-col gap-1.5">
                        <button 
                            id="pill-btn-login" 
                            type="button" 
                            onclick="switchAuthTab('login')" 
                            class="px-5 py-2.5 rounded-xl text-xs font-black font-heading tracking-wider transition-all duration-300 shadow-sm bg-[#e50027] text-white"
                        >
                            LOGIN
                        </button>
                        <button 
                            id="pill-btn-register" 
                            type="button" 
                            onclick="switchAuthTab('register')" 
                            class="px-5 py-2.5 rounded-xl text-xs font-black font-heading tracking-wider transition-all duration-300 text-gray-500 hover:text-gray-900 bg-transparent"
                        >
                            SIGN IN
                        </button>
                    </div>
                </div>
            </div>

            <!-- ================= RIGHT PANEL (Form Area) ================= -->
            <div class="w-full md:w-[58%] bg-white p-8 md:p-12 md:pl-16 flex flex-col justify-center relative">
                
                <!-- Mobile Switch Tabs (Visible on small screens) -->
                <div class="flex md:hidden items-center justify-center mb-6">
                    <div class="bg-gray-100 p-1 rounded-full flex gap-1 w-full max-w-[260px]">
                        <button 
                            id="mobile-tab-login" 
                            type="button" 
                            onclick="switchAuthTab('login')" 
                            class="flex-1 py-2 rounded-full text-xs font-bold text-center transition-all bg-[#e50027] text-white shadow-sm"
                        >
                            LOGIN
                        </button>
                        <button 
                            id="mobile-tab-register" 
                            type="button" 
                            onclick="switchAuthTab('register')" 
                            class="flex-1 py-2 rounded-full text-xs font-bold text-center transition-all text-gray-600 hover:text-gray-900"
                        >
                            SIGN IN
                        </button>
                    </div>
                </div>

                <!-- Top Avatar Icon -->
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-red-100 via-rose-50 to-red-100 border-2 border-red-200/80 flex items-center justify-center mx-auto mb-2.5 shadow-sm text-[#e50027]">
                        <i class="fas fa-user text-2xl"></i>
                    </div>
                    <h2 id="form-heading" class="text-2xl font-black font-heading text-gray-900 tracking-wider">
                        LOGIN
                    </h2>
                </div>

                <!-- Flash Feedback / Error Messages -->
                @if(session('success'))
                    <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-xs flex items-center gap-2.5">
                        <i class="fas fa-check-circle text-green-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->has('login_error'))
                    <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs flex items-center gap-2.5">
                        <i class="fas fa-exclamation-circle text-[#e50027]"></i>
                        <span>{{ $errors->first('login_error') }}</span>
                    </div>
                @endif

                <!-- ================= TAB 1: LOGIN FORM ================= -->
                <div id="tab-content-login">
                    <form action="{{ route('customer.login.submit') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="_form_type" value="login">

                        <!-- Email Input -->
                        <div>
                            <div class="relative">
                                <label for="login-email" class="absolute -top-2.5 left-3 bg-white px-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-600">
                                    Email
                                </label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    id="login-email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    placeholder="nama@gmail.com"
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm pr-11 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#e50027] focus:border-transparent transition"
                                >
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[#e50027] text-sm pointer-events-none">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            @if($errors->has('email') && old('_form_type') !== 'register')
                                <p class="text-red-500 text-[11px] mt-1">{{ $errors->first('email') }}</p>
                            @endif
                        </div>

                        <!-- Password Input -->
                        <div>
                            <div class="relative">
                                <label for="login-password" class="absolute -top-2.5 left-3 bg-white px-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-600">
                                    Password
                                </label>
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="login-password" 
                                    required 
                                    placeholder="••••••••"
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm pr-11 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#e50027] focus:border-transparent transition"
                                >
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                            @if($errors->has('password') && old('_form_type') !== 'register')
                                <p class="text-red-500 text-[11px] mt-1">{{ $errors->first('password') }}</p>
                            @endif
                        </div>

                        <!-- Forgot Password Link & Remember Me -->
                        <div class="flex items-center justify-between text-xs pt-0.5">
                            <label class="flex items-center gap-1.5 cursor-pointer text-gray-600 select-none">
                                <input type="checkbox" name="remember" class="w-3.5 h-3.5 text-[#e50027] rounded border-gray-300 focus:ring-[#e50027]">
                                <span>Ingat saya</span>
                            </label>
                            <a href="javascript:void(0)" onclick="alert('Silakan hubungi WhatsApp Admin jika Anda lupa kata sandi akun.')" class="text-gray-400 hover:text-[#e50027] transition font-medium">
                                Forgot Password?
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button 
                                type="submit" 
                                class="w-full py-3.5 px-6 bg-[#e50027] hover:bg-red-700 text-white font-extrabold rounded-full shadow-lg shadow-red-200 active:scale-[0.99] transition-all font-heading tracking-wider text-sm uppercase"
                            >
                                LOGIN
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ================= TAB 2: REGISTER / SIGN IN FORM ================= -->
                <div id="tab-content-register" class="hidden">
                    <form action="{{ route('customer.register.submit') }}" method="POST" class="space-y-3.5">
                        @csrf
                        <input type="hidden" name="_form_type" value="register">

                        <!-- Nama Lengkap -->
                        <div>
                            <div class="relative">
                                <label for="reg-name" class="absolute -top-2.5 left-3 bg-white px-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-600">
                                    Nama Lengkap
                                </label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    id="reg-name" 
                                    value="{{ old('name') }}" 
                                    required 
                                    placeholder="Budi Santoso"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm pr-11 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#e50027] focus:border-transparent transition"
                                >
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>
                            @if($errors->has('name'))
                                <p class="text-red-500 text-[11px] mt-1">{{ $errors->first('name') }}</p>
                            @endif
                        </div>

                        <!-- Email -->
                        <div>
                            <div class="relative">
                                <label for="reg-email" class="absolute -top-2.5 left-3 bg-white px-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-600">
                                    Email (Gmail)
                                </label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    id="reg-email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    placeholder="nama@gmail.com"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm pr-11 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#e50027] focus:border-transparent transition"
                                >
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[#e50027] text-sm pointer-events-none">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            @if($errors->has('email') && old('_form_type') === 'register')
                                <p class="text-red-500 text-[11px] mt-1">{{ $errors->first('email') }}</p>
                            @endif
                        </div>

                        <!-- Phone / WhatsApp -->
                        <div>
                            <div class="relative">
                                <label for="reg-phone" class="absolute -top-2.5 left-3 bg-white px-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-600">
                                    No. WhatsApp / HP
                                </label>
                                <input 
                                    type="tel" 
                                    name="phone" 
                                    id="reg-phone" 
                                    value="{{ old('phone') }}" 
                                    placeholder="08123456789"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm pr-11 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#e50027] focus:border-transparent transition"
                                >
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-emerald-500 text-sm pointer-events-none">
                                    <i class="fab fa-whatsapp"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <div class="relative">
                                <label for="reg-password" class="absolute -top-2.5 left-3 bg-white px-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-600">
                                    Password Akun
                                </label>
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="reg-password" 
                                    required 
                                    placeholder="Min. 6 karakter bebas"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm pr-11 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#e50027] focus:border-transparent transition"
                                >
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                            @if($errors->has('password') && old('_form_type') === 'register')
                                <p class="text-red-500 text-[11px] mt-1">{{ $errors->first('password') }}</p>
                            @endif
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <div class="relative">
                                <label for="reg-password-confirm" class="absolute -top-2.5 left-3 bg-white px-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-600">
                                    Ulangi Password
                                </label>
                                <input 
                                    type="password" 
                                    name="password_confirmation" 
                                    id="reg-password-confirm" 
                                    required 
                                    placeholder="Ketik ulang password"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm pr-11 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#e50027] focus:border-transparent transition"
                                >
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-1">
                            <button 
                                type="submit" 
                                class="w-full py-3.5 px-6 bg-[#e50027] hover:bg-red-700 text-white font-extrabold rounded-full shadow-lg shadow-red-200 active:scale-[0.99] transition-all font-heading tracking-wider text-sm uppercase"
                            >
                                SIGN IN
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Social Login Footer (Matches Image 2) -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-400 mb-3">Or login with</p>
                    <div class="flex items-center justify-center gap-3">
                        <button type="button" onclick="alert('Fitur login Google akan segera hadir!')" class="w-9 h-9 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center text-red-500 hover:scale-105 transition-transform" title="Masuk dengan Google">
                            <i class="fab fa-google text-sm"></i>
                        </button>
                        <button type="button" onclick="alert('Fitur login Facebook akan segera hadir!')" class="w-9 h-9 rounded-full bg-[#1877f2] shadow-sm flex items-center justify-center text-white hover:scale-105 transition-transform" title="Masuk dengan Facebook">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Footer Copyright -->
    <div class="py-2 text-center text-xs text-white/50">
        &copy; {{ date('Y') }} 3GFood Culinary Delivery. Hak Cipta Dilindungi.
    </div>

    <!-- Client-Side Tab Switching Script -->
    <script>
        function switchAuthTab(tabName) {
            const heading = document.getElementById('form-heading');
            const loginContent = document.getElementById('tab-content-login');
            const registerContent = document.getElementById('tab-content-register');

            // Desktop Buttons
            const pillLogin = document.getElementById('pill-btn-login');
            const pillRegister = document.getElementById('pill-btn-register');

            // Mobile Buttons
            const mobLogin = document.getElementById('mobile-tab-login');
            const mobRegister = document.getElementById('mobile-tab-register');

            if (tabName === 'register') {
                heading.innerText = 'SIGN IN';
                loginContent.classList.add('hidden');
                registerContent.classList.remove('hidden');

                // Desktop Pill styling
                pillLogin.className = 'px-5 py-2.5 rounded-xl text-xs font-black font-heading tracking-wider transition-all duration-300 text-gray-500 hover:text-gray-900 bg-transparent';
                pillRegister.className = 'px-5 py-2.5 rounded-xl text-xs font-black font-heading tracking-wider transition-all duration-300 shadow-sm bg-[#e50027] text-white';

                // Mobile Tab styling
                if (mobLogin && mobRegister) {
                    mobLogin.className = 'flex-1 py-2 rounded-full text-xs font-bold text-center transition-all text-gray-600 hover:text-gray-900';
                    mobRegister.className = 'flex-1 py-2 rounded-full text-xs font-bold text-center transition-all bg-[#e50027] text-white shadow-sm';
                }
            } else {
                heading.innerText = 'LOGIN';
                loginContent.classList.remove('hidden');
                registerContent.classList.add('hidden');

                // Desktop Pill styling
                pillLogin.className = 'px-5 py-2.5 rounded-xl text-xs font-black font-heading tracking-wider transition-all duration-300 shadow-sm bg-[#e50027] text-white';
                pillRegister.className = 'px-5 py-2.5 rounded-xl text-xs font-black font-heading tracking-wider transition-all duration-300 text-gray-500 hover:text-gray-900 bg-transparent';

                // Mobile Tab styling
                if (mobLogin && mobRegister) {
                    mobLogin.className = 'flex-1 py-2 rounded-full text-xs font-bold text-center transition-all bg-[#e50027] text-white shadow-sm';
                    mobRegister.className = 'flex-1 py-2 rounded-full text-xs font-bold text-center transition-all text-gray-600 hover:text-gray-900';
                }
            }
        }

        // Initialize active tab based on server param or error state
        document.addEventListener('DOMContentLoaded', function() {
            @php
                $initialTab = session('active_tab', $activeTab ?? 'login');
                if (old('_form_type') === 'register' || $errors->has('name') || $errors->has('password_confirmation')) {
                    $initialTab = 'register';
                }
            @endphp
            switchAuthTab('{{ $initialTab }}');
        });
    </script>
</body>
</html>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun Pembeli | 3GFood</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fdfbf7;
        }
        .font-heading {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between text-gray-900 bg-[#fdfbf7]">

    <!-- Header bar -->
    <header class="py-6 px-6 md:px-16 flex items-center justify-between border-b border-gray-100 bg-white">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
            <div class="relative flex items-center justify-center w-10 h-10 bg-[#e50027] rounded-full shadow-md shadow-red-200">
                <i class="fas fa-utensils text-white text-base"></i>
            </div>
            <span class="text-xl font-extrabold font-heading text-gray-900 leading-tight">
                3G<span class="text-[#e50027]">Food</span>
            </span>
        </a>
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-[#e50027] transition-colors">
            <i class="fas fa-arrow-left"></i> Kembali ke Menu
        </a>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center p-6 md:p-10">
        <div class="w-full max-w-lg bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 p-8 md:p-10 relative overflow-hidden">
            <!-- Decorative Accent Top -->
            <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-[#e50027] via-orange-500 to-[#e50027]"></div>

            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-red-50 text-[#e50027] mb-3">
                    <i class="fas fa-user-plus text-2xl"></i>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold font-heading text-gray-900 tracking-tight">Daftar Akun Pembeli</h1>
                <p class="text-sm text-gray-500 mt-2">Daftar sekarang untuk kemudahan pemesanan cepat dan simpan riwayat pesanan.</p>
            </div>

            <!-- Helpful security note for customer -->
            <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-3">
                <i class="fas fa-info-circle text-amber-600 text-base mt-0.5"></i>
                <div>
                    <span class="font-bold">Tips Keamanan:</span> Masukkan alamat email aktif Anda. Password di bawah bebas dibuat sendiri khusus untuk akun 3GFood ini (<strong>bukan</strong> password email asli Anda).
                </div>
            </div>

            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm">
                    <div class="flex items-center gap-2 font-semibold mb-1">
                        <i class="fas fa-exclamation-circle text-[#e50027]"></i>
                        <span>Pendaftaran Belum Lengkap</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('customer.register.submit') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fas fa-user"></i>
                        </span>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name') }}" 
                            required 
                            placeholder="Contoh: Budi Santoso"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#e50027] focus:border-transparent transition"
                        >
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Alamat Email (Gmail) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email') }}" 
                            required 
                            placeholder="budi@gmail.com"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#e50027] focus:border-transparent transition"
                        >
                    </div>
                </div>

                <!-- Nomor HP / WhatsApp (Opsional) -->
                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Nomor WhatsApp / HP <span class="text-gray-400 font-normal lowercase">(opsional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fab fa-whatsapp"></i>
                        </span>
                        <input 
                            type="tel" 
                            name="phone" 
                            id="phone" 
                            value="{{ old('phone') }}" 
                            placeholder="081234567890"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#e50027] focus:border-transparent transition"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Buat Password Akun <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            required 
                            placeholder="Minimal 6 karakter"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#e50027] focus:border-transparent transition"
                        >
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Ulangi Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation" 
                            required 
                            placeholder="Ketik ulang password"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#e50027] focus:border-transparent transition"
                        >
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full py-4 px-6 bg-[#e50027] hover:bg-red-700 text-white font-bold rounded-2xl shadow-lg shadow-red-200 active:scale-[0.99] transition-all flex items-center justify-center gap-2 font-heading tracking-wide"
                    >
                        <i class="fas fa-check-circle"></i> Selesaikan Pendaftaran
                    </button>
                </div>
            </form>

            <!-- Bottom prompt to Login -->
            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-sm text-gray-600">
                    Sudah memiliki akun pembeli? 
                    <a href="{{ route('customer.login') }}" class="font-bold text-[#e50027] hover:underline">
                        Masuk di Sini
                    </a>
                </p>
            </div>
        </div>
    </main>

    <!-- Footer note -->
    <footer class="py-6 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} 3GFood Culinary Delivery. Hak Cipta Dilindungi.
    </footer>
</body>
</html>

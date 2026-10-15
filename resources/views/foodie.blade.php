<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $screens[$screen] ?? '3GFood' }} | Food Delivery</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    <link rel="preload" as="image" href="{{ asset('images/logo.jpg') }}">
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-[#ede5d8] text-[#1c1917]">

    <!-- Navbar -->
    <nav class="flex items-center justify-between px-6 py-4 bg-[#f4ece1]/95 backdrop-blur-md md:px-16 w-full fixed top-0 z-50 border-b border-[#d5c7b2] shadow-[0_2px_15px_rgba(40,20,10,0.03)]">
        <!-- Official Logo -->
        <a href="{{ url('/?screen=home') }}" class="flex items-center gap-3 group">
            <img src="{{ asset('images/logo.jpg') }}" alt="3GFood Logo" class="w-11 h-11 object-contain rounded-2xl shadow-sm border border-[#d5c7b2] bg-[#f4ece1] group-hover:scale-105 transition-transform duration-300">
            <div>
                <span class="text-xl font-black font-heading leading-none tracking-tight block text-[#1c1917]">3G<span class="text-[#e50027]">Food</span></span>
                <span class="text-[10px] text-[#780d20] font-semibold tracking-wider uppercase block mt-0.5">Saveur & Tradition</span>
            </div>
        </a>

        <!-- Links -->
        <div class="hidden md:flex items-center gap-8 font-semibold text-sm">
            <a href="{{ url('/?screen=home') }}" class="{{ $screen === 'home' ? 'text-[#e50027]' : 'hover:text-[#e50027] transition-colors' }}">Home</a>
            <a href="{{ url('/?screen=home#menu-section') }}" class="hover:text-[#e50027] transition-colors">Menu</a>
            <a href="{{ url('/?screen=track') }}" class="{{ $screen === 'track' ? 'text-[#e50027]' : 'hover:text-[#e50027] transition-colors' }}">Track Order</a>
            <a href="{{ url('/?screen=settings') }}" class="{{ $screen === 'settings' ? 'text-[#e50027]' : 'hover:text-[#e50027] transition-colors' }}">Pengaturan</a>
            <a href="{{ url('/?screen=feedback') }}" class="{{ $screen === 'feedback' ? 'text-[#e50027]' : 'hover:text-[#e50027] transition-colors' }}">Contact</a>
        </div>

        <!-- Icons, Cart & Customer Auth -->
        <div class="flex items-center gap-4 md:gap-5">
            <a href="{{ url('/?screen=cart') }}" class="relative text-stone-800 hover:text-[#e50027] transition-colors p-1">
                <i class="fas fa-shopping-bag text-lg"></i>
                <span id="nav-cart-badge" class="absolute -top-1 -right-2 flex items-center justify-center w-[18px] h-[18px] text-[10px] font-bold text-white bg-[#e50027] border-2 border-[#f4ece1] rounded-full transition-transform {{ $cartCount > 0 ? '' : 'hidden' }}">{{ $cartCount }}</span>
            </a>

            <!-- Customer Account Area (Khusus Informasi Akun) -->
            <div class="pl-3 border-l border-[#d5c7b2]">
                @if($currentCustomer)
                    <!-- State: Logged In -> Tombol Khusus Informasi Akun -->
                    <button type="button" 
                            onclick="openAccountModal()" 
                            id="btn-account-info" 
                            class="flex items-center gap-2.5 py-1.5 px-3.5 rounded-full border border-[#d5c7b2] hover:border-[#e50027] bg-[#f4ece1] hover:bg-red-50/60 transition-all shadow-sm cursor-pointer group active:scale-95" 
                            title="Klik untuk Informasi Akun">
                        @if($currentCustomer->avatar_url)
                            <img src="{{ $currentCustomer->avatar_url }}" alt="{{ $currentCustomer->name }}" class="w-8 h-8 rounded-full object-cover shadow-sm ring-1 ring-red-200">
                        @else
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#e50027] to-[#ff4d6d] text-white flex items-center justify-center text-xs font-extrabold shadow-sm shadow-red-200">
                                {{ strtoupper(substr($currentCustomer->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="text-left hidden sm:block">
                            <span class="text-[10px] text-stone-400 font-semibold uppercase tracking-wider block leading-none">Informasi Akun</span>
                            <span class="text-xs font-bold text-stone-800 max-w-[110px] md:max-w-[140px] truncate block leading-tight mt-0.5 group-hover:text-[#e50027] transition-colors">
                                {{ $currentCustomer->name }}
                            </span>
                        </div>
                        <i class="fas fa-id-card text-xs text-stone-400 group-hover:text-[#e50027] transition-colors ml-0.5"></i>
                    </button>
                @else
                    <!-- State: Guest (Akun Pengguna) -->
                    <a href="{{ route('customer.login') }}" class="flex items-center gap-2 py-1.5 px-3.5 rounded-full border border-[#d5c7b2] hover:border-[#e50027] bg-[#f4ece1] hover:bg-red-50/60 transition-all shadow-sm group" title="Buka Akun Pembeli">
                        <div class="w-7 h-7 rounded-full bg-red-50 group-hover:bg-[#e50027] text-[#e50027] group-hover:text-white flex items-center justify-center text-xs font-bold transition-colors">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="text-xs font-bold text-stone-800 group-hover:text-[#e50027] transition-colors leading-tight">
                            Masuk
                        </span>
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <!-- Main Wrapper -->
    <main id="app-main" class="pt-[88px] min-h-screen pb-28 md:pb-12 transition-opacity duration-150">
        
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-6 mt-4">
                <div class="p-4 text-sm text-green-800 rounded-2xl bg-green-50 border border-green-200 flex items-center justify-between shadow-sm" role="alert">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @if(!$shopOpen)
            <div class="max-w-7xl mx-auto px-6 mt-6">
                <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50" role="alert">
                    <span class="font-medium">⚠️ Toko sedang tutup saat ini.</span> Jam operasional: {{ $shopSettings->jam_buka ?? '08:00' }} - {{ $shopSettings->jam_tutup ?? '21:00' }}
                </div>
            </div>
        @endif

        @if($screen === 'home')
            <!-- Hero Section -->
            <section class="relative flex flex-col md:flex-row items-center justify-between overflow-hidden bg-[#ede5d8]">
                <!-- Background Pattern (Subtle) -->
                <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M54.627 0l.83.83-53.797 53.797-.83-.83L54.627 0zM27.5 31c-1.933 0-3.5-1.567-3.5-3.5s1.567-3.5 3.5-3.5 3.5 1.567 3.5 3.5-1.567 3.5-3.5 3.5z\' fill=\'%23000000\' fill-opacity=\'1\' fill-rule=\'evenodd\'/%3E%3C/svg%3E');"></div>
                
                <!-- Left Content -->
                <div class="relative z-10 w-full md:w-1/2 px-6 md:pl-20 py-16 md:py-32">
                    <h1 class="text-[52px] md:text-[68px] font-extrabold font-heading leading-[1.1] mb-4 text-[#1c1917]">
                        We Deliver <br>The Taste Of Life
                    </h1>
                    <p class="text-stone-600 text-lg mb-10 font-medium">Get It Delivered Right To Your Door!</p>
                    
                    <a href="#menu-section" class="inline-flex items-center justify-center px-10 py-4 text-sm font-bold text-white bg-[#e50027] rounded-full hover:bg-red-700 transition-colors shadow-lg shadow-red-200 whitespace-nowrap">
                        Browse Menu <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>

                <!-- Right Content (Wine Red Curve with Official Banner Presentation) -->
                <div class="relative w-full md:w-1/2 min-h-[420px] md:min-h-[580px] custom-curved-bg flex items-center justify-center p-6 md:p-12 overflow-visible">
                    <!-- Glow effect -->
                    <div class="absolute w-72 h-72 bg-[#e50027]/25 rounded-full blur-3xl pointer-events-none"></div>

                    <!-- Main Banner Card -->
                    <div class="relative z-10 w-full max-w-[480px] bg-[#f4ece1] p-3.5 sm:p-4 rounded-3xl shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] border border-[#d5c7b2] transform md:-rotate-2 hover:rotate-0 transition-transform duration-500">
                        <div class="overflow-hidden rounded-2xl border border-[#d5c7b2] bg-[#e0d5c4] shadow-inner p-2">
                            <img src="{{ asset('images/banner.jpg') }}" alt="3GFood - Saveur & Tradition" class="w-full h-auto object-cover hover:scale-105 transition-transform duration-500 mix-blend-multiply">
                        </div>
                        <div class="mt-3 px-2 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#e50027] animate-pulse"></span>
                                <span class="font-black font-heading text-stone-900 tracking-wide text-xs">OFFICIAL BANNER</span>
                            </div>
                            <span class="text-[11px] font-semibold text-[#780d20] italic">Saveur & Tradition</span>
                        </div>
                    </div>

                    <!-- Floating Social Sidebar -->
                    <div class="absolute right-4 md:right-6 top-1/2 -translate-y-1/2 flex flex-col gap-3.5 bg-[#f4ece1] border border-[#d5c7b2] p-2.5 rounded-full shadow-xl z-20">
                        <a href="#" class="w-8 h-8 flex items-center justify-center text-stone-500 hover:text-[#e50027] rounded-full hover:bg-red-50 transition"><i class="fab fa-facebook-f text-xs"></i></a>
                        <a href="#" class="w-8 h-8 flex items-center justify-center text-stone-500 hover:text-[#e50027] rounded-full hover:bg-red-50 transition"><i class="fab fa-twitter text-xs"></i></a>
                        <a href="#" class="w-8 h-8 flex items-center justify-center text-stone-500 hover:text-[#e50027] rounded-full hover:bg-red-50 transition"><i class="fab fa-instagram text-xs"></i></a>
                    </div>
                </div>
            </section>

            <!-- Browse Category & Products -->
            <section id="menu-section" class="max-w-7xl mx-auto px-6 py-20">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold font-heading mb-4 text-[#1c1917]">Browse Food Category</h2>
                    <p class="text-stone-500 max-w-2xl mx-auto text-sm leading-relaxed">Nikmati beragam hidangan autentik terbaik 3GFood dengan citarasa lezat khas Nusantara.</p>
                </div>
                
                <div class="flex flex-wrap justify-center gap-4 mb-12">
                    <a href="{{ url('/?screen=home#menu-section') }}" class="px-6 py-2 rounded-full text-sm font-semibold transition-colors {{ !$category ? 'bg-[#e50027] text-white shadow-lg shadow-red-200' : 'bg-[#dfd3c0] text-stone-700 hover:bg-[#e4d8c8]' }}">Semua</a>
                    <a href="{{ url('/?screen=home&category=Paket Nasi Liwet#menu-section') }}" class="px-6 py-2 rounded-full text-sm font-semibold transition-colors {{ $category === 'Paket Nasi Liwet' ? 'bg-[#e50027] text-white shadow-lg shadow-red-200' : 'bg-[#dfd3c0] text-stone-700 hover:bg-[#e4d8c8]' }}">Paket Nasi Liwet</a>
                    <a href="{{ url('/?screen=home&category=Lauk Utama#menu-section') }}" class="px-6 py-2 rounded-full text-sm font-semibold transition-colors {{ $category === 'Lauk Utama' ? 'bg-[#e50027] text-white shadow-lg shadow-red-200' : 'bg-[#dfd3c0] text-stone-700 hover:bg-[#e4d8c8]' }}">Lauk Utama</a>
                    <a href="{{ url('/?screen=home&category=Menu Tambahan#menu-section') }}" class="px-6 py-2 rounded-full text-sm font-semibold transition-colors {{ $category === 'Menu Tambahan' ? 'bg-[#e50027] text-white shadow-lg shadow-red-200' : 'bg-[#dfd3c0] text-stone-700 hover:bg-[#e4d8c8]' }}">Menu Tambahan</a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($visibleMenu as $itemId => $item)
                    <div class="bg-[#f4ece1] rounded-3xl p-6 text-center shadow-[0_8px_30px_rgba(40,20,10,0.04)] border border-[#d5c7b2] hover:-translate-y-2 transition-transform duration-300">
                        <div class="relative bg-[#e5dbcb] rounded-2xl p-6 mb-6 h-48 flex items-center justify-center overflow-hidden">
                            @if(in_array($itemId, [1,2,5,7]))
                            <span class="absolute top-4 left-4 z-10 bg-[#e50027] text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase shadow-sm">Recommended</span>
                            @endif
                            <img src="{{ $item[3] }}" alt="{{ $item[0] }}" loading="lazy" decoding="async" class="w-full h-full object-cover filter drop-shadow-md mix-blend-multiply">
                        </div>
                        
                        <div class="flex justify-center text-amber-400 text-[10px] mb-3 gap-[2px]">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <h3 class="font-bold text-[#1c1917] mb-2 truncate">{{ $item[0] }}</h3>
                        <p class="font-bold text-[#1c1917] mb-6 text-lg">{{ $item[1] }}</p>
                        <a href="{{ url('/cart/add/' . $itemId) }}" onclick="instantAddToCart('{{ url('/cart/add/' . $itemId) }}', event, this)" class="inline-block bg-[#e50027] text-white font-bold text-xs px-8 py-3 rounded-full hover:bg-red-700 transition-all shadow-lg shadow-red-200 active:scale-95 cursor-pointer">Add To Cart</a>
                    </div>
                    @endforeach
                </div>
            </section>
        
        @else
            <!-- Non-Home Screens (Cart, Track, Checkout, etc.) -->
            <section class="max-w-5xl mx-auto px-6 py-12">
                <div class="mb-10 text-center">
                    <span class="text-[#e50027] font-bold text-xs tracking-wider uppercase mb-2 block">{{ $screens[$screen] ?? '' }}</span>
                    <h1 class="text-4xl font-extrabold font-heading">{{ match($screen) { 'cart' => 'Your Cart', 'buyer' => 'Checkout Details', 'payment' => 'Payment Method', 'confirmation' => 'Order Success', 'status' => 'Order Status', 'track' => 'Track Order', 'feedback' => 'Contact Us', 'settings' => 'Pengaturan Akun', default => $screens[$screen] } }}</h1>
                </div>

                @if($screen === 'cart')
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <div class="lg:col-span-2 bg-[#f4ece1] rounded-3xl p-8 shadow-[0_8px_30px_rgba(40,20,10,0.04)] border border-[#d5c7b2]">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-xl font-bold text-[#1c1917]">Order List</h2>
                                @if(count($cart))<a href="{{ url('/cart/clear') }}" class="text-sm text-red-500 hover:underline">Clear all</a>@endif
                            </div>
                            
                            <div class="space-y-6">
                                @forelse($cart as $itemId => $quantity)
                                <div class="flex items-center gap-4 py-4 border-b border-[#d5c7b2] last:border-0">
                                    <div class="bg-[#e5dbcb] p-2 rounded-xl h-20 w-20 flex-shrink-0 flex items-center justify-center">
                                        <img src="{{ $menu[$itemId][3] ?? '' }}" loading="lazy" decoding="async" class="object-cover h-full w-full mix-blend-multiply rounded-md">
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-[#1c1917]">{{ $menu[$itemId][0] ?? 'Menu tidak tersedia' }}</h3>
                                        <p class="text-stone-500 text-xs">{{ $menu[$itemId][1] ?? 'Harga tidak tersedia' }}</p>
                                        <div class="inline-block mt-2 bg-[#e0d5c4] border border-[#d5c7b2] text-[#e50027] text-xs font-bold px-3 py-1 rounded-md">{{ $quantity }} item</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-[#1c1917] mb-2">Rp {{ number_format(($menu[$itemId][2] ?? 0) * $quantity, 0, ',', '.') }}</div>
                                        <a href="{{ url('/cart/remove/' . $itemId) }}" class="text-xs text-red-500 hover:underline"><i class="fas fa-trash-alt"></i> Remove</a>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-10 text-stone-400">
                                    <i class="fas fa-shopping-basket text-4xl mb-4"></i>
                                    <p>Your cart is empty.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="bg-[#f4ece1] rounded-3xl p-8 shadow-[0_8px_30px_rgba(40,20,10,0.04)] border border-[#d5c7b2] h-fit">
                            <h2 class="text-xl font-bold mb-6 text-[#1c1917]">Order Summary</h2>
                            <div class="space-y-4 text-sm">
                                <div class="flex justify-between text-stone-500"><span>Subtotal</span> <strong class="text-[#1c1917]">Rp {{ number_format($subtotal, 0, ',', '.') }}</strong></div>
                                <div class="flex justify-between text-stone-500"><span>Delivery</span> <strong class="text-[#1c1917]">Rp {{ number_format($delivery, 0, ',', '.') }}</strong></div>
                                
                                @if($voucherDiscount > 0)
                                    <div class="flex justify-between text-[#e50027]"><span>Voucher</span> <strong>- Rp {{ number_format($voucherDiscount, 0, ',', '.') }}</strong></div>
                                @else
                                    <div class="pt-4 border-t border-[#d5c7b2]">
                                        <h3 class="font-bold mb-3 text-[#1c1917]">Voucher Code</h3>
                                        <form id="voucher-form" method="POST" class="flex gap-2">
                                            @csrf
                                            <input id="voucher-code" type="text" name="voucher_code" placeholder="Enter code" class="flex-1 bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 rounded-lg px-4 py-2 text-sm outline-none focus:border-[#e50027]" />
                                            <button class="bg-[#1c1917] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-stone-800 transition-colors" type="submit">Apply</button>
                                        </form>
                                        <div id="voucher-message" class="text-xs mt-2 text-red-500 font-medium"></div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mt-6 pt-6 border-t border-[#d5c7b2] border-dashed flex justify-between items-center text-lg">
                                <span class="font-bold text-[#1c1917]">Total</span>
                                <strong id="order-total" class="text-[#e50027] font-extrabold">Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                            </div>
                            
                            @if(count($cart))
                                <a href="{{ url('/?screen=buyer') }}" class="block w-full text-center mt-8 bg-[#e50027] text-white font-bold py-4 rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200">Checkout Now <i class="fas fa-arrow-right ml-2"></i></a>
                            @endif
                        </div>
                    </div>
                @elseif($screen === 'buyer')
                    <div class="max-w-2xl mx-auto bg-[#f4ece1] rounded-3xl p-8 md:p-10 shadow-[0_8px_30px_rgba(40,20,10,0.04)] border border-[#d5c7b2]">
                        <h2 class="text-2xl font-bold mb-6 text-center text-[#1c1917]">Delivery Details</h2>

                        @if($currentCustomer)
                            <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-200 text-green-900 text-xs flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <i class="fas fa-check-circle text-green-600 text-base"></i>
                                    <span>Pemesanan akan otomatis terhubung ke akun: <strong>{{ $currentCustomer->name }}</strong> ({{ $currentCustomer->email }})</span>
                                </div>
                                <span class="bg-green-200 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">Terhubung</span>
                            </div>
                        @else
                            <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <i class="fas fa-info-circle text-amber-600 text-base"></i>
                                    <span>Ingin riwayat pesanan tersimpan di akun Anda?</span>
                                </div>
                                <a href="{{ route('customer.login') }}" class="font-bold text-[#e50027] hover:underline shrink-0">
                                    Masuk / Daftar &rarr;
                                </a>
                            </div>
                        @endif

                        <form class="space-y-5" method="POST" action="{{ route('checkout.buyer') }}">
                            @csrf
                            <div>
                                <label class="block text-sm font-bold text-stone-700 mb-2">Full Name</label>
                                <input type="text" name="customer_name" value="{{ old('customer_name', $buyer['customer_name'] ?? '') }}" required placeholder="John Doe" class="w-full bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#e50027] focus:bg-[#f4ece1] outline-none transition-all text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-stone-700 mb-2">Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone', $buyer['phone'] ?? '') }}" required placeholder="08xx-xxxx-xxxx" class="w-full bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#e50027] focus:bg-[#f4ece1] outline-none transition-all text-sm">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-stone-700 mb-2">Perumahan</label>
                                    <input type="text" name="perumahan" required placeholder="Nama Perumahan" class="w-full bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#e50027] focus:bg-[#f4ece1] outline-none transition-all text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-stone-700 mb-2">Blok</label>
                                    <input type="text" name="blok" required placeholder="Contoh: A2" class="w-full bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#e50027] focus:bg-[#f4ece1] outline-none transition-all text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-stone-700 mb-2">Tahap (Opsional)</label>
                                    <input type="text" name="tahap" placeholder="Contoh: 3" class="w-full bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#e50027] focus:bg-[#f4ece1] outline-none transition-all text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-stone-700 mb-2">RT / RW (Opsional)</label>
                                    <input type="text" name="rt_rw" placeholder="01/02" class="w-full bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#e50027] focus:bg-[#f4ece1] outline-none transition-all text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-stone-700 mb-2">Catatan Tambahan (Opsional)</label>
                                <textarea name="catatan" placeholder="Contoh: Sambal dibanyakin, jangan pedas, dll..." class="w-full bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#e50027] focus:bg-[#f4ece1] outline-none transition-all text-sm min-h-[80px]"></textarea>
                            </div>
                            @if($errors->any())<p class="text-red-500 text-sm font-medium">{{ $errors->first() }}</p>@endif
                            <button type="submit" class="w-full bg-[#e50027] text-white font-bold py-4 rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200 mt-4">Continue to Payment <i class="fas fa-arrow-right ml-2"></i></button>
                        </form>
                    </div>
                @elseif($screen === 'payment')
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <div class="lg:col-span-2 bg-[#f4ece1] rounded-3xl p-8 shadow-[0_8px_30px_rgba(40,20,10,0.04)] border border-[#d5c7b2]">
                            <h2 class="text-xl font-bold mb-6 text-[#1c1917]">Select Payment Method</h2>
                            <form method="POST" action="{{ route('checkout.payment') }}" class="space-y-4">
                                @csrf
                                <label class="flex items-start gap-4 p-5 rounded-2xl border-2 border-transparent bg-[#e5dbcb] cursor-pointer hover:bg-[#ece2d4] transition-colors has-[:checked]:bg-red-50 has-[:checked]:border-[#e50027]">
                                    <input type="radio" name="payment_method" value="cash" checked class="mt-1 accent-[#e50027]">
                                    <div>
                                        <b class="text-[#1c1917] font-bold">Cash on Delivery (COD)</b>
                                        <p class="text-stone-500 text-sm mt-1">Please prepare exact cash amount.</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-start gap-4 p-5 rounded-2xl border-2 border-transparent bg-[#e5dbcb] cursor-pointer hover:bg-[#ece2d4] transition-colors has-[:checked]:bg-red-50 has-[:checked]:border-[#e50027]">
                                    <input type="radio" name="payment_method" value="bank" class="mt-1 accent-[#e50027]">
                                    <div>
                                        <b class="text-[#1c1917] font-bold">Bank Transfer</b>
                                        <p class="text-stone-500 text-sm mt-1">Transfer to our official bank account.</p>
                                        <div class="mt-4 p-4 bg-[#f4ece1] rounded-xl border border-[#d5c7b2]">
                                            <p class="text-xs text-stone-500 mb-1">BCA Account</p>
                                            <p class="font-bold text-lg text-[#1c1917]">1234 5678 9012</p>
                                            <p class="text-sm font-medium text-stone-600">A/N 3GFood Delivery</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="flex items-start gap-4 p-5 rounded-2xl border-2 border-transparent bg-[#e5dbcb] cursor-pointer hover:bg-[#ece2d4] transition-colors has-[:checked]:bg-red-50 has-[:checked]:border-[#e50027]">
                                    <input type="radio" name="payment_method" value="digital" class="mt-1 accent-[#e50027]">
                                    <div>
                                        <b class="text-[#1c1917] font-bold">E-Wallet / QRIS</b>
                                        <p class="text-stone-500 text-sm mt-1">Pay with GoPay, OVO, DANA or QRIS.</p>
                                        <div class="mt-4 p-4 bg-[#f4ece1] rounded-xl border border-[#d5c7b2] inline-block text-center">
                                            <div class="w-32 h-32 bg-[#dfd3c0] flex items-center justify-center font-bold text-stone-400 mb-2 rounded-lg border-2 border-dashed border-[#dcd0be]">QRIS</div>
                                            <p class="text-xs font-bold text-[#1c1917]">Scan to Pay</p>
                                        </div>
                                    </div>
                                </label>
                                
                                @if($errors->any())<p class="text-red-500 text-sm font-medium">{{ $errors->first() }}</p>@endif
                                
                                <button type="submit" class="w-full bg-[#e50027] text-white font-bold py-4 rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200 mt-6">Confirm Order <i class="fas fa-check ml-2"></i></button>
                            </form>
                        </di                        <div class="bg-[#f4ece1] rounded-3xl p-8 shadow-[0_8px_30px_rgba(40,20,10,0.04)] border border-[#d5c7b2] h-fit">
                            <h2 class="text-xl font-bold mb-6 text-[#1c1917]">Order Total</h2>
                            <div class="space-y-4 text-sm">
                                <div class="flex justify-between text-stone-500"><span>Subtotal</span> <strong class="text-[#1c1917]">Rp {{ number_format($subtotal, 0, ',', '.') }}</strong></div>
                                <div class="flex justify-between text-stone-500"><span>Delivery</span> <strong class="text-[#1c1917]">Rp {{ number_format($delivery, 0, ',', '.') }}</strong></div>
                                @if($voucherDiscount > 0)
                                    <div class="flex justify-between text-[#e50027]"><span>Voucher</span> <strong>- Rp {{ number_format($voucherDiscount, 0, ',', '.') }}</strong></div>
                                @endif
                            </div>
                            <div class="mt-6 pt-6 border-t border-[#d5c7b2] border-dashed flex justify-between items-center text-lg">
                                <span class="font-bold text-[#1c1917]">Total</span>
                                <strong class="text-[#e50027] font-extrabold">Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                @elseif($screen === 'confirmation')
                    <div class="max-w-xl mx-auto bg-[#f4ece1] rounded-3xl p-10 text-center shadow-[0_8px_30px_rgba(40,20,10,0.04)] border border-[#d5c7b2]">
                        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-6">
                            <i class="fas fa-check"></i>
                        </div>
                        <h2 class="text-3xl font-extrabold font-heading mb-4 text-[#1c1917]">Order Received!</h2>
                        <p class="text-stone-500 mb-8">Your order has been successfully placed and is waiting for confirmation.</p>
                        
                        <div class="bg-[#e0d5c4] border-2 border-dashed border-[#e50027]/40 rounded-xl p-6 mb-8 text-[#e50027]">
                            <p class="text-sm font-bold text-stone-500 mb-1">ORDER ID</p>
                            <p class="text-2xl font-black text-[#1c1917]">#{{ $order['order_code'] ?? 'FE-BARU' }}</p>
                        </div>
                        
                        @if(($order['payment_method'] ?? '') === 'bank')
                            <div class="bg-[#e5dbcb] rounded-2xl p-6 mb-8 border border-[#d5c7b2]">
                                <h3 class="font-bold text-[#1c1917] mb-2">Silakan transfer ke Rekening BCA:</h3>
                                <div class="bg-[#f4ece1] p-4 rounded-xl shadow-sm text-center border border-[#d5c7b2] inline-block mb-3">
                                    <p class="text-2xl font-black text-[#e50027] tracking-widest">1234 5678 9012</p>
                                    <p class="font-bold text-stone-500 text-sm mt-1">A/N 3GFood Delivery</p>
                                </div>
                                <p class="text-sm text-stone-500">Jumlah Tagihan: <strong class="text-[#1c1917]">Rp {{ number_format($order['total'] ?? 0, 0, ',', '.') }}</strong></p>
                            </div>
                        @elseif(($order['payment_method'] ?? '') === 'digital')
                            <div class="bg-[#e5dbcb] rounded-2xl p-6 mb-8 border border-[#d5c7b2]">
                                <h3 class="font-bold text-[#1c1917] mb-4">Silakan scan QRIS di bawah ini:</h3>
                                <div class="bg-[#f4ece1] p-4 rounded-xl shadow-sm border border-[#d5c7b2] inline-block text-center mb-3">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=QRIS_3GFOOD_DUMMY" alt="QRIS 3GFood" class="mx-auto mb-2 w-40 h-40">
                                    <p class="font-bold text-stone-500 text-sm">QRIS 3GFood</p>
                                </div>
                                <p class="text-sm text-stone-500">Jumlah Tagihan: <strong class="text-[#1c1917]">Rp {{ number_format($order['total'] ?? 0, 0, ',', '.') }}</strong></p>
                            </div>
                        @endif
                        
                        <p class="text-sm text-stone-500 font-medium mb-6"><i class="fas fa-info-circle mr-1"></i> Jangan lupa unggah bukti pembayaran di halaman Lacak Pesanan.</p>
                        
                        <a href="{{ url('/?screen=status') }}" class="inline-block bg-[#e50027] text-white font-bold px-8 py-4 rounded-full hover:bg-red-700 transition-colors shadow-lg shadow-red-200">Lacak & Unggah Bukti</a>
                    </div>
                @elseif($screen === 'track' || $screen === 'status')
                    <div class="max-w-2xl mx-auto bg-[#f4ece1] rounded-3xl p-8 md:p-10 shadow-[0_8px_30px_rgba(40,20,10,0.04)] border border-[#d5c7b2]">

                        @if($currentCustomer && $customerOrders->isNotEmpty() && !$trackCode)
                            <div class="mb-8">
                                <h4 class="font-bold text-sm text-stone-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <i class="fas fa-history text-[#e50027]"></i> Riwayat Pesanan Saya ({{ $currentCustomer->name }})
                                </h4>
                                <div class="space-y-3">
                                    @foreach($customerOrders as $cOrder)
                                        <div class="p-4 rounded-2xl border border-[#d5c7b2] bg-[#e5dbcb] hover:bg-[#ede2d2] transition flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                            <div>
                                                <span class="font-bold text-stone-900">#{{ $cOrder->order_code }}</span>
                                                <span class="text-xs text-stone-400 block">{{ $cOrder->created_at->format('d M Y H:i') }}</span>
                                                <span class="text-xs font-semibold text-stone-700">Rp {{ number_format($cOrder->total, 0, ',', '.') }} &bull; {{ strtoupper($cOrder->payment_method) }}</span>
                                            </div>
                                            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                                                <span class="px-3 py-1 rounded-full text-xs font-bold 
                                                     {{ in_array($cOrder->status, ['completed', 'confirmed']) ? 'bg-green-100 text-green-800' : 'bg-rose-100 text-[#e50027]' }}">
                                                    {{ match($cOrder->status) {
                                                        'waiting', 'waiting_owner', 'waiting_payment' => 'Menunggu Konfirmasi',
                                                        'accepted' => 'Diterima',
                                                        'cooking' => 'Sedang Dimasak',
                                                        'ready_to_deliver' => 'Siap Diantar',
                                                        'delivering' => 'Sedang Diantar',
                                                        'completed', 'confirmed' => 'Selesai',
                                                        default => $cOrder->status
                                                    } }}
                                                </span>
                                                <a href="{{ url('/?screen=track&order_code=' . $cOrder->order_code) }}" class="text-xs font-bold text-[#e50027] hover:underline flex items-center gap-1">
                                                    Lacak <i class="fas fa-chevron-right text-[10px]"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 pt-4 border-t border-[#d5c7b2] text-xs text-stone-500">
                                    Atau cari pesanan dengan memasukkan Kode Pesanan di bawah:
                                </div>
                            </div>
                        @endif

                        <form method="GET" action="{{ url('/') }}" class="flex flex-col sm:flex-row gap-4 mb-8">
                            <input type="hidden" name="screen" value="track">
                            <input type="text" name="order_code" value="{{ request('order_code', $order['order_code'] ?? '') }}" required placeholder="Enter Order ID or WhatsApp..." class="flex-1 bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#e50027] focus:bg-[#f4ece1] outline-none transition-all text-sm">
                            <button type="submit" class="bg-[#e50027] text-white font-bold px-8 py-4 rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200 whitespace-nowrap"><i class="fas fa-search mr-2"></i> Track</button>
                        </form>

                        @if($trackCode || isset($order['status']))
                            @php
                                $tracked = $trackedOrder ?? ($order ?? null);
                                $currStatus = $tracked['status'] ?? 'waiting';
                                $stepMap = [
                                    'waiting' => 1, 'waiting_owner' => 1, 'waiting_payment' => 1,
                                    'accepted' => 1,
                                    'cooking' => 2,
                                    'ready_to_deliver' => 3,
                                    'delivering' => 4,
                                    'completed' => 5, 'confirmed' => 5
                                ];
                                $currentStepNum = $stepMap[$currStatus] ?? 1;
                            @endphp
                            @if($tracked)
                                <div class="bg-[#fdfbf7] rounded-3xl p-6 md:p-8 border border-[#d5c7b2] shadow-sm mb-6" id="liveOrderCard" data-order-code="{{ $tracked['order_code'] }}">
                                    <!-- Status Header -->
                                    <div class="flex items-center justify-between mb-6 flex-wrap gap-3 pb-4 border-b border-[#e5dbcb]">
                                        <div>
                                            <div class="text-xs font-bold text-stone-500 uppercase tracking-wider">Pesanan #{{ $tracked['order_code'] }}</div>
                                            <h3 class="text-xl md:text-2xl font-black text-[#1c1917] mt-1" id="liveOrderTitle">
                                                {{ match($currStatus) {
                                                    'waiting', 'waiting_owner', 'waiting_payment' => 'Menunggu Konfirmasi Resto',
                                                    'accepted' => 'Pesanan Diterima & Masuk Antrean',
                                                    'cooking' => 'Sedang Dimasak di Dapur',
                                                    'ready_to_deliver' => 'Pesanan Siap Diantar',
                                                    'delivering' => 'Kurir Sedang Dalam Perjalanan',
                                                    'completed', 'confirmed' => 'Pesanan Selesai Diantar',
                                                    default => 'Status: ' . $currStatus
                                                } }}
                                            </h3>
                                            <p class="text-xs text-stone-600 mt-1" id="liveOrderDesc">
                                                {{ match($currStatus) {
                                                    'waiting', 'waiting_owner', 'waiting_payment' => 'Pesanan Anda telah diterima sistem dan menunggu konfirmasi kasir/admin.',
                                                    'accepted' => 'Pesanan telah dikonfirmasi dan segera disiapkan di dapur.',
                                                    'cooking' => 'Makanan sedang dimasak fresh untuk menjaga cita rasa terbaik.',
                                                    'ready_to_deliver' => 'Makanan sudah selesai dimasak, dikemas rapi, dan siap dijemput kurir.',
                                                    'delivering' => 'Kurir sedang dalam perjalanan menuju alamat Anda. Mohon siapkan pembayaran bila COD.',
                                                    'completed', 'confirmed' => 'Pesanan telah sampai dan diterima dengan baik. Selamat menikmati hidangan 3GFood!',
                                                    default => ''
                                                } }}
                                            </p>
                                        </div>
                                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200 shadow-sm">
                                            <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span>
                                            <span>Live Real-Time</span>
                                        </div>
                                    </div>

                                    <!-- 5-Step Visual Stepper -->
                                    <div class="mb-6">
                                        <div class="grid grid-cols-5 gap-1 md:gap-2 text-center" id="liveStepperContainer">
                                            @php
                                                $stepLabels = [
                                                    1 => ['title' => 'Diterima', 'icon' => 'fa-check-circle'],
                                                    2 => ['title' => 'Dimasak', 'icon' => 'fa-utensils'],
                                                    3 => ['title' => 'Siap', 'icon' => 'fa-box'],
                                                    4 => ['title' => 'Diantar', 'icon' => 'fa-motorcycle'],
                                                    5 => ['title' => 'Selesai', 'icon' => 'fa-flag-checkered'],
                                                ];
                                            @endphp
                                            @foreach($stepLabels as $sNum => $sInfo)
                                                @php
                                                    $isPassed = ($currentStepNum > $sNum);
                                                    $isCurrent = ($currentStepNum === $sNum);
                                                @endphp
                                                <div class="flex flex-col items-center step-item" data-step="{{ $sNum }}">
                                                    <div class="w-9 h-9 md:w-11 md:h-11 rounded-full flex items-center justify-center font-bold text-xs md:text-sm transition-all shadow-sm step-bubble
                                                        {{ $isCurrent ? 'bg-[#e50027] text-white ring-4 ring-red-100 scale-105' : ($isPassed ? 'bg-green-600 text-white' : 'bg-stone-200 text-stone-400') }}">
                                                        @if($isPassed)
                                                            <i class="fas fa-check text-xs"></i>
                                                        @else
                                                            <i class="fas {{ $sInfo['icon'] }} text-xs"></i>
                                                        @endif
                                                    </div>
                                                    <span class="text-[11px] md:text-xs font-bold mt-2 step-label {{ $isCurrent ? 'text-[#e50027]' : ($isPassed ? 'text-green-800' : 'text-stone-400') }}">
                                                        {{ $sInfo['title'] }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                        <!-- Progress bar line -->
                                        <div class="w-full bg-stone-200 h-1.5 rounded-full mt-3 overflow-hidden">
                                            <div id="liveStepProgressBar" class="h-full bg-gradient-to-r from-red-500 to-green-500 transition-all duration-500" style="width: {{ ($currentStepNum / 5) * 100 }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Assigned Courier Card -->
                                    <div id="liveCourierCard" class="mt-4 p-4 rounded-2xl bg-[#f4ece1] border border-[#d5c7b2] flex items-center justify-between gap-3 {{ !empty($tracked['courier']) ? '' : 'hidden' }}">
                                        <div class="flex items-center gap-3">
                                            <div class="w-11 h-11 rounded-full bg-red-100 text-[#e50027] flex items-center justify-center font-bold text-lg flex-shrink-0">
                                                🛵
                                            </div>
                                            <div>
                                                <div class="text-[10px] text-stone-500 font-bold uppercase tracking-wider">Kurir Pengantar</div>
                                                <div class="font-bold text-sm text-stone-900" id="liveCourierName">{{ $tracked['courier']['name'] ?? 'Kurir 3GFood' }}</div>
                                                <div class="text-xs text-stone-600" id="liveCourierPhone">{{ $tracked['courier']['phone'] ?? '-' }}</div>
                                            </div>
                                        </div>
                                        <a id="liveCourierWaBtn" href="https://wa.me/{{ preg_replace('/\D+/', '', $tracked['courier']['phone'] ?? '') }}" target="_blank" class="px-4 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold text-xs inline-flex items-center gap-1.5 transition shadow-sm whitespace-nowrap">
                                            <i class="fab fa-whatsapp"></i> Hubungi Kurir
                                        </a>
                                    </div>

                                    <!-- Order Items Breakdown -->
                                    @if(!empty($tracked['items']))
                                    <div class="mt-5 pt-4 border-t border-[#e5dbcb]">
                                        <h4 class="font-bold text-xs text-stone-700 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                                            <i class="fas fa-receipt text-stone-400"></i> Rincian Menu Dipesan
                                        </h4>
                                        <div class="space-y-1.5" id="liveOrderItemsList">
                                            @foreach($tracked['items'] as $it)
                                            <div class="flex justify-between items-center text-xs md:text-sm py-1 border-b border-stone-200/50 last:border-0">
                                                <span class="text-stone-800 font-medium">{{ $it['quantity'] }}x {{ $it['menu_name'] ?? $it['name'] ?? 'Menu' }}</span>
                                                <span class="font-bold text-stone-900">Rp {{ number_format($it['total'] ?? (($it['unit_price'] ?? $it['price'] ?? 0) * $it['quantity']), 0, ',', '.') }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Payment info & total -->
                                    <div class="mt-5 pt-4 border-t border-[#e5dbcb] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                        <div>
                                            <div class="text-xs text-stone-500 font-medium">Metode Pembayaran: <strong class="text-stone-900 uppercase font-bold">{{ $tracked['payment_method'] === 'cash' ? 'COD (Bayar di Tempat)' : strtoupper($tracked['payment_method']) }}</strong></div>
                                            <div class="text-xs text-stone-500 mt-0.5">Alamat Antar: <span class="text-stone-800">{{ $tracked['address'] }}</span></div>
                                        </div>
                                        <div class="text-left sm:text-right">
                                            <div class="text-xs text-stone-500 font-medium">Total Pembayaran</div>
                                            <div class="text-lg md:text-xl font-black text-[#e50027]">Rp {{ number_format($tracked['total'], 0, ',', '.') }}</div>
                                        </div>
                                    </div>

                                    <!-- Payment proof upload if bank or digital -->
                                    @if(in_array($tracked['payment_method'], ['bank', 'digital']))
                                        <div class="mt-5 pt-5 border-t border-[#e5dbcb]">
                                            @if(($tracked['payment_method'] === 'bank') && !($tracked['payment_proof'] ?? null))
                                                <div class="mb-4 bg-[#f4ece1] p-4 rounded-xl border border-[#d5c7b2] text-center">
                                                    <p class="text-xs text-stone-500 mb-1">Transfer Bank Rekening Resmi:</p>
                                                    <p class="text-xl font-black tracking-widest text-[#e50027]">1234 5678 9012</p>
                                                    <p class="text-xs font-bold text-stone-600">BCA a/n 3GFood Delivery</p>
                                                </div>
                                            @elseif(($tracked['payment_method'] === 'digital') && !($tracked['payment_proof'] ?? null))
                                                <div class="mb-4 bg-[#f4ece1] p-4 rounded-xl border border-[#d5c7b2] text-center">
                                                    <p class="text-xs text-stone-500 mb-2">Scan QRIS Resmi 3GFood:</p>
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=QRIS_3GFOOD_DUMMY" alt="QRIS" class="mx-auto w-32 h-32 mb-1 rounded-lg shadow-sm border border-stone-200">
                                                </div>
                                            @endif

                                            @if($tracked['payment_proof'] ?? null)
                                                <div class="bg-green-100 text-green-700 px-4 py-3 rounded-xl font-bold text-xs inline-flex items-center gap-2">
                                                    <i class="fas fa-file-invoice text-sm"></i> Bukti Pembayaran Telah Diupload
                                                </div>
                                            @else
                                                <h4 class="font-bold text-xs uppercase tracking-wider mb-2 text-[#1c1917]">Upload Bukti Pembayaran</h4>
                                                <form method="POST" action="{{ url('/checkout/proof') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                                                    @csrf
                                                    <input type="hidden" name="order_code" value="{{ $tracked['order_code'] }}">
                                                    <input type="file" name="payment_proof" required accept="image/*" class="w-full text-xs file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-[#e50027] hover:file:bg-red-100 bg-white p-1 rounded-xl border border-[#d5c7b2]">
                                                    <button type="submit" class="bg-[#1c1917] text-white font-bold py-2.5 px-6 rounded-xl hover:bg-stone-800 text-xs cursor-pointer shadow whitespace-nowrap">Upload Bukti</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl font-bold text-sm flex items-center gap-3">
                                    <i class="fas fa-exclamation-circle text-lg"></i> Pesanan tidak ditemukan. Periksa kembali Kode Pesanan atau Nomor HP Anda.
                                </div>
                            @endif
                        @endif
                    </div>
                @elseif($screen === 'feedback')
                    <div class="max-w-2xl mx-auto bg-[#f4ece1] rounded-3xl p-8 md:p-10 shadow-[0_8px_30px_rgba(40,20,10,0.04)] border border-[#d5c7b2]">
                        <form method="POST" action="{{ route('feedback.whatsapp') }}" class="space-y-5">
                            @csrf
                            <div>
                                <label class="block text-sm font-bold text-stone-700 mb-2">Name</label>
                                <input type="text" name="name" value="{{ old('name', $buyer['customer_name'] ?? '') }}" required placeholder="Your name" class="w-full bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#e50027] focus:bg-[#f4ece1] outline-none transition-all text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-stone-700 mb-2">Message</label>
                                <textarea name="message" required placeholder="Tell us what you think..." class="w-full bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#e50027] focus:bg-[#f4ece1] outline-none transition-all text-sm min-h-[150px]"></textarea>
                            </div>
                            @if($errors->has('feedback'))<p class="text-red-500 text-sm font-medium">{{ $errors->first() }}</p>@endif
                            <button type="submit" class="w-full bg-[#e50027] text-white font-bold py-4 rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200 mt-4 cursor-pointer"><i class="fab fa-whatsapp mr-2 text-lg"></i> Send via WhatsApp</button>
                        </form>
                    </div>
                @elseif($screen === 'settings')
                    <div class="max-w-3xl mx-auto">
                        <!-- Navigation Back Breadcrumb -->
                        <div class="flex items-center justify-between mb-6">
                            <a href="{{ url('/?screen=home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-[#e50027] transition-colors">
                                <i class="fas fa-arrow-left text-xs"></i> Kembali ke Beranda
                            </a>
                            <span class="text-xs text-gray-400 font-medium">Terakhir diperbarui: {{ $currentCustomer ? ($currentCustomer->updated_at ? $currentCustomer->updated_at->format('d/m/Y H:i') : '-') : '-' }}</span>
                        </div>

                        <!-- Error Messages Alert if any -->
                        @if(isset($errors) && $errors->any())
                            <div class="p-4 mb-6 rounded-2xl bg-red-50 border border-red-200 text-sm text-red-800 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-exclamation-circle text-red-600 text-lg mt-0.5"></i>
                                    <div>
                                        <h4 class="font-bold">Ada beberapa kesalahan input:</h4>
                                        <ul class="list-disc list-inside mt-1 space-y-1 text-xs">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Main Settings Form Card -->
                        <div class="bg-[#f4ece1] rounded-3xl p-6 sm:p-10 shadow-[0_10px_35px_rgba(40,20,10,0.04)] border border-[#d5c7b2]">
                            @if($currentCustomer)
                            <form id="settingsForm" method="POST" action="{{ route('customer.settings.update') }}" enctype="multipart/form-data" class="space-y-8">
                                @csrf
                                
                                <!-- Hidden inputs for avatar methods -->
                                <input type="file" id="avatarGalleryInput" name="avatar" accept="image/png,image/jpeg,image/webp,image/gif" class="hidden">
                                <input type="file" id="avatarNativeCameraInput" accept="image/*" capture="user" class="hidden">
                                <input type="hidden" id="avatarBase64Input" name="avatar_base64" value="">
                                <input type="hidden" id="removeAvatarInput" name="remove_avatar" value="0">

                                <!-- Studio Foto Profil -->
                                <div class="pb-8 border-b border-[#d5c7b2]">
                                    <label class="block text-xs font-bold text-stone-400 uppercase tracking-wider mb-4">Foto Profil Akun</label>
                                    
                                    <div class="flex flex-col sm:flex-row items-center gap-6">
                                        <!-- Avatar Container with Preview -->
                                        <div class="relative group cursor-pointer" onclick="document.getElementById('avatarGalleryInput').click()" title="Klik untuk ganti foto">
                                            <div id="avatarPreviewContainer" class="w-28 h-28 rounded-full ring-4 ring-red-100 shadow-lg overflow-hidden bg-[#e5dbcb] flex items-center justify-center relative">
                                                @if($currentCustomer->avatar_url)
                                                    <img id="avatarPreviewImg" src="{{ $currentCustomer->avatar_url }}" alt="{{ $currentCustomer->name }}" class="w-full h-full object-cover">
                                                    <div id="avatarInitials" class="w-full h-full bg-gradient-to-tr from-[#e50027] to-[#ff4d6d] text-white flex items-center justify-center text-3xl font-black hidden">
                                                        {{ strtoupper(substr($currentCustomer->name, 0, 1)) }}
                                                    </div>
                                                @else
                                                    <img id="avatarPreviewImg" src="" alt="" class="w-full h-full object-cover hidden">
                                                    <div id="avatarInitials" class="w-full h-full bg-gradient-to-tr from-[#e50027] to-[#ff4d6d] text-white flex items-center justify-center text-3xl font-black">
                                                        {{ strtoupper(substr($currentCustomer->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Camera Icon Badge -->
                                            <div class="absolute bottom-1 right-1 w-9 h-9 rounded-full bg-[#e50027] hover:bg-red-700 text-white flex items-center justify-center text-xs shadow-md border-2 border-[#f4ece1] transition group-hover:scale-110">
                                                <i class="fas fa-camera"></i>
                                            </div>
                                        </div>

                                        <!-- Photo Action Details & Options -->
                                        <div class="flex-1 text-center sm:text-left space-y-3">
                                            <div>
                                                <h3 class="text-xl font-bold font-heading text-stone-900 leading-tight">{{ $currentCustomer->name }}</h3>
                                                <p class="text-xs text-stone-500 mt-0.5">Atur foto profil Anda agar mudah dikenali oleh kurir dan penjual</p>
                                                <div id="avatarStatusBadge" class="hidden mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <i class="fas fa-check-circle text-emerald-500"></i> <span id="avatarStatusText">Foto baru dipilih (belum disimpan)</span>
                                                </div>
                                            </div>

                                            <!-- Action Buttons: Galeri & Ambil Langsung -->
                                            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-1">
                                                <!-- Opsi 1: Pilih dari Galeri -->
                                                <button type="button" 
                                                        onclick="document.getElementById('avatarGalleryInput').click()" 
                                                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#eee5d8] hover:bg-[#e4d9ca] text-stone-800 text-xs font-bold transition active:scale-95 shadow-sm cursor-pointer">
                                                    <i class="fas fa-images text-emerald-600 text-sm"></i>
                                                    <span>Pilih dari Galeri</span>
                                                </button>

                                                <!-- Opsi 2: Ambil Langsung (Kamera) -->
                                                <button type="button" 
                                                        onclick="openCameraModal()" 
                                                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-[#e50027] text-xs font-bold transition active:scale-95 shadow-sm border border-red-200 cursor-pointer">
                                                    <i class="fas fa-camera text-[#e50027] text-sm"></i>
                                                    <span>Ambil Langsung (Kamera)</span>
                                                </button>

                                                <!-- Opsi 3: Hapus Foto Profil -->
                                                @if($currentCustomer->avatar)
                                                <button type="button" 
                                                        id="btnRemoveAvatar" 
                                                        onclick="triggerRemoveAvatar()" 
                                                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-red-500 hover:text-red-700 hover:bg-red-50 text-xs font-semibold transition cursor-pointer">
                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                    <span>Hapus Foto</span>
                                                </button>
                                                @endif
                                            </div>

                                            <p class="text-[11px] text-stone-400">Pilih dari galeri foto atau jepret langsung lewat kamera. Format JPG, PNG, WEBP max 5MB.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bagian 1: Data Diri & Kontak -->
                                <div class="space-y-5 pb-8 border-b border-[#d5c7b2]">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-red-100 text-[#e50027] flex items-center justify-center text-xs">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <h3 class="text-base font-bold text-stone-900 font-heading">Informasi Profil</h3>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <!-- Nama Lengkap -->
                                        <div>
                                            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-stone-400 text-sm">
                                                    <i class="fas fa-user-edit"></i>
                                                </span>
                                                <input type="text" name="name" value="{{ old('name', $currentCustomer->name) }}" required placeholder="Nama Anda" class="w-full bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 focus:border-red-300 focus:bg-[#f4ece1] rounded-xl pl-11 pr-4 py-3.5 focus:ring-2 focus:ring-[#e50027] outline-none transition text-sm font-medium">
                                            </div>
                                        </div>

                                        <!-- Alamat Email -->
                                        <div>
                                            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Alamat Email <span class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-stone-400 text-sm">
                                                    <i class="fas fa-envelope"></i>
                                                </span>
                                                <input type="email" name="email" value="{{ old('email', $currentCustomer->email) }}" required placeholder="email@anda.com" class="w-full bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 focus:border-red-300 focus:bg-[#f4ece1] rounded-xl pl-11 pr-4 py-3.5 focus:ring-2 focus:ring-[#e50027] outline-none transition text-sm font-medium">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Nomor HP / WhatsApp -->
                                    <div>
                                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Nomor HP / WhatsApp</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-stone-400 text-sm">
                                                <i class="fab fa-whatsapp text-emerald-600"></i>
                                            </span>
                                            <input type="tel" name="phone" value="{{ old('phone', $currentCustomer->phone) }}" placeholder="Contoh: 081234567890" class="w-full bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 focus:border-red-300 focus:bg-[#f4ece1] rounded-xl pl-11 pr-4 py-3.5 focus:ring-2 focus:ring-[#e50027] outline-none transition text-sm font-medium">
                                        </div>
                                        <p class="text-[11px] text-stone-400 mt-1.5">Nomor ini mempermudah kurir menghubungi Anda saat pesanan sedang dikirim.</p>
                                    </div>
                                </div>

                                <!-- Bagian 2: Keamanan & Kata Sandi -->
                                <div class="space-y-5 pb-8 border-b border-[#d5c7b2]">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs">
                                                <i class="fas fa-lock"></i>
                                            </div>
                                            <h3 class="text-base font-bold text-stone-900 font-heading">Keamanan & Kata Sandi</h3>
                                        </div>
                                        <span class="text-[11px] text-stone-400 italic">Opsional (isi hanya jika ingin diubah)</span>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Kata Sandi Saat Ini</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-stone-400 text-sm">
                                                <i class="fas fa-key"></i>
                                            </span>
                                            <input type="password" id="current_password" name="current_password" placeholder="Masukkan kata sandi lama Anda" class="w-full bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 focus:border-red-300 focus:bg-[#f4ece1] rounded-xl pl-11 pr-11 py-3.5 focus:ring-2 focus:ring-[#e50027] outline-none transition text-sm font-medium">
                                            <button type="button" onclick="togglePasswordVisibility('current_password', this)" class="absolute inset-y-0 right-0 pr-4 flex items-center text-stone-400 hover:text-stone-600 cursor-pointer">
                                                <i class="fas fa-eye text-sm"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <!-- Kata Sandi Baru -->
                                        <div>
                                            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Kata Sandi Baru</label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-stone-400 text-sm">
                                                    <i class="fas fa-shield-alt"></i>
                                                </span>
                                                <input type="password" id="new_password" name="new_password" placeholder="Minimal 6 karakter" class="w-full bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 focus:border-red-300 focus:bg-[#f4ece1] rounded-xl pl-11 pr-11 py-3.5 focus:ring-2 focus:ring-[#e50027] outline-none transition text-sm font-medium">
                                                <button type="button" onclick="togglePasswordVisibility('new_password', this)" class="absolute inset-y-0 right-0 pr-4 flex items-center text-stone-400 hover:text-stone-600 cursor-pointer">
                                                    <i class="fas fa-eye text-sm"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Konfirmasi Kata Sandi Baru -->
                                        <div>
                                            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Konfirmasi Kata Sandi Baru</label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-stone-400 text-sm">
                                                    <i class="fas fa-check-double"></i>
                                                </span>
                                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Ulangi kata sandi baru" class="w-full bg-[#e5dbcb] border border-[#d5c7b2] text-stone-900 focus:border-red-300 focus:bg-[#f4ece1] rounded-xl pl-11 pr-11 py-3.5 focus:ring-2 focus:ring-[#e50027] outline-none transition text-sm font-medium">
                                                <button type="button" onclick="togglePasswordVisibility('new_password_confirmation', this)" class="absolute inset-y-0 right-0 pr-4 flex items-center text-stone-400 hover:text-stone-600 cursor-pointer">
                                                    <i class="fas fa-eye text-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ringkasan & Tombol Aksi Simpan -->
                                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                                    <div class="flex items-center gap-3 w-full sm:w-auto">
                                        <button type="submit" id="btnSubmitSettings" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-full bg-[#e50027] hover:bg-red-700 text-white font-bold text-sm shadow-lg shadow-red-200 transition active:scale-95 cursor-pointer">
                                            <i class="fas fa-save"></i>
                                            <span>Simpan Pengaturan</span>
                                        </button>
                                        <a href="{{ url('/?screen=home') }}" class="w-full sm:w-auto text-center px-6 py-3.5 rounded-full bg-[#eee5d8] hover:bg-[#e4d9ca] text-stone-700 font-semibold text-sm transition">
                                            Batal
                                        </a>
                                    </div>

                                    <div class="text-center sm:text-right w-full sm:w-auto">
                                        <p class="text-[11px] text-stone-400">Total riwayat pesanan: <span class="font-bold text-stone-700">{{ $customerOrders->count() }} pesanan</span></p>
                                    </div>
                                </div>
                            </form>
                            @else
                            <div class="text-center py-12">
                                <div class="w-16 h-16 rounded-full bg-red-50 text-[#e50027] flex items-center justify-center text-2xl mx-auto mb-4">
                                    <i class="fas fa-user-lock"></i>
                                </div>
                                <h3 class="text-xl font-bold font-heading mb-2 text-[#1c1917]">Silakan Masuk Terlebih Dahulu</h3>
                                <p class="text-sm text-stone-500 max-w-md mx-auto mb-6">Anda perlu login ke akun pembeli untuk melihat dan mengubah pengaturan profil Anda.</p>
                                <a href="{{ route('customer.login') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-[#e50027] hover:bg-red-700 text-white font-bold text-sm shadow-lg shadow-red-200 transition">
                                    <i class="fas fa-sign-in-alt"></i> Masuk ke Akun
                                </a>
                            </div>
                            @endif
                        </div>

                        @if($currentCustomer)
                        <!-- Danger Zone / Logout Area -->
                        <div class="mt-8 bg-red-50/50 rounded-3xl p-6 border border-red-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="text-center sm:text-left">
                                <h4 class="text-sm font-bold text-stone-900">Keluar dari Akun</h4>
                                <p class="text-xs text-stone-500 mt-0.5">Sesi Anda akan dihentikan dan Anda perlu login kembali untuk memesan.</p>
                            </div>
                            <form action="{{ route('customer.logout') }}" method="POST" onsubmit="handleInstantCustomerLogout(event, this)">
                                @csrf
                                <button type="submit" id="btn-logout-settings" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-sm shadow-red-200 transition cursor-pointer">
                                    <i class="fas fa-sign-out-alt"></i> Logout Sekarang
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                @else
                    <!-- Fallback -->
                    <div class="max-w-2xl mx-auto bg-[#f4ece1] rounded-3xl p-8 md:p-10 shadow-[0_8px_30px_rgba(40,20,10,0.04)] border border-[#d5c7b2] text-center">
                        <h2 class="text-2xl font-bold mb-4 text-[#1c1917]">Page Not Found</h2>
                        <a href="{{ url('/?screen=home') }}" class="inline-block bg-[#e50027] text-white font-bold px-8 py-3 rounded-full hover:bg-red-700 transition-colors">Go to Home</a>
                    </div>
                @endif
            </section>
        @endif
    </main>

    <footer class="bg-[#f4ece1] border-t border-[#d5c7b2] py-10 mt-10">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <div class="flex items-center justify-center gap-3 mb-4">
                <img src="{{ asset('images/logo.jpg') }}" alt="3GFood Logo" class="w-10 h-10 object-contain rounded-2xl border border-[#d5c7b2] bg-[#f4ece1] shadow-sm">
                <div class="text-left">
                    <span class="text-lg font-black font-heading leading-tight block text-[#1c1917]">3G<span class="text-[#e50027]">Food</span></span>
                    <span class="text-[10px] text-[#780d20] font-semibold tracking-wider uppercase block">Saveur & Tradition</span>
                </div>
            </div>
            <p class="text-stone-400 text-xs">© {{ date('Y') }} 3GFood - Saveur & Tradition. All rights reserved.</p>
        </div>
    </footer>

    <!-- Modal Khusus Informasi Akun Pelanggan -->
    @if($currentCustomer)
    <div id="accountModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity cursor-pointer" onclick="closeAccountModal()"></div>

        <!-- Modal Dialog Box -->
        <div class="relative bg-[#f4ece1] rounded-3xl shadow-2xl w-full max-w-md max-h-[92vh] overflow-y-auto z-10 border border-[#d5c7b2] transform transition-all my-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
            <!-- Modal Header Background -->
            <div class="relative bg-gradient-to-br from-[#800015] via-[#b80020] to-[#e50027] px-6 pt-5 pb-11 text-white text-center">
                <!-- Close Button -->
                <button type="button" onclick="closeAccountModal()" class="absolute top-3.5 right-3.5 w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition-colors cursor-pointer z-20" title="Tutup">
                    <i class="fas fa-times text-xs"></i>
                </button>

                <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-white/20 text-[10px] font-semibold backdrop-blur-sm mb-1.5 text-white">
                    <i class="fas fa-shield-alt text-amber-300"></i> Akun Pelanggan 3GFood
                </div>
                <h3 class="text-lg font-black font-heading tracking-wide">INFORMASI AKUN</h3>
                <p class="text-[11px] text-red-100/90">Detail identitas & status keanggotaan Anda</p>
            </div>

            <!-- Profile Info Body -->
            <div class="relative px-6 pb-5 pt-0">
                <!-- Floating Avatar -->
                <div class="-mt-9 flex flex-col items-center">
                    <div class="w-18 h-18 rounded-full bg-[#f4ece1] p-1 shadow-lg ring-4 ring-[#f4ece1]">
                        @if($currentCustomer->avatar_url)
                            <img src="{{ $currentCustomer->avatar_url }}" alt="{{ $currentCustomer->name }}" class="w-16 h-16 rounded-full object-cover shadow-inner">
                        @else
                            <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-[#e50027] to-[#ff4d6d] text-white flex items-center justify-center text-2xl font-black shadow-inner">
                                {{ strtoupper(substr($currentCustomer->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h4 class="text-base font-bold text-stone-900 mt-2 text-center">{{ $currentCustomer->name }}</h4>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Member Aktif 3GFood
                    </span>
                </div>

                <!-- Account Information Rows -->
                <div class="mt-4 space-y-2">
                    <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-[#e5dbcb] border border-[#d5c7b2]">
                        <div class="w-8 h-8 rounded-xl bg-red-100/80 text-[#e50027] flex items-center justify-center text-xs shrink-0">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-semibold text-stone-400 uppercase tracking-wider">Email Terdaftar</p>
                            <p class="text-xs font-bold text-stone-800 truncate">{{ $currentCustomer->email }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-[#e5dbcb] border border-[#d5c7b2]">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100/80 text-emerald-600 flex items-center justify-center text-xs shrink-0">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-semibold text-stone-400 uppercase tracking-wider">Nomor Handphone / WhatsApp</p>
                            <p class="text-xs font-bold text-stone-800 truncate">{{ $currentCustomer->phone ?: 'Belum diisi (opsional)' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-[#e5dbcb] border border-[#d5c7b2]">
                        <div class="w-8 h-8 rounded-xl bg-amber-100/80 text-amber-600 flex items-center justify-center text-xs shrink-0">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-semibold text-stone-400 uppercase tracking-wider">Bergabung Sejak</p>
                            <p class="text-xs font-bold text-stone-800 truncate">{{ $currentCustomer->created_at ? $currentCustomer->created_at->format('d M Y') : '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Statistic Cards -->
                <div class="mt-3 grid grid-cols-2 gap-2">
                    <div class="p-2.5 rounded-2xl bg-red-50/60 border border-red-100 text-center">
                        <p class="text-[10px] text-stone-500 font-semibold uppercase tracking-wider">Total Pesanan</p>
                        <p class="text-base font-black text-[#e50027] mt-0.5">{{ $customerOrders->count() }} <span class="text-[11px] font-semibold text-stone-500">kali</span></p>
                    </div>
                    <div class="p-2.5 rounded-2xl bg-amber-50/60 border border-amber-100 text-center">
                        <p class="text-[10px] text-stone-500 font-semibold uppercase tracking-wider">Akses Portal</p>
                        <p class="text-xs font-bold text-amber-800 mt-0.5">Akun Pelanggan</p>
                    </div>
                </div>

                <!-- Navigation & Action Buttons -->
                <div class="mt-5 space-y-2">
                    <a href="{{ url('/?screen=settings') }}" class="w-full flex items-center justify-between px-4 py-2.5 rounded-2xl bg-red-50 hover:bg-red-100 text-[#e50027] text-xs font-bold transition">
                        <span class="flex items-center gap-2.5">
                            <i class="fas fa-gear text-[#e50027]"></i> Pengaturan Akun
                        </span>
                        <i class="fas fa-chevron-right text-[10px]"></i>
                    </a>

                    <a href="{{ url('/?screen=track') }}" class="w-full flex items-center justify-between px-4 py-2.5 rounded-2xl bg-[#eee5d8] hover:bg-red-50 hover:text-[#e50027] text-stone-700 text-xs font-bold transition">
                        <span class="flex items-center gap-2.5">
                            <i class="fas fa-receipt text-[#e50027]"></i> Riwayat & Lacak Pesanan
                        </span>
                        <i class="fas fa-chevron-right text-[10px] text-stone-400"></i>
                    </a>

                    <form action="{{ route('customer.logout') }}" method="POST" onsubmit="handleInstantCustomerLogout(event, this)" class="pt-1">
                        @csrf
                        <button type="submit" id="btn-logout-modal" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition shadow-sm shadow-red-200 cursor-pointer">
                            <i class="fas fa-sign-out-alt"></i> Keluar dari Akun (Logout)
                        </button>
                    </form>

                    <button type="button" onclick="closeAccountModal()" class="w-full flex items-center justify-center px-4 py-2 rounded-2xl bg-[#eee5d8] hover:bg-[#e4d9ca] text-stone-700 text-xs font-semibold transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Mobile Bottom Navigation Bar (Khusus Tampilan Mobile - Sesuai Desain Modern) -->
    <nav class="fixed bottom-3 inset-x-3 sm:inset-x-6 md:hidden z-50 bg-[#f4ece1]/95 backdrop-blur-xl border border-[#d5c7b2] shadow-[0_12px_40px_rgba(40,20,10,0.12)] rounded-3xl px-2 py-2 flex items-center justify-around transition-all">
        <!-- 1. Home / Beranda -->
        <a href="{{ url('/?screen=home') }}" class="flex flex-col items-center justify-center py-1.5 px-3 rounded-2xl transition-all duration-200 {{ ($screen === 'home' && !request()->has('category')) ? 'bg-red-50 text-[#e50027] font-bold shadow-sm' : 'text-stone-400 hover:text-stone-700 font-medium' }}">
            <i class="fas fa-home text-base"></i>
            <span class="text-[10px] mt-0.5 leading-none">Beranda</span>
        </a>

        <!-- 2. Menu -->
        <a href="{{ url('/?screen=home#menu-section') }}" class="flex flex-col items-center justify-center py-1.5 px-3 rounded-2xl transition-all duration-200 {{ ($screen === 'home' && request()->has('category')) ? 'bg-red-50 text-[#e50027] font-bold shadow-sm' : 'text-stone-400 hover:text-stone-700 font-medium' }}">
            <i class="fas fa-utensils text-base"></i>
            <span class="text-[10px] mt-0.5 leading-none">Menu</span>
        </a>

        <!-- 3. Keranjang (with live badge counter) -->
        <a href="{{ url('/?screen=cart') }}" class="relative flex flex-col items-center justify-center py-1.5 px-3 rounded-2xl transition-all duration-200 {{ in_array($screen, ['cart', 'buyer', 'payment', 'confirmation']) ? 'bg-red-50 text-[#e50027] font-bold shadow-sm' : 'text-stone-400 hover:text-stone-700 font-medium' }}">
            <div class="relative">
                <i class="fas fa-shopping-bag text-base"></i>
                <span id="mobile-cart-badge" class="absolute -top-1.5 -right-2.5 min-w-[17px] h-[17px] px-1 bg-[#e50027] text-white text-[9px] font-black rounded-full flex items-center justify-center border border-[#f4ece1] transition-transform {{ $cartCount > 0 ? '' : 'hidden' }}">
                    {{ $cartCount }}
                </span>
            </div>
            <span class="text-[10px] mt-0.5 leading-none">Keranjang</span>
        </a>

        <!-- 4. Lacak / Status Pesanan -->
        <a href="{{ url('/?screen=track') }}" class="flex flex-col items-center justify-center py-1.5 px-3 rounded-2xl transition-all duration-200 {{ ($screen === 'track' || $screen === 'status') ? 'bg-red-50 text-[#e50027] font-bold shadow-sm' : 'text-stone-400 hover:text-stone-700 font-medium' }}">
            <i class="fas fa-clock-rotate-left text-base"></i>
            <span class="text-[10px] mt-0.5 leading-none">Pesanan</span>
        </a>

        <!-- 5. Pengaturan / Profil Akun -->
        <a href="{{ url('/?screen=settings') }}" class="flex flex-col items-center justify-center py-1.5 px-3 rounded-2xl transition-all duration-200 {{ $screen === 'settings' ? 'bg-red-50 text-[#e50027] font-bold shadow-sm' : 'text-stone-400 hover:text-stone-700 font-medium' }}">
            @if($currentCustomer && $currentCustomer->avatar_url)
                <img src="{{ $currentCustomer->avatar_url }}" alt="Profile" class="w-[18px] h-[18px] rounded-full object-cover ring-1 {{ $screen === 'settings' ? 'ring-[#e50027]' : 'ring-stone-300' }}">
            @else
                <i class="fas fa-gear text-base"></i>
            @endif
            <span class="text-[10px] mt-0.5 leading-none">Pengaturan</span>
        </a>
    </nav>

    <!-- Modal Ambil Foto Langsung (Interactive Live Camera Viewfinder) -->
    <div id="cameraModal" class="fixed inset-0 z-[110] hidden items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/80 backdrop-blur-md transition-opacity cursor-pointer" onclick="closeCameraModal()"></div>

        <!-- Modal Content -->
        <div class="relative bg-gray-900 rounded-3xl shadow-2xl w-full max-w-md overflow-hidden z-10 border border-gray-800 text-white transform transition-all my-auto">
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-gray-800 flex items-center justify-between bg-gray-900/90">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-red-600/20 text-red-500 flex items-center justify-center text-xs">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold font-heading text-white">Ambil Foto Langsung</h3>
                        <p class="text-[10px] text-gray-400">Posisikan wajah Anda di dalam lingkaran</p>
                    </div>
                </div>
                <button type="button" onclick="closeCameraModal()" class="w-8 h-8 rounded-full bg-gray-800 hover:bg-gray-700 text-gray-300 flex items-center justify-center transition cursor-pointer">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <!-- Viewport Feed Area -->
            <div class="relative p-4 flex flex-col items-center justify-center bg-black min-h-[320px]">
                <!-- Video Stream Element Container -->
                <div class="relative w-full aspect-square max-w-[300px] rounded-2xl overflow-hidden bg-gray-950 flex items-center justify-center border-2 border-gray-800 shadow-inner">
                    <video id="cameraFeed" autoplay playsinline muted class="w-full h-full object-cover"></video>
                    
                    <!-- Hidden Canvas for Snapshot Capture -->
                    <canvas id="cameraCanvas" class="hidden"></canvas>

                    <!-- Snapshot Frozen Image Preview -->
                    <img id="snapshotPreview" src="" alt="Snapshot" class="w-full h-full object-cover hidden">

                    <!-- Overlay Face Circular Guide -->
                    <div id="cameraGuideRing" class="absolute inset-5 rounded-full border-2 border-dashed border-white/40 pointer-events-none flex items-center justify-center">
                        <span class="text-[10px] text-white/70 bg-black/50 px-2 py-0.5 rounded-full backdrop-blur-sm">Pusatkan Wajah</span>
                    </div>

                    <!-- Flash Effect overlay -->
                    <div id="cameraFlash" class="absolute inset-0 bg-white opacity-0 pointer-events-none transition-opacity duration-150"></div>

                    <!-- Loading / Error Message Overlay -->
                    <div id="cameraLoadingOverlay" class="absolute inset-0 bg-gray-950/95 flex flex-col items-center justify-center p-6 text-center">
                        <i class="fas fa-spinner fa-spin text-2xl text-red-500 mb-2"></i>
                        <p id="cameraLoadingText" class="text-xs text-gray-300 font-medium">Memulai kamera...</p>
                        <p id="cameraLoadingSub" class="text-[10px] text-gray-500 mt-1">Harap izinkan akses kamera pada browser Anda.</p>
                        <button type="button" id="btnRetryCamera" onclick="startCameraStream()" class="mt-3 px-3 py-1.5 rounded-lg bg-gray-800 text-xs text-gray-200 hidden hover:bg-gray-700">
                            Coba Lagi
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Footer Controls -->
            <div class="p-5 bg-gray-900 border-t border-gray-800 flex flex-col items-center gap-3">
                <!-- State 1: Live Controls (Capture & Switch Camera) -->
                <div id="cameraLiveControls" class="flex items-center justify-center gap-6 w-full">
                    <!-- Switch Camera Button -->
                    <button type="button" onclick="switchCameraFacing()" class="w-11 h-11 rounded-full bg-gray-800 hover:bg-gray-700 text-gray-300 flex items-center justify-center transition active:scale-95 cursor-pointer" title="Tukar Kamera">
                        <i class="fas fa-rotate text-sm"></i>
                    </button>

                    <!-- Shutter Button -->
                    <button type="button" onclick="captureSnapshot()" class="w-16 h-16 rounded-full border-4 border-white/80 bg-[#e50027] hover:bg-red-700 flex items-center justify-center shadow-lg shadow-red-600/40 active:scale-90 transition group cursor-pointer" title="Jepret Foto">
                        <div class="w-6 h-6 rounded-full bg-white transition group-hover:scale-90"></div>
                    </button>

                    <!-- Native Camera fallback -->
                    <button type="button" onclick="triggerNativeCameraFallback()" class="w-11 h-11 rounded-full bg-gray-800 hover:bg-gray-700 text-gray-300 flex items-center justify-center transition active:scale-95 cursor-pointer" title="Kamera Bawaan HP">
                        <i class="fas fa-mobile-screen-button text-sm"></i>
                    </button>
                </div>

                <!-- State 2: Snapshot Preview Controls (Retake & Confirm) -->
                <div id="cameraPreviewControls" class="hidden flex items-center justify-center gap-3 w-full">
                    <button type="button" onclick="retakeSnapshot()" class="flex-1 py-3 px-4 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fas fa-arrow-rotate-left"></i> Ulangi Foto
                    </button>
                    <button type="button" onclick="confirmSnapshot()" class="flex-1 py-3 px-4 rounded-xl bg-[#e50027] hover:bg-red-700 text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-lg shadow-red-600/40 cursor-pointer">
                        <i class="fas fa-check"></i> Gunakan Foto Ini
                    </button>
                </div>

                <div class="text-center pt-1">
                    <button type="button" onclick="triggerNativeCameraFallback()" class="text-[11px] text-gray-400 hover:text-white underline transition cursor-pointer">
                        Atau buka Aplikasi Kamera Bawaan HP Anda
                    </button>
                </div>
            </div>
        </div>
    </div> <!-- /#cameraModal -->

    <!-- ==================== LIVE CHAT WIDGET (PEMBELI KE ADMIN) ==================== -->
    <div id="liveChatWidget" class="relative">
        <!-- Floating Chat Button & Bubble -->
        <div class="fixed z-[9999] bottom-20 right-4 sm:bottom-6 sm:right-6 flex items-center gap-3">
            <!-- Floating Bubble Tooltip -->
            <div id="chatFloatingBubble" onclick="toggleLiveChat()" class="flex items-center gap-2.5 bg-white/95 backdrop-blur-md px-4 py-2.5 rounded-full shadow-2xl border border-[#d5c7b2] text-xs font-bold text-stone-800 cursor-pointer hover:border-[#e50027] hover:bg-white transition-all transform hover:scale-105 group select-none">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                </span>
                <span>
                    @if($currentCustomer)
                        Chat Admin • <span class="text-[#e50027]">{{ $currentCustomer->name }}</span>
                    @else
                        Chat ke Admin
                    @endif
                </span>
                <span class="text-sm group-hover:scale-110 transition-transform">👋</span>
                <button type="button" onclick="event.stopPropagation(); document.getElementById('chatFloatingBubble').style.display='none'" class="text-stone-400 hover:text-stone-600 ml-1 p-0.5" title="Tutup">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
            </div>

            <!-- Trigger Button -->
            <button type="button" id="chatTriggerBtn" onclick="toggleLiveChat()" class="relative w-14 h-14 rounded-full bg-gradient-to-tr from-[#e50027] to-[#ff2b4e] hover:from-red-700 hover:to-red-600 text-white flex items-center justify-center shadow-2xl shadow-red-500/40 ring-4 ring-white/90 active:scale-95 transition-all duration-200 cursor-pointer" title="Chat dengan Admin 3GFood">
                <i id="chatIconOpen" class="fas fa-comments text-2xl"></i>
                <i id="chatIconClose" class="fas fa-times text-xl hidden"></i>
                <span id="chatUnreadBadge" class="absolute -top-1 -right-1 min-w-[20px] h-[20px] px-1.5 bg-amber-400 text-stone-900 text-[10px] font-black rounded-full flex items-center justify-center border-2 border-white shadow-sm hidden">
                    0
                </span>
            </button>
        </div>

        <!-- Chat Popup Window -->
        <div id="liveChatModal" class="fixed z-[9999] bottom-20 right-4 sm:bottom-24 sm:right-6 w-[calc(100vw-32px)] sm:w-[390px] h-[530px] max-h-[82vh] bg-[#fdfbf7] border border-[#d5c7b2] rounded-3xl shadow-[0_25px_60px_rgba(40,20,10,0.35)] flex flex-col overflow-hidden transition-all duration-300 transform scale-90 opacity-0 pointer-events-none">
            <!-- Chat Header -->
            <div class="bg-gradient-to-r from-[#e50027] to-[#ba001b] text-white p-4 flex items-center justify-between shadow-md">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 flex items-center justify-center text-white text-base font-bold shadow-inner">
                            <i class="fas fa-headset"></i>
                        </div>
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></span>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold font-heading text-white flex items-center gap-1.5">
                            CS Admin 3GFood
                            <span class="text-[9px] bg-green-500/30 text-green-100 border border-green-300/30 px-2 py-0.5 rounded-full font-medium">Online</span>
                        </h4>
                        <p class="text-[11px] text-white/80">Layanan Konsultasi & Bantuan Pesanan</p>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" onclick="toggleLiveChat()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer" title="Tutup Chat">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Customer Identity Banner (Sesuai Akun) -->
            <div class="px-4 py-2.5 bg-[#f4ece1] border-b border-[#d5c7b2] flex items-center justify-between text-xs text-stone-700">
                <div class="flex items-center gap-2 truncate">
                    @if($currentCustomer)
                        <div class="w-5 h-5 rounded-full bg-[#e50027] text-white text-[10px] font-bold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($currentCustomer->name, 0, 1)) }}
                        </div>
                        <span class="truncate">Akun: <strong class="text-stone-900">{{ $currentCustomer->name }}</strong></span>
                        <span class="text-[10px] bg-[#dfd3c0] text-stone-700 font-semibold px-1.5 py-0.5 rounded">Member</span>
                    @else
                        <i class="fas fa-user-circle text-stone-400 text-sm"></i>
                        <span>Tamu • <a href="{{ route('customer.login') }}" class="text-[#e50027] font-bold hover:underline">Masuk Akun</a></span>
                    @endif
                </div>
                <a href="https://wa.me/{{ config('services.whatsapp.admin_phone', '6281234567890') }}" target="_blank" class="text-[#22c55e] hover:underline font-bold text-xs inline-flex items-center gap-1 shrink-0 ml-2" title="Hubungi via WhatsApp">
                    <i class="fab fa-whatsapp"></i> WA
                </a>
            </div>

            <!-- Chat Messages Container -->
            <div id="chatMessagesContainer" class="flex-1 p-4 overflow-y-auto space-y-3 bg-[#f7f2ea]/70">
                <!-- Welcome Bubble -->
                <div class="flex items-start gap-2 max-w-[85%]">
                    <div class="w-7 h-7 rounded-full bg-red-100 text-[#e50027] flex items-center justify-center text-xs shrink-0 mt-1 shadow-sm">
                        <i class="fas fa-store"></i>
                    </div>
                    <div>
                        <div class="bg-white p-3 rounded-2xl rounded-tl-sm text-xs text-stone-800 shadow-sm border border-[#e5dbcb] leading-relaxed">
                            Halo Kak! 👋 Ada yang bisa kami bantu seputar pesanan, menu, atau pengiriman kurir hari ini?
                        </div>
                        <span class="text-[9px] text-stone-400 mt-1 block pl-1">Baru saja</span>
                    </div>
                </div>

                <!-- Quick Action Chips -->
                <div id="chatQuickChips" class="pt-2 flex flex-wrap gap-1.5">
                    <button type="button" onclick="sendQuickMessage('Ada rekomendasi menu favorit hari ini?')" class="px-2.5 py-1.5 bg-white hover:bg-red-50 text-[11px] font-medium text-stone-700 hover:text-[#e50027] rounded-full border border-[#d5c7b2] transition cursor-pointer shadow-2xs">
                        🍽️ Rekomendasi Menu
                    </button>
                    <button type="button" onclick="sendQuickMessage('Berapa tarif ongkir kurir ke alamat saya?')" class="px-2.5 py-1.5 bg-white hover:bg-red-50 text-[11px] font-medium text-stone-700 hover:text-[#e50027] rounded-full border border-[#d5c7b2] transition cursor-pointer shadow-2xs">
                        🛵 Cek Ongkir Kurir
                    </button>
                    <button type="button" onclick="sendQuickMessage('Toko buka jam berapa ya kak?')" class="px-2.5 py-1.5 bg-white hover:bg-red-50 text-[11px] font-medium text-stone-700 hover:text-[#e50027] rounded-full border border-[#d5c7b2] transition cursor-pointer shadow-2xs">
                        ⏱️ Jam Buka Toko
                    </button>
                </div>
            </div>

            <!-- Chat Footer Input -->
            <div class="p-3 bg-white border-t border-[#d5c7b2]">
                @if(!$currentCustomer)
                <div id="guestNameInputWrapper" class="mb-2 hidden">
                    <input type="text" id="chatGuestName" placeholder="Nama Anda (opsional)" class="w-full text-xs px-3 py-1.5 bg-[#f4ece1] rounded-lg border border-[#d5c7b2] outline-none focus:border-[#e50027]">
                </div>
                @endif
                <form id="chatMessageForm" onsubmit="handleSendChatMessage(event)" class="flex items-center gap-2">
                    <input type="text" id="chatInputMessage" placeholder="Ketik pesan..." required maxlength="1000" autocomplete="off" class="flex-1 bg-[#f4ece1] text-stone-900 placeholder:text-stone-400 rounded-full px-4 py-2.5 text-xs font-medium border border-[#d5c7b2] outline-none focus:border-[#e50027] focus:bg-white transition">
                    <button type="submit" id="chatSendBtn" class="w-9 h-9 rounded-full bg-[#e50027] hover:bg-red-700 text-white flex items-center justify-center shrink-0 transition shadow-md shadow-red-200 active:scale-95 cursor-pointer">
                        <i class="fas fa-paper-plane text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

<script>
  function openAccountModal() {
    const modal = document.getElementById('accountModal');
    if (modal) {
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeAccountModal() {
    const modal = document.getElementById('accountModal');
    if (modal) {
      modal.classList.remove('flex');
      modal.classList.add('hidden');
      document.body.style.overflow = '';
    }
  }

  /* === CAMERA STREAM & VIEWPORT CONTROLS === */
  let currentCameraStream = null;
  let currentFacingMode = 'user'; // 'user' (depan) atau 'environment' (belakang)
  let capturedSnapshotBase64 = null;

  function openCameraModal() {
    const modal = document.getElementById('cameraModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    startCameraStream();
  }

  function closeCameraModal() {
    stopCameraStream();
    const modal = document.getElementById('cameraModal');
    if (modal) {
      modal.classList.remove('flex');
      modal.classList.add('hidden');
      document.body.style.overflow = '';
    }
  }

  async function startCameraStream() {
    const video = document.getElementById('cameraFeed');
    const loadingOverlay = document.getElementById('cameraLoadingOverlay');
    const loadingText = document.getElementById('cameraLoadingText');
    const loadingSub = document.getElementById('cameraLoadingSub');
    const btnRetry = document.getElementById('btnRetryCamera');
    const liveControls = document.getElementById('cameraLiveControls');
    const previewControls = document.getElementById('cameraPreviewControls');
    const snapshotPreview = document.getElementById('snapshotPreview');

    if (snapshotPreview) snapshotPreview.classList.add('hidden');
    if (video) video.classList.remove('hidden');
    if (liveControls) liveControls.classList.remove('hidden');
    if (previewControls) previewControls.classList.add('hidden');

    if (loadingOverlay) loadingOverlay.classList.remove('hidden');
    if (btnRetry) btnRetry.classList.add('hidden');
    if (loadingText) loadingText.textContent = 'Memulai kamera...';
    if (loadingSub) loadingSub.textContent = 'Harap izinkan akses kamera jika diminta browser.';

    stopCameraStream();

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      if (loadingText) loadingText.textContent = 'Kamera tidak didukung oleh browser ini.';
      if (loadingSub) loadingSub.textContent = 'Gunakan tombol Kamera Bawaan HP di bawah.';
      if (btnRetry) btnRetry.classList.add('hidden');
      return;
    }

    try {
      const constraints = {
        video: {
          facingMode: currentFacingMode,
          width: { ideal: 1080 },
          height: { ideal: 1080 }
        },
        audio: false
      };
      const stream = await navigator.mediaDevices.getUserMedia(constraints);
      currentCameraStream = stream;
      if (video) {
        video.srcObject = stream;
        video.style.transform = (currentFacingMode === 'user') ? 'scaleX(-1)' : 'scaleX(1)';
        await video.play();
      }
      if (loadingOverlay) loadingOverlay.classList.add('hidden');
    } catch (err) {
      console.warn('Camera stream error:', err);
      if (loadingText) loadingText.textContent = 'Tidak dapat mengakses kamera.';
      if (loadingSub) loadingSub.textContent = 'Izin kamera ditolak atau kamera sedang digunakan aplikasi lain. Anda dapat menggunakan kamera bawaan ponsel.';
      if (btnRetry) btnRetry.classList.remove('hidden');
    }
  }

  function stopCameraStream() {
    if (currentCameraStream) {
      currentCameraStream.getTracks().forEach(track => track.stop());
      currentCameraStream = null;
    }
  }

  function switchCameraFacing() {
    currentFacingMode = (currentFacingMode === 'user') ? 'environment' : 'user';
    startCameraStream();
  }

  function triggerNativeCameraFallback() {
    closeCameraModal();
    const nativeInput = document.getElementById('avatarNativeCameraInput');
    if (nativeInput) nativeInput.click();
  }

  function captureSnapshot() {
    const video = document.getElementById('cameraFeed');
    const canvas = document.getElementById('cameraCanvas');
    const snapshotPreview = document.getElementById('snapshotPreview');
    const flash = document.getElementById('cameraFlash');
    const liveControls = document.getElementById('cameraLiveControls');
    const previewControls = document.getElementById('cameraPreviewControls');

    if (!video || !canvas) return;

    // Flash animation
    if (flash) {
      flash.style.opacity = '0.9';
      setTimeout(() => { flash.style.opacity = '0'; }, 120);
    }

    const size = Math.min(video.videoWidth || 640, video.videoHeight || 640);
    canvas.width = size;
    canvas.height = size;
    const ctx = canvas.getContext('2d');

    const startX = ((video.videoWidth || 640) - size) / 2;
    const startY = ((video.videoHeight || 640) - size) / 2;

    if (currentFacingMode === 'user') {
      ctx.translate(size, 0);
      ctx.scale(-1, 1);
    }
    ctx.drawImage(video, startX, startY, size, size, 0, 0, size, size);

    capturedSnapshotBase64 = canvas.toDataURL('image/jpeg', 0.88);

    if (snapshotPreview) {
      snapshotPreview.src = capturedSnapshotBase64;
      snapshotPreview.classList.remove('hidden');
    }
    if (video) video.classList.add('hidden');
    if (liveControls) liveControls.classList.add('hidden');
    if (previewControls) previewControls.classList.remove('hidden');
  }

  function retakeSnapshot() {
    capturedSnapshotBase64 = null;
    const video = document.getElementById('cameraFeed');
    const snapshotPreview = document.getElementById('snapshotPreview');
    const liveControls = document.getElementById('cameraLiveControls');
    const previewControls = document.getElementById('cameraPreviewControls');

    if (snapshotPreview) snapshotPreview.classList.add('hidden');
    if (video) video.classList.remove('hidden');
    if (liveControls) liveControls.classList.remove('hidden');
    if (previewControls) previewControls.classList.add('hidden');
  }

  function confirmSnapshot() {
    if (!capturedSnapshotBase64) return;
    const base64Input = document.getElementById('avatarBase64Input');
    const galleryInput = document.getElementById('avatarGalleryInput');
    const previewImg = document.getElementById('avatarPreviewImg');
    const initials = document.getElementById('avatarInitials');
    const statusBadge = document.getElementById('avatarStatusBadge');
    const statusText = document.getElementById('avatarStatusText');
    const removeInput = document.getElementById('removeAvatarInput');

    if (base64Input) base64Input.value = capturedSnapshotBase64;
    if (galleryInput) galleryInput.value = '';
    if (removeInput) removeInput.value = '0';

    if (previewImg) {
      previewImg.src = capturedSnapshotBase64;
      previewImg.classList.remove('hidden');
    }
    if (initials) initials.classList.add('hidden');

    if (statusBadge && statusText) {
      statusText.textContent = 'Foto kamera baru siap disimpan!';
      statusBadge.classList.remove('hidden');
    }

    closeCameraModal();
  }

  function triggerRemoveAvatar() {
    if (!confirm('Apakah Anda yakin ingin menghapus foto profil?')) return;
    const removeInput = document.getElementById('removeAvatarInput');
    const base64Input = document.getElementById('avatarBase64Input');
    const galleryInput = document.getElementById('avatarGalleryInput');
    const previewImg = document.getElementById('avatarPreviewImg');
    const initials = document.getElementById('avatarInitials');
    const statusBadge = document.getElementById('avatarStatusBadge');
    const statusText = document.getElementById('avatarStatusText');

    if (removeInput) removeInput.value = '1';
    if (base64Input) base64Input.value = '';
    if (galleryInput) galleryInput.value = '';

    if (previewImg) {
      previewImg.src = '';
      previewImg.classList.add('hidden');
    }
    if (initials) {
      initials.classList.remove('hidden');
    }
    if (statusBadge && statusText) {
      statusText.textContent = 'Foto profil akan dihapus saat Anda menekan Simpan Pengaturan.';
      statusBadge.classList.remove('hidden');
    }
  }

  function togglePasswordVisibility(inputId, button) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const icon = button.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      if (icon) {
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      }
    } else {
      input.type = 'password';
      if (icon) {
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }
  }

  /* === GALLERY & NATIVE CAMERA FILE EVENT LISTENERS === */
  document.addEventListener('DOMContentLoaded', function() {
    const galleryInput = document.getElementById('avatarGalleryInput');
    if (galleryInput) {
      galleryInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
          const file = e.target.files[0];
          const reader = new FileReader();
          reader.onload = function(evt) {
            const previewImg = document.getElementById('avatarPreviewImg');
            const initials = document.getElementById('avatarInitials');
            const statusBadge = document.getElementById('avatarStatusBadge');
            const statusText = document.getElementById('avatarStatusText');
            const base64Input = document.getElementById('avatarBase64Input');
            const removeInput = document.getElementById('removeAvatarInput');

            if (base64Input) base64Input.value = '';
            if (removeInput) removeInput.value = '0';

            if (previewImg) {
              previewImg.src = evt.target.result;
              previewImg.classList.remove('hidden');
            }
            if (initials) {
              initials.classList.add('hidden');
            }
            if (statusBadge && statusText) {
              statusText.textContent = 'Foto dari galeri siap disimpan: ' + file.name;
              statusBadge.classList.remove('hidden');
            }
          };
          reader.readAsDataURL(file);
        }
      });
    }

    const nativeCamInput = document.getElementById('avatarNativeCameraInput');
    if (nativeCamInput) {
      nativeCamInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
          const file = e.target.files[0];
          try {
            const dt = new DataTransfer();
            dt.items.add(file);
            if (galleryInput) galleryInput.files = dt.files;
          } catch(err) {}

          const reader = new FileReader();
          reader.onload = function(evt) {
            const previewImg = document.getElementById('avatarPreviewImg');
            const initials = document.getElementById('avatarInitials');
            const statusBadge = document.getElementById('avatarStatusBadge');
            const statusText = document.getElementById('avatarStatusText');
            const base64Input = document.getElementById('avatarBase64Input');
            const removeInput = document.getElementById('removeAvatarInput');

            if (base64Input) base64Input.value = '';
            if (removeInput) removeInput.value = '0';

            if (previewImg) {
              previewImg.src = evt.target.result;
              previewImg.classList.remove('hidden');
            }
            if (initials) initials.classList.add('hidden');
            if (statusBadge && statusText) {
              statusText.textContent = 'Foto dari kamera bawaan siap disimpan!';
              statusBadge.classList.remove('hidden');
            }
          };
          reader.readAsDataURL(file);
        }
      });
    }
  });

  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
      closeAccountModal();
      closeCameraModal();
    }
  });

  (function(){
    const voucherForm = document.getElementById('voucher-form');
    if (!voucherForm) return;
    const subtotal = {{ $subtotal ?? 0 }};
    const csrf = '{{ csrf_token() }}';
    const messageNode = document.getElementById('voucher-message');
    const totalNode = document.getElementById('order-total');
    const codeNode = document.getElementById('voucher-code');

    voucherForm.addEventListener('submit', async function(event) {
      event.preventDefault();
      const code = (codeNode ? codeNode.value : '').trim();
      if (!code) {
        messageNode.className = 'text-xs mt-2 text-red-500 font-medium';
        messageNode.textContent = 'Masukkan kode voucher terlebih dahulu.';
        return;
      }

      messageNode.className = 'text-xs mt-2 text-gray-500 font-medium';
      messageNode.textContent = 'Memeriksa voucher...';

      try {
        const response = await fetch('{{ route('voucher.check') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ voucher_code: code, subtotal: subtotal })
        });

        const data = await response.json();
        if (!response.ok || !data.valid) {
          messageNode.className = 'text-xs mt-2 text-red-500 font-medium';
          messageNode.textContent = data.message || 'Voucher tidak valid.';
          return;
        }

        messageNode.className = 'text-xs mt-2 text-green-600 font-medium';
        messageNode.textContent = data.message + ' Diskon: Rp ' + Number(data.discount_amount || 0).toLocaleString('id-ID');

        if (totalNode) {
          const total = Math.max(subtotal + 1000 - Number(data.discount_amount || 0), 0);
          totalNode.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
      } catch (error) {
        messageNode.className = 'text-xs mt-2 text-red-500 font-medium';
        messageNode.textContent = 'Terjadi kesalahan saat mengecek voucher.';
      }
    });
  })();
  /* ==========================================================================
     INSTANT ENGINE: ZERO LOADING LAG ROUTER & INSTANT ACTIONS
     ========================================================================== */

  // 1. Toast Notification for instantaneous feedback
  function showMiniToast(message, type = 'success') {
    let toast = document.getElementById('app-mini-toast');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'app-mini-toast';
      toast.className = 'fixed top-20 right-4 sm:right-8 z-[120] px-4 py-2.5 rounded-2xl bg-[#1c1917] text-white text-xs font-bold shadow-xl border border-white/10 flex items-center gap-2.5 transform transition-all duration-300 opacity-0 -translate-y-2 pointer-events-none';
      document.body.appendChild(toast);
    }
    const icon = type === 'success' ? '<i class="fas fa-check-circle text-emerald-400"></i>' : '<i class="fas fa-info-circle text-amber-400"></i>';
    toast.innerHTML = `${icon} <span>${message}</span>`;
    toast.classList.remove('opacity-0', '-translate-y-2', 'pointer-events-none');
    toast.classList.add('opacity-100', 'translate-y-0');
    setTimeout(() => {
      toast.classList.remove('opacity-100', 'translate-y-0');
      toast.classList.add('opacity-0', '-translate-y-2', 'pointer-events-none');
    }, 2200);
  }

  // 2. Update Cart Counters Across Header & Bottom Navigation
  function updateCartBadges(count) {
    const navBadge = document.getElementById('nav-cart-badge');
    const mobBadge = document.getElementById('mobile-cart-badge');
    const num = parseInt(count, 10) || 0;
    
    [navBadge, mobBadge].forEach(badge => {
      if (!badge) return;
      badge.textContent = num;
      if (num > 0) {
        badge.classList.remove('hidden');
        badge.classList.add('scale-125');
        setTimeout(() => badge.classList.remove('scale-125'), 200);
      } else {
        badge.classList.add('hidden');
      }
    });
  }

  // 3. Instant Add to Cart without page reload
  async function instantAddToCart(url, e, btn) {
    if (e) e.preventDefault();
    if (btn) {
      btn.classList.add('scale-95');
      setTimeout(() => btn.classList.remove('scale-95'), 120);
    }
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrf || '',
          'Accept': 'application/json'
        }
      });
      const data = await res.json().catch(() => null);
      if (data && data.success) {
        updateCartBadges(data.cartCount);
        showMiniToast(data.message || 'Menu ditambahkan ke keranjang!');
        screenCache.delete("{{ url('/?screen=cart') }}");
        screenCache.delete("/?screen=cart");
      } else {
        window.location.href = url;
      }
    } catch(err) {
      window.location.href = url;
    }
  }

  // 4. Instant Logout without loading delays
  async function handleInstantCustomerLogout(e, form) {
    if (e) e.preventDefault();
    const btn = form ? form.querySelector('button[type="submit"]') : null;
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Keluar...';
    }
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || (form ? form.querySelector('input[name="_token"]')?.value : '');
      await fetch("{{ route('customer.logout') }}", {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrf || '',
          'Accept': 'application/json'
        }
      });
    } catch(err) {
      // fallback
    }
    screenCache.clear();
    window.location.href = "{{ url('/?screen=home') }}";
  }

  // 5. In-Memory Page Cache & Router for 0ms Screen Switching
  const screenCache = new Map();
  const dynamicScreensList = ['track', 'status', 'cart', 'payment', 'confirmation', 'buyer'];

  function isDynamicScreenUrl(url) {
    try {
      const u = new URL(url, window.location.origin);
      const scr = u.searchParams.get('screen');
      return scr && dynamicScreensList.includes(scr);
    } catch(e) {
      return false;
    }
  }

  async function fetchScreenContent(url) {
    const cleanUrl = url.split('#')[0];
    if (!isDynamicScreenUrl(cleanUrl) && screenCache.has(cleanUrl)) {
      return screenCache.get(cleanUrl);
    }
    const res = await fetch(cleanUrl, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    if (!res.ok) throw new Error('Network error');
    const htmlText = await res.text();
    const parser = new DOMParser();
    const doc = parser.parseFromString(htmlText, 'text/html');
    const mainEl = doc.getElementById('app-main');
    const titleEl = doc.querySelector('title');
    if (!mainEl) throw new Error('No main element found');

    const data = {
      html: mainEl.innerHTML,
      title: titleEl ? titleEl.innerText : document.title,
      url: cleanUrl
    };
    if (!isDynamicScreenUrl(cleanUrl)) {
      screenCache.set(cleanUrl, data);
    }
    return data;
  }

  // Prefetch disabled to prevent server connection congestion & execution timeouts
  function setupPrefetching() {
    // Fast in-memory cache handles already-visited screens without speculative requests
  }

  // Update navigation active states
  function updateNavActiveStates(targetUrl) {
    const urlObj = new URL(targetUrl, window.location.origin);
    const screen = urlObj.searchParams.get('screen') || 'home';
    const hasCategory = urlObj.searchParams.has('category');

    // Update Desktop Nav
    document.querySelectorAll('.md\\:flex a[href]').forEach(link => {
      const lUrl = new URL(link.getAttribute('href'), window.location.origin);
      const lScreen = lUrl.searchParams.get('screen') || 'home';
      const isMenuLink = link.getAttribute('href').includes('#menu-section');

      if (isMenuLink) {
        if (hasCategory) {
          link.className = 'text-[#e50027] transition-colors';
        } else {
          link.className = 'hover:text-[#e50027] transition-colors';
        }
      } else if (lScreen === screen && !hasCategory) {
        link.className = 'text-[#e50027] transition-colors';
      } else {
        link.className = 'hover:text-[#e50027] transition-colors';
      }
    });

    // Update Mobile Bottom Nav
    const bottomNav = document.querySelector('nav.fixed.bottom-3');
    if (bottomNav) {
      bottomNav.querySelectorAll('a[href]').forEach(link => {
        const lUrl = new URL(link.getAttribute('href'), window.location.origin);
        const lScreen = lUrl.searchParams.get('screen') || 'home';
        const isMenu = link.getAttribute('href').includes('#menu-section');

        let isActive = false;
        if (isMenu) {
          isActive = (screen === 'home' && hasCategory);
        } else if (lScreen === 'home') {
          isActive = (screen === 'home' && !hasCategory);
        } else if (lScreen === 'cart') {
          isActive = ['cart', 'buyer', 'payment', 'confirmation'].includes(screen);
        } else if (lScreen === 'track') {
          isActive = (screen === 'track' || screen === 'status');
        } else if (lScreen === 'settings') {
          isActive = (screen === 'settings');
        }

        if (isActive) {
          link.className = 'flex flex-col items-center justify-center py-1.5 px-3 rounded-2xl transition-all duration-200 bg-red-50 text-[#e50027] font-bold shadow-sm';
        } else {
          link.className = 'flex flex-col items-center justify-center py-1.5 px-3 rounded-2xl transition-all duration-200 text-stone-400 hover:text-stone-700 font-medium';
        }
      });
    }
  }

  // Instant Screen Navigation Handler
  async function instantNavigate(url, pushState = true) {
    const mainEl = document.getElementById('app-main');
    if (!mainEl) {
      window.location.href = url;
      return;
    }

    try {
      mainEl.style.opacity = '0.5';

      const data = await fetchScreenContent(url);
      mainEl.innerHTML = data.html;
      if (data.title) document.title = data.title;

      if (pushState) {
        history.pushState(null, data.title, url);
      }

      updateNavActiveStates(url);
      setupPrefetching();
      rebindDynamicHandlers();

      mainEl.style.opacity = '1';

      if (url.includes('#menu-section')) {
        const menuSec = document.getElementById('menu-section');
        if (menuSec) menuSec.scrollIntoView({ behavior: 'smooth' });
        else window.scrollTo({ top: 0, behavior: 'smooth' });
      } else {
        window.scrollTo({ top: 0, behavior: 'instant' });
      }
    } catch(err) {
      mainEl.style.opacity = '1';
      window.location.href = url;
    }
  }

  // Intercept click on screen links
  document.addEventListener('click', function(e) {
    const link = e.target.closest('a[href]');
    if (!link) return;
    const href = link.getAttribute('href');
    if (!href) return;

    if (href.startsWith('#')) return;
    if (href.startsWith('tel:') || href.startsWith('mailto:') || href.startsWith('https://wa.me')) return;
    if (link.getAttribute('target') === '_blank') return;
    if (link.hasAttribute('download')) return;
    if (href.includes('/cart/add/')) return;
    if (href.includes('/customer/login') || href.includes('/customer/register') || href.includes('/admin')) return;

    const isInternalScreen = href.includes('?screen=') || href === '/' || href.includes('/?screen=home') || (href.startsWith('http') && href.includes(window.location.host) && href.includes('?screen='));

    if (isInternalScreen) {
      e.preventDefault();
      instantNavigate(link.href, true);
    }
  });

  window.addEventListener('popstate', function() {
    instantNavigate(window.location.href, false);
  });

  function rebindDynamicHandlers() {
    initCustomerOrderTracker();
    const galleryInput = document.getElementById('avatarGalleryInput');
    if (galleryInput && !galleryInput.dataset.bound) {
      galleryInput.dataset.bound = 'true';
      galleryInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
          const file = e.target.files[0];
          const reader = new FileReader();
          reader.onload = function(evt) {
            const previewImg = document.getElementById('avatarPreviewImg');
            const initials = document.getElementById('avatarInitials');
            const statusBadge = document.getElementById('avatarStatusBadge');
            const statusText = document.getElementById('avatarStatusText');
            const base64Input = document.getElementById('avatarBase64Input');
            const removeInput = document.getElementById('removeAvatarInput');

            if (base64Input) base64Input.value = '';
            if (removeInput) removeInput.value = '0';
            if (previewImg) { previewImg.src = evt.target.result; previewImg.classList.remove('hidden'); }
            if (initials) { initials.classList.add('hidden'); }
            if (statusBadge && statusText) {
              statusText.textContent = 'Foto dari galeri siap disimpan: ' + file.name;
              statusBadge.classList.remove('hidden');
            }
          };
          reader.readAsDataURL(file);
        }
      });
    }

    const nativeCamInput = document.getElementById('avatarNativeCameraInput');
    if (nativeCamInput && !nativeCamInput.dataset.bound) {
      nativeCamInput.dataset.bound = 'true';
      nativeCamInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
          const file = e.target.files[0];
          try {
            const dt = new DataTransfer();
            dt.items.add(file);
            if (galleryInput) galleryInput.files = dt.files;
          } catch(err) {}
          const reader = new FileReader();
          reader.onload = function(evt) {
            const previewImg = document.getElementById('avatarPreviewImg');
            const initials = document.getElementById('avatarInitials');
            const statusBadge = document.getElementById('avatarStatusBadge');
            const statusText = document.getElementById('avatarStatusText');
            const base64Input = document.getElementById('avatarBase64Input');
            const removeInput = document.getElementById('removeAvatarInput');

            if (base64Input) base64Input.value = '';
            if (removeInput) removeInput.value = '0';
            if (previewImg) { previewImg.src = evt.target.result; previewImg.classList.remove('hidden'); }
            if (initials) initials.classList.add('hidden');
            if (statusBadge && statusText) {
              statusText.textContent = 'Foto dari kamera bawaan siap disimpan!';
              statusBadge.classList.remove('hidden');
            }
          };
          reader.readAsDataURL(file);
        }
      });
    }
  }

  // ==================== REAL-TIME LIVE CHAT (CUSTOMER) ====================
  let chatIsOpen = false;
  let chatPollTimer = null;
  let chatLastMessageId = 0;
  const audioCtx = (window.AudioContext || window.webkitAudioContext) ? new (window.AudioContext || window.webkitAudioContext)() : null;

  function playChatNotificationSound() {
    try {
      if (!audioCtx) return;
      if (audioCtx.state === 'suspended') audioCtx.resume();
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      osc.connect(gain);
      gain.connect(audioCtx.destination);
      osc.type = 'sine';
      osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
      osc.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.15); // A5
      gain.gain.setValueAtTime(0.12, audioCtx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.25);
      osc.start();
      osc.stop(audioCtx.currentTime + 0.25);
    } catch(e) {}
  }

  function toggleLiveChat() {
    if (chatIsOpen) {
      closeLiveChat();
    } else {
      openLiveChat();
    }
  }

  function openLiveChat() {
    chatIsOpen = true;
    const modal = document.getElementById('liveChatModal');
    const iconOpen = document.getElementById('chatIconOpen');
    const iconClose = document.getElementById('chatIconClose');
    const bubble = document.getElementById('chatFloatingBubble');

    if (modal) {
      modal.classList.remove('scale-90', 'opacity-0', 'pointer-events-none');
      modal.classList.add('scale-100', 'opacity-100', 'pointer-events-auto');
    }
    if (iconOpen) iconOpen.classList.add('hidden');
    if (iconClose) iconClose.classList.remove('hidden');
    if (bubble) bubble.style.display = 'none';

    const badge = document.getElementById('chatUnreadBadge');
    if (badge) badge.classList.add('hidden');

    loadChatMessages(true);
    startChatPolling();

    setTimeout(() => {
      const input = document.getElementById('chatInputMessage');
      if (input) input.focus();
    }, 150);
  }

  function closeLiveChat() {
    chatIsOpen = false;
    const modal = document.getElementById('liveChatModal');
    const iconOpen = document.getElementById('chatIconOpen');
    const iconClose = document.getElementById('chatIconClose');

    if (modal) {
      modal.classList.add('scale-90', 'opacity-0', 'pointer-events-none');
      modal.classList.remove('scale-100', 'opacity-100', 'pointer-events-auto');
    }
    if (iconOpen) iconOpen.classList.remove('hidden');
    if (iconClose) iconClose.classList.add('hidden');

    stopChatPolling();
  }

  function startChatPolling() {
    stopChatPolling();
    chatPollTimer = setInterval(() => {
      if (chatIsOpen) {
        loadChatMessages(false);
      }
    }, 3000);
  }

  function stopChatPolling() {
    if (chatPollTimer) {
      clearInterval(chatPollTimer);
      chatPollTimer = null;
    }
  }

  async function loadChatMessages(isInitial = false) {
    try {
      const url = `{{ route('chat.messages') }}?last_id=${chatLastMessageId}`;
      const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (!res.ok) return;
      const data = await res.json();
      if (!data.success) return;

      if (data.messages && data.messages.length > 0) {
        let hasNewAdminMsg = false;
        data.messages.forEach(msg => {
          if (msg.id > chatLastMessageId) {
            chatLastMessageId = msg.id;
          }
          appendMessageBubble(msg);
          if (msg.sender_type === 'admin') hasNewAdminMsg = true;
        });

        if (hasNewAdminMsg && !isInitial) {
          playChatNotificationSound();
        }

        scrollChatToBottom();
      }

      if (isInitial && data.unread_count && data.unread_count > 0 && !chatIsOpen) {
        const badge = document.getElementById('chatUnreadBadge');
        if (badge) {
          badge.innerText = data.unread_count;
          badge.classList.remove('hidden');
        }
      }

      if (chatLastMessageId > 0) {
        const chips = document.getElementById('chatQuickChips');
        if (chips) chips.style.display = 'none';
      }
    } catch(err) {
      // Offline fallback
    }
  }

  function appendMessageBubble(msg) {
    const container = document.getElementById('chatMessagesContainer');
    if (!container) return;

    if (document.getElementById(`chat-msg-${msg.id}`)) return;

    const isCustomer = (msg.sender_type === 'customer');
    const wrapper = document.createElement('div');
    wrapper.id = `chat-msg-${msg.id}`;
    wrapper.className = isCustomer ? 'flex items-end justify-end gap-2' : 'flex items-start gap-2 max-w-[85%]';

    if (isCustomer) {
      wrapper.innerHTML = `
        <div class="text-right">
          <div class="bg-[#e50027] text-white p-3 rounded-2xl rounded-br-sm text-xs shadow-sm max-w-[260px] text-left leading-relaxed">
            ${escapeHtml(msg.message)}
          </div>
          <span class="text-[9px] text-stone-400 mt-1 block pr-1">${msg.time}</span>
        </div>
      `;
    } else {
      wrapper.innerHTML = `
        <div class="w-7 h-7 rounded-full bg-red-100 text-[#e50027] flex items-center justify-center text-xs shrink-0 mt-1 shadow-sm">
          <i class="fas fa-headset"></i>
        </div>
        <div>
          <div class="bg-white text-stone-800 p-3 rounded-2xl rounded-tl-sm text-xs shadow-sm border border-[#e5dbcb] max-w-[260px] leading-relaxed">
            ${escapeHtml(msg.message)}
          </div>
          <span class="text-[9px] text-stone-400 mt-1 block pl-1">${msg.sender_name} • ${msg.time}</span>
        </div>
      `;
    }

    container.appendChild(wrapper);
  }

  function scrollChatToBottom() {
    const container = document.getElementById('chatMessagesContainer');
    if (container) {
      container.scrollTop = container.scrollHeight;
    }
  }

  function escapeHtml(str) {
    return str.replace(/[&<>"']/g, function(m) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
    });
  }

  function sendQuickMessage(text) {
    const input = document.getElementById('chatInputMessage');
    if (input) {
      input.value = text;
      handleSendChatMessage(new Event('submit'));
    }
  }

  async function handleSendChatMessage(e) {
    if (e && e.preventDefault) e.preventDefault();
    const input = document.getElementById('chatInputMessage');
    if (!input || !input.value.trim()) return;

    const messageText = input.value.trim();
    input.value = '';

    const guestInput = document.getElementById('chatGuestName');
    const guestName = guestInput ? guestInput.value.trim() : '';

    const tempId = 'temp-' + Date.now();
    const nowTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    appendMessageBubble({
      id: tempId,
      sender_type: 'customer',
      sender_name: 'Anda',
      message: messageText,
      time: nowTime
    });
    scrollChatToBottom();

    const chips = document.getElementById('chatQuickChips');
    if (chips) chips.style.display = 'none';

    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch("{{ route('chat.send') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          message: messageText,
          guest_name: guestName
        })
      });

      if (res.ok) {
        const data = await res.json();
        if (data.success && data.message) {
          const tempEl = document.getElementById(`chat-msg-${tempId}`);
          if (tempEl) tempEl.id = `chat-msg-${data.message.id}`;
          if (data.message.id > chatLastMessageId) {
            chatLastMessageId = data.message.id;
          }
        }
      }
    } catch(err) {
      // Error handling
    }
  }

  // === REALTIME ORDER TRACKING POLLE (3.5s) ===
  let customerTrackingTimer = null;
  let lastKnownOrderStep = null;

  function initCustomerOrderTracker() {
    const card = document.getElementById('liveOrderCard');
    if (!card) {
      if (customerTrackingTimer) {
        clearInterval(customerTrackingTimer);
        customerTrackingTimer = null;
      }
      return;
    }
    const orderCode = card.getAttribute('data-order-code');
    if (!orderCode) return;

    if (customerTrackingTimer) clearInterval(customerTrackingTimer);
    customerTrackingTimer = setInterval(() => {
      pollCustomerOrderStatus(orderCode);
    }, 3500);
  }

  async function pollCustomerOrderStatus(orderCode) {
    try {
      const res = await fetch(`{{ url('/api/order/status') }}/${orderCode}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (!res.ok) return;
      const data = await res.json();
      if (!data.success) return;

      const titleEl = document.getElementById('liveOrderTitle');
      const descEl = document.getElementById('liveOrderDesc');
      const progEl = document.getElementById('liveStepProgressBar');
      const courierCard = document.getElementById('liveCourierCard');
      const courierName = document.getElementById('liveCourierName');
      const courierPhone = document.getElementById('liveCourierPhone');
      const courierWa = document.getElementById('liveCourierWaBtn');

      if (titleEl && data.status_label) titleEl.innerText = data.status_label;
      if (descEl && data.status_desc) descEl.innerText = data.status_desc;
      if (progEl && data.status_step) progEl.style.width = `${(data.status_step / 5) * 100}%`;

      // Update stepper bubbles
      if (data.status_step) {
        if (lastKnownOrderStep !== null && lastKnownOrderStep !== data.status_step) {
          playCustomerChime();
        }
        lastKnownOrderStep = data.status_step;

        document.querySelectorAll('#liveStepperContainer .step-item').forEach(item => {
          const sNum = parseInt(item.getAttribute('data-step'));
          const bubble = item.querySelector('.step-bubble');
          const label = item.querySelector('.step-label');
          if (!bubble || !label) return;

          if (sNum < data.status_step) {
            bubble.className = 'w-9 h-9 md:w-11 md:h-11 rounded-full flex items-center justify-center font-bold text-xs md:text-sm transition-all shadow-sm step-bubble bg-green-600 text-white';
            bubble.innerHTML = '<i class="fas fa-check text-xs"></i>';
            label.className = 'text-[11px] md:text-xs font-bold mt-2 step-label text-green-800';
          } else if (sNum === data.status_step) {
            bubble.className = 'w-9 h-9 md:w-11 md:h-11 rounded-full flex items-center justify-center font-bold text-xs md:text-sm transition-all shadow-sm step-bubble bg-[#e50027] text-white ring-4 ring-red-100 scale-105';
            label.className = 'text-[11px] md:text-xs font-bold mt-2 step-label text-[#e50027]';
          } else {
            bubble.className = 'w-9 h-9 md:w-11 md:h-11 rounded-full flex items-center justify-center font-bold text-xs md:text-sm transition-all shadow-sm step-bubble bg-stone-200 text-stone-400';
            label.className = 'text-[11px] md:text-xs font-bold mt-2 step-label text-stone-400';
          }
        });
      }

      // Update courier card
      if (data.courier && courierCard) {
        courierCard.classList.remove('hidden');
        if (courierName) courierName.innerText = data.courier.name;
        if (courierPhone) courierPhone.innerText = data.courier.phone;
        if (courierWa) courierWa.href = data.courier.whatsapp_url;
      }
    } catch(e) {}
  }

  function playCustomerChime() {
    try {
      const AudioCtx = window.AudioContext || window.webkitAudioContext;
      if (!AudioCtx) return;
      const ctx = new AudioCtx();
      const now = ctx.currentTime;
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(587.33, now);
      osc.frequency.setValueAtTime(880.0, now + 0.15);
      gain.gain.setValueAtTime(0.2, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.45);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start(now);
      osc.stop(now + 0.45);
    } catch(e) {}
  }

  // === REALTIME STORE OPEN/CLOSED SYNC ===
  async function pollStoreStatus() {
    try {
      const res = await fetch("{{ url('/api/store-status') }}", {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (!res.ok) return;
      const data = await res.json();
      if (!data.success) return;

      const banner = document.getElementById('customerStoreClosedBanner');
      if (banner) {
        if (!data.shop_open) {
          banner.style.display = 'block';
        } else {
          banner.style.display = 'none';
        }
      }
    } catch(e) {}
  }

  document.addEventListener('DOMContentLoaded', function() {
    setupPrefetching();
    // Cek unread chat saat halaman awal dibuka (sekali saja)
    loadChatMessages(true);
    initCustomerOrderTracker();
    setInterval(pollStoreStatus, 15000);

    if (document.getElementById('app-main') && !isDynamicScreenUrl(window.location.href)) {
      screenCache.set(window.location.href.split('#')[0], {
        html: document.getElementById('app-main').innerHTML,
        title: document.title,
        url: window.location.href.split('#')[0]
      });
    }
  });
</script>
</body>
</html>

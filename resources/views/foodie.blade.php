<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $screens[$screen] ?? '3GFood' }} | Food Delivery</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-[#fdfbf7] text-[#111111]">

    <!-- Navbar -->
    <nav class="flex items-center justify-between px-6 py-5 bg-white md:px-16 w-full fixed top-0 z-50 shadow-sm">
        <!-- Logo -->
        <a href="{{ url('/?screen=home') }}" class="flex items-center gap-3">
            <div class="relative flex items-center justify-center w-10 h-10 bg-[#e50027] rounded-full">
                <!-- Cookie dots -->
                <div class="absolute w-1.5 h-1.5 bg-white rounded-full top-2 left-3"></div>
                <div class="absolute w-1 h-1 bg-white rounded-full top-5 left-2"></div>
                <div class="absolute w-1.5 h-1.5 bg-white rounded-full top-6 left-6"></div>
                <div class="absolute w-1 h-1 bg-white rounded-full top-3 right-3"></div>
            </div>
            <span class="text-xl font-bold font-heading leading-tight">Food<br><span class="font-normal text-sm">Delivery</span></span>
        </a>

        <!-- Links -->
        <div class="hidden md:flex items-center gap-8 font-semibold text-sm">
            <a href="{{ url('/?screen=home') }}" class="{{ $screen === 'home' ? 'text-[#e50027]' : 'hover:text-[#e50027] transition-colors' }}">Home</a>
            <a href="{{ url('/?screen=home#menu-section') }}" class="hover:text-[#e50027] transition-colors">Menu</a>
            <a href="{{ url('/?screen=track') }}" class="{{ $screen === 'track' ? 'text-[#e50027]' : 'hover:text-[#e50027] transition-colors' }}">Track Order</a>
            <a href="{{ url('/?screen=feedback') }}" class="{{ $screen === 'feedback' ? 'text-[#e50027]' : 'hover:text-[#e50027] transition-colors' }}">Contact</a>
        </div>

        <!-- Icons & Phone -->
        <div class="flex items-center gap-6">
            <a href="#" class="text-gray-800 hover:text-[#e50027] transition-colors"><i class="fas fa-search text-lg"></i></a>
            <a href="{{ url('/?screen=cart') }}" class="relative text-gray-800 hover:text-[#e50027] transition-colors">
                <i class="fas fa-shopping-bag text-lg"></i>
                @if($cartCount > 0)
                <span class="absolute -top-2 -right-2 flex items-center justify-center w-[18px] h-[18px] text-[10px] font-bold text-white bg-[#e50027] border-2 border-white rounded-full">{{ $cartCount }}</span>
                @endif
            </a>
            <a href="tel:+11234567890" class="hidden md:flex items-center gap-2 px-6 py-3 text-sm font-bold text-white bg-[#e50027] rounded-full hover:bg-red-700 transition-colors shadow-lg shadow-red-200">
                <i class="fas fa-phone-alt"></i> +1 123 456 7890
            </a>
        </div>
    </nav>

    <!-- Main Wrapper -->
    <main class="pt-[88px] min-h-screen">
        
        @if(!$shopOpen)
            <div class="max-w-7xl mx-auto px-6 mt-6">
                <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50" role="alert">
                    <span class="font-medium">⚠️ Toko sedang tutup saat ini.</span> Jam operasional: {{ $shopSettings->jam_buka ?? '08:00' }} - {{ $shopSettings->jam_tutup ?? '21:00' }}
                </div>
            </div>
        @endif

        @if($screen === 'home')
            <!-- Hero Section -->
            <section class="relative flex flex-col md:flex-row items-center justify-between overflow-hidden bg-[#fdfbf7]">
                <!-- Background Pattern (Subtle) -->
                <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M54.627 0l.83.83-53.797 53.797-.83-.83L54.627 0zM27.5 31c-1.933 0-3.5-1.567-3.5-3.5s1.567-3.5 3.5-3.5 3.5 1.567 3.5 3.5-1.567 3.5-3.5 3.5z\' fill=\'%23000000\' fill-opacity=\'1\' fill-rule=\'evenodd\'/%3E%3C/svg%3E');"></div>
                
                <!-- Left Content -->
                <div class="relative z-10 w-full md:w-1/2 px-6 md:pl-20 py-16 md:py-32">
                    <h1 class="text-[52px] md:text-[68px] font-extrabold font-heading leading-[1.1] mb-4 text-[#111]">
                        We Deliver <br>The Taste Of Life
                    </h1>
                    <p class="text-gray-600 text-lg mb-10 font-medium">Get It Delivered Right To Your Door!</p>
                    
                    <a href="#menu-section" class="inline-flex items-center justify-center px-10 py-4 text-sm font-bold text-white bg-[#e50027] rounded-full hover:bg-red-700 transition-colors shadow-lg shadow-red-200 whitespace-nowrap">
                        Browse Menu <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>

                <!-- Right Content (Yellow Curve) -->
                <div class="relative w-full md:w-1/2 min-h-[400px] md:min-h-[600px] custom-curved-bg flex items-center justify-center overflow-visible">
                    <!-- Scooter Image -->
                    <img src="https://plus.unsplash.com/premium_photo-1661609139580-c1190bc1fdf6?q=80&w=600&auto=format&fit=crop" alt="Scooter Delivery" class="relative z-10 w-[80%] max-w-[500px] object-cover rounded-3xl shadow-2xl -ml-12 mt-12 mix-blend-multiply" style="clip-path: circle(40%);">
                    
                    <!-- Floating Social Sidebar -->
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 flex flex-col gap-4 bg-white p-3 rounded-full shadow-lg">
                        <a href="#" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-[#e50027] rounded-full hover:bg-red-50"><i class="fab fa-facebook-f text-sm"></i></a>
                        <a href="#" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-[#e50027] rounded-full hover:bg-red-50"><i class="fab fa-twitter text-sm"></i></a>
                        <a href="#" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-[#e50027] rounded-full hover:bg-red-50"><i class="fab fa-linkedin-in text-sm"></i></a>
                        <a href="#" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-[#e50027] rounded-full hover:bg-red-50"><i class="fab fa-youtube text-sm"></i></a>
                        <a href="#" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-[#e50027] rounded-full hover:bg-red-50"><i class="fab fa-instagram text-sm"></i></a>
                    </div>
                </div>
            </section>

            <!-- Browse Category & Products -->
            <section id="menu-section" class="max-w-7xl mx-auto px-6 py-20">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold font-heading mb-4">Browse Food Category</h2>
                    <p class="text-gray-500 max-w-2xl mx-auto text-sm leading-relaxed">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                </div>
                
                <div class="flex flex-wrap justify-center gap-4 mb-12">
                    <a href="{{ url('/?screen=home#menu-section') }}" class="px-6 py-2 rounded-full text-sm font-semibold transition-colors {{ !$category ? 'bg-[#e50027] text-white shadow-lg shadow-red-200' : 'bg-[#f4f5f7] text-gray-600 hover:bg-gray-200' }}">Semua</a>
                    <a href="{{ url('/?screen=home&category=Paket Nasi Liwet#menu-section') }}" class="px-6 py-2 rounded-full text-sm font-semibold transition-colors {{ $category === 'Paket Nasi Liwet' ? 'bg-[#e50027] text-white shadow-lg shadow-red-200' : 'bg-[#f4f5f7] text-gray-600 hover:bg-gray-200' }}">Paket Nasi Liwet</a>
                    <a href="{{ url('/?screen=home&category=Lauk Utama#menu-section') }}" class="px-6 py-2 rounded-full text-sm font-semibold transition-colors {{ $category === 'Lauk Utama' ? 'bg-[#e50027] text-white shadow-lg shadow-red-200' : 'bg-[#f4f5f7] text-gray-600 hover:bg-gray-200' }}">Lauk Utama</a>
                    <a href="{{ url('/?screen=home&category=Menu Tambahan#menu-section') }}" class="px-6 py-2 rounded-full text-sm font-semibold transition-colors {{ $category === 'Menu Tambahan' ? 'bg-[#e50027] text-white shadow-lg shadow-red-200' : 'bg-[#f4f5f7] text-gray-600 hover:bg-gray-200' }}">Menu Tambahan</a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($visibleMenu as $itemId => $item)
                    <div class="bg-white rounded-3xl p-6 text-center shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50 hover:-translate-y-2 transition-transform duration-300">
                        <div class="relative bg-[#f4f5f7] rounded-2xl p-6 mb-6 h-48 flex items-center justify-center overflow-hidden">
                            @if(in_array($itemId, [1,2,5,7]))
                            <span class="absolute top-4 left-4 z-10 bg-[#fdc52c] text-[#111] text-[10px] font-bold px-3 py-1 rounded-full uppercase">Recommended</span>
                            @endif
                            <img src="{{ $item[3] }}" alt="{{ $item[0] }}" class="w-full h-full object-cover filter drop-shadow-md mix-blend-multiply">
                        </div>
                        
                        <div class="flex justify-center text-[#fdc52c] text-[10px] mb-3 gap-[2px]">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <h3 class="font-bold text-[#111] mb-2 truncate">{{ $item[0] }}</h3>
                        <p class="font-bold text-[#111] mb-6 text-lg">{{ $item[1] }}</p>
                        <a href="{{ url('/cart/add/' . $itemId) }}" class="inline-block bg-[#e50027] text-white font-bold text-xs px-8 py-3 rounded-full hover:bg-red-700 transition-colors shadow-lg shadow-red-200">Add To Cart</a>
                    </div>
                    @endforeach
                </div>
            </section>
        
        @else
            <!-- Non-Home Screens (Cart, Track, Checkout, etc.) -->
            <section class="max-w-5xl mx-auto px-6 py-12">
                <div class="mb-10 text-center">
                    <span class="text-[#e50027] font-bold text-xs tracking-wider uppercase mb-2 block">{{ $screens[$screen] ?? '' }}</span>
                    <h1 class="text-4xl font-extrabold font-heading">{{ match($screen) { 'cart' => 'Your Cart', 'buyer' => 'Checkout Details', 'payment' => 'Payment Method', 'confirmation' => 'Order Success', 'status' => 'Order Status', 'track' => 'Track Order', 'feedback' => 'Contact Us', default => $screens[$screen] } }}</h1>
                </div>

                @if($screen === 'cart')
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <div class="lg:col-span-2 bg-white rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-xl font-bold">Order List</h2>
                                @if(count($cart))<a href="{{ url('/cart/clear') }}" class="text-sm text-red-500 hover:underline">Clear all</a>@endif
                            </div>
                            
                            <div class="space-y-6">
                                @forelse($cart as $itemId => $quantity)
                                <div class="flex items-center gap-4 py-4 border-b border-gray-100 last:border-0">
                                    <div class="bg-[#f4f5f7] p-2 rounded-xl h-20 w-20 flex-shrink-0 flex items-center justify-center">
                                        <img src="{{ $menu[$itemId][3] ?? '' }}" class="object-cover h-full w-full mix-blend-multiply rounded-md">
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-[#111]">{{ $menu[$itemId][0] ?? 'Menu tidak tersedia' }}</h3>
                                        <p class="text-gray-500 text-xs">{{ $menu[$itemId][1] ?? 'Harga tidak tersedia' }}</p>
                                        <div class="inline-block mt-2 bg-[#fdfbf7] border border-gray-200 text-[#e50027] text-xs font-bold px-3 py-1 rounded-md">{{ $quantity }} item</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-[#111] mb-2">Rp {{ number_format(($menu[$itemId][2] ?? 0) * $quantity, 0, ',', '.') }}</div>
                                        <a href="{{ url('/cart/remove/' . $itemId) }}" class="text-xs text-red-500 hover:underline"><i class="fas fa-trash-alt"></i> Remove</a>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-10 text-gray-400">
                                    <i class="fas fa-shopping-basket text-4xl mb-4"></i>
                                    <p>Your cart is empty.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50 h-fit">
                            <h2 class="text-xl font-bold mb-6">Order Summary</h2>
                            <div class="space-y-4 text-sm">
                                <div class="flex justify-between text-gray-500"><span>Subtotal</span> <strong class="text-[#111]">Rp {{ number_format($subtotal, 0, ',', '.') }}</strong></div>
                                <div class="flex justify-between text-gray-500"><span>Delivery</span> <strong class="text-[#111]">Rp {{ number_format($delivery, 0, ',', '.') }}</strong></div>
                                
                                @if($voucherDiscount > 0)
                                    <div class="flex justify-between text-[#e50027]"><span>Voucher</span> <strong>- Rp {{ number_format($voucherDiscount, 0, ',', '.') }}</strong></div>
                                @else
                                    <div class="pt-4 border-t border-gray-100">
                                        <h3 class="font-bold mb-3">Voucher Code</h3>
                                        <form id="voucher-form" method="POST" class="flex gap-2">
                                            @csrf
                                            <input id="voucher-code" type="text" name="voucher_code" placeholder="Enter code" class="flex-1 border border-gray-200 rounded-lg px-4 py-2 text-sm outline-none focus:border-[#e50027]" />
                                            <button class="bg-[#111] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-800 transition-colors" type="submit">Apply</button>
                                        </form>
                                        <div id="voucher-message" class="text-xs mt-2 text-red-500 font-medium"></div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mt-6 pt-6 border-t border-gray-100 border-dashed flex justify-between items-center text-lg">
                                <span class="font-bold">Total</span>
                                <strong id="order-total" class="text-[#e50027] font-extrabold">Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                            </div>
                            
                            @if(count($cart))
                                <a href="{{ url('/?screen=buyer') }}" class="block w-full text-center mt-8 bg-[#e50027] text-white font-bold py-4 rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200">Checkout Now <i class="fas fa-arrow-right ml-2"></i></a>
                            @endif
                        </div>
                    </div>
                @elseif($screen === 'buyer')
                    <div class="max-w-2xl mx-auto bg-white rounded-3xl p-8 md:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50">
                        <h2 class="text-2xl font-bold mb-6 text-center">Delivery Details</h2>
                        <form class="space-y-5" method="POST" action="{{ route('checkout.buyer') }}">
                            @csrf
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Full Name</label>
                                <input type="text" name="customer_name" value="{{ old('customer_name', $buyer['customer_name'] ?? '') }}" required placeholder="John Doe" class="w-full bg-[#f4f5f7] border-0 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#e50027] outline-none transition-all text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone', $buyer['phone'] ?? '') }}" required placeholder="08xx-xxxx-xxxx" class="w-full bg-[#f4f5f7] border-0 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#e50027] outline-none transition-all text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Delivery Address</label>
                                <textarea name="address" required placeholder="Full address..." class="w-full bg-[#f4f5f7] border-0 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#e50027] outline-none transition-all text-sm min-h-[120px]">{{ old('address', $buyer['address'] ?? '') }}</textarea>
                            </div>
                            @if($errors->any())<p class="text-red-500 text-sm font-medium">{{ $errors->first() }}</p>@endif
                            <button type="submit" class="w-full bg-[#e50027] text-white font-bold py-4 rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200 mt-4">Continue to Payment <i class="fas fa-arrow-right ml-2"></i></button>
                        </form>
                    </div>
                @elseif($screen === 'payment')
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <div class="lg:col-span-2 bg-white rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50">
                            <h2 class="text-xl font-bold mb-6">Select Payment Method</h2>
                            <form method="POST" action="{{ route('checkout.payment') }}" class="space-y-4">
                                @csrf
                                <label class="flex items-start gap-4 p-5 rounded-2xl border-2 border-transparent bg-[#f4f5f7] cursor-pointer hover:bg-gray-100 transition-colors has-[:checked]:bg-red-50 has-[:checked]:border-[#e50027]">
                                    <input type="radio" name="payment_method" value="cash" checked class="mt-1 accent-[#e50027]">
                                    <div>
                                        <b class="text-[#111] font-bold">Cash on Delivery (COD)</b>
                                        <p class="text-gray-500 text-sm mt-1">Please prepare exact cash amount.</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-start gap-4 p-5 rounded-2xl border-2 border-transparent bg-[#f4f5f7] cursor-pointer hover:bg-gray-100 transition-colors has-[:checked]:bg-red-50 has-[:checked]:border-[#e50027]">
                                    <input type="radio" name="payment_method" value="bank" class="mt-1 accent-[#e50027]">
                                    <div>
                                        <b class="text-[#111] font-bold">Bank Transfer</b>
                                        <p class="text-gray-500 text-sm mt-1">Transfer to our official bank account.</p>
                                        <div class="mt-4 p-4 bg-white rounded-xl border border-gray-200">
                                            <p class="text-xs text-gray-500 mb-1">BCA Account</p>
                                            <p class="font-bold text-lg">1234 5678 9012</p>
                                            <p class="text-sm font-medium">A/N 3GFood Delivery</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="flex items-start gap-4 p-5 rounded-2xl border-2 border-transparent bg-[#f4f5f7] cursor-pointer hover:bg-gray-100 transition-colors has-[:checked]:bg-red-50 has-[:checked]:border-[#e50027]">
                                    <input type="radio" name="payment_method" value="digital" class="mt-1 accent-[#e50027]">
                                    <div>
                                        <b class="text-[#111] font-bold">E-Wallet / QRIS</b>
                                        <p class="text-gray-500 text-sm mt-1">Pay with GoPay, OVO, DANA or QRIS.</p>
                                        <div class="mt-4 p-4 bg-white rounded-xl border border-gray-200 inline-block text-center">
                                            <div class="w-32 h-32 bg-gray-100 flex items-center justify-center font-bold text-gray-400 mb-2 rounded-lg border-2 border-dashed border-gray-300">QRIS</div>
                                            <p class="text-xs font-bold">Scan to Pay</p>
                                        </div>
                                    </div>
                                </label>
                                
                                @if($errors->any())<p class="text-red-500 text-sm font-medium">{{ $errors->first() }}</p>@endif
                                
                                <button type="submit" class="w-full bg-[#e50027] text-white font-bold py-4 rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200 mt-6">Confirm Order <i class="fas fa-check ml-2"></i></button>
                            </form>
                        </div>

                        <div class="bg-white rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50 h-fit">
                            <h2 class="text-xl font-bold mb-6">Order Total</h2>
                            <div class="space-y-4 text-sm">
                                <div class="flex justify-between text-gray-500"><span>Subtotal</span> <strong class="text-[#111]">Rp {{ number_format($subtotal, 0, ',', '.') }}</strong></div>
                                <div class="flex justify-between text-gray-500"><span>Delivery</span> <strong class="text-[#111]">Rp {{ number_format($delivery, 0, ',', '.') }}</strong></div>
                                @if($voucherDiscount > 0)
                                    <div class="flex justify-between text-[#e50027]"><span>Voucher</span> <strong>- Rp {{ number_format($voucherDiscount, 0, ',', '.') }}</strong></div>
                                @endif
                            </div>
                            <div class="mt-6 pt-6 border-t border-gray-100 border-dashed flex justify-between items-center text-lg">
                                <span class="font-bold">Total</span>
                                <strong class="text-[#e50027] font-extrabold">Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                @elseif($screen === 'confirmation')
                    <div class="max-w-xl mx-auto bg-white rounded-3xl p-10 text-center shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50">
                        <div class="w-20 h-20 bg-green-100 text-green-500 rounded-full flex items-center justify-center text-4xl mx-auto mb-6">
                            <i class="fas fa-check"></i>
                        </div>
                        <h2 class="text-3xl font-extrabold font-heading mb-4">Order Received!</h2>
                        <p class="text-gray-500 mb-8">Your order has been successfully placed and is waiting for confirmation.</p>
                        
                        <div class="bg-[#fdfbf7] border-2 border-dashed border-[#fdc52c] rounded-xl p-6 mb-8 text-[#fdc52c]">
                            <p class="text-sm font-bold text-gray-500 mb-1">ORDER ID</p>
                            <p class="text-2xl font-black text-[#111]">#{{ $order['order_code'] ?? 'FE-BARU' }}</p>
                        </div>
                        
                        @if(($order['payment_method'] ?? '') === 'bank')
                            <div class="bg-gray-50 rounded-2xl p-6 mb-8 border border-gray-200">
                                <h3 class="font-bold text-[#111] mb-2">Silakan transfer ke Rekening BCA:</h3>
                                <div class="bg-white p-4 rounded-xl shadow-sm text-center border border-gray-200 inline-block mb-3">
                                    <p class="text-2xl font-black text-[#e50027] tracking-widest">1234 5678 9012</p>
                                    <p class="font-bold text-gray-500 text-sm mt-1">A/N 3GFood Delivery</p>
                                </div>
                                <p class="text-sm text-gray-500">Jumlah Tagihan: <strong class="text-[#111]">Rp {{ number_format($order['total'] ?? 0, 0, ',', '.') }}</strong></p>
                            </div>
                        @elseif(($order['payment_method'] ?? '') === 'digital')
                            <div class="bg-gray-50 rounded-2xl p-6 mb-8 border border-gray-200">
                                <h3 class="font-bold text-[#111] mb-4">Silakan scan QRIS di bawah ini:</h3>
                                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 inline-block text-center mb-3">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=QRIS_3GFOOD_DUMMY" alt="QRIS 3GFood" class="mx-auto mb-2 w-40 h-40">
                                    <p class="font-bold text-gray-500 text-sm">QRIS 3GFood</p>
                                </div>
                                <p class="text-sm text-gray-500">Jumlah Tagihan: <strong class="text-[#111]">Rp {{ number_format($order['total'] ?? 0, 0, ',', '.') }}</strong></p>
                            </div>
                        @endif
                        
                        <p class="text-sm text-gray-500 font-medium mb-6"><i class="fas fa-info-circle mr-1"></i> Jangan lupa unggah bukti pembayaran di halaman Lacak Pesanan.</p>
                        
                        <a href="{{ url('/?screen=status') }}" class="inline-block bg-[#e50027] text-white font-bold px-8 py-4 rounded-full hover:bg-red-700 transition-colors shadow-lg shadow-red-200">Lacak & Unggah Bukti</a>
                    </div>
                @elseif($screen === 'track' || $screen === 'status')
                    <div class="max-w-2xl mx-auto bg-white rounded-3xl p-8 md:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50">
                        <form method="GET" action="{{ url('/') }}" class="flex flex-col sm:flex-row gap-4 mb-8">
                            <input type="hidden" name="screen" value="track">
                            <input type="text" name="order_code" value="{{ request('order_code', $order['order_code'] ?? '') }}" required placeholder="Enter Order ID or WhatsApp..." class="flex-1 bg-[#f4f5f7] border-0 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#e50027] outline-none transition-all text-sm">
                            <button type="submit" class="bg-[#e50027] text-white font-bold px-8 py-4 rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200 whitespace-nowrap"><i class="fas fa-search mr-2"></i> Track</button>
                        </form>

                        @if($trackCode || isset($order['status']))
                            @php
                                $tracked = $trackedOrder ?? ($order ?? null);
                            @endphp
                            @if($tracked)
                                <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl font-bold text-sm mb-6 flex items-center gap-3">
                                    <i class="fas fa-check-circle text-lg"></i> Order Found: #{{ $tracked['order_code'] }}
                                </div>
                                <div class="bg-[#f4f5f7] rounded-2xl p-6 border border-gray-100">
                                    <h3 class="text-lg font-bold mb-4">Status: 
                                        <span class="text-[#e50027]">
                                        {{ match($tracked['status']) {
                                            'waiting', 'waiting_owner', 'waiting_payment' => 'Awaiting Confirmation',
                                            'accepted' => 'Order Accepted',
                                            'cooking' => 'Cooking in progress',
                                            'ready_to_deliver' => 'Ready for delivery',
                                            'delivering' => 'Out for Delivery',
                                            'completed', 'confirmed' => 'Delivered / Completed',
                                            default => 'Unknown'
                                        } }}
                                        </span>
                                    </h3>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <p>Total Amount: <strong class="text-[#111]">Rp {{ number_format($tracked['total'], 0, ',', '.') }}</strong></p>
                                        <p>Payment Method: <strong class="text-[#111] uppercase">{{ $tracked['payment_method'] }}</strong></p>
                                    </div>
                                    
                                    @if(in_array($tracked['payment_method'], ['bank', 'digital']))
                                        <div class="mt-6 pt-6 border-t border-gray-200">
                                            @if(($tracked['payment_method'] === 'bank') && !($tracked['payment_proof'] ?? null))
                                                <div class="mb-4 bg-gray-50 p-4 rounded-xl border border-gray-200 text-center">
                                                    <p class="text-sm text-gray-500 mb-1">Transfer BCA</p>
                                                    <p class="text-xl font-bold tracking-widest text-[#e50027]">1234 5678 9012</p>
                                                    <p class="text-xs font-bold text-gray-500">A/N 3GFood Delivery</p>
                                                </div>
                                            @elseif(($tracked['payment_method'] === 'digital') && !($tracked['payment_proof'] ?? null))
                                                <div class="mb-4 bg-gray-50 p-4 rounded-xl border border-gray-200 text-center">
                                                    <p class="text-sm text-gray-500 mb-2">Scan QRIS</p>
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=QRIS_3GFOOD_DUMMY" alt="QRIS" class="mx-auto w-32 h-32 mb-1">
                                                </div>
                                            @endif

                                            @if($tracked['payment_proof'] ?? null)
                                                <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg font-bold text-sm inline-flex items-center gap-2">
                                                    <i class="fas fa-file-invoice"></i> Payment proof uploaded
                                                </div>
                                            @else
                                                <h4 class="font-bold text-sm mb-3">Upload Payment Proof</h4>
                                                <form method="POST" action="{{ url('/checkout/proof') }}" enctype="multipart/form-data" class="flex flex-col gap-3">
                                                    @csrf
                                                    <input type="hidden" name="order_code" value="{{ $tracked['order_code'] }}">
                                                    <input type="file" name="payment_proof" required accept="image/*" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-[#e50027] hover:file:bg-red-100">
                                                    <button type="submit" class="bg-[#111] text-white font-bold py-2 px-6 rounded-full self-start hover:bg-gray-800 text-sm">Upload</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl font-bold text-sm flex items-center gap-3">
                                    <i class="fas fa-exclamation-circle text-lg"></i> Order not found. Check your Order ID.
                                </div>
                            @endif
                        @endif
                    </div>
                @elseif($screen === 'feedback')
                    <div class="max-w-2xl mx-auto bg-white rounded-3xl p-8 md:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50">
                        <form method="POST" action="{{ route('feedback.whatsapp') }}" class="space-y-5">
                            @csrf
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Name</label>
                                <input type="text" name="name" value="{{ old('name', $buyer['customer_name'] ?? '') }}" required placeholder="Your name" class="w-full bg-[#f4f5f7] border-0 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#e50027] outline-none transition-all text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Message</label>
                                <textarea name="message" required placeholder="Tell us what you think..." class="w-full bg-[#f4f5f7] border-0 rounded-xl px-5 py-4 focus:ring-2 focus:ring-[#e50027] outline-none transition-all text-sm min-h-[150px]"></textarea>
                            </div>
                            @if($errors->has('feedback'))<p class="text-red-500 text-sm font-medium">{{ $errors->first('feedback') }}</p>@endif
                            <button type="submit" class="w-full bg-[#e50027] text-white font-bold py-4 rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200 mt-4"><i class="fab fa-whatsapp mr-2 text-lg"></i> Send via WhatsApp</button>
                        </form>
                    </div>
                @else
                    <!-- Fallback -->
                    <div class="max-w-2xl mx-auto bg-white rounded-3xl p-8 md:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50 text-center">
                        <h2 class="text-2xl font-bold mb-4">Page Not Found</h2>
                        <a href="{{ url('/?screen=home') }}" class="inline-block bg-[#e50027] text-white font-bold px-8 py-3 rounded-full hover:bg-red-700 transition-colors">Go to Home</a>
                    </div>
                @endif
            </section>
        @endif
    </main>

    <footer class="bg-white border-t border-gray-100 py-10 mt-10">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <div class="flex items-center justify-center gap-2 mb-6">
                <div class="w-8 h-8 bg-[#e50027] rounded-full flex items-center justify-center"><i class="fas fa-cookie-bite text-white text-xs"></i></div>
                <span class="text-lg font-bold font-heading">Food Delivery</span>
            </div>
            <p class="text-gray-400 text-sm">© {{ date('Y') }} 3GFood Delivery. All rights reserved.</p>
        </div>
    </footer>

<script>
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
</script>
</body>
</html>

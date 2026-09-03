@php
    $screens = ['home' => 'Beranda', 'cart' => 'Keranjang', 'status' => 'Status Pesanan', 'feedback' => 'Kritik dan Saran'];
    $screen = $screen ?? 'home';
    $category = $category ?? null;
    $menu = [];
    foreach (($managedMenu ?? collect()) as $managedItem) {
        $menu[$managedItem->id] = [$managedItem->name, 'Rp ' . number_format($managedItem->price, 0, ',', '.'), $managedItem->price, $managedItem->image_url ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=85', $managedItem->category];
    }
    $cart = session('cart', []);
    $buyer = session('buyer', []);
    $subtotal = 0;
    foreach ($cart as $itemId => $quantity) {
        $subtotal += ($menu[$itemId][2] ?? 0) * $quantity;
    }
    $delivery = $subtotal > 0 ? 1000 : 0;
    $total = $subtotal + $delivery;
    $visibleMenu = $category
        ? collect($menu)->filter(fn ($item) => $item[4] === $category)->all()
        : $menu;
    $cartCount = array_sum($cart);
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $screens[$screen] ?? '3GFood' }} | 3GFood</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        .checkout-form label { display:block; margin:18px 0; font-weight:700; }
        .checkout-form input, .checkout-form textarea { display:block; width:100%; margin-top:8px; padding:13px 14px; border:1px solid var(--line); border-radius:8px; background:#fff; font-size:15px; }
        .checkout-form textarea { min-height:110px; resize:vertical; }
        .payment-choice { display:flex!important; align-items:flex-start; gap:12px; padding:16px; border:1px solid var(--line); border-radius:10px; cursor:pointer; margin-bottom:10px; }
        .payment-choice input { width:auto; margin:3px 0 0; }
        .payment-choice small { display:block; color:var(--muted); font-weight:400; margin-top:4px; }
        .payment-info { margin-top:10px; padding:18px; background:var(--soft); border-radius:10px; }
        .payment-info strong { display:block; font-size:22px; margin-top:8px; }
        .qr-placeholder { width:130px; height:130px; margin-top:12px; display:grid; place-items:center; background:repeating-linear-gradient(45deg,#29251f 0 4px,#fff 4px 8px); color:#fff; font-size:11px; text-align:center; }
        .error-text { color:#b42318; font-size:13px; margin-top:8px; }
        .feedback-panel { max-width:620px; margin:36px 0 0; padding:24px; background:#fff; border:1px solid var(--line); border-radius:12px; }
        .feedback-panel h2 { margin:0 0 6px; font:700 22px 'Playfair Display',serif; }
        .feedback-panel p { color:var(--muted); line-height:1.6; margin:0 0 16px; }
        .feedback-panel label { display:block; margin:12px 0; font-weight:700; }
        .feedback-panel input, .feedback-panel textarea { display:block; width:100%; margin-top:7px; padding:11px 12px; border:1px solid var(--line); border-radius:8px; background:#fff; }
        .feedback-panel textarea { min-height:100px; resize:vertical; }
        .cart-heading { display:flex; align-items:center; justify-content:space-between; gap:16px; }
        .remove-btn { color:#b42318; font-size:12px; font-weight:700; white-space:nowrap; }

        /* MOBILE BOTTOM NAV */
        .mobile-bottom-nav { display:none; position:fixed; bottom:0; left:0; right:0; height:64px; background:#fff; border-top:1px solid var(--line); z-index:999; align-items:stretch; box-shadow:0 -4px 20px rgba(0,0,0,.07); }
        .mobile-bottom-nav a { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:3px; color:#a8a095; font-size:10px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; transition:color .2s; position:relative; }
        .mobile-bottom-nav a.active { color:var(--orange); }
        .mobile-bottom-nav a.active .nav-icon { background:var(--soft); }
        .nav-icon { display:flex; align-items:center; justify-content:center; width:38px; height:28px; border-radius:10px; transition:background .2s; }
        .nav-icon svg { width:21px; height:21px; stroke-width:1.8; }
        .nav-badge { position:absolute; top:4px; right:calc(50% - 20px); background:var(--orange); color:#fff; border-radius:99px; font-size:9px; font-weight:700; min-width:16px; height:16px; padding:0 4px; display:flex; align-items:center; justify-content:center; }

        /* MOBILE TOP BAR */
        .mobile-top-bar { display:none; align-items:center; justify-content:space-between; padding:14px 18px 12px; background:#fff; border-bottom:1px solid var(--line); position:sticky; top:0; z-index:100; box-shadow:0 2px 10px rgba(0,0,0,.05); }
        .m-brand { font:800 20px 'Playfair Display',serif; color:var(--orange); }
        .m-brand span { color:var(--ink); }
        .m-cart-icon { position:relative; display:flex; align-items:center; justify-content:center; width:40px; height:36px; background:var(--soft); border-radius:10px; }
        .m-cart-icon svg { width:20px; height:20px; stroke:var(--ink); stroke-width:1.8; }
        .m-badge { position:absolute; top:-4px; right:-4px; background:var(--orange); color:#fff; border-radius:99px; font-size:9px; font-weight:700; min-width:16px; height:16px; display:flex; align-items:center; justify-content:center; padding:0 3px; }

        /* MOBILE HERO */
        .mobile-hero { display:none; margin:14px 18px; background:linear-gradient(135deg,var(--orange) 0%,#e07340 100%); border-radius:18px; padding:22px 20px; color:#fff; position:relative; overflow:hidden; }
        .mobile-hero::after { content:'🍱'; position:absolute; right:16px; bottom:-4px; font-size:68px; opacity:.22; }
        .mobile-hero h2 { font:800 22px 'Playfair Display',serif; margin:0 0 6px; line-height:1.2; }
        .mobile-hero p { font-size:13px; margin:0; opacity:.88; line-height:1.5; }
        .mobile-hero a { display:inline-block; margin-top:14px; background:#fff; color:var(--orange); border-radius:8px; padding:8px 18px; font-size:12px; font-weight:700; }

        @media (max-width:700px) {
            .mobile-bottom-nav { display:flex; }
            .mobile-top-bar { display:flex; }
            .mobile-hero { display:block; }
            .sidebar { display:none !important; }
            .topbar { display:none !important; }
            .app-shell { display:block; }
            .content { padding:0 0 80px !important; max-width:100% !important; margin:0 !important; }
            .welcome-row { display:none; }
            .section-head { padding:14px 18px 0; margin:0; }
            .section-head h2 { font-size:17px; }
            .category-row { padding:10px 18px 0; margin:0; gap:8px; overflow-x:auto; flex-wrap:nowrap; scrollbar-width:none; -webkit-overflow-scrolling:touch; }
            .category-row::-webkit-scrollbar { display:none; }
            .category { padding:8px 14px; font-size:12px; white-space:nowrap; }
            .menu-grid { grid-template-columns:repeat(2,1fr) !important; gap:12px !important; padding:12px 18px !important; margin:0 !important; }
            .menu-card>img { height:118px; }
            .menu-card>div { padding:10px 12px 12px; min-height:88px; }
            .menu-card h3 { font-size:12px; margin:0 0 2px; }
            .menu-card p { display:none; }
            .add-btn { width:26px; height:26px; font-size:17px; right:10px; bottom:10px; }
            .page-heading { padding:18px 18px 14px; }
            .page-heading h1 { font-size:28px !important; }
            .two-col { display:block; padding:0 18px; }
            .summary { margin-top:16px; }
            .cart-item img { width:66px; height:56px; border-radius:8px; }
            .primary-btn { justify-content:center; }
            .checkout-form { padding:0 18px; }
            .success-view { margin:50px auto; padding:0 24px; }
            .panel.status-panel { margin:0 18px; }
        }
    </style>
</head>
<body>
<div class="app-shell">
    {{-- DESKTOP SIDEBAR --}}
    <aside class="sidebar">
        <a class="brand" href="{{ url('/?screen=home') }}">3G<strong>Food</strong></a>
        <small class="side-label">MENU UTAMA</small>
        @foreach($screens as $key => $label)
            <a class="nav-item {{ $screen === $key ? 'active' : '' }}" href="{{ url('/?screen=' . $key) }}">
                <span>{{ $key === 'home' ? '🏠' : ($key === 'cart' ? '🛒' : ($key === 'status' ? '📋' : '💬')) }}</span>
                <span>{{ $label }}</span>
            </a>
        @endforeach
        <div class="sidebar-note"><b>Lapar?</b><br>Temukan makanan favoritmu hari ini.<br><a href="{{ url('/?screen=home') }}">Pesan sekarang</a></div>
    </aside>

    {{-- MOBILE TOP BAR --}}
    <div class="mobile-top-bar">
        <span class="m-brand">3G<span>Food</span></span>
        <a class="m-cart-icon" href="{{ url('/?screen=cart') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            @if($cartCount > 0)<span class="m-badge">{{ $cartCount }}</span>@endif
        </a>
    </div>

    <main class="content">
        <header class="topbar">
            <span class="breadcrumb">3GFood / <b>{{ $screens[$screen] ?? 'Checkout' }}</b></span>
            <span></span>
        </header>

        @if($screen === 'home')
            <div class="mobile-hero">
                <h2>Waktunya makan<br>yang enak! 🍽️</h2>
                <p>Pesan favoritmu, kami antar ke depan pintu.</p>
                <a href="{{ url('/?screen=home') }}">Lihat Menu →</a>
            </div>
            <section class="welcome-row"><div><small class="eyebrow">SELAMAT DATANG</small><h1>Waktunya makan<br><em>yang enak.</em></h1><p class="muted">Pesan hidangan favoritmu, kami antar sampai depan pintu.</p></div><img class="hero-food" src="{{ $menu[3][3] ?? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=85' }}" alt="Makanan sehat"></section>
            <div class="section-head"><h2>Mau makan apa hari ini?</h2></div>
            <div class="category-row">
                <a class="category {{ !$category ? 'active' : '' }}" href="{{ url('/?screen=home') }}">Semua</a>
                <a class="category {{ $category === 'Paket Nasi Liwet' ? 'active' : '' }}" href="{{ url('/?screen=home&category=Paket Nasi Liwet') }}">🍛 Paket Nasi Liwet</a>
                <a class="category {{ $category === 'Lauk Utama' ? 'active' : '' }}" href="{{ url('/?screen=home&category=Lauk Utama') }}">🥘 Lauk Utama</a>
                <a class="category {{ $category === 'Menu Tambahan' ? 'active' : '' }}" href="{{ url('/?screen=home&category=Menu Tambahan') }}">🍟 Menu Tambahan</a>
            </div>
            <div class="menu-grid">
                @foreach($visibleMenu as $itemId => $item)
                <article class="menu-card"><img src="{{ $item[3] }}" alt="{{ $item[0] }}"><div><h3>{{ $item[0] }}</h3><p>Hidangan pilihan chef</p><strong>{{ $item[1] }}</strong><a class="add-btn" href="{{ url('/cart/add/' . $itemId) }}">+</a></div></article>
                @endforeach
            </div>
        @elseif($screen === 'cart')
            <div class="page-heading"><small class="eyebrow">PESANANMU</small><h1>Keranjang <em>Belanja.</em></h1><p class="muted">{{ count($cart) ? 'Item pilihanmu siap dipesan.' : 'Keranjangmu masih kosong.' }}</p></div>
            <div class="two-col">
                <section class="panel">
                    <div class="cart-heading"><h2>Daftar Pesanan</h2>@if(count($cart))<a class="remove-btn" href="{{ url('/cart/clear') }}">Kosongkan</a>@endif</div>
                    @forelse($cart as $itemId => $quantity)
                    <div class="cart-item"><img src="{{ $menu[$itemId][3] ?? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=85' }}" alt=""><span><b>{{ $menu[$itemId][0] ?? 'Menu tidak tersedia' }}</b><small>{{ $menu[$itemId][1] ?? 'Harga tidak tersedia' }}</small><label>{{ $quantity }} item</label></span><strong>Rp {{ number_format(($menu[$itemId][2] ?? 0) * $quantity, 0, ',', '.') }}</strong><a class="remove-btn" href="{{ url('/cart/remove/' . $itemId) }}">Hapus</a></div>
                    @empty<p class="muted">Belum ada makanan. Kembali ke Beranda untuk memilih menu.</p>
                    @endforelse
                </section>
                <aside class="summary">
                    <h2>Ringkasan Pesanan</h2>
                    <p>Subtotal <b>Rp {{ number_format($subtotal, 0, ',', '.') }}</b></p>
                    <p>Pengantaran <b>Rp {{ number_format($delivery, 0, ',', '.') }}</b></p>
                    <hr><h3>Total <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></h3>
                    @if(count($cart))<a class="primary-btn" href="{{ url('/?screen=buyer') }}">Lanjutkan Checkout →</a>@endif
                </aside>
            </div>

        @elseif($screen === 'feedback')
            <div class="page-heading"><small class="eyebrow">MASUKAN PELANGGAN</small><h1>Kritik <em>dan Saran.</em></h1><p class="muted">Bantu kami memberikan pelayanan yang lebih baik.</p></div>
            <section class="feedback-panel">
                <h2>Kritik dan Saran</h2>
                <p>Punya masukan untuk 3GFood? Pesanmu akan dibuka langsung di WhatsApp admin.</p>
                <form method="POST" action="{{ route('feedback.whatsapp') }}">
                    @csrf
                    <label>Nama<input name="name" value="{{ old('name', $buyer['customer_name'] ?? '') }}" required placeholder="Nama kamu"></label>
                    <label>Pesan<textarea name="message" required placeholder="Tulis kritik atau saran kamu..."></textarea></label>
                    @if($errors->has('feedback'))<p class="error-text">{{ $errors->first('feedback') }}</p>@endif
                    <button class="primary-btn" type="submit">Kirim ke WhatsApp Admin</button>
                </form>
            </section>

        @elseif($screen === 'buyer')
            <div class="page-heading"><small class="eyebrow">LANGKAH 01 / 02</small><h1>Data <em>Pembeli.</em></h1><p class="muted">Isi detail pengantaran agar pesanan sampai dengan tepat.</p></div>
            <div class="two-col"><section class="panel"><h2>Informasi Kontak</h2><form class="checkout-form" method="POST" action="{{ route('checkout.buyer') }}">@csrf<label>Nama lengkap<input name="customer_name" value="{{ old('customer_name', $buyer['customer_name'] ?? '') }}" required placeholder="Nama lengkap kamu"></label><label>Nomor telepon<input name="phone" value="{{ old('phone', $buyer['phone'] ?? '') }}" required placeholder="08xx-xxxx-xxxx"></label><label>Alamat pengantaran<textarea name="address" required placeholder="Tulis alamat lengkap kamu...">{{ old('address', $buyer['address'] ?? '') }}</textarea></label>@if($errors->any())<p class="error-text">{{ $errors->first() }}</p>@endif<button class="primary-btn" type="submit">Lanjut ke Pembayaran →</button></form></section></div>

        @elseif($screen === 'payment')
            <div class="page-heading"><small class="eyebrow">LANGKAH 02 / 02</small><h1>Metode <em>Pembayaran.</em></h1><p class="muted">Pilih pembayaran, lalu tunggu owner mengonfirmasi pembayaranmu.</p></div>
            <div class="two-col"><section class="panel"><h2>Pilih metode pembayaran</h2><form class="checkout-form" method="POST" action="{{ route('checkout.payment') }}">@csrf<label class="payment-choice"><input type="radio" name="payment_method" value="cash" checked><span><b>Bayar di tempat (COD)</b><small>Mohon siapkan uang pas sebesar total pembayaran saat pesanan tiba.</small></span></label><label class="payment-choice"><input type="radio" name="payment_method" value="bank"><span><b>Transfer bank</b><small>BCA, BNI, BRI, atau Mandiri.</small></span></label><div class="payment-info"><b>Rekening owner</b><strong>BCA 1234 5678 9012</strong><span>3GFood</span></div><label class="payment-choice" style="margin-top:10px"><input type="radio" name="payment_method" value="digital"><span><b>Dompet digital / QRIS</b><small>GoPay, OVO, DANA, atau scan QRIS.</small></span></label><div class="payment-info"><b>QRIS owner</b><div class="qr-placeholder">QRIS<br>3GFOOD</div></div>@if($errors->any())<p class="error-text">{{ $errors->first() }}</p>@endif<button class="primary-btn" type="submit">Konfirmasi Pesanan →</button></form></section><aside class="summary"><h2>Total Pembayaran</h2><p>Subtotal <b>Rp {{ number_format($subtotal, 0, ',', '.') }}</b></p><p>Pengantaran <b>Rp {{ number_format($delivery, 0, ',', '.') }}</b></p><hr><h3>Total <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></h3></aside></div>

        @elseif($screen === 'confirmation')
            <div class="success-view"><div class="success-icon">✓</div><small class="eyebrow">PESANAN BERHASIL DIBUAT</small><h1>Pesananmu sedang<br><em>menunggu konfirmasi.</em></h1><p class="muted">Owner akan memeriksa pembayaranmu. Status akan diperbarui setelah pembayaran diterima.</p><div class="order-code">ORDER ID <b>#{{ $order['order_code'] ?? 'FE-BARU' }}</b></div><a class="primary-btn" href="{{ url('/?screen=status') }}">Lihat Status Pesanan →</a></div>

        @else
            @php
                $s = $order['status'] ?? 'waiting';
                $steps = ['waiting' => 1, 'waiting_owner' => 1, 'waiting_payment' => 1, 'accepted' => 2, 'cooking' => 3, 'ready_to_deliver' => 4, 'delivering' => 5, 'completed' => 6, 'confirmed' => 6];
                $step = $steps[$s] ?? 1;

                $pill = match($s) {
                    'accepted' => 'Diterima Owner',
                    'cooking' => 'Sedang Dimasak',
                    'ready_to_deliver' => 'Siap Diantar',
                    'delivering' => 'Sedang Diantar',
                    'completed', 'confirmed' => 'Selesai',
                    default => 'Menunggu Konfirmasi',
                };

                $title = match($s) {
                    'accepted' => 'Pesanan Diterima',
                    'cooking' => 'Sedang Dimasak 🍳',
                    'ready_to_deliver' => 'Menunggu Kurir 📦',
                    'delivering' => 'Pesanan Diantar 🚴',
                    'completed', 'confirmed' => 'Pesanan Selesai 🎉',
                    default => 'Menunggu Owner',
                };

                $desc = match($s) {
                    'accepted' => 'Owner telah menerima pesananmu dan sedang dipersiapkan.',
                    'cooking' => 'Pesananmu sedang dimasak oleh dapur.',
                    'ready_to_deliver' => 'Masakan selesai dan sedang menunggu di-pickup kurir.',
                    'delivering' => 'Kurir sedang dalam perjalanan mengantar pesananmu.',
                    'completed', 'confirmed' => 'Pesanan sudah selesai. Terima kasih!',
                    default => 'Pesananmu sudah masuk dan menunggu konfirmasi dari owner.',
                };
            @endphp
            <div class="page-heading"><small class="eyebrow">{{ $order['order_code'] ?? 'BELUM ADA PESANAN' }}</small><h1>Status <em>Pesanan.</em></h1><p class="muted">Pantau progress pesananmu secara real-time.</p></div>
            <section class="panel status-panel">
                <span class="status-pill">{{ $pill }}</span>
                <h2>{{ $title }}</h2>
                <p class="muted">{{ $desc }}</p>
                <div class="timeline">
                    <div class="timeline-step done"><span>✓</span><b>Dibuat<small>Berhasil</small></b></div>
                    <div class="timeline-step {{ $step >= 2 ? 'done' : '' }}"><span>{{ $step >= 2 ? '✓' : '2' }}</span><b>Diterima<small>{{ $step >= 2 ? 'Selesai' : 'Menunggu' }}</small></b></div>
                    <div class="timeline-step {{ $step >= 3 ? 'done' : '' }}"><span>{{ $step >= 3 ? '✓' : '3' }}</span><b>Dimasak<small>{{ $step >= 4 ? 'Selesai' : ($step == 3 ? 'Proses' : 'Menunggu') }}</small></b></div>
                    <div class="timeline-step {{ $step >= 5 ? 'done' : '' }}"><span>{{ $step >= 5 ? '✓' : '4' }}</span><b>Diantar<small>{{ $step >= 6 ? 'Selesai' : ($step == 5 ? 'Proses' : 'Menunggu') }}</small></b></div>
                </div>
            </section>
        @endif
    </main>
</div>

{{-- MOBILE BOTTOM NAVIGATION --}}
<nav class="mobile-bottom-nav">
    <a href="{{ url('/?screen=home') }}" class="{{ $screen === 'home' ? 'active' : '' }}">
        <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
        Beranda
    </a>
    <a href="{{ url('/?screen=cart') }}" class="{{ $screen === 'cart' ? 'active' : '' }}">
        @if($cartCount > 0)<span class="nav-badge">{{ $cartCount }}</span>@endif
        <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg></span>
        Keranjang
    </a>
    <a href="{{ url('/?screen=status') }}" class="{{ $screen === 'status' ? 'active' : '' }}">
        <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
        Status
    </a>
    <a href="{{ url('/?screen=feedback') }}" class="{{ $screen === 'feedback' ? 'active' : '' }}">
        <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 11.5a8.5 8.5 0 01-9 8.5 8.7 8.7 0 01-3.8-.9L3 21l1.9-4.7A8.5 8.5 0 1112 3.5"/><path d="M8 10h8M8 14h5"/></svg></span>
        Kritik &amp; Saran
    </a>
</nav>
</body>
</html>


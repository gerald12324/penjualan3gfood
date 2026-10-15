<?php

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Courier;
use App\Models\CourierPayout;
use App\Models\PengaturanToko;
use App\Models\PencairanKurir;
use App\Models\Voucher;
use App\Models\Customer;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Fitur Live Chat Pelanggan (Front Store)
|--------------------------------------------------------------------------
*/
Route::get('/chat/messages', [ChatController::class, 'getCustomerMessages'])->name('chat.messages');
Route::post('/chat/send', [ChatController::class, 'sendCustomerMessage'])->name('chat.send');

/*
|--------------------------------------------------------------------------
| Autentikasi Khusus Pelanggan / Customer (Guard: customer)
|--------------------------------------------------------------------------
*/
Route::get('/customer/login', [CustomerAuthController::class, 'showLoginForm'])->name('customer.login');
Route::post('/customer/login', [CustomerAuthController::class, 'login'])->name('customer.login.submit');
Route::get('/customer/register', [CustomerAuthController::class, 'showRegisterForm'])->name('customer.register');
Route::post('/customer/register', [CustomerAuthController::class, 'register'])->name('customer.register.submit');
Route::match(['get', 'post'], '/customer/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

Route::get('/home', function () {
    return redirect('/admin');
});

Route::get('/login', function () {
    return redirect('/admin/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', function () {
        return view('login');
    })->name('login');

    Route::post('/admin/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/admin');
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak sesuai.',
        ])->onlyInput('email');
    })->name('admin.login.submit');

});

Route::get('/admin/register', function () {
    if (\App\Models\User::count() > 0 && !\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('login')->withErrors(['email' => 'Hanya admin yang dapat mendaftarkan admin baru. Silakan login terlebih dahulu.']);
    }
    return view('register');
})->name('register');

Route::post('/admin/register', function (Request $request) {
    if (\App\Models\User::count() > 0 && !\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('login')->withErrors(['email' => 'Hanya admin yang dapat mendaftarkan admin baru. Silakan login terlebih dahulu.']);
    }

    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $newUser = \App\Models\User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
    ]);

    if (!\Illuminate\Support\Facades\Auth::check()) {
        \Illuminate\Support\Facades\Auth::login($newUser);
        return redirect('/admin')->with('success', 'Akun admin berhasil dibuat dan langsung login.');
    }

    return redirect('/admin')->with('success', 'Admin baru berhasil didaftarkan.');
})->name('admin.register.submit');

Route::middleware('auth')->post('/admin/settings', function (Request $request) {
    $data = $request->validate([
        'status_toko' => ['required', 'in:buka,tutup'],
        'jam_buka' => ['required', 'date_format:H:i'],
        'jam_tutup' => ['required', 'date_format:H:i'],
    ]);

    PengaturanToko::updateOrCreate([], $data);
    Cache::forget('shop_settings');

    return back()->with('success', 'Pengaturan toko berhasil diperbarui.');
})->name('admin.settings.store');

Route::middleware('auth')->post('/admin/vouchers', function (Request $request) {
    $data = $request->validate([
        'kode_voucher' => ['required', 'string', 'max:30', 'unique:voucher,kode_voucher'],
        'jenis_potongan' => ['required', 'in:nominal,persen'],
        'nilai_potongan' => ['required', 'integer', 'min:1'],
        'min_belanja' => ['required', 'integer', 'min:0'],
        'tanggal_berakhir' => ['required', 'date'],
        'kuota' => ['required', 'integer', 'min:1'],
        'status' => ['required', 'in:aktif,nonaktif'],
    ]);

    Voucher::create([
        'kode_voucher' => strtoupper(trim($data['kode_voucher'])),
        'jenis_potongan' => $data['jenis_potongan'],
        'nilai_potongan' => $data['nilai_potongan'],
        'min_belanja' => $data['min_belanja'],
        'tanggal_berakhir' => $data['tanggal_berakhir'],
        'kuota' => $data['kuota'],
        'status' => $data['status'],
    ]);

    return back()->with('success', 'Voucher berhasil ditambahkan.');
})->name('admin.vouchers.store');

Route::middleware('auth')->delete('/admin/vouchers/{voucher}', function (\App\Models\Voucher $voucher) {
    $voucher->delete();
    return back()->with('success', 'Voucher berhasil dihapus.');
})->name('admin.vouchers.delete');

Route::post('/voucher/check', function (Request $request) {
    $voucherCode = strtoupper(trim((string) $request->input('voucher_code', '')));
    $subtotal = (int) $request->input('subtotal', 0);

    if ($voucherCode === '') {
        return response()->json(['valid' => false, 'message' => 'Kode voucher wajib diisi.'], 422);
    }

    $voucher = Voucher::where('kode_voucher', $voucherCode)->first();
    if (!$voucher) {
        return response()->json(['valid' => false, 'message' => 'Kode voucher tidak ditemukan.'], 422);
    }

    if ($voucher->status !== 'aktif') {
        return response()->json(['valid' => false, 'message' => 'Voucher tidak aktif saat ini.'], 422);
    }

    if ($voucher->tanggal_berakhir && now()->toDateString() > $voucher->tanggal_berakhir) {
        return response()->json(['valid' => false, 'message' => 'Voucher sudah kedaluwarsa.'], 422);
    }

    if ($subtotal < $voucher->min_belanja) {
        return response()->json([
            'valid' => false,
            'message' => 'Subtotal belum mencapai minimal belanja Rp ' . number_format($voucher->min_belanja, 0, ',', '.'),
        ], 422);
    }

    $discount = $voucher->jenis_potongan === 'persen'
        ? (int) floor($subtotal * ($voucher->nilai_potongan / 100))
        : (int) $voucher->nilai_potongan;

    $request->session()->put('applied_voucher', [
        'kode_voucher' => $voucher->kode_voucher,
        'discount_amount' => $discount,
        'jenis_potongan' => $voucher->jenis_potongan,
        'nilai_potongan' => $voucher->nilai_potongan,
    ]);

    return response()->json([
        'valid' => true,
        'message' => 'Voucher berhasil digunakan.',
        'discount_amount' => $discount,
        'kode_voucher' => $voucher->kode_voucher,
    ]);
})->name('voucher.check');

Route::post('/voucher/clear', function (Request $request) {
    $request->session()->forget('applied_voucher');

    return response()->json(['valid' => false, 'message' => 'Voucher dibatalkan.']);
})->name('voucher.clear');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Area Pelanggan / Front Store (Dapat Diakses Guest & Customer)
|--------------------------------------------------------------------------
*/
Route::any('/', function () {
        $screenParam = request()->query('screen', 'home');
        $screen = in_array($screenParam, ['home', 'cart', 'buyer', 'payment', 'confirmation', 'status', 'feedback', 'track', 'settings'], true) ? $screenParam : 'home';
        $category = request()->query('category');
        
        $sessionOrder = session('order');
        $order = $sessionOrder && isset($sessionOrder['order_code'])
            ? Order::where('order_code', $sessionOrder['order_code'])->first()?->toArray()
            : null;

        $shopSettings = PengaturanToko::latest()->first() ?? (object)[
            'status_toko' => 'buka',
            'jam_buka' => '08:00',
            'jam_tutup' => '21:00',
        ];

        $shopOpen = ($shopSettings->status_toko === 'buka');
        if ($shopOpen && !empty($shopSettings->jam_buka) && !empty($shopSettings->jam_tutup)) {
            $nowTime = date('H:i');
            $shopOpen = $nowTime >= $shopSettings->jam_buka && $nowTime <= $shopSettings->jam_tutup;
        }

        $managedMenu = MenuItem::where('is_available', true)->where('is_visible', true)->orderBy('category')->orderBy('name')->get();
        
        $menu = [];
        foreach ($managedMenu as $managedItem) {
            $menu[$managedItem->id] = [
                $managedItem->name, 
                'Rp ' . number_format($managedItem->price, 0, ',', '.'), 
                $managedItem->price, 
                $managedItem->image_url ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=85', 
                $managedItem->category
            ];
        }
        
        $visibleMenu = $category ? collect($menu)->filter(fn ($item) => $item[4] === $category)->all() : $menu;
        
        $cart = session('cart', []);
        $cartCount = array_sum($cart);
        $buyer = session('buyer', []);
        
        $subtotal = 0;
        foreach ($cart as $itemId => $quantity) {
            $subtotal += ($menu[$itemId][2] ?? 0) * $quantity;
        }
        
        $delivery = $subtotal > 0 ? 1000 : 0;
        $voucher = session('applied_voucher', []);
        $voucherDiscount = (int) ($voucher['discount_amount'] ?? 0);
        $grandTotal = max($subtotal + $delivery - $voucherDiscount, 0);
        $total = $subtotal + $delivery;

        $currentCustomer = Auth::guard('customer')->user();
        if (empty($buyer) && $currentCustomer) {
            $buyer = [
                'customer_name' => $currentCustomer->name,
                'phone' => $currentCustomer->phone ?? '',
                'address' => '',
            ];
        }

        $screens = [
            'home' => 'Beranda',
            'cart' => 'Keranjang',
            'status' => 'Status Pesanan',
            'feedback' => 'Kritik dan Saran',
            'track' => 'Lacak Pesanan',
            'settings' => 'Pengaturan Akun'
        ];

        // Track order logic
        $trackedOrder = null;
        $customerOrders = $currentCustomer ? Order::with('items')->where('customer_id', $currentCustomer->id)->latest()->get() : collect();
        $trackCode = request()->query('order_code');
        if ($trackCode) {
            $trackedOrderModel = Order::with(['items', 'courier'])->where('order_code', $trackCode)->orWhere('phone', $trackCode)->first();
            $trackedOrder = $trackedOrderModel ? $trackedOrderModel->toArray() : null;
        }

        return view('foodie', compact(
            'screens', 'screen', 'category', 'menu', 'cart', 'buyer', 
            'subtotal', 'delivery', 'voucher', 'voucherDiscount', 'grandTotal', 
            'shopSettings', 'shopOpen', 'total', 'visibleMenu', 'cartCount', 'order', 'managedMenu', 'trackedOrder', 'trackCode',
            'currentCustomer', 'customerOrders'
        ));
    });

    Route::match(['get', 'post'], '/cart/add/{item}', function (Request $request, int $item) {
        $menuItem = MenuItem::where('id', $item)->where('is_available', true)->where('is_visible', true)->first();
        if (!$menuItem) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu tidak tersedia atau sedang disembunyikan.',
                ], 404);
            }
            return redirect()->to('/?screen=home')->with('error', 'Menu tidak tersedia atau sedang disembunyikan.');
        }

        $cart = session('cart', []);
        $cart[$item] = ($cart[$item] ?? 0) + 1;
        session(['cart' => $cart]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cartCount' => array_sum($cart),
                'message' => 'Item ditambahkan ke keranjang.',
            ]);
        }

        return redirect()->to('/?screen=home');
    })->whereNumber('item');

    Route::match(['get', 'post'], '/cart/remove/{item}', function (Request $request, int $item) {
        $cart = session('cart', []);
        unset($cart[$item]);
        session(['cart' => $cart]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cartCount' => array_sum($cart),
                'message' => 'Item dihapus dari keranjang.',
            ]);
        }

        return redirect()->to('/?screen=cart');
    })->whereNumber('item');

    Route::match(['get', 'post'], '/cart/clear', function (Request $request) {
        session()->forget('cart');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cartCount' => 0,
                'message' => 'Keranjang dikosongkan.',
            ]);
        }

        return redirect()->to('/?screen=cart');
    });

    Route::post('/checkout/buyer', function (Request $request) {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'perumahan' => ['required', 'string', 'max:100'],
            'blok' => ['required', 'string', 'max:20'],
            'tahap' => ['nullable', 'string', 'max:20'],
            'rt_rw' => ['nullable', 'string', 'max:20'],
            'catatan' => ['nullable', 'string', 'max:200'],
        ]);
        
        $fullAddress = "Perumahan " . $data['perumahan'] . " Blok " . $data['blok'];
        if (!empty($data['tahap'])) $fullAddress .= " Tahap " . $data['tahap'];
        if (!empty($data['rt_rw'])) $fullAddress .= " RT/RW " . $data['rt_rw'];
        if (!empty($data['catatan'])) $fullAddress .= " | Catatan: " . $data['catatan'];

        session(['buyer' => [
            'customer_name' => $data['customer_name'],
            'phone' => $data['phone'],
            'address' => $fullAddress
        ]]);
        return redirect()->to('/?screen=payment');
    })->name('checkout.buyer');

    Route::post('/checkout/payment', function (Request $request) {
        $method = $request->validate(['payment_method' => ['required', 'in:cash,bank,digital']])['payment_method'];
        $buyer = session('buyer');
        abort_unless($buyer, 302, 'Data pembeli belum diisi.');
        $subtotal = 0;
        foreach (session('cart', []) as $itemId => $quantity) {
            $menuItem = MenuItem::where('id', $itemId)->where('is_available', true)->where('is_visible', true)->first();
            $subtotal += ($menuItem?->price ?? 0) * $quantity;
        }
        abort_unless($subtotal > 0, 302, 'Keranjang masih kosong.');
        
        // Simpan pesanan dengan mengikat customer_id jika pembeli sedang login
        $customerId = Auth::guard('customer')->id();

        $order = Order::create([
            ...$buyer,
            'customer_id' => $customerId,
            'order_code' => 'FE-' . now()->format('ymdHis'),
            'payment_method' => $method,
            'total' => $subtotal + 1000,
            'status' => 'waiting',
        ]);
        foreach (session('cart', []) as $itemId => $quantity) {
            $menuItem = MenuItem::where('id', $itemId)->where('is_available', true)->where('is_visible', true)->first();
            if ($menuItem) {
                $order->items()->create([
                    'menu_item_id' => $menuItem->id,
                    'menu_name' => $menuItem->name,
                    'unit_price' => $menuItem->price,
                    'quantity' => $quantity,
                ]);
            }
        }
        session(['order' => $order->fresh()->toArray()]);
        session()->forget('cart');
        return redirect()->to('/?screen=confirmation');
    })->name('checkout.payment');

    Route::post('/checkout/proof', function (Request $request) {
        $data = $request->validate([
            'order_code' => ['required', 'string'],
            'payment_proof' => ['required', 'image', 'max:5120'],
        ]);

        $order = Order::where('order_code', $data['order_code'])->firstOrFail();
        $path = $request->file('payment_proof')->store('proofs', 'public');
        
        $order->update([
            'payment_proof' => asset('storage/' . $path),
            'status' => 'waiting_owner'
        ]);

        return redirect()->to('/?screen=track&order_code=' . $order->order_code)->with('success', 'Bukti pembayaran berhasil diunggah.');
    })->name('checkout.proof');

    Route::middleware('auth:customer')->group(function () {
        Route::post('/customer/settings', [CustomerAuthController::class, 'updateSettings'])->name('customer.settings.update');
        Route::post('/customer/avatar/remove', [CustomerAuthController::class, 'removeAvatar'])->name('customer.avatar.remove');
    });

Route::middleware('auth')->get('/admin', function () {
    $status = request()->query('status');
    $orders = Order::with(['items', 'customer'])
        ->latest()
        ->when(in_array($status, ['waiting', 'accepted', 'cooking', 'ready_to_deliver', 'delivering', 'completed'], true), function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->get();

    $couriers = Courier::withCount('orders')->orderBy('name')->get();
    $payouts = CourierPayout::with('courier')->latest()->get();
    $managedMenu = MenuItem::orderBy('category')->get();
    $vouchers = Voucher::latest()->get();
    $shopSettings = PengaturanToko::latest()->first() ?? PengaturanToko::create([
        'status_toko' => 'buka',
        'jam_buka' => '08:00',
        'jam_tutup' => '21:00',
    ]);
    $reportFrom = request()->query('from', now()->startOfMonth()->toDateString());
    $reportTo = request()->query('to', now()->toDateString());
    try {
        $reportFromDate = Carbon::parse($reportFrom)->startOfDay();
        $reportToDate = Carbon::parse($reportTo)->endOfDay();
    } catch (\Exception $exception) {
        $reportFrom = now()->startOfMonth()->toDateString();
        $reportTo = now()->toDateString();
        $reportFromDate = now()->startOfMonth()->startOfDay();
        $reportToDate = now()->endOfDay();
    }
    $reportOrders = Order::with('items')
        ->whereIn('status', ['completed', 'confirmed'])
        ->whereBetween('created_at', [$reportFromDate, $reportToDate])
        ->latest()
        ->get();

    $reportFoodRevenue = $reportOrders->sum(function ($order) {
        $base = max(0, (int) $order->total - 1000);
        return $base;
    });
    $reportDelivery = $reportOrders->count() * 1000;
    $reportPayouts = CourierPayout::whereBetween('created_at', [$reportFromDate, $reportToDate])->sum('amount');
    $reportProfit = $reportFoodRevenue + $reportDelivery - $reportPayouts;
    $reportRevenue = $reportFoodRevenue + $reportDelivery;
    $bestSellingMenus = OrderItem::whereHas('order', function ($query) use ($reportFromDate, $reportToDate) {
            $query->whereIn('status', ['completed', 'confirmed'])
                ->whereBetween('created_at', [$reportFromDate, $reportToDate]);
        })
        ->selectRaw('menu_name, SUM(quantity) as total_quantity, SUM(unit_price * quantity) as total_sales')
        ->groupBy('menu_name')
        ->orderByDesc('total_quantity')
        ->limit(10)
        ->get();

    return view('admin', compact('orders', 'status', 'couriers', 'payouts', 'managedMenu', 'vouchers', 'shopSettings', 'reportFrom', 'reportTo', 'reportOrders', 'reportRevenue', 'reportFoodRevenue', 'reportDelivery', 'reportPayouts', 'reportProfit', 'bestSellingMenus'));
})->name('admin.dashboard');

Route::post('/feedback/whatsapp', function (Request $request) {
    $data = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'message' => ['required', 'string', 'max:1000'],
    ]);
    $adminPhone = \App\Services\WhatsAppService::normalizePhone(config('services.whatsapp.admin_phone', '6281234567890'));
    if (!$adminPhone) {
        $adminPhone = '6281234567890';
    }
    $message = "Halo Admin 3GFood, saya {$data['name']}.\n\nKritik dan saran:\n{$data['message']}";
    return redirect()->away('https://wa.me/'.$adminPhone.'?text='.rawurlencode($message));
})->name('feedback.whatsapp');

Route::middleware('auth')->delete('/admin/orders', function () {
    $orderCount = Order::count();

    \Illuminate\Support\Facades\DB::transaction(function () {
        Order::query()->delete();
    });

    return back()->with('success', $orderCount.' pesanan berhasil dihapus. Daftar pesanan sekarang kosong.');
})->name('admin.orders.clear');

Route::middleware('auth')->post('/admin/menu', function (Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:120',
        'category' => 'required|in:Paket Nasi Liwet,Lauk Utama,Menu Tambahan',
        'price' => 'required|integer|min:0',
        'description' => 'nullable|string',
        'is_available' => 'nullable|boolean',
        'is_visible' => 'nullable|boolean',
        'image' => 'nullable|image|max:2048',
    ]);
    $data['is_available'] = $request->boolean('is_available', true);
    $data['is_visible'] = $request->boolean('is_visible', true);
    $imagePath = $request->file('image')?->store('menu', 'public');
    $data['image_url'] = $imagePath ? asset('storage/' . $imagePath) : null;
    unset($data['image']);
    MenuItem::create($data);
    Cache::forget('managed_menu_active');
    return back()->with('success', 'Menu berhasil ditambahkan.');
})->name('admin.menu.store');

Route::middleware('auth')->post('/admin/couriers', function (Request $request) {
    Courier::create($request->validate(['name' => 'required|string|max:120', 'phone' => 'required|string|max:30']));
    return back()->with('success', 'Kurir berhasil ditambahkan.');
})->name('admin.couriers.store');

Route::middleware('auth')->patch('/admin/orders/{order}/accept', function (Order $order) {
    if ($order->status === 'waiting' || $order->status === 'waiting_owner' || $order->status === 'waiting_payment') {
        $order->update(['status' => 'accepted', 'accepted_at' => now()]);
    }
    return back()->with('success', 'Pesanan diterima dan mulai disiapkan.');
})->name('admin.orders.accept');

Route::middleware('auth')->patch('/admin/orders/{order}/cook', function (Order $order) {
    if ($order->status === 'accepted') {
        $order->update(['status' => 'cooking', 'cooking_at' => now()]);
    }
    return back()->with('success', 'Pesanan sedang dimasak.');
})->name('admin.orders.cook');

Route::middleware('auth')->patch('/admin/orders/{order}/cooked', function (Order $order) {
    if ($order->status === 'cooking') {
        \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
            $order->update(['status' => 'ready_to_deliver', 'cooked_at' => now()]);
            
            $message = "Pesanan #{$order->order_code} sudah siap diantar. Terima kasih sudah memesan.";
            $existingLog = $order->whatsappLogs()->where('status', 'sent')->first();
            
            if (!$existingLog) {
                \App\Services\WhatsAppService::sendMessage($order, $message);
            }
        });

        $order->load('whatsappLogs');
        $latestLog = $order->latestWhatsappLog;
        if ($latestLog && $latestLog->status === 'sent') {
            return back()->with('success', 'Pesanan berhasil diubah menjadi Siap Diantar dan notifikasi WhatsApp berhasil dikirim.');
        } else {
            return back()->with('success', 'Pesanan selesai dimasak dan siap diantar. (Gagal kirim notifikasi WhatsApp)');
        }
    }
    return back();
})->name('admin.orders.cooked');

Route::middleware('auth')->post('/admin/orders/{order}/resend-wa', function (Order $order) {
    $message = "Pesanan #{$order->order_code} sudah siap diantar. Terima kasih sudah memesan.";
    \App\Services\WhatsAppService::sendMessage($order, $message);
    
    $order->load('whatsappLogs');
    $latestLog = $order->latestWhatsappLog;
    if ($latestLog && $latestLog->status === 'sent') {
        return back()->with('success', 'Notifikasi WhatsApp berhasil dikirim ulang.');
    } else {
        return back()->with('success', 'Gagal kirim ulang notifikasi WhatsApp. Pastikan kredensial benar.');
    }
})->name('admin.orders.resend_wa');

Route::middleware('auth')->patch('/admin/orders/{order}/courier', function (Request $request, Order $order) {
    $request->validate(['courier_id' => 'required|exists:couriers,id']);
    if ($order->status === 'ready_to_deliver') {
        $order->update([
            'courier_id' => $request->courier_id,
            'status' => 'delivering',
            'delivering_at' => now()
        ]);
        return back()->with('success', 'Kurir berhasil ditetapkan dan pesanan mulai diantar.');
    }
    
    $order->update(['courier_id' => $request->courier_id]);
    return back()->with('success', 'Kurir pesanan berhasil diperbarui.');
})->name('admin.orders.courier');

Route::middleware('auth')->patch('/admin/orders/{order}/complete', function (Order $order) {
    if ($order->status === 'delivering' || $order->status === 'ready_to_deliver') {
        $order->update(['status' => 'completed', 'completed_at' => now()]);
    }
    return back()->with('success', 'Pesanan ditandai selesai.');
})->name('admin.orders.complete');

Route::middleware('auth')->post('/admin/payouts', function (Request $request) {
    $courier = Courier::findOrFail($request->input('courier_id'));
    $completedCount = Order::where('courier_id', $courier->id)
        ->whereIn('status', ['completed', 'confirmed'])
        ->count();

    $amount = (int) $request->input('amount', $completedCount * 2000);

    CourierPayout::create([
        'courier_id' => $courier->id,
        'amount' => $amount,
        'period' => $request->input('period', now()->format('F Y')),
        'status' => 'pending',
        'notes' => $request->input('notes', 'Pencairan otomatis dari pesanan selesai.'),
    ]);

    return back()->with('success', 'Pencairan kurir berhasil dibuat untuk ' . $courier->name . ' sebesar Rp ' . number_format($amount, 0, ',', '.') . '.');
})->name('admin.payouts.store');

Route::middleware('auth')->delete('/admin/payouts', function () {
    $payoutCount = CourierPayout::count();
    CourierPayout::query()->delete();

    return back()->with('success', $payoutCount.' riwayat pencairan berhasil dihapus.');
})->name('admin.payouts.clear');

Route::middleware('auth')->patch('/admin/payouts/{payout}/paid', function (CourierPayout $payout) {
    $payout->update(['status' => 'paid']);

    return back()->with('success', 'Pencairan kurir ditandai sudah dibayar.');
})->name('admin.payouts.paid');

Route::middleware('auth')->get('/admin/orders/{order}/receipt', function (Order $order) {
    $order->load(['items', 'courier']);
    return view('receipt', compact('order'));
})->name('admin.orders.receipt');

Route::middleware('auth')->patch('/admin/couriers/{courier}/reset-orders', function (Courier $courier) {
    $completedStatuses = ['completed', 'confirmed'];
    $completedOrders = $courier->orders()->whereIn('status', $completedStatuses)->update(['courier_id' => null]);

    return back()->with('success', 'Total pengantaran kurir '.$courier->name.' berhasil direset menjadi 0 ('.$completedOrders.' pesanan selesai diarsipkan).');
})->name('admin.couriers.reset-orders');

Route::middleware('auth')->patch('/admin/menu/{menu}', function (Request $request, MenuItem $menu) {
    $data = $request->validate([
        'name' => 'required|string|max:120',
        'category' => 'required|in:Paket Nasi Liwet,Lauk Utama,Menu Tambahan',
        'price' => 'required|integer|min:0',
        'description' => 'nullable|string',
        'is_available' => 'nullable|boolean',
        'is_visible' => 'nullable|boolean',
        'image' => 'nullable|image|max:2048',
    ]);
    $data['is_available'] = $request->boolean('is_available', $menu->is_available);
    $data['is_visible'] = $request->boolean('is_visible', $menu->is_visible);
    if ($request->file('image')) {
        $imagePath = $request->file('image')->store('menu', 'public');
        $data['image_url'] = asset('storage/' . $imagePath);
    }
    unset($data['image']);
    $menu->update($data);
    Cache::forget('managed_menu_active');
    return back()->with('success', 'Menu berhasil diupdate.');
})->name('admin.menu.update');

Route::middleware('auth')->patch('/admin/menu/{menu}/toggle-visibility', function (Request $request, MenuItem $menu) {
    $menu->is_visible = !$menu->is_visible;
    $menu->save();
    Cache::forget('managed_menu_active');

    $statusLabel = $menu->is_visible ? 'ditampilkan' : 'disembunyikan';
    $message = "Menu \"{$menu->name}\" berhasil {$statusLabel} " . ($menu->is_visible ? 'di' : 'dari') . ' etalase pembeli.';

    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'success' => true,
            'is_visible' => $menu->is_visible,
            'message' => $message,
        ]);
    }

    return back()->with('success', $message);
})->name('admin.menu.toggle-visibility');

Route::middleware('auth')->delete('/admin/menu/{menu}', function (MenuItem $menu) {
    $menu->delete();
    Cache::forget('managed_menu_active');
    return back()->with('success', 'Menu berhasil dihapus.');
})->name('admin.menu.delete');

Route::middleware('auth')->patch('/admin/couriers/{courier}', function (Request $request, Courier $courier) {
    $courier->update($request->validate(['name' => 'required|string|max:120', 'phone' => 'required|string|max:30']));
    return back()->with('success', 'Data kurir berhasil diupdate.');
})->name('admin.couriers.update');

Route::middleware('auth')->delete('/admin/couriers/{courier}', function (Courier $courier) {
    $courier->delete();
    return back()->with('success', 'Kurir berhasil dihapus.');
})->name('admin.couriers.delete');

/*
|--------------------------------------------------------------------------
| Fitur Live Chat Admin (Dashboard)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/admin/chat/conversations', [ChatController::class, 'getAdminConversations'])->name('admin.chat.conversations');
    Route::get('/admin/chat/thread/{key}', [ChatController::class, 'getAdminThread'])->name('admin.chat.thread');
    Route::post('/admin/chat/reply', [ChatController::class, 'sendAdminReply'])->name('admin.chat.reply');

    /*
    |--------------------------------------------------------------------------
    | Real-Time Polling Dashboard Admin
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/api/realtime-poll', function (Request $request) {
        $lastOrderId = (int) $request->query('last_order_id', 0);
        $currentMaxId = Order::max('id') ?? 0;
        $hasNewOrder = ($lastOrderId > 0 && $currentMaxId > $lastOrderId);

        $counts = [
            'waiting' => Order::whereIn('status', ['waiting', 'waiting_owner', 'waiting_payment'])->count(),
            'cooking' => Order::where('status', 'cooking')->count(),
            'ready_to_deliver' => Order::where('status', 'ready_to_deliver')->count(),
            'delivering' => Order::where('status', 'delivering')->count(),
            'completed' => Order::whereIn('status', ['completed', 'confirmed'])->count(),
            'total' => Order::count(),
            'revenue' => (int) Order::whereIn('status', ['completed', 'confirmed'])->sum('total'),
            'couriers' => Courier::count(),
        ];
        $counts['formatted_revenue'] = 'Rp ' . number_format($counts['revenue'], 0, ',', '.');

        $chatUnread = \App\Models\ChatMessage::where('sender_type', 'customer')->where('is_read', false)->count();

        // Ambil data pesanan terbaru untuk sync tabel realtime
        $latestOrders = Order::with(['items', 'courier', 'customer'])
            ->latest()
            ->limit(25)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_code' => $order->order_code,
                    'customer_name' => $order->customer_name,
                    'phone' => $order->phone,
                    'address' => $order->address,
                    'total' => (int) $order->total,
                    'total_formatted' => 'Rp ' . number_format($order->total, 0, ',', '.'),
                    'status' => $order->status,
                    'payment_method' => $order->payment_method,
                    'payment_proof' => $order->payment_proof,
                    'courier_id' => $order->courier_id,
                    'courier_name' => $order->courier?->name,
                    'courier_phone' => $order->courier?->phone,
                    'created_at_formatted' => $order->created_at ? $order->created_at->format('d/m/Y H:i') : '-',
                    'items_summary' => $order->items->map(fn ($it) => $it->quantity . 'x ' . $it->menu_name)->implode(', '),
                ];
            });

        // Get new orders only (for toast notification)
        $newOrders = [];
        if ($hasNewOrder) {
            $newOrders = Order::where('id', '>', $lastOrderId)
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_code' => $order->order_code,
                        'customer_name' => $order->customer_name,
                        'total' => (int) $order->total,
                    ];
                });
        }

        return response()->json([
            'success' => true,
            'has_new_order' => $hasNewOrder,
            'last_order_id' => $currentMaxId,
            'counts' => $counts,
            'chat_unread' => $chatUnread,
            'orders' => $newOrders,
            'latest_orders' => $latestOrders,
        ]);
    })->name('admin.realtime.poll');
});

/*
|--------------------------------------------------------------------------
| Real-Time Order Tracking & Status Check (Front Store)
|--------------------------------------------------------------------------
*/
Route::get('/api/order/status/{order_code}', function ($orderCode) {
    $order = Order::with(['courier', 'items'])
        ->where('order_code', $orderCode)
        ->orWhere('phone', $orderCode)
        ->first();

    if (!$order) {
        return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
    }

    $stepperMap = [
        'waiting' => 1,
        'waiting_owner' => 1,
        'waiting_payment' => 1,
        'accepted' => 2,
        'cooking' => 3,
        'ready_to_deliver' => 4,
        'delivering' => 4,
        'completed' => 5,
        'confirmed' => 5,
    ];

    $labelMap = [
        'waiting' => 'Menunggu Konfirmasi',
        'waiting_owner' => 'Menunggu Verifikasi Pembayaran',
        'waiting_payment' => 'Menunggu Pembayaran',
        'accepted' => 'Pesanan Diterima & Disiapkan',
        'cooking' => 'Sedang Dimasak oleh Koki',
        'ready_to_deliver' => 'Pesanan Siap Diantar',
        'delivering' => 'Sedang Diantar oleh Kurir',
        'completed' => 'Pesanan Selesai / Terkirim',
        'confirmed' => 'Pesanan Dikonfirmasi Selesai',
    ];

    $descMap = [
        'waiting' => 'Pesanan Anda telah diterima sistem dan menunggu konfirmasi kasir/admin.',
        'waiting_owner' => 'Bukti pembayaran sedang diverifikasi oleh admin toko.',
        'waiting_payment' => 'Silakan lakukan pembayaran sesuai metode yang dipilih.',
        'accepted' => 'Admin telah mengonfirmasi pesanan Anda dan segera diteruskan ke dapur.',
        'cooking' => 'Dapur 3GFood sedang memasak hidangan lezat pesanan Anda dengan higienis.',
        'ready_to_deliver' => 'Makanan sudah selesai dimasak dan dikemas, menunggu kurir berangkat.',
        'delivering' => 'Kurir 3GFood sedang dalam perjalanan mengantarkan pesanan ke alamat Anda.',
        'completed' => 'Pesanan telah berhasil diantar dan diterima. Selamat menikmati hidangan!',
        'confirmed' => 'Pesanan telah selesai. Terima kasih telah mempercayai 3GFood!',
    ];

    return response()->json([
        'success' => true,
        'order' => [
            'id' => $order->id,
            'order_code' => $order->order_code,
            'status' => $order->status,
            'status_label' => $labelMap[$order->status] ?? $order->status,
            'status_desc' => $descMap[$order->status] ?? '',
            'step' => $stepperMap[$order->status] ?? 1,
            'total' => (int) $order->total,
            'total_formatted' => 'Rp ' . number_format($order->total, 0, ',', '.'),
            'payment_method' => $order->payment_method,
            'payment_proof' => $order->payment_proof,
            'courier_name' => $order->courier?->name,
            'courier_phone' => $order->courier?->phone,
            'created_at' => $order->created_at?->format('d/m/Y H:i'),
            'accepted_at' => $order->accepted_at?->format('H:i'),
            'cooking_at' => $order->cooking_at?->format('H:i'),
            'cooked_at' => $order->cooked_at?->format('H:i'),
            'delivering_at' => $order->delivering_at?->format('H:i'),
            'completed_at' => $order->completed_at?->format('H:i'),
            'items_count' => $order->items->count(),
            'items' => $order->items->map(function ($it) {
                return [
                    'name' => $it->menu_name,
                    'quantity' => $it->quantity,
                    'unit_price' => $it->unit_price,
                    'total' => $it->quantity * $it->unit_price,
                ];
            }),
        ],
    ]);
})->name('api.order.status');

/*
|--------------------------------------------------------------------------
| Real-Time Toko Status Check
|--------------------------------------------------------------------------
*/
Route::get('/api/store-status', function () {
    $shopSettings = PengaturanToko::latest()->first() ?? (object)[
        'status_toko' => 'buka',
        'jam_buka' => '08:00',
        'jam_tutup' => '21:00',
    ];

    $shopOpen = ($shopSettings->status_toko === 'buka');
    if ($shopOpen && !empty($shopSettings->jam_buka) && !empty($shopSettings->jam_tutup)) {
        $nowTime = date('H:i');
        $shopOpen = $nowTime >= $shopSettings->jam_buka && $nowTime <= $shopSettings->jam_tutup;
    }

    return response()->json([
        'success' => true,
        'shop_open' => $shopOpen,
        'status_toko' => $shopSettings->status_toko,
        'jam_buka' => $shopSettings->jam_buka,
        'jam_tutup' => $shopSettings->jam_tutup,
        'current_time' => date('H:i'),
    ]);
})->name('api.store.status');



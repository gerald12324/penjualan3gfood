<?php

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Courier;
use App\Models\CourierPayout;
use App\Models\PengaturanToko;
use App\Models\PencairanKurir;
use App\Models\Voucher;

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

Route::middleware('auth')->get('/admin/register', function () {
    return view('register');
})->name('register');

Route::middleware('auth')->post('/admin/register', function (Request $request) {
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    \App\Models\User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
    ]);

    return redirect('/admin')->with('success', 'Admin baru berhasil didaftarkan.');
})->name('admin.register.submit');

Route::middleware('auth')->post('/admin/settings', function (Request $request) {
    $data = $request->validate([
        'status_toko' => ['required', 'in:buka,tutup'],
        'jam_buka' => ['required', 'date_format:H:i'],
        'jam_tutup' => ['required', 'date_format:H:i'],
    ]);

    PengaturanToko::updateOrCreate([], $data);

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

Route::any('/', function () {
    $screenParam = request()->query('screen', 'home');
    $screen = in_array($screenParam, ['home', 'cart', 'buyer', 'payment', 'confirmation', 'status', 'feedback', 'track'], true) ? $screenParam : 'home';
    $category = request()->query('category');
    
    $sessionOrder = session('order');
    $order = $sessionOrder && isset($sessionOrder['order_code'])
        ? Order::where('order_code', $sessionOrder['order_code'])->first()?->toArray()
        : null;

    $shopSettings = PengaturanToko::latest()->first() ?? PengaturanToko::create([
        'status_toko' => 'buka',
        'jam_buka' => '08:00',
        'jam_tutup' => '21:00',
    ]);

    $shopOpen = ($shopSettings->status_toko === 'buka');
    if ($shopOpen && $shopSettings->jam_buka && $shopSettings->jam_tutup) {
        $nowTime = \Carbon\Carbon::now()->format('H:i');
        $shopOpen = $nowTime >= $shopSettings->jam_buka && $nowTime <= $shopSettings->jam_tutup;
    }

    $managedMenu = MenuItem::where('is_available', true)->orderBy('category')->orderBy('name')->get();
    
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

    $screens = ['home' => 'Beranda', 'cart' => 'Keranjang', 'status' => 'Status Pesanan', 'feedback' => 'Kritik dan Saran', 'track' => 'Lacak Pesanan'];

    // Track order logic
    $trackedOrder = null;
    $trackCode = request()->query('order_code');
    if ($trackCode) {
        $trackedOrder = Order::where('order_code', $trackCode)->orWhere('phone', $trackCode)->first()?->toArray();
    }

    return view('foodie', compact(
        'screens', 'screen', 'category', 'menu', 'cart', 'buyer', 
        'subtotal', 'delivery', 'voucher', 'voucherDiscount', 'grandTotal', 
        'shopSettings', 'shopOpen', 'total', 'visibleMenu', 'cartCount', 'order', 'managedMenu', 'trackedOrder', 'trackCode'
    ));
});

Route::get('/cart/add/{item}', function (int $item) {
    $cart = session('cart', []);
    $cart[$item] = ($cart[$item] ?? 0) + 1;
    session(['cart' => $cart]);
    return redirect()->to('/?screen=home#menu-section');
})->whereNumber('item');

Route::get('/cart/remove/{item}', function (int $item) {
    $cart = session('cart', []);
    unset($cart[$item]);
    session(['cart' => $cart]);

    return redirect()->to('/?screen=cart');
})->whereNumber('item');

Route::get('/cart/clear', function () {
    session()->forget('cart');

    return redirect()->to('/?screen=cart');
});

Route::post('/checkout/buyer', function (Request $request) {
    $data = $request->validate([
        'customer_name' => ['required', 'string', 'max:120'],
        'phone' => ['required', 'string', 'max:30'],
        'address' => ['required', 'string', 'max:500'],
    ]);
    session(['buyer' => $data]);
    return redirect()->to('/?screen=payment');
})->name('checkout.buyer');

Route::post('/checkout/payment', function (Request $request) {
    $method = $request->validate(['payment_method' => ['required', 'in:cash,bank,digital']])['payment_method'];
    $buyer = session('buyer');
    abort_unless($buyer, 302, 'Data pembeli belum diisi.');
    $subtotal = 0;
    foreach (session('cart', []) as $itemId => $quantity) {
        $menuItem = MenuItem::where('id', $itemId)->where('is_available', true)->first();
        $subtotal += ($menuItem?->price ?? 0) * $quantity;
    }
    abort_unless($subtotal > 0, 302, 'Keranjang masih kosong.');
    $order = Order::create([
        ...$buyer,
        'order_code' => 'FE-' . now()->format('ymdHis'),
        'payment_method' => $method,
        'total' => $subtotal + 1000,
        'status' => 'waiting',
    ]);
    foreach (session('cart', []) as $itemId => $quantity) {
        $menuItem = MenuItem::where('id', $itemId)->where('is_available', true)->first();
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

Route::middleware('auth')->get('/admin', function () {
    $status = request()->query('status');
    $orders = Order::latest()
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
    $adminPhone = \App\Services\WhatsAppService::normalizePhone(config('services.whatsapp.admin_phone', ''));
    if (!$adminPhone) {
        return back()->withErrors(['feedback' => 'Nomor WhatsApp admin belum dikonfigurasi.']);
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
        'image' => 'nullable|image|max:2048',
    ]);
    $data['is_available'] = $request->boolean('is_available', true);
    $imagePath = $request->file('image')?->store('menu', 'public');
    $data['image_url'] = $imagePath ? asset('storage/' . $imagePath) : null;
    unset($data['image']);
    MenuItem::create($data);
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
        'image' => 'nullable|image|max:2048',
    ]);
    $data['is_available'] = $request->boolean('is_available', $menu->is_available);
    if ($request->file('image')) {
        $imagePath = $request->file('image')->store('menu', 'public');
        $data['image_url'] = asset('storage/' . $imagePath);
    }
    unset($data['image']);
    $menu->update($data);
    return back()->with('success', 'Menu berhasil diupdate.');
})->name('admin.menu.update');

Route::middleware('auth')->delete('/admin/menu/{menu}', function (MenuItem $menu) {
    $menu->delete();
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
